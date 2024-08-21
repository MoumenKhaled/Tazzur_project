 <?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Company\ApplicationCourseController;
use App\Http\Controllers\Company\ApplicationJobController;
use App\Http\Controllers\Company\AuthController;
use App\Http\Controllers\Company\ConsulutionController;

use App\Http\Controllers\Company\CourseController;
use App\Http\Controllers\Company\JobController;
use App\Http\Controllers\Company\SurveyController;
use App\Http\Controllers\Company\SeekerController;
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
    Route::post('register', 'register');
    Route::post('forget_password', 'forget_password');
    Route::post('confirm_reset_password', 'reset_password');
    Route::post('resend_code', 'resend_code');

    Route::post('verify_code', 'verify_code');
    Route::group(['middleware'=>['auth:company']],function (){
        Route::post('complete_register', 'complete_register')->middleware(['is_verified']);
        Route::get('check_status', 'check_status');
        Route::post('logout', 'logout');
        Route::get('profile', 'profile');
        Route::post('delete_account', 'delete_account');
        Route::get('companies/download_docs/{id}', 'downloadDocuments');
    });
});
Route::post('accept_refuse/course/{id}', [ApplicationCourseController::class, 'accept_refuse'])->name('accept_refuse');
 Route::middleware(['auth:company','is_complete'])->group(function ()
   {
    Route::apiResources([
        'consulutions' =>ConsulutionController::class,
        'surveys' =>SurveyController::class,
        'courses' =>CourseController::class,
        'jobs' =>JobController::class,
    ]);

    Route::post('/consulutions/{id}/review', [ConsulutionController::class, 'review']);

Route::controller(SeekerController::class)->group(function () {
        Route::get('seeker/cv/{id}', 'user_cv');
    });

    Route::controller(JobController::class)->group(function () {
        Route::post('jobs/clone/{id}', 'clone');
    });
    Route::controller(CourseController::class)->group(function () {
        Route::post('courses/clone/{id}', 'cloneCourse');
    });
Route::controller(ApplicationCourseController::class)->group(function () {
        Route::get('applications/course/{id}', 'applications')->name('company_applications_course');
        Route::get('details_application/course/{id}', 'details_application');

    });
Route::controller(ApplicationJobController::class)->group(function () {
        Route::get('applications/job/{id}', 'applications')->name('company_application_jobs');
        Route::get('details_application/job/{id}', 'details_application');
        Route::post('accept_refuse/job/{id}', 'accept_refuse');
    });
});
