<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Manager;
use App\Http\Controllers\BaseController;
use App\Models\FcmToken;
use Hash;
class AuthController extends BaseController
{
    public function login(Request $request)
    {
        $validator=$request->validate(['email'=>'required|email','password'=>'required']);
        $user = Manager::where('email', $validator['email'])->first();

        if (!$user) return $this->sendError(404,'The account you are trying to access does not exist.');

        else if ($user){
            if(Hash::check($validator['password'], $user->password)) {
            $credentials = request(['email', 'password']);
            $token = auth()->guard('manager')->attempt($credentials);
            $data = [
                'id' => $user->id,
                'email' => $user->email,
                'role_name' => json_decode($user->role_name),
                'name' => $user->name,
                'token' => $token
             ];
             if ($request->fcm_token) {
               

                FcmToken::firstOrCreate([
                    'token' => $request->fcm_token
                ], [
                    'user_id' => $user->id,
                    'token' => $request->fcm_token,
                    'type' => 'manager'
                ]);
            }

             return $this->sendResponse($data, 'You have been logged in successfully');
        }
        else return $this->sendError(401,'Authentication failed, please check your password.');
        }
    }
    public function logout()
    {
    $request->user()->token()->revoke();
    return $this->sendResponse('done', 'Successfully logged out');
    }


}
