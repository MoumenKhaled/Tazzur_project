<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Http\Controllers\BaseController;
use App\Services\FirebaseService;
use App\Models\FcmToken;
use App\Models\Follower;
use App\Models\User;
use App\Models\Company;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Queue;

class CourseController extends BaseController
{


    public function index(Request $request)
    {
        $query = Course::query();
        $filterable = ['status', 'location', 'price', 'start_date', 'type'];


        foreach ($filterable as $filter) {
            if ($request->has($filter)) {
                if ($filter == 'start_date') {
                    $query->whereDate($filter, '=', $request->$filter);
                } else {
                    $query->where($filter, $request->$filter);
                }
            }
        }

        $perPage = $request->input('per_page', 2);

        $company = auth()->guard('company')->user();
        $courses = $query->where('company_id', $company->id)->select(['id', 'name', 'created_at'])->paginate($perPage);

        return $this->sendResponse($courses, 'Courses retrieved successfully');
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(Request $request, NotificationService $notificationService)
    {
        $validator = $request->validate([
            'duration' => 'required',
            'number_trainees' => 'required',
            'topic' => 'required',
            'type' => 'required',
            'start_date' => 'required|date|after:3 days',
            'end_date' => 'required|date|after_or_equal:start_date|after:start_date + 1 days'
        ]);
        $course = new Course();
        $course->duration = $request->duration;
        $course->number_trainees = $request->number_trainees;
        $course->topic = $request->topic;
        $course->type = json_encode($request->type);
        
      //  $course->type = $request->type;
        $course->name = $request->name;
        $course->company_id = auth()->guard('company')->user()->id;
        $course->start_date = $request->start_date;
        $course->end_date = $request->end_date;
        $course->days = $request->days;
        $course->price = $request->price;
        $course->location = $request->location;
        $course->status = 'current';

        $course->save();
        $company = Company::where('id', auth()->guard('company')->user()->id)->first();

        $messages = [
            'ar' => [
                'title' => 'فرصة تدريبية جديدة متاحة الآن!',
                'description' => 'اكتشف الفرص التدريبية الجديدة لتطوير مهاراتك وتعزيز مسيرتك المهنية. تفقد التفاصيل الآن وقدم طلبك!'
            ],
            'en' => [
                'title' => 'New Training Opportunity Available Now!',
                'description' => 'Explore the new training opportunities offered  to enhance your skills and advance your career. Check out the details now and apply!'
            ]
        ];
        $url = route('course_details', ['id' => $course->id,]);
        $followers = Follower::where('company_id', $company->id)->pluck('user_id');
        // $customerToken = FcmToken::whereIn('user_id', $followers)->where('type', 'user')->pluck('token')->toArray();
        // if (!empty($customerToken)) {
        //     FirebaseService::sendNotification($customerToken, $messages,  $url);
        // }
        // $notification = new \App\Notifications\ServiceNotification($messages,  $url);
        // foreach ($followers as $followerId) {
        //     $user = User::find($followerId);
        //     if ($user) {
        //         $user->notify(new \App\Notifications\ServiceNotification($messages,  $url));
        //     }
        // }
        Queue::push(function () use ($messages, $url, $followers) {
            $customerToken = FcmToken::whereIn('user_id', $followers)->where('type', 'user')->pluck('token')->toArray();

            if (!empty($customerToken)) {
                FirebaseService::sendNotification($customerToken, $messages, $url);
            }

            foreach ($followers as $followerId) {
                $user = User::find($followerId);
                if ($user) {
                    $user->notify(new \App\Notifications\ServiceNotification($messages, $url));
                }
            }
        });
        return $this->sendResponse($course, 'course');
    }
    public function cloneCourse(Request $request, $id)
    {

        $course = Course::find($id);
        if (!$course) {
            return $this->sendError(404, 'course not found');
        }
        $newCourse = new Course();
        $newCourse->duration = $course->duration;
        $newCourse->number_trainees = $course->number_trainees;
        $newCourse->topic = $course->topic;
        $newCourse->type = $course->type;
        $newCourse->name = $course->name;
        $newCourse->company_id = auth()->guard('company')->user()->id;
        $newCourse->start_date = $course->start_date;
        $newCourse->end_date = $course->end_date;
        $newCourse->days = $course->days;
        $newCourse->price = $course->price;
        $newCourse->location = $course->location;
        $newCourse->status = 'active';


        $newCourse->save();



        $company = Company::where('id', auth()->guard('company')->user()->id)->first();

        $messages = [
            'ar' => [
                'title' => 'فرصة تدريبية جديدة متاحة الآن!',
                'description' => 'اكتشف الفرص التدريبية الجديدة لتطوير مهاراتك وتعزيز مسيرتك المهنية. تفقد التفاصيل الآن وقدم طلبك!'
            ],
            'en' => [
                'title' => 'New Training Opportunity Available Now!',
                'description' => 'Explore the new training opportunities offered  to enhance your skills and advance your career. Check out the details now and apply!'
            ]
        ];
        $messages = [
            'ar' => [
                'title' => 'فرصة تدريبية جديدة متاحة الآن!',
                'description' => 'اكتشف الفرص التدريبية الجديدة لتطوير مهاراتك وتعزيز مسيرتك المهنية. تفقد التفاصيل الآن وقدم طلبك!'
            ],
            'en' => [
                'title' => 'New Training Opportunity Available Now!',
                'description' => 'Explore the new training opportunities offered  to enhance your skills and advance your career. Check out the details now and apply!'
            ]
        ];
        $url = route('course_details', ['id' => $course->id,]);
        $followers = Follower::where('company_id', $company->id)->pluck('user_id');
        $customerToken = FcmToken::whereIn('user_id', $followers)->where('type', 'user')->pluck('token')->toArray();
        if (!empty($customerToken)) {
            FirebaseService::sendNotification($customerToken, $messages,  $url);
        }
        $notification = new \App\Notifications\ServiceNotification($messages,  $url);
        foreach ($followers as $followerId) {
            $user = User::find($followerId);
            if ($user) {
                $user->notify(new \App\Notifications\ServiceNotification($messages,  $url));
            }
        }
        return $this->sendResponse($newCourse, 'Course cloned successfully.');
    }


    /**
     * Display the specified course.
     */
    public function show(string $id)
    {
        $course = Course::with('company')->find($id);
        if (!$course) {
            return $this->sendError(404, 'Course not found.');
        }

        // Extract company data from the course object


        $companyData = $course->company ? [
            'status' => $course->company->status,
            'name' => $course->company->name,
            'phone' => $course->company->phone,
            'topic' => $course->company->topic,
            'location_map' => $course->company->location_map,
            'location' => $course->company->location,
            'type' => $course->company->type,
            'logo' => $course->company->logo,
            'email' => $course->company->email,
            'about_us' => $course->company->about_us,
        ] : null;
        $course = Course::find($id);
        return $this->sendResponse([
            'course' => $course,
            'company' => $companyData
        ], 'course details with company');
    }


    /**
     * Update the specified course in storage.
     */
    public function update(Request $request, string $id)
    {
        $course = Course::find($id);
        if (!$course) {
            return $this->sendError(404, 'Course not found.');
        }
        $course->fill($request->all()); // This assumes $fillable is properly defined in your Course model
        $course->save();
        return $this->sendResponse($course, 'course');
    }

    /**
     * Remove the specified course from storage.
     */
    public function destroy(string $id)
    {
        $course = Course::find($id);
        if (!$course) {
            return $this->sendError(404, 'Course not found.');
        }

        $course->delete();
        return $this->sendResponse(true, 'Course deleted successfully');
    }
}
