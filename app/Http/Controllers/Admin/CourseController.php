<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Http\Controllers\BaseController;
class CourseController extends BaseController
{
    // public function index(Request $request)
    // {
    //     $query = Course::query();
    //     $filterable = ['status', 'location', 'price', 'start_date', 'type', 'company_id'];

    //     foreach ($filterable as $filter) {
    //         if ($request->has($filter)) {
    //             $value = $request->input($filter);
    //             if ($filter == 'start_date') {
    //                 $query->whereDate($filter, '=', $value);
    //             } else {
    //                 $query->where($filter, $value);
    //             }
    //         }
    //     }


    //     $courses = $query->select(['id', 'name', 'created_at', 'company_id'])
    //                      ->with('company:id,name')
    //                      ->get()
    //                      ->map(function ($course) {
    //                          return [
    //                              'id' => $course->id,
    //                              'name' => $course->name,
    //                              'created_at' => $course->created_at,
    //                              'company_name' => $course->company->name ?? 'N/A'
    //                          ];
    //                      });

    //     return $this->sendResponse($courses, 'Courses retrieved successfully');
    // }

    public function index(Request $request)
{
    $query = Course::query();
    $filterable = ['status', 'location', 'price', 'start_date', 'type', 'company_id'];

    foreach ($filterable as $filter) {
        if ($request->has($filter)) {
            $value = $request->input($filter);
            if ($filter == 'start_date') {
                $query->whereDate($filter, '=', $value);
            } else {
                $query->where($filter, $value);
            }
        }
    }


    $perPage = $request->input('per_page', 2);


    $courses = $query->select(['id', 'name', 'created_at', 'company_id'])
                     ->with('company:id,name')
                     ->paginate($perPage)
                     ->through(function ($course) {
                         return [
                             'id' => $course->id,
                             'name' => $course->name,
                             'created_at' => $course->created_at,
                             'company_name' => $course->company->name ?? 'N/A'
                         ];
                     });

    return $this->sendResponse($courses, 'Courses retrieved successfully');
}


    public function show(string $id)
    {
        $course = Course::with('company')->find($id);
        if (!$course) {
            return $this->sendError(404, 'Course not found.');
        }
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
    public function destroy(string $id)
    {
        $course = Course::find($id);
        if (!$course) {
            return $this->sendError(404,'Course not found.');
        }

        $course->delete();
        return $this->sendResponse(true, 'Course deleted successfully');

    }
}
