<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Company;
use App\Models\Form;
use App\Models\FormQuestion;
use App\Models\FormOption;
use App\Models\UserQuestion;
use App\Models\JobApplication;
use App\Models\User;
use App\Models\Manager;
use App\Models\User_Cv;
use App\Models\ExperienceYearsTranslate;
use App\Models\Locations;
use App\Models\Topics;
use DB;
use Illuminate\Pagination\Paginator;
use App\Notifications\ApplicationJobNotification;
use App\Services\FirebaseService;
use App\Models\Advisor;
use App\Services\NotificationService;
use App\Models\FcmToken;
class JobController extends Controller
{
    public function my_jobs()
    {
        $page = request()->query('page', 1);
        $user_id = auth()->id();
        $user = User::find($user_id);
        $user_cv = User_Cv::where('user_id', $user_id)->first();
        if (!$user_cv) {
            return response()->json(['message' => 'Please complete your information'], 400);
        }

        $topics =  $user->topic;
        $workcity = json_decode($user_cv->work_city);

        $locations = Locations::whereIn('name_ar', $workcity)
            ->orWhereIn('name_en', $workcity)
            ->get();

        $topics = Topics::whereIn('name_ar', $topics)
            ->orWhereIn('name_en', $topics)
            ->get();

        $experience_years = ExperienceYearsTranslate::where('name_ar', $user->experience_years)
            ->orWhere('name_en', $user->experience_years)
            ->get();


        $jobQuery = Job::where('status', 'current');

        if ($locations->isNotEmpty()) {
            $jobQuery->whereIn('location', $locations->pluck('name_ar'))
            ->orWhereIn('location', $locations->pluck('name_en'));

        }

        if ($topics->isNotEmpty()) {
             $jobQuery->whereIn('topic', $topics->pluck('name_ar'))
            ->orWhereIn('topic', $topics->pluck('name_en'));
        }

        if ($experience_years->isNotEmpty()) {
            $jobQuery->whereIn('experiense_years', $experience_years->pluck('name_ar'))
                ->orWhereIn('experiense_years', $experience_years->pluck('name_en'));
        }

        if (!empty($user->gender)) {
            if ($user->gender === 'Male' || $user->gender === 'ذكر') {
                $jobQuery->WhereIn('gender', ['Male', 'ذكر']);

            }
            else if ($user->gender === 'Female' || $user->gender === 'انثى') {
                $jobQuery->WhereIn('gender', ['Female', 'انثى']);
            }
        }

        if (!empty($user->driving_license)) {
            $jobQuery->Where('is_required_license', $user->driving_license);

        }

        $jobs = $jobQuery->distinct()
            ->with('company:id,name,logo,location,about_us');

        $currentJobs = $jobs->paginate(10);
        return response()->json($currentJobs, 200);
    }

    public function job_details($id)
{
        $job = Job::where('id', $id)->with('company:id,name,logo,location,about_us')->firstOrFail();
        return $job;
}
    public function all_jobs()
{
    $page = request()->query('page', 1);
    $jobs = Job::where('status', 'current')
        ->distinct()
        ->with('company:id,name,logo,location,about_us')
        ->paginate(10);

    return $jobs;
}
    public function filter(Request $request)
{
        $validator = $request->validate([
            'topic',
            'experience_years',
            'work_city',
        ]);

        $topicArray = ($request->topic) ?? [];
        $experienceYearsArray = ($request->experience_years) ?? [];
        $workCityArray = ($request->work_city) ?? [];

        $locations = Locations::whereIn('name_ar', $workCityArray)
        ->orWhereIn('name_en', $workCityArray)
        ->get();

        $topics = Topics::whereIn('name_ar', $topicArray)
            ->orWhereIn('name_en', $topicArray)
            ->get();

        $experience_years=ExperienceYearsTranslate::whereIn('name_ar', $experienceYearsArray)
        ->orWhereIn('name_en', $experienceYearsArray)
        ->get();

        $jobQuery = Job::where('status', 'current');

        if ($locations->isNotEmpty()) {
            $jobQuery->WhereIn('location', $locations->pluck('name_ar'))
                ->orWhereIn('location', $locations->pluck('name_en'));
        }

        if ($topics->isNotEmpty()) {
            $jobQuery->WhereIn('topic', $topics->pluck('name_ar'))
                ->orWhereIn('topic', $topics->pluck('name_en'));
        }

        if ($experience_years->isNotEmpty()) {
            $jobQuery->WhereIn('experiense_years', $experience_years->pluck('name_ar'))
                ->orWhereIn('experiense_years', $experience_years->pluck('name_en'));
        }

    $page = request()->query('page', 1);

    $jobQuery = $jobQuery->distinct()
    ->with('company:id,name,logo,location,about_us');


    $currentJobs = $jobQuery->paginate(10);

    return $currentJobs;
}
    public function search(Request $request)
{
    $validator = $request->validate([
        'value' => 'required',
    ]);
    $check= false;
    $page = request()->query('page', 1);
    $var = '%' . $request->value . '%';

    $location = Locations::where('name_ar','like',$var)
    ->orWhere('name_en', 'like', $var)
    ->first();

    $topic = Topics::where('name_ar','like',$var)
    ->orWhere('name_en', 'like', $var)
    ->first();

    $company = Company::where('name', 'like', $var)->pluck('id');


    $value = Job::where('status','current');

    if ($location) {
        $value->Where('location', 'like', $location->name_ar)
              ->orWhere('location', 'like', $location->name_en);
              $check= true;
            }

    if ($topic) {
        $value->Where('topic', 'like', $topic->name_en)
              ->orWhere('topic', 'like', $topic->name_ar);
              $check= true;
    }

    if ($company->isNotEmpty()) {
        $company_jobs=Job::whereIn('company_id',$company)->where('hidden_name',false)->pluck('id');
        $value->WhereIn('company_id', $company_jobs);
        $check= true;
    }
        $value->distinct()
        ->with('company:id,name,logo,location,about_us');


    if (!$check){
      $value = $value->Where('location', 'like', $var)
        ->Where('topic', 'like', $var);
    }

    $Jobs = $value->paginate(10);
    return response()->json($Jobs, 200);
}
    public function show_questions($id)
{
    $forms = Form::where('job_id', $id)->get();
    $formIds = $forms->pluck('id')->toArray();
    $questions = FormQuestion::whereIn('form_id', $formIds)->with('options')->get();

    return $questions;
}

    public function answer_and_applay(Request $request,$id, NotificationService $notificationService)
{
    $validator = $request->validate([
        'questions'
    ]);
    $user_id=auth()->id();

    $isexist=JobApplication::where([
        ['user_id',$user_id],
        ['job_id',$id]
    ])->first();
    if ($isexist){
        return response()->json(['message'=>'Sorry, you have already applied for the job'], 400);
    }

    $form=Form::where('job_id',$id)->first();
    foreach($request->questions as $question){
        $user_question=new UserQuestion();
        $user_question->user_id=$user_id;
        $user_question->question_id= $question['id'];
        $user_question->form_options_id=$question['answer_id'];
        $user_question->form_id=$form->id;
        $user_question->save();
    }
    //job application
        $job_application = new JobApplication();
        $job_application->user_id=$user_id;
        $job_application->job_id=$id;
        $job_application->save();


        $job=Job::where('id',$id)->first();
        $manager=Manager::first();


       $managers = Manager::where(function ($query) {

            $query->whereJsonContains('role_name',   '["job_requests_coordinator"]')
                  ->orWhereJsonContains('role_name',  '["admin"]' );
        })->get();

      //  $managers=Manager::get();



        $user=User::find($user_id);

        $messages = [
            'ar' => [
                'title' => 'متقدم جديد لفرصة العمل!',
                'description' => 'تم تقديم طلب جديد من مستخدم لفرصة العمل التي نشرتموها. يرجى مراجعة تفاصيل التقديم واتخاذ الإجراءات اللازمة.'
            ],
            'en' => [
                'title' => 'New Applicant for the Job Opportunity!',
                'description' => 'A new application has been submitted by a user for the job opportunity you posted. Please review the application details and take the necessary actions.'
            ]
        ];
        $url = "http://86.38.218.161:8082/JobUsers/" . $job->id;
        foreach ($managers as $manager) {
            $notificationService->sendNotification($manager, $messages, $url, 'manager');
        }
        // $customerToken = FcmToken::where(
        //     [
        //         'user_id' => $manager->id,
        //         'type' => 'manager',
        //     ]
        // )->pluck('token')->toArray();
        // if (!empty($customerToken)) {
        //     FirebaseService::sendNotification($customerToken, $messages,  $url);
        // }
        // $notification = new \App\Notifications\ServiceNotification($messages,  $url);
        // $manager->notify($notification);

     //   $manager->notify(new ApplicationJobNotification($job,$user));


        return response()->json(['message'=>'You have successfully applied for the job'], 200);
    }

}
