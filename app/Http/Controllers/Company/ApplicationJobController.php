<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Services\NotificationService;
use App\Models\User;

class ApplicationJobController extends BaseController
{
    public function applications($job_id, Request $request)
    {
        $perPage = $request->input('per_page', 2);
        $status = $request->query('status', 'pending');

        $applications = \App\Models\JobApplication::with(['user' => function ($query) {
            $query->select('id', 'first_name', 'last_name');
        }])
            ->select('id', 'user_id', 'status', 'priority_application')
            ->where('job_id', $job_id)
            ->where('status', $status)
            ->paginate($perPage)
            ->through(function ($application) {
                return [
                    'id' => $application->id,
                    'user_id' => $application->user_id,
                    'status' => $application->status,
                    'priority_application' => $application->priority_application,
                    'name' => $application->user->first_name . ' ' . $application->user->last_name,
                    'image' => optional($application->user->user_cv)->image,
                    'topic' => $application->user->topic,
                ];
            });


        return $this->sendResponse($applications, 'Applications retrieved successfully');
    }

    public function details_application($application_id)
    {
        $application = \App\Models\JobApplication::with('user.user_cv')
            ->where('id', $application_id)
            ->first();

        if (!$application) {
            return $this->sendError(404, 'Application not found.');
        }
        $user = $application->user;
        $user->image = optional($user->user_cv)->image;

        return $this->sendResponse($user, 'application');
    }


    public function accept_refuse(Request $request, $application_id, NotificationService $notificationService)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected',
            'date' => 'required_if:status,accepted|date',
            'time' => 'required_if:status,accepted|date_format:H:i',
            'location' => 'required_if:status,accepted|string',
            'phone' => 'required_if:status,accepted|string',
        ], [
            'date.required_if' => 'The date field is required when the status is accepted.',
            'time.required_if' => 'The time field is required when the status is accepted.',
            'location.required_if' => 'The location field is required when the status is accepted.',
            'phone.required_if' => 'The phone field is required when the status is accepted.',
        ]);

        $application = \App\Models\JobApplication::find($application_id);

        if (!$application) {
            return $this->sendError(404, 'Application not found.');
        }

        $application->status = $request->status;

        if ($request->status == 'accepted') {
            $messages = [
                'ar' => [
                    'title' => 'طلبكم قد تم قبوله!',
                    'description' => "تهانينا، لقد تم قبول طلبكم لفرصة العمل. تفاصيل المقابلة كالتالي:\nالتاريخ: " . $request->date . "\nالوقت: " . $request->time . "\nالمكان: " . $request->location . "\nيرجى الاتصال على الرقم التالي لأي استفسار: " . $request->phone
                ],
                'en' => [
                    'title' => 'Your application has been accepted!',
                    'description' => "Congratulations, your application for the job opportunity has been accepted. The details of the interview are as follows:\nDate: " . $request->date . "\nTime: " . $request->time . "\nLocation: " . $request->location . "\nPlease call the following number for any inquiries: " . $request->phone
                ]
            ];
            // $application->date = $request->date;
            // $application->time = $request->time;
            // $application->location = $request->location;
            // $application->phone = $request->phone;
        } else {
            $messages = [
                'ar' => [
                    'title' => 'طلبكم لم يتم قبوله',
                    'description' => "نشكركم على اهتمامكم بالانضمام إلى فريقنا. للأسف، بعد مراجعة طلبكم بعناية، نعلمكم بأننا لن نتمكن من تقديم فرصة عمل لكم في هذا الوقت. نشجعكم على التقديم مجددًا في المستقبل عند توفر فرص جديدة."
                ],

                'en' => [
                    'title' => 'Your application has not been accepted',
                    'description' => "Thank you for your interest in joining our team. Unfortunately, after careful review of your application, we are unable to offer you a position at this time. We encourage you to reapply in the future as new opportunities become available."
                ]
            ];
        }
        $url = '';
        $user = User::where('id', $application->user_id)->first();
        $notificationService->sendNotification($user, $messages, $url, 'user');
        $application->save();

        return $this->sendResponse($request->status, "Application has been {$request->status}.");
    }
}
