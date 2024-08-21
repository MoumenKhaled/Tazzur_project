<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Notifications\ApplicationsJobFiterNotification;
use App\Services\FirebaseService;
use App\Models\FcmToken;
use App\Models\User;
use App\Models\Company;
class ApplicationJobController extends BaseController
{
    public function index(Request $request, $job_id)
    {
        $perPage = $request->input('per_page', 2);
        $applications = \App\Models\JobApplication::with('user')
            ->where('job_id', $job_id)
            ->select(['id', 'user_id', 'status', 'priority_application'])
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
        $application = \App\Models\JobApplication::with('user')
            ->where('id', $application_id)
            ->first();

        if (!$application) {
            return $this->sendError(404, 'Application not found.');
        }
        return $this->sendResponse($application->user, 'application');
    }
    public function filter() {}
    public function transfer() {}
    public function priority($application_id, $number)
    {

        $application = \App\Models\JobApplication::find($application_id);
        if (!$application) {
            return $this->sendError(404, 'Application not found');
        }
        $job_id = $application->job_id;
        $total_applications = \App\Models\JobApplication::where('job_id', $job_id)->count();
        if ($number < 0 || $number > $total_applications) {
            return $this->sendError(422, 'Invalid priority number. It must be between 0 and ' . $total_applications);
        }
        $existing_priority = \App\Models\JobApplication::where('job_id', $job_id)
            ->where('priority_application', $number)
            ->where('id', '!=', $application_id)
            ->exists();
        if ($existing_priority) {
            return $this->sendError(421, 'Another application already has this priority number');
        }
        $application->priority_application = $number;
        $application->save();
        return $this->sendResponse($application, 'Priority updated successfully');
    }


    public function convert(Request $request,$job_id)
    {
        $job = \App\Models\Job::find($job_id);
        if (!$job) {
            return $this->sendError(404, 'Job not found');
        }
        // if ($job->is_converted) {
        //     return $this->sendError(404, 'Job is already converted');
        // }
        $applications = \App\Models\JobApplication::where(
            [
                'job_id'=>$job_id,
                'status'=>'waitig',
            ])->get();
        if ($applications->isEmpty()) {
            return $this->sendError(422, 'No applications found for this job');
        }
        // foreach ($applications as $application) {
        //     if ($application->priority_application === 0) {
        //         return $this->sendError(422, 'Not all applications have a priority set');
        //     }
        // }
        $job->is_converted = true;
    //    $job->status="finite";
        $job->save();
        $company = Company::where('id', $job->company_id)->first();


        $messages = [
            'ar' => [
                'title' => 'اكتمال فلترة السير الذاتية',
                'description' => 'الآن يمكنكم الاطلاع على قائمة المرشحين المؤهلين جاهزة للمراجعة والمقابلات'
            ],
            'en' => [
                'title' => 'Completion of CV Filtering',
                'description' => 'You can now review the list of qualified candidates ready for interviews'
            ]
        ];

        $url = route('company_application_jobs', ['id' => $job->id, 'status' => 'active']);
        $customerToken = FcmToken::where(
            [
                'user_id' => $company->id,
                'type' => 'company',
            ]
        )->pluck('token')->toArray();
        if (!empty($customerToken)) {
            FirebaseService::sendNotification($customerToken, $messages,  $url);
        }
        $notification = new \App\Notifications\ServiceNotification($messages,$url);
        $company->notify($notification);
        return $this->sendResponse($job, 'Job converted successfully');
    }
}
