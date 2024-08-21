<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\Job;
use App\Models\User;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Broadcasting\Channel;

class ApplicationJobNotification extends Notification
{
    use Queueable;
    protected $job;
    protected $user;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Job $job,User $user)
    {
        $this->job = $job;
        $this->user = $user;
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
            'body' => "A new  Application Job (#{$this->job->name}) by (#{$this->user->first_name}) ",
            'url' => url("applications/jobs/{$this->job->id}"),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'body' => "A new  Application Job (#{$this->job->name}) by (#{$this->user->first_name}) ",
            'url' => url("applications/jobs/{$this->job->id}"),
        ]);
    }
     public function broadcastOn()
    {
        return new Channel('public-App.Models.Manager');
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
