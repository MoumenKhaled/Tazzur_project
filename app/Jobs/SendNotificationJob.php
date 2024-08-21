<?php

namespace App\Jobs;

use App\Models\FcmToken;
use App\Services\FirebaseService;
use App\Notifications\ServiceNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $messages;
    protected $url;
    protected $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $messages, $url, $type = 'user')
    {
        $this->user = $user;
        $this->messages = $messages;
        $this->url = $url;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $customerToken = FcmToken::where([
            'user_id' => $this->user->id,
            'type' => $this->type,
        ])->pluck('token')->toArray();


        if (!empty($customerToken)) {
            FirebaseService::sendNotification($customerToken, $this->messages, $this->url);
        }

        $notification = new ServiceNotification($this->messages, $this->url);
        $this->user->notify($notification);
    }
}
