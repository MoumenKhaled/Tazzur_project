<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Job;
use App\Http\Controllers\BaseController;
use App\Services\FirebaseService;
use App\Models\FcmToken;
use App\Models\User;
use App\Models\Company;
use App\Services\NotificationService;
use App\Models\Follower;
use Illuminate\Support\Facades\Queue;

class JobController extends BaseController
{
    // public function index(Request $request)
    // {
    //     $query = Job::query();
    //     $filterable = ['status', 'gender', 'end_date', 'company_id'];
    //     foreach ($filterable as $filter) {
    //         if ($request->has($filter)) {
    //             if ($filter == 'end_date') {
    //                 $query->whereDate($filter, '=', $request->$filter);
    //             } else {
    //                 $query->where($filter, $request->$filter);
    //             }
    //         }
    //     }
    //     $jobs = $query->select(['id', 'job_title', 'topic', 'created_at', 'company_id'])
    //                   ->with('company:id,name')
    //                   ->get()
    //                   ->map(function ($job) {
    //                       return [
    //                           'id' => $job->id,
    //                           'job_title' => $job->job_title,
    //                           'topic' => $job->topic,
    //                           'created_at' => $job->created_at,
    //                           'company_name' => $job->company->name ?? 'N/A'
    //                       ];
    //                   });

    //     return $this->sendResponse($jobs, 'Courses retrieved successfully');
    // }
    public function index(Request $request)
    {
        $query = Job::query();
        $filterable = ['status', 'gender', 'end_date', 'company_id', 'is_converted'];
        foreach ($filterable as $filter) {
            if ($request->has($filter)) {
                if ($filter == 'end_date') {
                    $query->whereDate($filter, '=', $request->$filter);
                } else {
                    $query->where($filter, $request->$filter);
                }
            }
        }
        $perPage = $request->input('per_page', 2);
        $jobs = $query->select(['id', 'job_title', 'topic', 'created_at', 'company_id'])
            ->with('company:id,name')
            ->paginate($perPage)
            ->through(function ($job) {
                return [
                    'id' => $job->id,
                    'job_title' => $job->job_title,
                    'topic' => $job->topic,
                    'created_at' => $job->created_at,
                    'company_name' => $job->company->name ?? 'N/A'
                ];
            });
        return $this->sendResponse($jobs, 'Jobs retrieved successfully');
    }


    public function show(string $id)
    {
        $job = Job::with(['forms.questions.options'])->where('id', $id)->first();
        if (!$job) {
            return $this->sendError(404, 'Job not found');
        }
        // Extract company data from the course object
        $companyData = $job->company ? [
            'status' => $job->company->status,
            'name' => $job->company->name,
            'phone' => $job->company->phone,
            'topic' => $job->company->topic,
            'location_map' => $job->company->location_map,
            'location' => $job->company->location,
            'type' => $job->company->type,
            'logo' => $job->company->logo,
            'email' => $job->company->email,
            'about_us' => $job->company->about_us,
        ] : null;
        $job = Job::with(['forms.questions.options'])->where('id', $id)->first();
        $job->required_languages = json_decode($job->required_languages, true);
        return $this->sendResponse([
            'job' => $job,
            'company' => $companyData
        ], 'job details with company');
    }
    protected function sendNotificationToCompany($company, $status, $job, $notificationService)
    {

        $messages = [
            'accepted' => [
                'ar' => [
                    'title' => 'تم قبول فرصة عمل!',
                    'description' => "تم قبول الوظيفة: {$job->title} بنجاح."
                ],
                'en' => [
                    'title' => 'Job Opportunity Accepted!',
                    'description' => "The job {$job->title} has been successfully accepted."
                ]
            ],
            'rejected' => [
                'ar' => [
                    'title' => 'تم رفض فرصة عمل!',
                    'description' => "تم رفض الوظيفة: {$job->title}."
                ],
                'en' => [
                    'title' => 'Job Opportunity Rejected!',
                    'description' => "The job {$job->title} has been rejected."
                ]
            ]
        ];
        $notificationData = $messages[$status];
        $url = "http://86.38.218.161:8081/CurrentJobDetails/" . $job->id;
        //$url = route('jobs.show', ['job' => $job->id]);
        $notificationService->sendNotification($company, $notificationData, $url, 'company');
        // $customerToken = FcmToken::where([
        //     'user_id' => $company->id,
        //     'type' => 'company',
        // ])->pluck('token')->toArray();


        // if (!empty($customerToken)) {
        //     FirebaseService::sendNotification($customerToken, $notificationData,  route('jobs.show', ['job' => $job->id]));
        // }
        // $notification = new \App\Notifications\ServiceNotification($notificationData,  route('jobs.show', ['job' => $job->id]));
        // $company->notify($notification);
    }

    public function accept_refuse(Request $request, string $id, NotificationService $notificationService)
    {

        $request->validate(
            [
                'status' => 'required|in:accepted,rejected',
            ],
            // [
            //     'date.required_if' => 'The date field is required when the status is accepted.',
            //     'time.required_if' => 'The time field is required when the status is accepted.',
            //     'location.required_if' => 'The location field is required when the status is accepted.',
            //     'phone.required_if' => 'The phone field is required when the status is accepted.',
            // ]
        );
        $job = \App\Models\Job::find($id);
        if (!$job) {
            return $this->sendError(404, 'job not found.');
        }
        $company = Company::where('id', $job->company_id)->first();
        if ($request->status == 'accepted') {
            $job->status = 'current';
            $this->sendNotificationToCompany($company, 'accepted', $job, $notificationService);

            $messages = [
                'ar' => [
                    'title' => 'فرصة عمل جديدة متاحة الآن!',
                    'description' => 'تفقد الفرصة الجديدة التي نشرتها شركة . قد تكون هذه فرصتك لتحقيق طموحاتك المهنية. اكتشف التفاصيل الآن وقدم على الوظيفة إذا كانت تناسب مهاراتك'
                ],
                'en' => [
                    'title' => 'New Job Opportunity Available Now!',
                    'description' => 'Check out the new opportunity posted by Company . This could be your chance to advance your career. Discover the details now and apply if it matches your skills.'
                ]
            ];

            $url = route('job_details', ['id' => $job->id]);   
            $followers = Follower::where('company_id', $company->id)->pluck('user_id');
            Queue::push(function () use ($messages, $url, $followers) {
                $customerToken = FcmToken::whereIn('user_id', $followers)->where('type', 'user')->pluck('token')->toArray();

                if (!empty($customerToken)) {
                    FirebaseService::sendNotification($customerToken, $messages, $url);
                }

                foreach ($followers as $followerId) {
                    $user = User::find($followerId);
                    if ($user) {
                        $user->notify(new \App\Notifications\ServiceNotification($messages, $url));
                    }
                }
            });
            // $customerToken = FcmToken::whereIn('user_id', $followers)->where('type', 'user')->pluck('token')->toArray();


            // if (!empty($customerToken)) {
            //     FirebaseService::sendNotification($customerToken, $messages,  $url);
            // }
            // foreach ($followers as $followerId) {
            //     $user = User::find($followerId);
            //     if ($user) {
            //         $user->notify(new \App\Notifications\ServiceNotification($messages, $url));
            //     }
            // }
        } else {
            $job->status = 'rejected';
            $this->sendNotificationToCompany($company, 'rejected', $job, $notificationService);
        }
        $job->save();

        return $this->sendResponse($job->status, "job has been {$request->status}.");
    }
    public function destroy(string $id)
    {
        $job = Job::where('id', $id)->first();

        if (!$job) {
            return $this->sendError(404, 'Job not found');
        }

        $job->delete();
        return $this->sendResponse(true, 'Job deleted successfully!');
    }
}
