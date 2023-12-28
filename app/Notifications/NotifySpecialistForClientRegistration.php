<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotifySpecialistForClientRegistration extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 5;

    private $client;

    /**
     * NotifyClient constructor.
     *
     * @param mixed $client
     */
    public function __construct($client)
    {
        $this->client = $client;
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
            ->subject('Уведомления от Helfe')
            ->line(sprintf('Клиент <%s> зарегистрировано', $this->client))
            ->line('Если Вы ничего не делали, то просто проигнорируйте это письмо.');
    }
}
