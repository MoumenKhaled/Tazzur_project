<?php
namespace App\Services;



use Illuminate\Support\Facades\Auth;

class NotificationDatabaseService
{
    protected $user;

    public function __construct()
    {
        $this->user = $this->getAuthenticatedUser();
    }

    protected function getAuthenticatedUser()
    {
   
        $guard = request()->route('guard', 'web');
        return Auth::guard($guard)->user();
    }

    public function getAllNotifications()
    {
        return $this->user->notifications;
    }

    public function getUnreadNotifications()
    {
        return $this->user->unreadNotifications;
    }

    public function markAsRead($notificationId)
    {
        $notification = $this->user->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
            return true;
        }

        return false;
    }

    public function markAllAsRead()
    {
        $this->user->unreadNotifications->markAsRead();
    }

    public function deleteNotification($notificationId)
    {
        $notification = $this->user->notifications()->find($notificationId);

        if ($notification) {
            $notification->delete();
            return true;
        }

        return false;
    }

    public function deleteAllNotifications()
    {
        $this->user->notifications()->delete();
    }
}
