<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Rating;
use App\Notifications\OrderStatusChanged;
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

    /** List the worker's orders, grouped by lifecycle stage. */
    public function index(Request $request): View
    {
        $base = fn () => $request->user()->ordersAsWorker()->with('student');

        return view('worker.orders.index', [
            'pending' => $base()->where('status', 'pending')->latest()->get(),
            'active' => $base()->whereIn('status', ['accepted', 'picked_up', 'washing', 'ironing', 'ready'])->latest()->get(),
            'completed' => $base()->whereIn('status', ['completed', 'cancelled', 'rejected'])->latest()->take(20)->get(),
        ]);
    }

    /** Show one order with the actions available at its current status. */
    public function show(Request $request, Order $order): View
    {
        $this->authorizeOrder($request, $order);

        $order->load(['items', 'student', 'statusHistory.changedBy', 'ratings']);

        return view('worker.orders.show', [
            'order' => $order,
            'nextStatuses' => $this->orders->nextStatusesFor($order->status),
            'workerRating' => $order->ratings->firstWhere('direction', Rating::WORKER_TO_STUDENT),
        ]);
    }

    /** Accept a pending order. */
    public function accept(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeOrder($request, $order);

        if ($order->status !== 'pending') {
            return back()->withErrors(['status' => 'Only pending orders can be accepted.']);
        }

        $this->orders->transition($order, 'accepted', $request->user(), 'Accepted by worker.');
        $order->student->notify(new OrderStatusChanged($order, 'accepted'));

        return back()->with('status', "Order {$order->reference} accepted.");
    }

    /** Reject a pending order. */
    public function reject(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeOrder($request, $order);

        if ($order->status !== 'pending') {
            return back()->withErrors(['status' => 'Only pending orders can be rejected.']);
        }

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $order->cancel_reason = $validated['reason'] ?? null;
        $this->orders->transition($order, 'rejected', $request->user(), $validated['reason'] ?? 'Rejected by worker.');
        $order->student->notify(new OrderStatusChanged($order, 'rejected'));

        return back()->with('status', "Order {$order->reference} rejected.");
    }

    /** Advance an accepted order forward through the laundry pipeline. */
    public function advance(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeOrder($request, $order);

        $validated = $request->validate([
            'status' => ['required', 'string'],
        ]);

        if (! $this->orders->canWorkerTransition($order->status, $validated['status'])) {
            return back()->withErrors(['status' => 'That is not a valid next step for this order.']);
        }

        $this->orders->transition($order, $validated['status'], $request->user());
        $order->student->notify(new OrderStatusChanged($order, $validated['status']));

        return back()->with('status', "Order {$order->reference} updated.");
    }

    /** Worker rates the student after a completed order. */
    public function rateStudent(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeOrder($request, $order);

        if (! $order->isCompleted()) {
            return back()->withErrors(['rating' => 'You can only rate a completed order.']);
        }

        $exists = Rating::where('order_id', $order->id)
            ->where('direction', Rating::WORKER_TO_STUDENT)
            ->exists();
        if ($exists) {
            return back()->withErrors(['rating' => 'You have already rated this student.']);
        }

        $validated = $request->validate([
            'stars' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        Rating::create([
            'order_id' => $order->id,
            'rater_id' => $request->user()->id,
            'ratee_id' => $order->student_id,
            'direction' => Rating::WORKER_TO_STUDENT,
            'stars' => $validated['stars'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return back()->with('status', 'Your rating of the student has been recorded.');
    }

    /** Ensure the current worker owns the order. */
    private function authorizeOrder(Request $request, Order $order): void
    {
        abort_unless($order->worker_id === $request->user()->id, 403);
    }
}
