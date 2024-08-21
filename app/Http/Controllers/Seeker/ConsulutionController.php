<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserConsultation;
use DB;
use App\Services\FirebaseService;
use App\Models\FcmToken;
use App\Services\NotificationService;
use App\Models\Manager;
class ConsulutionController extends Controller
{

    public function get_topics()
{
        $user_id=auth()->id();
        $topics=(User::find($user_id)['topic']);
        return response()->json(['topics'=>$topics] ,200);
}
    public function get_consultations(Request $request)
{
        $validator=$request->validate([
            'topic'=>'required',
        ]);
        $user_id=auth()->id();
        $user_consultation=UserConsultation::where([
            ['user_id',$user_id],
            ['topic',$request->topic]
        ])->get();

        return response()->json(['user_consultation'=>$user_consultation] ,200);
}

    public function send_message(Request $request, NotificationService $notificationService)
{
        $validator = $request->validate([
        'message' => 'required',
        'topic' => 'required'
        ]);

        $user_id = auth()->id();
        $consultationCount = UserConsultation::where([
            ['user_id', $user_id],
            ['topic', $request->topic]
        ])->count();

        $latest_message = UserConsultation::where([
             ['user_id', $user_id],
             ['topic', $request->topic]
        ])->latest()->first();

        if ($latest_message && !$latest_message->advisor_reply) {
            return response()->json(['message' => 'We apologize, you must wait for the consultant’s reply to the previous consultation before you can request a new consultation'], 400);
        }

        if ($consultationCount >= 5) {
            return response()->json(['message' => 'We apologize, you cannot send more than 5 messages for the same topic'], 400);
        }

        $consulution = new UserConsultation();
        $consulution->user_message=$request->message;
        $consulution->user_id=$user_id;
        $consulution->topic=$request->topic;
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
            $query->whereJsonContains('role_name',  '["user_consultation_coordinator"]')
                  ->orWhereJsonContains('role_name',  '["admin"]' );
        })->get();
       // $managers = Manager::get();
     //   $manager=Manager::first();
           $url = "http://86.38.218.161:8082/UserConsulations/" . $consulution->id;

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



        return response()->json(['consulution' => $consulution], 200);
}
    public function review($id, Request $request)
{

        $validator = $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
            'review' => 'required|string'
        ]);

        $consultation = DB::table('users_consultation')->where('id', $id)->first();
        if (!$consultation) {
            return response()->json(['message'=>'Consultation not found.'], 200);

        }
        if (is_null($consultation->advisor_reply)) {
            return response()->json(['message'=>'Advisor reply is required before review.'],400);
        }
        if (!is_null($consultation->review)) {
            return response()->json(['message'=>'Consultation has already been reviewed.'],400);
        }
        DB::table('users_consultation')->where('id', $id)->update([
            'rating' => $validator['rating'],
            'review' => $validator['review'],
            'updated_at' => now()
        ]);

        return response()->json([
            'consultation' => $consultation,
            'message'=>'Review submitted successfully.'
        ], 200);
    }
}
