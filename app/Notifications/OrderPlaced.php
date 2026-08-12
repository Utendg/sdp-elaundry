<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderPlaced extends Notification
{
    use Queueable;

    public function __construct(public Order $order)
    {
    }

    /**
     * Delivered to the worker via the database channel (in-app bell).
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order_placed',
            'order_id' => $this->order->id,
            'reference' => $this->order->reference,
            'message' => "New order {$this->order->reference} from {$this->order->student->name}.",
            // Worker order routes arrive in a later increment; link only once available.
            'url' => \Illuminate\Support\Facades\Route::has('worker.orders.show')
                ? route('worker.orders.show', $this->order)
                : null,
        ];
    }
}
