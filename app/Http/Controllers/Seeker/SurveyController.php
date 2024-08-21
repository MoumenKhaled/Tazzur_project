<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Survey;
use App\Models\SurveyOption;
use App\Models\Company;
use App\Models\Vote;
use App\Models\Follower;
class SurveyController extends Controller
{
    public function show_surveys()
    {   
        $user_id=auth()->id();
        $page = request()->query('page', 1);
        $companies=Follower::where('user_id',$user_id)->pluck('company_id');
        $acceptable_companies=Company::whereIn('id',$companies)->where('status','acceptable')->pluck('id');
        $surveys=Survey::whereIn('company_id',$acceptable_companies)->with('company:id,name,logo,location,about_us')
        ->with('options')->paginate(10);
        return $surveys;
    }
    public function survey_details($id)
    {
        $survey=Survey::where('id',$id)
        ->with('company:id,name,logo,location,about_us')
        ->with('options')->first();

        return $survey;

    }
    public function vote(Request $request,$id)
    {
        $validator=$request->validate([
            'option_id'=>'required'
        ]);
        $user_id=auth()->id();
        $vote=Vote::where([
            ['survey_id',$id],
            ['user_id',$user_id]
        ])->first();
        if ($vote){
            //Reduce old option vote
            $old_option_id=$vote->option_id;
            $option_count=SurveyOption::where('id',$old_option_id)->first();
            $option_count->vote_count=($option_count->vote_count)-1;
            $option_count->save(); 
            
            //update option 
            $vote->option_id=$request->option_id;
            $vote->save();
            
        }
        else{

            $vote=new Vote();
            $vote->user_id=$user_id;
            $vote->option_id=$request->option_id;
            $vote->survey_id=$id;
            $vote->save();
           
        }
        //option_count
        $option_count=SurveyOption::where('id',$request->option_id)->first();
        $option_count->vote_count=($option_count->vote_count)+1;
        $option_count->save(); 
        $vote['vote_count']=$option_count->vote_count;
        
        return response()->json([
            'message'=>'You have successfully voted for the survey',
            'vote'=>$vote
        ], 200);
    }
}
