<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dorm;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /** Monitor every order across all dorms, with optional filters. */
    public function index(Request $request): View
    {
        $orders = Order::with(['student', 'worker', 'dorm'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('dorm_id'), fn ($q) => $q->where('dorm_id', $request->integer('dorm_id')))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->string('search');
                $q->where('reference', 'like', "%{$term}%");
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'dorms' => Dorm::orderBy('name')->get(),
            'statuses' => ['pending', 'accepted', 'picked_up', 'washing', 'ironing', 'ready', 'completed', 'cancelled', 'rejected'],
            'filters' => $request->only(['status', 'dorm_id', 'search']),
        ]);
    }

    /** Full detail on a single order. */
    public function show(Order $order): View
    {
        $order->load(['items', 'student', 'worker', 'dorm', 'statusHistory.changedBy', 'ratings', 'complaints']);

        return view('admin.orders.show', ['order' => $order]);
    }
}
