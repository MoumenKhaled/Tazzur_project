<?php

namespace App\Http\Controllers\Company;

use App\Models\Job;
use App\Models\Form;
use App\Models\FormOption;
use App\Models\FormQuestion;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController;
use App\Notifications\AddJobNotification;
use DB;
use App\Services\FirebaseService;
use App\Models\FcmToken;
use App\Models\User;
use App\Models\Company;
use App\Models\Manager;
use App\Services\NotificationService;
class JobController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Job::query();
        $filterable = ['status', 'gender', 'end_date'];

        foreach ($filterable as $filter) {
            if ($request->has($filter)) {
                if ($filter == 'end_date') {
                    $query->whereDate($filter, '=', $request->$filter);
                } else {
                    $query->where($filter, $request->$filter);
                }
            }
        }

        $perPage = $request->input('per_page', 10);

        $company = auth()->guard('company')->user();
        $jobs = $query->where('company_id',$company->id)->select(['id', 'job_title', 'topic', 'created_at'])->paginate($perPage);

        return $this->sendResponse($jobs, 'Jobs retrieved successfully');
    }



    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request,NotificationService $notificationService)
    {
        $request->validate([
            'hidden_name' => 'nullable|boolean',
            'job_title' => 'required|string',
            'end_date' => 'required|date|after:3 days',
        ]);
        
        //status - special_qualifications - education_level - number_employees- end_date

        $jobData = $request->all();
        $jobData['status']='waiting';
        $jobData['end_date']='2012-12-22';
        if (isset($jobData['required_languages']) && is_array($jobData['required_languages'])) {
            $jobData['required_languages'] = json_encode($jobData['required_languages']);
        }


         if (isset($jobData['is_required_military']) && is_array($jobData['is_required_military'])) {
            $jobData['is_required_military'] = json_encode($jobData['is_required_military']);
        }
         if (isset($jobData['job_environment']) && is_array($jobData['job_environment'])) {
            $jobData['job_environment'] = json_encode($jobData['job_environment']);
        }
         if (isset($jobData['job_time']) && is_array($jobData['job_time'])) {
            $jobData['job_time'] = json_encode($jobData['job_time']);
        }
         if (isset($jobData['gender']) && is_array($jobData['gender'])) {
            $jobData['gender'] = json_encode($jobData['gender']);
        }



        $job = new Job($jobData);
        $job->company_id = auth()->guard('company')->user()->id;
        $job->save();
        

        if ($request->has('forms')) {
            $formData = $request->input('forms');
            $form = new Form([
                'is_required' => $formData['is_required'],
                'job_id' => $job->id
            ]);
            $form->save();

            if (isset($formData['questions']) && is_array($formData['questions'])) {
                foreach ($formData['questions'] as $questionData) {
                    $question = new FormQuestion([
                        'question' => $questionData['question'],
                        'form_id' => $form->id
                    ]);
                    $question->save();

                    if (isset($questionData['answers']) && is_array($questionData['answers'])) {
                        foreach ($questionData['answers'] as $optionText) {
                            if ($optionText !== null) {
                                $option = new FormOption([
                                    'option_text' => $optionText,
                                    'question_id' => $question->id
                                ]);
                                $option->save();
                            }
                        }
                    }
                }
            }
        }
        $manager=Manager::where('id',1)->first();
        $company=Company::find(auth()->guard('company')->user()->id);

        $messages = [
            'ar' => [
                'title' => 'مراجعة فرصة عمل جديدة مطلوبة!',
                'description' => 'تم نشر فرصة عمل جديدة وتحتاج إلى مراجعتكم. يرجى التحقق من التفاصيل واتخاذ قرار بالقبول أو الرفض بناءً على معايير الجودة المحددة.'
            ],

            'en' => [
                'title' => 'New Job Opportunity Review Required!',
                'description' => 'A new job opportunity posted by  requires your review. Please check the details and make a decision to approve or reject based on the established quality criteria.'
            ]
        ];

       
          $url = "http://86.38.218.161:8082/waitingJobDetails/" . $job->id;
      //  $managers = Manager::get();
        $managers = Manager::where(function ($query) {
        
        
            $query->whereJsonContains('role_name', '["job_posting_requests_coordinator"]')
                  ->orWhereJsonContains('role_name', '["admin"]');
        })->get();
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
        return $this->sendResponse($job, 'Job created successfully!');
    }




    /**
     * Display the specified resource.
     */
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


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            // قواعد التحقق من الصحة للحقول المطلوب تحديثها
        ]);

        $job = Job::where('id', $id)->where('company_id', auth()->guard('company')->user()->id)->first();

        if (!$job) {
            return $this->sendError(404,'Job not found');
        }

        $job->update($request->all());
        return $this->sendResponse($job, 'Job updated successfully!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $job = Job::where('id', $id)->where('company_id', auth()->guard('company')->user()->id)->first();

        if (!$job) {
            return $this->sendError(404,'Job not found');
        }

        $job->delete();
        return $this->sendResponse(true, 'Job deleted successfully!');
    }
    public function clone(Request $request, $jobId)
    {
        $existingJob = Job::with(['forms.questions.options'])->find($jobId);
        if (!$existingJob) {
            return $this->sendError(404, 'Job not found');
        }
        DB::beginTransaction();
        try {
            $newJobData = $existingJob->replicate();
            $newJobData->status = 'waiting';
            $newJobData->company_id = auth()->guard('company')->user()->id;
            $newJobData->save();
            foreach ($existingJob->forms as $form) {
                $newForm = $form->replicate();
                $newForm->job_id = $newJobData->id;
                $newForm->save();

                foreach ($form->questions as $question) {
                    $newQuestion = $question->replicate();
                    $newQuestion->form_id = $newForm->id;
                    $newQuestion->save();

                    foreach ($question->options as $option) {
                        $newOption = $option->replicate();
                        $newOption->question_id = $newQuestion->id;
                        $newOption->save();
                    }
                }
            }
            DB::commit();
            return $this->sendResponse($newJobData, 'Job cloned successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }




}
