<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\User_Cv;
use App\Models\Verification;
use App\Mail\RegisterUserMail;
use DB;
use Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Json;
use App\Models\FcmToken;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = $request->validate([
            'email' => 'required|email',
            'phone' => 'required',
            'password' => 'required|confirmed'
        ]);
        $email = User::where('email', $validator['email'])->first();
        if ($email) {
            return response()->json(['message' => 'This email has already been taken'], 400);
        } else {
            // Create a new user in the database
            $user = new User();
            $user->phone = $validator['phone'];
            $user->email =  $validator['email'];
            $user->password = Hash::make($validator['password']);
            $user->save();

            $verification = new Verification();
            $verification->code = rand(1000, 9999);
            $verification->email = $request->email;
            $verification->save();
            // Send verification email
            Mail::to($user->email)->send(new RegisterUserMail($user, $verification->code));
            $credentials = request(['email', 'password']);
            $token = auth()->guard('api')->attempt($credentials);
            $user = User::where('id', $user->id)->first();
            if ($request->fcm_token) {



                FcmToken::firstOrCreate([
                    'token' => $request->fcm_token
                ], [
                    'user_id' => $user->id,
                    'token' => $request->fcm_token,
                    'type' => 'user'
                ]);
            }

            $user = [
                'message' => 'The registration process was completed successfully',
                'user' => $user,
                'verfication' => $verification,
                'token' => $token
            ];
        }
        return response()->json($user, 200);
    }

    public function complete_register(Request $request)
    {
        $validator = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'governorate' => 'required',
            'address' => 'required',
            'marital_status' => 'required',
            'nationality' => 'required',
            'experience_years' => 'required',
            'education' => 'required',
            'birthday' => 'required',
            'gender' => 'required',
            'driving_license' => 'required',
            'military_status' => 'required',
            'topic' => 'required',
            'job_environment' => 'required',
            'job_time' => 'required',
            'work_city' => 'required',
            'job_current' => 'required',
        ]);
        $user_id = auth()->id();

        DB::beginTransaction();
        try {
            $this->complete_user($request, $user_id);
            $this->complete_user_cv($request, $user_id);
            $user = User::find($user_id);
            $user->complete_state = 1;
            $user->save();
            DB::commit();
            $response = [
                'message' => 'Your information has been completed successfully',
                'user' => $user
            ];
            return $response;
        } catch (\Exception $e) {
            DB::rollback();
        }
    }

    public function login(Request $request)
    {
        $validator = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $user = User::where('email', $validator['email'])->first();

        if (!$user) return response()->json(['message' => "This account doesn't exist"], 400);
        else if ($user) {
            if (Hash::check($validator['password'], $user->password)) {
                $credentials = request(['email', 'password']);
                $token = auth()->guard('api')->attempt($credentials);
                if ($request->fcm_token) {
                    FcmToken::firstOrCreate([
                        'token' => $request->fcm_token
                    ], [
                        'user_id' => $user->id,
                        'token' => $request->fcm_token,
                        'type' => 'user'
                    ]);
                }
                $user = [
                    'message' => 'You have been logged in successfully',
                    'user' => $user,
                    'token' => $token
                ];

                return $user;
            } else return response()->json(['message' => "The password is incorrect"], 402);
        }
    }

    public function forget_password(Request $request)
    {
        $validator = $request->validate([
            'email' => 'required|email',
        ]);
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $Password = Verification::updateOrCreate(
                ['email' => $request->email],
                [
                    'email' => $request->email,
                    'code' => rand(1000, 9999),
                ]
            );
            Mail::to($user->email)->send(new RegisterUserMail($user, $Password->code));

            return ['message' => 'confirmation code has been sent successfully'];
        } else {
            return response()->json(['message' => "This email does not exist"], 400);
        }
    }

    public function verify_code(Request $request)
    {
        $validator = $request->validate([
            'code' => 'required',
            'email' => 'required'
        ]);
        $user = User::where('email', $request->email)->first();
        $verification = Verification::where([
            ['code', $request->code],
            ['email', $request->email]
        ])->first();
        if ($verification && $user->email_verified_at == null) {
            $user->email_verified_at = now();
            $user->save();
        }
        if ($verification) {
            $verification->delete();

            return ['message' => 'The account has been activated successfully'];
        } else {
            return response()->json(['message' => 'Error in the entered code, please try again'], 400);
        }
    }

    public function resend_code(Request $request)
    {
        $validator = $request->validate([
            'email' => 'required|email',
        ]);

        $verification = Verification::where('email', $request->email)->first();
        $email = User::where('email', $request->email)->first();
        if ($verification && $email) {
            $today = Carbon::today();
            $lastVerificationDate = $verification->last_resend_date ? Carbon::parse($verification->last_resend_date) : null;
            if (!$lastVerificationDate || !$lastVerificationDate->isSameDay($today)) {
                $verification->num_of_resend = 0;
            }

            if ($verification->num_of_resend >= 2) {
                return response()->json(['message' => 'You cannot re-send the verification code more than twice per day'], 400);
            } else {
                $verification->code = rand(1000, 9999);
                $verification->num_of_resend += 1;
                $verification->last_resend_date = $today;
                $verification->save();

                Mail::to($verification->email)->send(new RegisterUserMail($verification, $verification->code));

                $verification = [
                    'message' => 'The code has been sent successfully',
                    'verification' => $verification,
                ];
            }
            return $verification;
        } else {
            return response()->json(['message' => "This email does not exist"], 400);
        }
    }
    public function confirm_reset_password(Request $request)
    {
        $validator = $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);
        $user = user::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'user not found'], 400);
        } else {
            $user->password = bcrypt($request->password);
            $user->save();
            return ['message' => 'Reset Password Successfully!'];
        }
        return response()->json(['message' => 'This email does not exist'], 400);
    }

    public function change_password(Request $request)
    {
        $validator = $request->validate([
            'password' => 'required|confirmed'
        ]);
        $user_id = auth()->id();
        $user = User::find($user_id);
        $user->password = Hash::make($validator['password']);
        $user->save();
        return response()->json(['message' => 'Password updated successfully!'], 200);
    }

    public function delete_account(Request $request)
    {
        $validator = $request->validate([
            'password' => 'required'
        ]);
        $user_id = auth()->id();
        $user = User::find($user_id);
        if (Hash::check($validator['password'], $user->password)) {
            $user->delete();
            return response()->json(['message' => 'Account deleted successfully!'], 200);
        } else return response()->json(['message' => "The password is incorrect"], 402);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
    private function complete_user($request, $user_id)
    {
        $user = User::where('id', $user_id)->first();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->governorate = $request->governorate;
        $user->address = $request->address;
        $user->marital_status = $request->marital_status;
        $user->nationality = $request->nationality;
        $user->experience_years = $request->experience_years;
        $user->education = $request->education;
        $user->birthday = $request->birthday;
        $user->gender = $request->gender;
        $user->driving_license = $request->driving_license;
        $user->military_status = $request->military_status;
        $user->status = 'active';
        $user->topic = json_encode($request->topic);
        $user->save();
    }
    private function complete_user_cv($request, $user_id)
    {
        $is_exist = User_Cv::where('user_id', $user_id)->first();
        if ($is_exist) {
            $user_cv = $is_exist;
        } else {
            $user_cv = new User_Cv();
        }
        $user_cv->user_id = $user_id;
        $user_cv->job_environment = $request->job_environment;
        $user_cv->job_time = json_encode($request->job_time);
        $user_cv->job_environment = json_encode($request->job_environment);
        $user_cv->work_city = json_encode($request->work_city);
        $user_cv->job_current = $request->job_current;
        $user_cv->save();
    }
}
