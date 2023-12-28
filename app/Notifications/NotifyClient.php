<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotifyClient extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 5;

    private $specialist;

    /**
     * NotifyClient constructor.
     */
    public function __construct($specialist)
    {
        $this->specialist = $specialist;
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
        return ['mail'];
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
            ->subject('Приглашение в приложение')
            ->line(sprintf('Специалист: %s', $this->specialist))
            ->line('Приглашает Вас зарегистрироваться в приложении Helfe')
            ->line(sprintf('Play Market: %s', 'https://play.google.com/store/apps'))
            ->line(sprintf('APP STORE: %s', 'https://www.apple.com/app-store'))
            ->line('Если Вы ничего не делали, то просто проигнорируйте это письмо.');
    }
}
