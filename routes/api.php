<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('manager')->group(fn() => require __DIR__ . '/api/manager.php');
Route::prefix('advisor')->group(fn() => require __DIR__ . '/api/advisor.php');
Route::prefix('company')->group(fn() => require __DIR__ . '/api/company.php');
Route::prefix('seeker')->group(fn() => require __DIR__ . '/api/seeker.php');
Route::prefix('guest')->group(fn() => require __DIR__ . '/api/guest.php');

// use App\Services\NotificationService;

// Route::post('/notify', function (Request $request, NotificationService $service) {
//     $service->sendNotification($request->all());
//     return response()->json(['status' => 'success']);
// });

Route::prefix('{guard}')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'showNotifications'])->name('notifications.index');
    Route::post('/notifications/read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::delete('/notifications/delete/{id}', [NotificationController::class, 'deleteNotification'])->name('notifications.delete');
    Route::delete('/notifications/delete-all', [NotificationController::class, 'deleteAllNotifications'])->name('notifications.deleteAll');
});



