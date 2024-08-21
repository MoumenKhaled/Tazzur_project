<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\CompanyConsulution;
use App\Models\UserConsultation;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Broadcasting\Channel;
class AdvisorReplyToUserNotification extends Notification
{
    use Queueable;
    protected $user_consultation;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(UserConsultation $user_consultation)
    {
        $this->user_consultation = $user_consultation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }
    public function toDatabase($notifiable)
    {
        return [
            'body' => "A new Rebly Consulutation",
            'url' => url("/advisors/{$this->user_consultation->advisor_id}"),
        ];
    }

    public function toBroadcast($notifiable)
    {

        return new BroadcastMessage([
            'body' =>  "A new Rebly Consulutation",
            'url' => url("/advisors/{$this->user_consultation->advisor_id}"),
        ]);
    }
     public function broadcastOn()
    {
        return new Channel('public-App.Models.User');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
