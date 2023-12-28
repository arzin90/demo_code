<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class VerifyPhone extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 5;

    private $code;

    /**
     * VerifyEmail constructor.
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * Get the notification's channels.
     *
     * @param mixed $notifiable
     *
     * @return array|string
     */
    public function via($notifiable)
    {
        return [SmsChannel::class];
    }

    /**
     * @return string
     */
    public function toSms($notifiable)
    {
        return sprintf('Helfe: %d - код подтверждения', $this->code);
    }
}
