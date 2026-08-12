<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ServiceItem;
use App\Models\User;
use App\Notifications\OrderPlaced;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orders)
    {
    }

    /** List the student's own orders. */
    public function index(Request $request): View
    {
        $orders = $request->user()->ordersAsStudent()
            ->with('worker')
            ->latest()
            ->paginate(10);

        return view('student.orders.index', ['orders' => $orders]);
    }

    /** Show the order-builder form for a chosen worker. */
    public function create(Request $request): View
    {
        $worker = User::where('role', User::ROLE_WORKER)
            ->whereHas('workerProfile', fn ($q) => $q->where('is_approved', true))
            ->with('workerProfile', 'dorm')
            ->findOrFail($request->integer('worker'));

        return view('student.orders.create', [
            'worker' => $worker,
            'serviceItems' => ServiceItem::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    /** Persist a new order with snapshotted, server-priced line items. */
    public function store(Request $request): RedirectResponse
    {
        $student = $request->user();

        // Drop rows the student left at zero before validating.
        $request->merge([
            'items' => collect($request->input('items', []))
                ->filter(fn ($row) => (int) ($row['quantity'] ?? 0) > 0)
                ->values()
                ->all(),
        ]);

        $validated = $request->validate([
            'worker_id' => [
                'required',
                Rule::exists('users', 'id')->where('role', User::ROLE_WORKER),
            ],
            'notes' => ['nullable', 'string', 'max:1000'],
            'pickup_location' => ['nullable', 'string', 'max:255'],
            'scheduled_pickup_at' => ['nullable', 'date', 'after_or_equal:now'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.service_item_id' => ['required', Rule::exists('service_items', 'id')],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ], [
            'items.required' => 'Please select at least one item to launder.',
            'items.min' => 'Please select at least one item to launder.',
        ]);

        // Confirm the worker is approved and active.
        $worker = User::where('id', $validated['worker_id'])
            ->where('role', User::ROLE_WORKER)
            ->where('is_active', true)
            ->whereHas('workerProfile', fn ($q) => $q->where('is_approved', true))
            ->firstOrFail();

        // Price authoritatively from the official list; ignore any client prices.
        $priceMap = ServiceItem::whereIn('id', collect($validated['items'])->pluck('service_item_id'))
            ->get()
            ->keyBy('id');

        $order = DB::transaction(function () use ($student, $worker, $validated, $priceMap) {
            $order = Order::create([
                'student_id' => $student->id,
                'worker_id' => $worker->id,
                'dorm_id' => $student->dorm_id,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
                'pickup_location' => $validated['pickup_location'] ?? null,
                'scheduled_pickup_at' => $validated['scheduled_pickup_at'] ?? null,
                'total_price' => 0,
            ]);

            $total = 0;
            foreach ($validated['items'] as $row) {
                $item = $priceMap[$row['service_item_id']];
                $qty = (int) $row['quantity'];
                $lineTotal = $item->unit_price * $qty;
                $total += $lineTotal;

                $order->items()->create([
                    'service_item_id' => $item->id,
                    'item_name' => $item->name,
                    'service' => $item->service,
                    'unit_price' => $item->unit_price,
                    'quantity' => $qty,
                    'line_total' => $lineTotal,
                ]);
            }

            $order->update(['total_price' => $total]);

            // Log the initial status.
            $order->statusHistory()->create([
                'status' => 'pending',
                'note' => 'Order placed by student.',
                'changed_by' => $student->id,
            ]);

            return $order;
        });

        // Notify the worker of the new order.
        $worker->notify(new OrderPlaced($order));

        return redirect()
            ->route('student.orders.show', $order)
            ->with('status', "Order {$order->reference} placed successfully.");
    }

    /** Tracking view for a single order (must belong to the student). */
    public function show(Request $request, Order $order): View
    {
        $this->authorizeOrder($request, $order);

        $order->load(['items', 'worker.workerProfile', 'statusHistory.changedBy', 'ratings', 'dorm']);

        return view('student.orders.show', ['order' => $order]);
    }

    /** Cancel an order while it is still pending/accepted. */
    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeOrder($request, $order);

        if (! in_array($order->status, ['pending', 'accepted'], true)) {
            return back()->withErrors(['status' => 'This order can no longer be cancelled.']);
        }

        $validated = $request->validate([
            'cancel_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $order->cancel_reason = $validated['cancel_reason'] ?? null;
        $this->orders->transition($order, 'cancelled', $request->user(), $validated['cancel_reason'] ?? null);

        return back()->with('status', "Order {$order->reference} cancelled.");
    }

    /** Ensure the current student owns the order. */
    private function authorizeOrder(Request $request, Order $order): void
    {
        abort_unless($order->student_id === $request->user()->id, 403);
    }
}
