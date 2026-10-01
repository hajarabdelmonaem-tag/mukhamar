<?php

namespace App\Notifications;

use App\Notifications\Channels\UserNotificationChannel;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class GeneralNotification extends Notification
{
    public function __construct(
        public string $title,
        public string $body,
        public array $data = []
    ) {}

    public function via(object $notifiable): array
    {
        return [
            UserNotificationChannel::class,
            FcmChannel::class,
        ];
    }

    public function toFcm(object $notifiable): FcmMessage
    {
        return (new FcmMessage(
            notification: new FcmNotification(
                title: $this->title,
                body: $this->body,
            )
        ))->data($this->data);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'data' => $this->data,
        ];
    }
}
