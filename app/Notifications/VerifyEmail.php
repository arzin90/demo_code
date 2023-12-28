<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmail extends Notification implements ShouldQueue
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
        if ($notifiable->email) {
            return ['mail'];
        }

        return [SmsChannel::class];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject('Подтверждение почты')
            ->line('Ваш электронный адрес указан как контактный. Используйте ниже написанный номер для подтверждения этого адреса.')
            ->line($this->code)
            ->line('Если Вы ничего не делали, то просто проигнорируйте это письмо.');
    }

    /**
     * @return string
     */
    public function toSms($notifiable)
    {
        return sprintf('Helfe: %d - код подтверждения', $this->code);
    }
}
