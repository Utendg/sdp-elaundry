<?php

namespace App\Notifications;

use App\Models\OrderNote;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class OrderNoteAdded extends Notification
{
    use Queueable;

    public function __construct(public OrderNote $note)
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
        $order = $this->note->order;

        return [
            'type' => 'order_note_added',
            'order_id' => $order->id,
            'reference' => $order->reference,
            'message' => "New note on order {$order->reference}: ".Str::limit($this->note->body, 60),
            'url' => route('student.orders.show', $order),
        ];
    }
}
