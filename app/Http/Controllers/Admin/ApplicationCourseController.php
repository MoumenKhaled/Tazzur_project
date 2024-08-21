<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

class ApplicationCourseController extends BaseController
{
    public function applications(Request $request, $course_id)
    {
        $perPage = $request->input('per_page', 2);
        $applications = \App\Models\CourseApplication::with('user')
            ->where('course_id', $course_id)
            ->paginate($perPage)
            ->through(function ($application) {
                return [
                    'id' => $application->id,
                    'user_id' => $application->user_id,
                    'status' => $application->status,
                    'name' => $application->user->first_name . ' ' . $application->user->last_name,
                    'image' => $application->user->user_cv->image ?? 'https://upload.wikimedia.org/wikipedia/commons/4/49/A_black_image.jpg',
                    'topic' => $application->user->topic,
                ];
            });

        return $this->sendResponse($applications, 'Applications retrieved successfully');
    }


    public function details_application($application_id)
    {
        $application = \App\Models\CourseApplication::with('user.user_cv')
        ->where('id', $application_id)
        ->first();

        if (!$application) {
            return $this->sendError(404,'Application not found.');
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
}
