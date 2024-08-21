<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\BaseController;

class ApplicationCourseController extends BaseController
{
    public function test() {}
    public function applications($course_id, Request $request)
    {
        // Set a default number of items per page
        $perPage = $request->input('per_page', 2);
        // Query with pagination and transformation
        $applications = \App\Models\CourseApplication::with('user')
            ->where('course_id', $course_id)
            ->paginate($perPage)->through(function ($application) {
                return [
                    'id' => $application->id,
                    'user_id' => $application->user_id,
                    'status' => $application->status,
                    'name' => $application->user->first_name . ' ' . $application->user->last_name,
                    'image' => optional($application->user->user_cv)->image,
                    'topic' => $application->user->topic,
                ];
            });

        // Return the paginated and transformed applications
        return $this->sendResponse($applications, 'Applications retrieved successfully');
    }



    public function details_application($application_id)
    {
        $application = \App\Models\CourseApplication::with('user.user_cv')
            ->where('id', $application_id)
            ->first();

        if (!$application) {
            return $this->sendError(404, 'Application not found.');
        }

        $user = $application->user;
        $user->image = optional($user->user_cv)->image;
        if ($application->status == 'applied') {
            $user->is_applied = true;
        } else {
            $user->is_applied = false;
        }
        return $this->sendResponse($user, 'application');
    }
    public function accept_refuse(Request $request, $application_id, NotificationService $notificationService)
    {
        $request->validate([
            'status' => 'required|in:applied,rejected',
        ]);
        $application = \App\Models\CourseApplication::find($application_id);
        if (!$application) {
            return $this->sendError(404, 'Application not found.');
        }
        $application->status = $request->status;
        $application->save();
        if ($request->status == 'applied') {
            $messages = [
                'ar' => [
                    'title' => 'طلب مقبول',
                    'description' => 'تم قبول طلبك لحضور الدورة التدريبية. يرجى تأكيد حضورك.'
                ],
                'en' => [
                    'title' => 'Application Accepted',
                    'description' => 'Your application to attend the course has been accepted. Please confirm your attendance.'
                ]
            ];
        } else {
            $messages = [
                'ar' => [
                    'title' => 'طلب غير مقبول',
                    'description' => 'نأسف، لم يتم قبول طلبك لحضور الدورة التدريبية.'
                ],
                'en' => [
                    'title' => 'Application Rejected',
                    'description' => 'Sorry, your application to attend the course has not been accepted.'
                ]
            ];
        }
        $url = '';
        $user = User::where('id', $application->user_id)->first();
        $notificationService->sendNotification($user, $messages, $url, 'user');


        return $this->sendResponse($request->status, "Application has been {$request->status}.");
    }
}
