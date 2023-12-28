<?php

namespace App\Notifications;

use App\Services\SMSService;
use Illuminate\Notifications\Notification;

class SmsChannel
{
    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     *
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toSms($notifiable);

        SMSService::sendMessage($message, $notifiable->phone);
    }
}
