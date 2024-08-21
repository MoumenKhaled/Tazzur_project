<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Company;
use App\Models\Course;
use App\Models\CourseApplication;
use App\Models\User;
use App\Models\ExperienceYearsTranslate;
use App\Models\Locations;
use App\Models\Topics;
use App\Services\FirebaseService;
use App\Models\FcmToken;
use App\Notifications\ApplicationCourseNotification;

class CourseController extends Controller
{
    public function courses()
{
        $page = request()->query('page', 1);
        $courses = Course::where('status', 'current')
            ->distinct()
            ->with('company:id,name,logo,location,about_us')
            ->paginate(10);

        return $courses;

}
    public function course_details($id)
{
        $job = Course::where('id', $id)->with('company:id,name,logo,location,about_us')->firstOrFail();
        return $job;
}
    public function filter_courses(Request $request)
{
        $validator = $request->validate([
            'topic',
            'work_city',
        ]);

        $page = request()->query('page', 1);

        $topicArray = $request->topic ?? [];
        $workCityArray = $request->work_city ?? [];

        $locations = Locations::whereIn('name_ar', $workCityArray)
        ->orWhereIn('name_en', $workCityArray)
        ->get();

        $topics = Topics::whereIn('name_ar', $topicArray)
            ->orWhereIn('name_en', $topicArray)
            ->get();

            $courseQuery = Course::where('status', 'current');

            if ($topics->isNotEmpty()) {
                $courseQuery=$courseQuery->where('topic', $topics->pluck('name_ar'))
                    ->orwhereIn('topic', $topics->pluck('name_en'));
            }
            
            if ($locations->isNotEmpty()) {
                $courseQuery=$courseQuery->where('location', $locations->pluck('name_ar'))
                    ->orwhereIn('location', $locations->pluck('name_en'));
            }
           

            $courseQuery->distinct()
            ->with('company:id,name,logo,location,about_us');


            $currentcourses = $courseQuery->paginate(10);

            return response()->json($currentcourses, 200);
}
    public function search_courses(Request $request)
{
        $validator = $request->validate([
            'value' => 'required',
        ]);
        $check= false;
        $page = request()->query('page', 1);
        $var = '%' . $request->value . '%';

        $location = Locations::where('name_ar','like',$var)
        ->orWhere('name_en', 'like', $var)
        ->first();

        $topic = Topics::where('name_ar','like',$var)
        ->orWhere('name_en', 'like', $var)
        ->first();

        $company = Company::where('name', 'like', $var)->pluck('id');

        $value = Course::where('status', 'current');

        if ($location) {
             $value->where('location', 'like', $location->name_ar)
                ->orWhere('location', 'like', $location->name_en);
             $check= true;
        }

        if ($topic) {
           $value->where('topic', 'like', $topic->name_en)
                ->orWhere('topic', 'like', $topic->name_ar);
            $check= true;
        }

        if ($company->isNotEmpty()) {
            $value->WhereIn('company_id', $company);
            $check= true;
        }

        $value = $value->distinct()
            ->with('company:id,name,logo,location,about_us');
        if (!$check){
            $value = $value->Where('location', 'like', $var)
            ->Where('topic', 'like', $var);
            }
              
        $currentcourses = $value->paginate(10);
        return response()->json($currentcourses, 200);
}
    public function interested(Request $request,$id)
{
        $user_id = auth()->id();
        $isexist=CourseApplication::where([
            ['user_id',$user_id],
            ['course_id',$id]
        ])->first();
        if ($isexist){
            return response()->json(['message'=>'Sorry,  You was interested in this course before'], 400);
        }

        $interested=new CourseApplication();
        $interested->user_id=$user_id;
        $interested->course_id=$id;
        $interested->status="interested";
        $interested->save();
        $course=Course::where('id',$id)->first();
        $company=Company::where('id',$course->company_id)->first();
        $user=User::find($user_id);
     //   $company->notify(new ApplicationCourseNotification($course,$user));

        $messages = [
            'ar' => [
                'title' => 'مهتم جديد بالكورس!',
                'description' => 'لديكم مستخدم جديد أظهر اهتمامًا بأحد الكورسات. يمكنكم مراجعة تفاصيل الاهتمام والتواصل مع المستخدم لمزيد من المعلومات',
            ],
            'en' => [
                'title' => 'New Interest in Your Course!',
                'description' => 'A new user has shown interest in one of your courses. You can review the interest details and contact the user for more information.'
            ]
        ];
     
        $url = "http://86.38.218.161:8081/CourseUsers/" . $course->id;

       // $url = route('company_applications_course', ['id' => $course->id,]);
        $customerToken = FcmToken::where(
            [
                'user_id' => $company->id,
                'type' => 'company',
            ]
        )->pluck('token')->toArray();
        if (!empty($customerToken)) {
            FirebaseService::sendNotification($customerToken, $messages, $url);
        }
       $notification = new \App\Notifications\ServiceNotification($messages , $url);
       $company->notify($notification);


        return response()->json(['message'=>'You have successfully interested for the course'], 200);
}

}
