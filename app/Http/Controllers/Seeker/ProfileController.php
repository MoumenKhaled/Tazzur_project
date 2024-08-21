<?php

namespace App\Http\Controllers\Seeker;
use App\Services\GeneratePDFService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\User_Cv;
use App\Models\UserLink;
use App\Models\Experience;
use App\Models\UserCourse;
use App\Models\UserReference;
use App\Models\Verification;
use Illuminate\Support\Facades\File;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Json;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Queue;

class ProfileController extends Controller
{
    public function show_profile()
{
        $user_id=auth()->id();
        $user=User::find($user_id);
        $user_cv=$user->user_cv;
        $links = $user->links;
        $user_info=[
            'image'=>$user_cv->image,
            'first_name'=>$user->first_name,
            'last_name'=>$user->last_name,
            'email'=>$user->email,
            'links'=>$links,
            'cv_file'=>$user_cv->cv_file
        ];
        return $user_info;
}
    public function update_personal_information(Request $request)
{
        $validator = $request->validate([
            'first_name',
            'last_name',
            'governorate',
            'address',
            'marital_status',
            'nationality',
            'image',
            'phone'
       ]);

       $user_id=auth()->id();
       $user=User::find($user_id);
       $user_cv=$user->user_cv;

        $user->first_name=isset($request->first_name) ? $request->first_name : $user->first_name;
        $user->last_name=isset($request->last_name) ? $request->last_name: $user->last_name;
        $user->governorate=isset($request->governorate) ? $request->governorate: $user->governorate;
        $user->address=isset($request->address) ? $request->address: $user->address;
        $user->marital_status=isset($request->marital_status) ? $request->marital_status: $user->marital_status;
        $user->nationality=isset($request->nationality) ? $request->nationality: $user->nationality;
        $user->phone=isset($request->phone) ? $request->phone: $user->phone;

        $user->save();

        $this->upload_image($request,$user_cv);

        //response date
        return response()->json([
            "message"=>'Personal information has been updated successfully',
            'image'=>$user_cv->image,
            'first_name'=>$user->first_name,
            'last_name'=>$user->last_name,
            'governorate'=>$user->governorate,
            'address'=>$user->address,
            'marital_status'=>$user->marital_status,
            'nationality'=>$user->nationality,
            'phone'=>$user->phone
        ],200);
}
    public function update_basic_information(Request $request)
{
        $validator = $request->validate([
            'gender',
            'driving_license',
            'military_status',
            'education',
            'experience_years',
            'birthday',
       ]);

       $user_id=auth()->id();
       $user=User::find($user_id);

        $user->gender=isset($request->gender) ? $request->gender : $user->gender;
        $user->driving_license=isset($request->driving_license) ? $request->driving_license: $user->driving_license;
        $user->military_status=isset($request->military_status) ? $request->military_status: $user->military_status;
        $user->education=isset($request->education) ? $request->education: $user->education;
        $user->experience_years=isset($request->experience_years) ? $request->experience_years: $user->experience_years;
        $user->birthday=isset($request->birthday) ? $request->birthday: $user->birthday;
        $user->save();

        //response date
        return response()->json([
            "message"=>'Basic information has been updated successfully',
            'driving_license'=>$user->driving_license,
            'military_status'=>$user->military_status,
            'education'=>$user->education,
            'experience_years'=>$user->experience_years,
            'birthday'=>$user->birthday,
            'gender'=>$user->gender,

        ],200);
}
    public function update_target_job_information(Request $request)
{
        $validator = $request->validate([
            'job_time',
            'job_environment',
            'topic',
            'job_current',
            'work_city',
       ]);

       $user_id=auth()->id();
       $user=User::find($user_id);
       $user_cv=$user->user_cv;

       $user_cv->job_time=isset($request->job_time) ? json_encode($request->job_time) : $user_cv->job_time;
       $user_cv->job_environment=isset($request->job_environment) ? json_encode($request->job_environment) : $user_cv->job_environment;
       $user_cv->job_current=isset($request->job_current) ? ($request->job_current) : $user_cv->job_current;
       $user_cv->work_city=isset($request->work_city) ? json_encode($request->work_city) : $user_cv->work_city;
       $user_cv->save();

        $user->topic=isset($request->topic) ? json_encode($request->topic): $user->topic;
        $user->save();

        //response date
        return response()->json([
            "message"=>'Target job information has been updated successfully',
            'job_time'=>json_decode($user_cv->job_time),
            'job_environment'=>json_decode($user_cv->job_environment),
            'job_current'=>$user_cv->job_current,
            'work_city'=>json_decode($user_cv->work_city),
            'topic'=>($user->topic),
        ],200);
}
    public function cv_information(Request $request)
{
        $validator = $request->validate([
            'job_field',
            'skills',
            'languages',
       ]);

       $user_id=auth()->id();
       $user=User::find($user_id);
       $user_cv=$user->user_cv;
       $experiences=$user->experiences;
       $traning_course=$user->cvCourses;
       $references=$user->references;
       $links=$user->links;
       $user_cv->languages=isset($request->languages) ? json_encode($request->languages) : $user_cv->languages;
       $user_cv->skills=isset($request->skills) ? ($request->skills) : $user_cv->skills;
       $user_cv->job_field=isset($request->job_field) ? ($request->job_field) : $user_cv->job_field;
       $user_cv->save();

       return response()->json([
        "message"=>'cv information has been updated successfully',
        'experiences'=>$experiences,
        'traning_course'=>$traning_course,
        'references'=>$references,
        'links'=>$links,
        'languages'=>json_decode($user_cv->languages),
        'skills'=>$user_cv->skills,
        'job_field'=>$user_cv->job_field,
    ],200);

}
    public function add_experience(Request $request)
{
        $validator = $request->validate([
            'company_name'=>'required',
            'job_title'=>'required',
            'start_date'=>'required',
            'end_date'=>'required',
            'name'=>'required',
            'details'
       ]);
       $user_id=auth()->id();
       $experience = new Experience();
       $experience->user_id=$user_id;
       $experience->company_name = $request->company_name;
       $experience->job_title = $request->job_title;
       $experience->start_date = $request->start_date;
       $experience->end_date = $request->end_date;
       $experience->name = $request->name;
       $experience->details = $request->details;

       $experience->save();
       return response()->json([
        "message"=>'Experience has been added successfully',
        'experiences'=>$experience,

    ],200);

}
    public function update_experience(Request $request,$id)
{
        $validator = $request->validate([
            'company_name',
            'job_title',
            'start_date',
            'end_date',
            'name',
            'details'
       ]);
       $user_id=auth()->id();
       $experience=Experience::find($id);

       $experience->company_name=isset($request->company_name) ? $request->company_name : $experience->company_name;
       $experience->job_title=isset($request->job_title) ? $request->job_title : $experience->job_title;
       $experience->start_date=isset($request->start_date) ? $request->start_date : $experience->start_date;
       $experience->end_date=isset($request->end_date) ? $request->end_date : $experience->end_date;
       $experience->name=isset($request->name) ? $request->name : $experience->name;
       $experience->details=isset($request->details) ? $request->details : $experience->details;

       $experience->save();
       return response()->json([
        "message"=>'Experience has been updated successfully',
        'experiences'=>$experience,

    ],200);

}
    public function add_traning_course(Request $request)
{
        $validator = $request->validate([
            'name'=>'required',
            'source'=>'required',
            'duration'=>'required',
            'image',
            'details',
       ]);
       $user_id=auth()->id();
       $usercourse = new UserCourse();
       $usercourse->user_id=$user_id;
       $usercourse->name = $request->name;
       $usercourse->source = $request->source;
       $usercourse->duration = $request->duration;
       $usercourse->details = $request->details;
       $usercourse->save();
       if($request->hasFile('image'))
        {
                $allowedFileExtension=['jpg','jpeg','png','bmp'];
                $file=$request->file('image');
                $extension=$file->getClientOriginalExtension();
                $check=in_array($extension,$allowedFileExtension);
                if ($check){
                  $Imagename="course$usercourse->id" . '.'. $file->getClientOriginalExtension();
                  $file->move(public_path("uploads/user_courses/user$user_id"),$Imagename);
                  $path="uploads/user_courses/user$user_id/$Imagename";
                  $usercourse->image=$path;
                  $usercourse->save();
                }
              else {
                return response()->json([
                    'status'=>false,
                    'message'=>'invalid image format'
                ],400);
              }
        }



       return response()->json([
        "message"=>'Traning course has been added successfully',
        'training_course'=>$usercourse,

    ],200);

}
    public function update_traning_course(Request $request,$id)
{
        $validator = $request->validate([
            'name',
            'source',
            'duration',
            'image',
            'details',
       ]);
       $user_id=auth()->id();

       $usercourse=usercourse::find($id);
       $usercourse->name=isset($request->name) ? $request->name : $usercourse->name;
       $usercourse->source=isset($request->source) ? $request->source : $usercourse->source;
       $usercourse->duration=isset($request->duration) ? $request->duration : $usercourse->duration;
       $usercourse->details=isset($request->details) ? $request->details : $usercourse->details;


       if($request->hasFile('image'))
        {
                $allowedFileExtension=['jpg','jpeg','png','bmp'];
                $file=$request->file('image');
                $extension=$file->getClientOriginalExtension();
                $check=in_array($extension,$allowedFileExtension);
                if ($check){
                    $Imagename="course$usercourse->id" . '.'. $file->getClientOriginalExtension();
                    $file->move(public_path("uploads/user_courses/user$user_id"),$Imagename);
                    $path="uploads/user_courses/user$user_id/$Imagename";
                  $usercourse->image=$path;
                }
              else {
                return response()->json([
                    'status'=>false,
                    'message'=>'invalid image format'
                ],400);
              }
        }

       $usercourse->save();

       return response()->json([
        "message"=>'Traning course has been updated successfully',
        'training_course'=>$usercourse,

    ],200);

}
    public function add_reference(Request $request)
{
        $validator = $request->validate([
            'name'=>'required',
            'employment'=>'required',
            'email'=>'required',
            'phone'=>'required',

       ]);
       $user_id=auth()->id();
       $userreference = new UserReference();
       $userreference->user_id=$user_id;
       $userreference->name = $request->name;
       $userreference->employment = $request->employment;
       $userreference->email = $request->email;
       $userreference->phone = $request->phone;

       $userreference->save();

       return response()->json([
        "message"=>'reference has been added successfully',
        'reference'=>$userreference,

    ],200);

}
    public function update_reference(Request $request,$id)
{
        $validator = $request->validate([
            'name',
            'employment',
            'email',
            'phone',

       ]);
       $user_id=auth()->id();

       $userreference=UserReference::find($id);
       $userreference->name=isset($request->name) ? $request->name : $userreference->name;
       $userreference->employment=isset($request->employment) ? $request->employment : $userreference->employment;
       $userreference->email=isset($request->email) ? $request->email : $userreference->email;
       $userreference->phone=isset($request->phone) ? $request->phone : $userreference->phone;
       $userreference->save();

       return response()->json([
        "message"=>'reference has been updated successfully',
        'reference'=>$userreference,

    ],200);

}
    public function add_link(Request $request)
{
        $validator = $request->validate([
            'title'=>'required',
            'link'=>'required',

       ]);
       $user_id=auth()->id();
       $userlink = new UserLink();
       $userlink->user_id=$user_id;
       $userlink->title = $request->title;
       $userlink->link = $request->link;

       $userlink->save();

       return response()->json([
        "message"=>'link has been added successfully',
        'reference'=>$userlink,

    ],200);

}
    public function update_link(Request $request,$id)
{
        $validator = $request->validate([
            'title',
            'link',

       ]);
       $userlink=UserLink::find($id);
       $userlink->title=isset($request->title) ? $request->title : $userlink->title;
       $userlink->link=isset($request->link) ? $request->link : $userlink->link;
       $userlink->save();

       return response()->json([
        "message"=>'link has been updated successfully',
        'reference'=>$userlink,

    ],200);

}

    public function upload_cv(Request $request)
{
    $validator = $request->validate([
        'cv_file' => 'required',
    ]);
    $user_id = auth()->id();
    $user_cv = User_Cv::where('user_id', $user_id)->first();

    if ($request->hasFile('cv_file')) {
        $allowedFileExtension = ['pdf','docx','doc'];
        $file = $request->file('cv_file');
        $extension = $file->getClientOriginalExtension();
        $check = in_array($extension, $allowedFileExtension);

        if ($check) {
            $filename = "user$user_id" . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/cvs/'), $filename);
            $path = "uploads/cvs/$filename";

            $user_cv->cv_file = $path;
            $user_cv->save();
        } else {
            return response()->json([
                'status' => false,
                'message' => 'invalid file format'
            ], 400);
        }
    }

    return response()->json([
        "message" => 'cv file has been uploaded successfully',
        'cv_file' => $user_cv->cv_file,
    ], 200);
}
public function delete_cv_file(Request $request)
{
    $user_id = auth()->id();
    $user_cv = User_Cv::where('user_id', $user_id)->first();

    if ($user_cv && $user_cv->cv_file) {
        // Assuming you have a base directory where files are stored
     $basePath = public_path('uploads/cvs');


        $filePath = $basePath . '/' . basename($user_cv->cv_file);  // Using basename() to extract the file name from URL

        if (File::exists($filePath)) {
            File::delete($filePath);
            $user_cv->cv_file = null;
            $user_cv->save();
            return response()->json(['message' => 'The cv file has been successfully deleted'], 200);
        } else {
            return response()->json(['message' => 'File does not exist'], 404);
        }
    }
    return response()->json(['message' => 'Failed to delete cv file or no file found'], 400);
}
    public function download_cv_file_old(Request $request)
{
       $user_id=auth()->id();
       $user=User::find($user_id);
       $user_cv=$user->user_cv;
       $experiences=$user->experiences;
       $traning_course=$user->cvCourses;
       $references=$user->references;
       $links=$user->links;
       return response()->json([
        $user,
        $user_cv,
        $experiences,
        $traning_course,
        $references,
        $links,
    ],200);

}







    private function upload_image($request,$user_cv)
    {
        if($request->hasFile('image'))
        {
                $allowedFileExtension=['jpg','jpeg','png','bmp'];
                $file=$request->file('image');
                $extension=$file->getClientOriginalExtension();
                $check=in_array($extension,$allowedFileExtension);
                if ($check){
                  $Imagename="user$user_cv->user_id". '.'. $file->getClientOriginalExtension();
                  $file->move(public_path('uploads/profiles'),$Imagename);
                  $path="uploads/profiles/$Imagename";
                  $user_cv->image=$path;
                  $user_cv->save();
                }
              else {
                return response()->json([
                    'status'=>false,
                    'message'=>'invalid image format'
                ],400);
              }
        }
    }
    public function download_cv_file()
    {
        $user_id=auth()->id();
        $user=User::where('id',$user_id)->with('user_cv','experiences','cvCourses','references','links')->first();
        $viewHtml = view('ats_cv_pdf', compact('user'));
        $pdf = GeneratePDFService::generate($viewHtml,'A4');
        return $pdf->download('$user_cv.pdf');
    }

}
