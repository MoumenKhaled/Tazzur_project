<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CompanyConsulution;
use App\Models\Advisor;
use App\Models\Manager;
use Auth;
use App\Services\FirebaseService;
use App\Models\FcmToken;
use DB;
use App\Http\Controllers\BaseController;
use App\Services\NotificationService;
class ConsulutionController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::guard('company')->user();
        $perPage = $request->input('per_page', 2);


        $advisors = Advisor::whereHas('consultations', function ($query) use ($user) {
            $query->where('company_id', $user->id);
        })->select(['id', 'name', 'email', 'topics', 'created_at','rating'])
          ->paginate($perPage);

        return $this->sendResponse($advisors, 'Advisors retrieved successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::guard('company')->user();

        $advisor = Advisor::find($id);
        if (!$advisor) {
            return $this->sendError(404, 'advisor not found');
        }
        $consultations = CompanyConsulution::where('company_id', $user->id)
                                      ->where('advisor_id', $id)
                                      ->orderBy('created_at')
                                      ->select('id','created_at','advisor_id','company_id','user_message','advisor_reply','review','rating')
                                      ->get();


                                      $response = [
                                        'advisor_name' => $advisor->name,
                                        'advisor_topic' => $advisor->topics,
                                        'advisor_rating' => $advisor->rating,
                                        'consultations' => $consultations
                                    ];
        return $this->sendResponse($response, 'consultations');
    }


    public function store(Request $request, NotificationService $notificationService)
    {
        $validator = $request->validate([
            'message' => 'required',
         //   'topic' => 'required'
            ]);

            $company = Auth::guard('company')->user();
            $consultationCount = CompanyConsulution::where([
                ['company_id', $company->id],
                ['topic', $company->topic]
            ])->count();

            $latest_message = CompanyConsulution::where([
                ['company_id', $company->id],['topic', $company->topic]
           ])->latest()->first();

           if ($latest_message && !$latest_message->advisor_reply) {
               return $this->sendError(400,'We apologize, you must wait for the consultant’s reply to the previous consultation before you can request a new consultation');
           }

             if ($consultationCount >= 5)
            {
                return $this->sendError(400,'We apologize, you cannot send more than 5 messages for the same topic');
            }

            $consulution = new CompanyConsulution();
            $consulution->user_message=$request->message;
            $consulution->company_id=$company->id;
            $consulution->topic=$company->topic;
            $consulution->save();



            $messages = [
                'ar' => [
                    'title' => 'استشارة جديدة تتطلب مراجعتك!',
                    'description' => 'تم استلام استشارة جديدة وتتطلب مراجعتك لتحديد المستشار المناسب لها',
                ],
                'en' => [
                    'title' => 'New Consultation Requires Your Review!',
                    'description' => '
                    "A new consultation has been received and requires your review to assign the appropriate consultant.'
                ]
            ];
            $managers = Manager::where(function ($query) {

                $query->whereJsonContains('role_name',   '["company_consultation_coordinator"]')
                      ->orWhereJsonContains('role_name',   '["admin"]');
            })->get();
         //   $managers = Manager::get();


           // $url = route('companyconsulting.show', ['CompanyConsulting' => $consulution->company_id]);

            $url = "http://86.38.218.161:8082/CompanyConsulations/" . $consulution->company_id;
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
            // $notification = new \App\Notifications\ServiceNotification($messages, $url);
            // $manager->notify($notification);



            $data = ['consulution' => $consulution,];
            return $this->sendResponse($data, 'The registration process was completed successfully');
    }
  public function update(Request $request, string $id)
    {

    }
    public function destroy(string $id)
    {

    }
    public function review(string $id, Request $request)
    {

        $validator = $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
            'review' => 'required|string'
        ]);


        $consultation = DB::table('companies_consultation')->where('id', $id)->first();


        if (!$consultation) {
            return $this->sendError(400,'Consultation not found.');

        }


        if (is_null($consultation->advisor_reply)) {
            return $this->sendError(400,'Advisor reply is required before review.');
        }


        if (!is_null($consultation->review)) {
            return $this->sendError(400,'Consultation has already been reviewed.');
        }


        DB::table('companies_consultation')->where('id', $id)->update([
            'rating' => $validator['rating'],
            'review' => $validator['review'],
            'updated_at' => now()
        ]);
        return $this->sendResponse($consultation, 'Review submitted successfully.');
    }

}
