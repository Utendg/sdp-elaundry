<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification
{
    use Queueable;

    public function __construct(public Order $order, public string $newStatus)
    {
    }

    /**
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
        $label = ucwords(str_replace('_', ' ', $this->newStatus));

        return [
            'type' => 'order_status_changed',
            'order_id' => $this->order->id,
            'reference' => $this->order->reference,
            'status' => $this->newStatus,
            'message' => "Order {$this->order->reference} is now: {$label}.",
            'url' => route('student.orders.show', $this->order),
        ];
    }
}
