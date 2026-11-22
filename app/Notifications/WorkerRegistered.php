<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WorkerRegistered extends Notification
{
    use Queueable;

    public function __construct(public User $worker)
    {
    }

    /**
     * Delivered to every administrator via the database channel (in-app bell).
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
        $dorm = $this->worker->dorm?->name;

        return [
            'type' => 'worker_registered',
            'worker_id' => $this->worker->id,
            'message' => "New laundry worker {$this->worker->name}".($dorm ? " ({$dorm})" : '').' is awaiting approval.',
            'url' => route('admin.workers.index'),
        ];
    }
}
