<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\User_Cv;
use App\Http\Controllers\BaseController;
class SeekerController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $perPage = $request->input('per_page', 2);
        $users = \App\Models\User::where('status', $status)->paginate($perPage);
        $response = $users->through(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'image' => optional($user->user_cv)->image,
                'topic' => $user->topic,
            ];
        });

        return $this->sendResponse($response, 'Users retrieved successfully');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = \App\Models\User::where('id', $id)->first();
        if (!$user) {
            return $this->sendError(404, 'user not found');
        }
        $userCv = \App\Models\User_Cv::where('user_id', $id)->first(['image']);
        $userData = $user->toArray();
        $userData['image'] = $userCv ? $userCv->image : null;
        return $this->sendResponse($userData, 'User data retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function block_unblock(Request $request, string $id)
    {

        $request->validate([
            'status' => 'required|in:block,active',
        ]);


        $user = \App\Models\User::find($id);

        if (!$user) {
            return $this->sendError(404, 'User not found.');
        }

        if ($request->status === 'block') {
            $user->status = 'banned';
        } else {
            $user->status = $request->status;
        }
        $user->save();


        return $this->sendResponse($user->status, "User has been {$request->status}.");
    }

    public function destroy(string $id)
    {
        //
    }
    public function user_cv($id)
{
    $user = User::where('id', $id)->with(['user_cv', 'experiences', 'cvCourses', 'references', 'links'])->first();

    if ($user && $user->user_cv) {

        $user->user_cv->work_city = json_decode($user->user_cv->work_city, true);
        $user->user_cv->languages = json_decode($user->user_cv->languages, true);

        $user->user_cv->job_level = json_decode($user->user_cv->job_level, true);
        $user->user_cv->job_environment = json_decode($user->user_cv->job_environment, true);
        $user->user_cv->job_time = json_decode($user->user_cv->job_time, true);
    }

    return $this->sendResponse($user, 'User Cv!', 200);
}


}
