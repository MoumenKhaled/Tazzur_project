<?php

namespace App\Http\Controllers;

use App\Services\NotificationDatabaseService as NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

   
    public function showNotifications(): JsonResponse
    {
        $notifications = $this->notificationService->getAllNotifications();
        return response()->json([
            'status' => 'success',
            'data' => $notifications
        ], 200);
    }

   
    public function markAsRead($id): JsonResponse
    {
        $this->notificationService->markAsRead($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Notification marked as read'
        ], 200);
    }


    public function markAllAsRead(): JsonResponse
    {
        $this->notificationService->markAllAsRead();
        return response()->json([
            'status' => 'success',
            'message' => 'All notifications marked as read'
        ], 200);
    }

  
    public function deleteNotification($id): JsonResponse
    {
        $this->notificationService->deleteNotification($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Notification deleted'
        ], 200);
    }

   
    public function deleteAllNotifications(): JsonResponse
    {
        $this->notificationService->deleteAllNotifications();
        return response()->json([
            'status' => 'success',
            'message' => 'All notifications deleted'
        ], 200);
    }
}
