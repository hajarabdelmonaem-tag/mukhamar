<?php

namespace App\Notifications\Channels;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification as BaseNotification;

class UserNotificationChannel
{
    /**
     * Send the given notification to the user_notifications table.
     *
     * @param  Model  $notifiable
     */
    public function send(mixed $notifiable, BaseNotification $notification): void
    {
        $data = method_exists($notification, 'toDatabase')
            ? $notification->toDatabase($notifiable)
            : $notification->toArray($notifiable);

        Notification::create([
            'user_id' => $notifiable->getKey(),
            'title' => $data['title'] ?? null,
            'body' => $data['body'] ?? null,
            'type' => $data['type'] ?? null,
            'action_url' => $data['action_url'] ?? null,
        ]);
    }
}
