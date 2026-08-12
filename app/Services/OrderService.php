<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;

class OrderService
{
    /**
     * Apply a new status to an order: persist it, stamp the relevant lifecycle
     * timestamp, and append an entry to the status-history log.
     */
    public function transition(Order $order, string $status, ?User $actor = null, ?string $note = null): Order
    {
        $order->status = $status;

        match ($status) {
            'accepted' => $order->accepted_at ??= now(),
            'completed' => $order->completed_at ??= now(),
            'cancelled', 'rejected' => $order->cancelled_at ??= now(),
            default => null,
        };

        $order->save();

        $order->statusHistory()->create([
            'status' => $status,
            'note' => $note,
            'changed_by' => $actor?->id,
        ]);

        // Keep the worker's completed-order counter in sync.
        if ($status === 'completed' && $order->worker_id) {
            $profile = $order->worker?->workerProfile;
            if ($profile) {
                $profile->increment('orders_completed');
            }
        }

        return $order;
    }
}
