<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\BaseController;
class SeekerController extends BaseController
{
    public function user_cv($id)
    {
        $user = User::where('id', $id)->with(['user_cv', 'experiences', 'cvCourses', 'references', 'links'])->first();
        if (!$user) {
            return $this->sendError(404, 'user not found');
        }

        if ($user && $user->user_cv) {

            $user->user_cv->work_city = json_decode($user->user_cv->work_city);
            $user->user_cv->languages = json_decode($user->user_cv->languages);

            $user->user_cv->job_level = json_decode($user->user_cv->job_level);
            $user->user_cv->job_environment = json_decode($user->user_cv->job_environment);
            $user->user_cv->job_time = json_decode($user->user_cv->job_time);


        }

        return $this->sendResponse($user, 'User Cv!', 200);
    }

}
