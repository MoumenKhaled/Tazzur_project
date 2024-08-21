<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserConsultation;
use App\Models\CompanyConsulution;
use App\Http\Controllers\BaseController;
use App\Services\FirebaseService;
use App\Models\FcmToken;
use App\Models\User;
use App\Models\Advisor;
class ConsulutionUserController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 2);
        $consultations = UserConsultation::where('advisor_id', null)
        ->with('user')
        ->orderBy('created_at')
        ->select('id','created_at','user_id','user_message','topic')
        ->paginate($perPage);
        $response = [

          'consultations' => $consultations->through(function ($consultation) {
              return [
                'id'=>$consultation->id,
                'user_id' => $consultation->user->id,
                  'request_date' => $consultation->created_at->toDateString(),  // Format date as needed
                  'user_name' => $consultation->user ? $consultation->user->first_name : 'No user',
                  'user_email' => $consultation->user ? $consultation->user->email : 'No Email',
                  'user_message' => $consultation->user_message,
                  'topic' => $consultation->topic,
              ];
          })
      ];

return $this->sendResponse($response, 'consultations');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $consultation = UserConsultation::where('id', $id)
        ->with('user')

        ->select('id','created_at','user_id','user_message','topic')
        ->first();
        if (!$consultation) {
            return $this->sendError(404, 'consultation not found');
        }
        $response = [


                  'id'=>$consultation->id,
                  'user_id' => $consultation->user->id,
                  'request_date' => $consultation->created_at->toDateString(),
                  'user_name' => $consultation->user ? $consultation->user->first_name : 'No user',
                  'user_email' => $consultation->user ? $consultation->user->email : 'No Email',
                  'user_message' => $consultation->user_message,
                  'topic' => $consultation->topic,

      ];

return $this->sendResponse($response, 'consultations');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function convert(Request $request,$id)
    {
        $validator = $request->validate([
            'advisor_id' => 'required',
            ]);
            $consultation = UserConsultation::where([
                ['id', $id]

            ])->first();
            $consultation->update([
                'advisor_id'=>$request->advisor_id,
            ]);
            $data = ['consulution' => $consultation,];
            $advisor=Advisor::where('id',$request->advisor_id)->first();
          
            $messages = [
                'ar' => [
                    'title' => 'تحويل استشارة جديدة إليك!',
                    'description' => 'لقد تم تحويل استشارة جديدة إليك من قبل المنسق. يرجى مراجعة التفاصيل في لوحة التحكم الخاصة بك والبدء بالرد في أقرب وقت ممكن'
                ],
                'en' => [
                    'title' => 'New Consultation Assigned to You!',
                    'description' => 'A new consultation has been assigned to you by the coordinator. Please review the details in your control panel and begin responding as soon as possible'
                ]
            ];

            $url = route('show_consultation_requests');
            $customerToken = FcmToken::where(
                [
                    'user_id' => $advisor->id,
                    'type' => 'advisor',
                ]
            )->pluck('token')->toArray();
            if (!empty($customerToken)) {
                FirebaseService::sendNotification($customerToken, $messages,  $url);
            }
            $notification = new \App\Notifications\ServiceNotification($messages,  $url);
            $advisor->notify($notification);
            return $this->sendResponse($data, 'consulution');
    }
}
