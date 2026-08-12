<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;

class OrderService
{
    /**
     * Valid forward status transitions a worker may perform, keyed by the
     * order's current status. Accept/reject (from "pending") are handled
     * through dedicated controller actions.
     *
     * @var array<string, array<int, string>>
     */
    public const WORKER_TRANSITIONS = [
        'accepted' => ['picked_up'],
        'picked_up' => ['washing'],
        'washing' => ['ironing', 'ready'], // ironing may be skipped for wash-only loads
        'ironing' => ['ready'],
        'ready' => ['completed'],
    ];

    /** The statuses a worker may move the given order to next. */
    public function nextStatusesFor(string $current): array
    {
        return self::WORKER_TRANSITIONS[$current] ?? [];
    }

    /** Whether a worker may move an order from $current to $target. */
    public function canWorkerTransition(string $current, string $target): bool
    {
        return in_array($target, $this->nextStatusesFor($current), true);
    }

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
