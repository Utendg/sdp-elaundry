<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WorkerDeclined extends Notification
{
    use Queueable;

    public function __construct(public ?string $reason = null)
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
        return [
            'type' => 'worker_declined',
            'message' => 'Your laundry worker application was declined'.($this->reason ? ": {$this->reason}" : '.'),
            'url' => route('dashboard'),
        ];
    }
}
