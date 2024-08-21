<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\AdvisorController;
use App\Http\Controllers\Admin\CoordinatorController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\Admin\SeekerController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ApplicationCourseController;
use App\Http\Controllers\Admin\ApplicationJobController;
use App\Http\Controllers\Admin\MainController;

use App\Http\Controllers\Admin\ConsulutionCompanyController;
use App\Http\Controllers\Admin\ConsulutionUserController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('logout', 'logout')->middleware(['auth:sanctum']);

});

Route::middleware(['api','auth:manager'])->group(function () {
    Route::middleware(['check.role:admin'])->group(function () {
    Route::apiResources([
        'companies' => CompanyController::class,
        'advisors' => AdvisorController::class,
        'coordinators' => CoordinatorController::class,
        'courses' => CourseController::class,
        'jobs' => JobController::class,
        'seekers' => SeekerController::class,
        'CompanyConsulting' => ConsulutionCompanyController::class,
        'UserConsulting' => ConsulutionUserController::class,
    ]);
    Route::controller(ConsulutionCompanyController::class)->group(function () {
        Route::post('CompanyConsulting/convert/{id}', 'convert');
    });
    Route::controller(ConsulutionUserController::class)->group(function () {
        Route::post('UserConsulting/convert/{id}', 'convert');
    });
    Route::controller(CompanyController::class)->group(function () {
        Route::post('companies/set_status/{id}', 'set_status');
        Route::get('companies/download_docs/{id}', 'downloadDocuments');
    });
    Route::controller(MainController::class)->group(function () {
        Route::get('getStatistics', 'getStatistics');
    });
    Route::controller(AdvisorController::class)->group(function () {
        Route::get('advisors/consultations/{user_id}/{advisor_id}', 'consultations');
        Route::get('advisors/get_advisor/{id}', 'get_advisor');
        Route::get('get_advisors', 'get_advisors');

    });
    Route::controller(CoordinatorController::class)->group(function () {
    });
    Route::controller(CourseController::class)->group(function () {
        //applications (id course)
    });
    Route::controller(JobController::class)->group(function () {

        Route::post('accept_refuse_job/{id}', 'accept_refuse');
    });
    Route::controller(ApplicationCourseController::class)->group(function () {
        //index(applications : id course)
        Route::get('course_applications/{id}', 'applications');  // id course
        Route::get('course_application_details/{id}', 'details_application');   // id application
    });
    Route::controller(ApplicationJobController::class)->group(function () {
        Route::get('job_applications/{id}', 'index')->name('manager_applications_jobs');
       
        Route::get('job_application_details/{id}', 'details_application');

        Route::post('applications/priority/{application_id}/{number}', 'priority');
        Route::post('applications/convert/{job_id}','convert');
        Route::post('job_filter', 'filter');
        Route::post('job_transfer', 'transfer');

         //index(applications : id job)   - filter - transfer
    });
    Route::controller(SeekerController::class)->group(function () {
    Route::post('block_unblock_seeker/{id}', 'block_unblock');
    Route::get('seeker/cv/{id}', 'user_cv');
    });
});

Route::middleware(['check.role:job_requests_coordinator,job_posting_requests_coordinator'])->group(function () {
    Route::apiResource('jobs', JobController::class)->only(['index', 'show']);
});
Route::middleware(['check.role:job_requests_coordinator'])->group(function () {
    Route::apiResources([
        'job_applications' => ApplicationJobController::class,
    ]);
    Route::controller(ApplicationJobController::class)->group(function () {
        Route::get('job_applications/{id}', 'index');
        Route::get('job_application_details/{id}', 'details_application');

        Route::post('applications/priority/{application_id}/{number}', 'priority');
        Route::post('applications/convert/{job_id}','convert');
        Route::post('job_filter', 'filter');
        Route::post('job_transfer', 'transfer');

    });
});
Route::middleware(['check.role:job_posting_requests_coordinator'])->group(function () {

    Route::controller(JobController::class)->group(function () {

        Route::post('accept_refuse_job/{id}', 'accept_refuse');
    });
});

Route::middleware(['check.role:job_requests_coordinator,user_consultation_coordinator'])->group(function () {
    Route::controller(SeekerController::class)->group(function () {
        Route::get('seeker/cv/{id}', 'user_cv');
        });
});

Route::middleware(['check.role:admin,job_requests_coordinator,job_posting_requests_coordinator,company_consultation_coordinator'])->group(function () {
    Route::apiResource('companies', CompanyController::class)->only(['show']);

    Route::get('companies/download_docs/{id}', [CompanyController::class, 'downloadDocuments']);

});

Route::middleware(['check.role:company_consultation_coordinator'])->group(function () {
    Route::apiResource('CompanyConsulting', ConsulutionCompanyController::class)->only(['index', 'show'])->names([
        'index' => 'companyconsulting.index',
        'show' => 'companyconsulting.show'
     ]);
    Route::controller(ConsulutionCompanyController::class)->group(function () {
        Route::post('CompanyConsulting/convert/{id}', 'convert');
    });
});
Route::middleware(['check.role:user_consultation_coordinator'])->group(function () {
    Route::apiResource('UserConsulting', ConsulutionUserController::class)->only(['index', 'show'])->names([
        'index' => 'userconsulting.index',
        'show' => 'userconsulting.show'
     ]);
    Route::apiResource('seekers', SeekerController::class)->only(['show']);
    Route::controller(ConsulutionUserController::class)->group(function () {
        Route::post('UserConsulting/convert/{id}', 'convert');
    });
});


Route::middleware(['check.role:user_consultation_coordinator,company_consultation_coordinator'])->group(function () {
    Route::controller(AdvisorController::class)->group(function () {
        Route::get('advisors/consultations/{user_id}/{advisor_id}', 'consultations');
        Route::get('advisors/get_advisor/{id}', 'get_advisor');
        Route::get('get_advisors', 'get_advisors');
    });
});




});

// Route::middleware(['check.role:job_requests_coordinator'])->group(function () {
//     Route::apiResource('jobs', JobController::class)->only(['index', 'show']);
//     Route::get('companies/{id}', [CompanyController::class, 'show']);
//     Route::get('companies/download_docs/{id}', [CompanyController::class, 'downloadDocuments']);
//     Route::get('seekers/{id}/cv', [SeekerController::class, 'user_cv']);
//     Route::apiResources([
//         'job_applications' => ApplicationJobController::class,
//     ]);
// });

// Route::middleware(['check.role:company_consultation_coordinator'])->group(function () {
//     Route::apiResource('CompanyConsulting', ConsulutionCompanyController::class)->only(['index', 'show', 'convert']);
//     Route::get('companies/{id}', [CompanyController::class, 'show']);
//     Route::get('companies/download_docs/{id}', [CompanyController::class, 'downloadDocuments']);
// });
// Route::middleware(['check.role:user_consultation_coordinator'])->group(function () {
//     Route::apiResource('UserConsulting', ConsulutionUserController::class)->only(['index', 'show', 'convert']);
//     Route::apiResource('seekers', SeekerController::class)->only(['show']);
//     Route::get('seekers/{id}/cv', [SeekerController::class, 'user_cv']);
// });
// Route::middleware(['check.role:job_posting_requests_coordinator'])->group(function () {
//     Route::apiResource('jobs', JobController::class)->only(['index', 'show','accept_refuse']);
//     Route::get('companies/{id}', [CompanyController::class, 'show']);
//     Route::get('companies/download_docs/{id}', [CompanyController::class, 'downloadDocuments']);
// });

//});
