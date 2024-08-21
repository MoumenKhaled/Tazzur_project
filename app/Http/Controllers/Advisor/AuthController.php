<?php

namespace App\Http\Controllers\Advisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Advisor;
use Illuminate\Support\Facades\Hash;
use App\Models\FcmToken;
class AuthController extends Controller
{
    public function add_addvisor(Request $request)
    {
        $validator=$request->validate([
            'name'=>'required',
            'email'=>'required|email',
            'password'=>'required',
            'topics'=>'required'
        ]);
        $email=Advisor::where('email', $validator['email'])->first();
        if ($email){
          return response()->json(['message' => 'This email has already been taken'],400);
        }
        else {
        // Create a new user in the database
        $advisor = new Advisor();
        $advisor->name = $validator['name'];
        $advisor->email =  $validator['email'];
        $advisor->password = Hash::make($validator['password']);
        $advisor->topics = $validator['topics'];
        $advisor->save();

       // Send verification email
       //Mail::to($advisor->email)->send(new RegisterUserMail($advisor,$advisor->verification_code));
       $credentials = request(['email', 'password']);
       $token = auth()->guard('advisors')->attempt($credentials);

        $advisor=[
            'message'=>'The registration process was completed successfully',
            'advisor' => $advisor,
            'token' => $token
        ];
        }
        return $advisor ;

    }
    public function login(Request $request)
    {
        $validator=$request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);
        $advisor = Advisor::where('email', $validator['email'])->first();
        if (!$advisor) return response()->json(['message'=>"This account doesn't exist"],400);
        else if ($advisor){
            if(Hash::check($validator['password'], $advisor->password)) {
            $credentials = request(['email', 'password']);
            $token = auth()->guard('advisors')->attempt($credentials);
            if ($request->fcm_token) {
             
                FcmToken::firstOrCreate([
                    'token' => $request->fcm_token
                ], [
                    'user_id' => $advisor->id,
                    'token' => $request->fcm_token,
                    'type' => 'advisor'
                ]);
            }
            $advisor = [
                'message'=>'You have been logged in successfully',
                'advisor' => $advisor,
                'token' => $token
             ];

             return $advisor;
        }
        else return response()->json(['message'=>"The password is incorrect"],402);
        }

    }
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
