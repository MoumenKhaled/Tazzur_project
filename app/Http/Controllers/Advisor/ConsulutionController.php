<?php

namespace App\Http\Controllers\Advisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Job;
use App\Models\Course;
use App\Models\Company;
use App\Models\UserConsultation;
use App\Models\CompanyConsulution;
use App\Notifications\AdvisorReblyNotification;
use App\Notifications\AdvisorReplyToUserNotification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Broadcasting\Channel;
use App\Services\FirebaseService;
use App\Models\Advisor;
use App\Models\FcmToken;

class ConsulutionController extends Controller
{

    public function show_consultation_requests()
{
        $advisor_id = auth()->id();
        $user_consultation = UserConsultation::where([
            ['advisor_id', $advisor_id],
            ['advisor_reply', null]
        ])
        ->with([
            'user:id,first_name,last_name,email',
            'user.user_cv:user_id,image'
        ])
        ->get();

        $company_consultation = CompanyConsulution::where([
            ['advisor_id', $advisor_id],
            ['advisor_reply', null]
        ])
        ->with('company:id,name,logo,email')
        ->get();

        return response()->json([
            'user_consultation' => $user_consultation,
            'company_consultation' => $company_consultation
    ]);
}
    public function show_user_messages($id)
{
        $advisor_id = auth()->id();
        $user_consultation = UserConsultation::where([
            ['advisor_id', $advisor_id],
            ['user_id', $id]
        ])->get();
        return $user_consultation;
}
    public function show_company_messages($id)
{
        $advisor_id = auth()->id();
        $company_consultation = CompanyConsulution::where([
            ['advisor_id', $advisor_id],
            ['company_id', $id]
        ])->get();
        return $company_consultation;
}

    public function reply_to_user(Request $request,$consultation_id)
{
        $validator=$request->validate([
            'reply'=>'required',
        ]);
        $user_consultation = UserConsultation::where('id',$consultation_id)->first();
        $user_consultation->advisor_reply=$request->reply;
        $user_consultation->save();
        $user=User::find($user_consultation->user_id);
        $messages = [
            'ar' => [
                'title' => 'تحديث جديد على استشارتكم!',
                'description' => 'تم الرد على استشارتكم من قِبل المستشار. يرجى الاطلاع على الرد لمعرفة التفاصيل'
            ],
            'en' => [
                'title' => 'New Update on Your Consultation!',
                'description' => 'Your consultation has been responded to by the consultant. Please review the response for further details and next steps.'
            ]
        ];
        $advisor=Advisor::where('id',$user_consultation->advisor_id)->first();
        $url = route('consultations_details', ['topic' => $advisor->topic]);
        $customerToken = FcmToken::where(
            [
                'user_id' => $user->id,
                'type' => 'user',
            ]
        )->pluck('token')->toArray();
        if (!empty($customerToken)) {
            FirebaseService::sendNotification($customerToken, $messages,  $url);
        }
        $notification = new \App\Notifications\ServiceNotification($messages,  $url);
        $user->notify($notification);

        return response()->json([
            'message' =>'The reply has been sent successfully',
            'user_consultation' => $user_consultation
    ]);

}
    public function reply_to_company(Request $request,$consultation_id)
{
        $validator=$request->validate([
            'reply'=>'required',
        ]);
        $company_consultation = CompanyConsulution::where('id',$consultation_id)->first();
        $company_consultation->advisor_reply=$request->reply;
        $company_consultation->save();
        $company=Company::find($company_consultation->company_id);

        $messages = [
            'ar' => [
                'title' => 'تحديث جديد على استشارتكم!',
                'description' => 'تم الرد على استشارتكم من قِبل المستشار. يرجى الاطلاع على الرد لمعرفة التفاصيل'
            ],
            'en' => [
                'title' => 'New Update on Your Consultation!',
                'description' => 'Your consultation has been responded to by the consultant. Please review the response for further details and next steps.'
            ]
        ];
        $url = "http://86.38.218.161:8081/yourconsultants/" . $company_consultation->id;
        //$url = route('consulutions.show', ['consulution' => $company_consultation->id]);
        $customerToken = FcmToken::where(
            [
                'user_id' => $company->id,
                'type' => 'company',
            ]
        )->pluck('token')->toArray();
        if (!empty($customerToken)) {
            FirebaseService::sendNotification($customerToken, $messages,  $url);
        }
        $notification = new \App\Notifications\ServiceNotification($messages,  $url);
        $company->notify($notification);


        //$company->notify(new AdvisorReblyNotification($company_consultation));
        return response()->json([
            'message' =>'The reply has been sent successfully',
            'user_consultation' => $company_consultation
    ]);

}
    public function show_user_information($user_id)
{
        $user=User::where('id',$user_id)->with('user_cv','experiences','cvCourses','references','links')->first();
        return $user;
}
    public function show_company_profile($company_id)
{
        $company=Company::where('id',$company_id)->first();
        $jobs = Job::where([
            //['status', ['current', 'حالية']],
            ['company_id',$company_id]
        ])->get();
        $courses = Course::where([
            //['status', ['current', 'حالية']],
            ['company_id',$company_id]
        ])->get();
        return response()->json([
            'company' => $company,
            'jobs' => $jobs,
            'courses' => $courses,
    ]);
}
}
