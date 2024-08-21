<?php
namespace App\Services;

use App\Jobs\SendNotificationJob;
use Illuminate\Support\Facades\Bus;

class NotificationService
{
    public function sendNotification($user, $messages, $url, $type = 'user')
    {
        Bus::dispatch(new SendNotificationJob($user, $messages, $url, $type));
    }
}


