<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Company;
use App\Models\User;
use App\Models\User_Cv;
use App\Models\Follower;
use App\Models\Course;
use App\Models\ExperienceYearsTranslate;
use App\Models\Locations;
use App\Models\Topics;

class CompanyController extends Controller
{
    public function all_companies()
    {
        $page = request()->query('page', 1);
        $companies = Company::where('status', 'acceptable')
        ->select('id', 'logo', 'name', 'topic')
        ->paginate(10);
        
        return $companies;
    }

    public function followed_companies()
    {
        $page = request()->query('page', 1);
        $user_id=auth()->id();
        $companies_id=Follower::where('user_id', $user_id)->pluck('company_id');
        $followed_companies = Company::whereIn('id',$companies_id)
        ->select('id', 'logo', 'name', 'topic');

        $accptance_companies=$followed_companies->where('status', 'acceptable')->paginate(10);
        return $accptance_companies;
    }
    public function filter_companies(Request $request)
    {
            $validator = $request->validate([
                'topic',
                'work_city',
            ]);
        
            $topicArray = $request->topic ?? [];
            $workCityArray = $request->work_city ?? [];
            
            $page = request()->query('page', 1);

            $topicArray = $request->topic ?? [];
            $workCityArray = $request->work_city ?? [];

            $locations = Locations::whereIn('name_ar', $workCityArray)
            ->orWhereIn('name_en', $workCityArray)
            ->get();

            $topics = Topics::whereIn('name_ar', $topicArray)
                ->orWhereIn('name_en', $topicArray)
                ->get();
            
            $companyQuery = Company::where('status', 'acceptable');

            
            if ($topics->isNotEmpty()) {
                 $companyQuery->WhereIn('topic', $topics->pluck('name_ar'))
                    ->orWhereIn('topic', $topics->pluck('name_en'));
            }
            
            if ($locations->isNotEmpty()) {
                 $companyQuery->WhereIn('location', $locations->pluck('name_ar'))
                    ->orWhereIn('location', $locations->pluck('name_en'));
            }
            

            $accptance_companies=$companyQuery->distinct()->paginate(10);
            
            return response()->json($accptance_companies, 200);
    }
    public function search_for_companies(Request $request)
    {
            $validator = $request->validate([
                'value' => 'required',
            ]);
        
            $page = request()->query('page', 1);
            $var = '%' . $request->value . '%';

            $location = Locations::where('name_ar','like',$var)
            ->orWhere('name_en', 'like', $var)
            ->first();

            $topic = Topics::where('name_ar','like',$var)
            ->orWhere('name_en', 'like', $var)
            ->first();

            $value = Company::where('status', 'acceptable');
            
            if ($location) {
                $value->Where('location', 'like', '%'. $location->name_ar .'%')
                    ->orWhere('location', 'like', '%'.$location->name_en .'%');
            }
        
            if ($topic) {
                $value->Where('topic', 'like', $topic->name_en)
                    ->orWhere('topic', 'like', $topic->name_ar);
            }
            if ($value){
                $value->Where('name','like',$var);
            }
            $value = $value->distinct();
            $accptance_companies=$value->paginate(10);
            return response()->json($accptance_companies, 200);
    }
    public function company_details($compnay_id)
{   
    $user_id = auth()->id();
    $follow = Follower::where([
        ['user_id', $user_id],
        ['company_id', $compnay_id]
    ])->first();
    
    $is_followed = $follow ? true : false;

    $company_details = Company::where('id', $compnay_id)
        ->with(['jobs' => function ($query) {
            $query->where('status', 'current')
                ->where('hidden_name', false);
        }])
         ->with(['courses' => function ($query) {
            $query->where('status', 'current');
        }])
        ->get()
        ->toArray();
    
    
    return array_merge($company_details, ['is_followed' => $is_followed]);
}

    public function follow_unfollow($compnay_id)
    {
        $user_id=auth()->id();
        $follow=Follower::where([
            ['user_id',$user_id],
            ['company_id',$compnay_id]
        ])->first();
        if ($follow){
            $follow->delete();
            return response()->json(['message'=>'You have successfully unfollowed the company'], 200);
        }
        $follow=new Follower();
        $follow->user_id=$user_id;
        $follow->company_id=$compnay_id;
        $follow->save();
        return response()->json(['message'=>'You have successfully followed the company'], 200);
    }

}
