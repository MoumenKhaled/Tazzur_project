<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceNotification extends Notification
{
    use Queueable;

    protected $messages;
    protected $url;

    /**
     * Create a new notification instance.
     *
     * @param array $messages Associative array containing 'title' and 'description' for localization.
     * @param string $url URL for the action button in the notification.
     */
    public function __construct(array $messages, $url = null)
    {
        $this->messages = $messages;
        $this->url = $url;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'messages' => $this->messages,
            'url' => $this->url,
        ];
    }
}
