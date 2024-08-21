<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Advisor;
use Hash;
use App\Models\UserConsultation;
use App\Models\CompanyConsulution;
use App\Http\Controllers\BaseController;
class AdvisorController extends BaseController
{
    /**
     * عرض قائمة بجميع المستشارين.
     */
    public function index(Request $request)
{
    $perPage = $request->input('per_page', 2);

    $advisors = Advisor::paginate($perPage)->through(function ($advisor) {
        return [
            'id' => $advisor->id,
            'name' => $advisor->name,
            'email' => $advisor->email,
            'topic' => trans($advisor->topics),
            'rating'=> $advisor->rating,
        ];
    });

    return $this->sendResponse($advisors, 'Advisors retrieved successfully');
}

    /**
     * إضافة مستشار جديد إلى قاعدة البيانات.
     */
    public function store(Request $request)
    {
        $validator=$request->validate([
            'name'=>'required',
            'email' => 'required|email|unique:advisors,email',
            'password' => ['required', 'min:8', 'regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/'],
            'topics' => ['required'],
        ]);

        $advisor = Advisor::create($request->all());
        $advisor->password = Hash::make($request['password']);
        $advisor->save();
        return $this->sendResponse($advisor, 'advisor');
    }
     public function get_advisors()
    {
        $advisors = Advisor::get();
        $data=[
            'advisors'=>$advisors,
        ];
        return $this->sendResponse($data, 'advisor');

    }

    /**
     * عرض بيانات مستشار محدد.
     */


    public function show(Request $request, $id)
    {
        $advisor = Advisor::with(['consultations.company', 'user_consultations.user'])->find($id);
        if (!$advisor) {
            return $this->sendError(404, 'advisor not found');
        }
        $type = $request->input('type');
        if ($type === 'companies')
        {
            $users = $advisor->consultations->pluck('company')->unique('id')->map(function ($company) {
                return [
                    'id' => $company->id,
                    'name' => $company->name,
                    'email' => $company->email,

                ];
            });

        } else
         {
            $users = $advisor->user_consultations->pluck('user')->unique('id')->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ];
            });

        }
        $advisor = Advisor::where('id',$id)->first();
        $data=[
            'advisor'=>$advisor,
            'users'=>$users,
        ];
        return $this->sendResponse($data, 'Consultations');

    }
    public function get_advisor($id)
    {
        $advisor = Advisor::where('id',$id)->first();
        if (!$advisor) {
            return $this->sendError(404, 'advisor not found');
        }
        $data=[
            'advisor'=>$advisor,
        ];
        return $this->sendResponse($data, 'advisor');

    }



    public function consultations($user_id,$advisor_id,Request $request)
    {
        $type = $request->input('type');
        if ($type === 'companies')
        {
            $consultations = CompanyConsulution::where(
                [
                  //  'company_id'=>$user_id,
                    'advisor_id'=>$advisor_id
                ])
                ->orderBy('created_at')
                ->select('id','created_at','advisor_id','company_id','user_message','advisor_reply','review','rating')
            ->get();
        }
        else
        {
            $consultations = UserConsultation::where(
                [
                    'user_id'=>$user_id,
                    'advisor_id'=>$advisor_id
                ])->orderBy('created_at')
                ->select('id','created_at','advisor_id','user_id','user_message','advisor_reply','review','rating')
            ->get();
        }


        return $this->sendResponse($consultations, 'consultations');
    }




    /**
     * تحديث بيانات مستشار محدد في قاعدة البيانات.
     */
    public function update(Request $request, string $id)
    {
        $validator=$request->validate([
            'name'=>'required',
            'email' => 'required|email|unique:advisors,email,'.$id,
            'password' => ['nullable', 'min:8','regex:/^(?=.*[a-zA-Z])(?=.*\d).+$/'],
            'topics' => ['required'],
        ]);
        $advisor = Advisor::find($id);
        if (!$advisor) {
            return $this->sendError(404, 'advisor not found');
        }
        $advisor->update($request->all());
        if($request->password)
        {
            $advisor->update([
                'password'=>Hash::make($request->password)
            ]);
        }
        
        return $this->sendResponse($advisor, 'advisor');
    }

    /**
     * حذف مستشار من قاعدة البيانات.
     */
    public function destroy(string $id)
    {
        $advisor = Advisor::find($id);
        if (!$advisor) {
            return $this->sendError(404, 'advisor not found');
        }
        Advisor::findOrFail($id)->delete();
        return $this->sendResponse(null, 'done');
    }
}
