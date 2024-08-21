<?php

namespace App\Notifications;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Channels\BroadcastChannel;
use Illuminate\Broadcasting\Channel;

class RegisterCompanyNotification extends Notification
{
    use Queueable;

    protected $company;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        //

        // return (new MailMessage)
        //             ->subject("New Order #{$this->order->number}")
        //             ->from('notification@ajyal-store.ps', 'AJYAL Store')
        //             ->greeting("Hi {$notifiable->name},")
        //             ->line("A new order (#{$this->order->number}) ")
        //             ->action('View Order', url('/dashboard'))
        //             ->line('Thank you for using our application!');
    }

    public function toDatabase($notifiable)
    {


        return [
            'body' => "A new login (#{$this->company->name}) ",
            'url' => url("/companies/{$this->company->id}"),
        ];
    }

    public function toBroadcast($notifiable)
    {

        return new BroadcastMessage([
            'body' => "A new login (#{$this->company->name}) ",
            'url' => url("/companies/{$this->company->id}"),
        ]);
    }
     public function broadcastOn()
    {
        return new Channel('public-App.Models.Manager');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
