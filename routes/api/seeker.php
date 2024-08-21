<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seeker\AuthController;
use App\Http\Controllers\Seeker\JobController;
use App\Http\Controllers\Seeker\CourseController;
use App\Http\Controllers\Seeker\SurveyController;
use App\Http\Controllers\Seeker\CompanyController;
use App\Http\Controllers\Seeker\ProfileController;
use App\Http\Controllers\Seeker\ConsulutionController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('forget_password', 'forget_password');
    Route::post('confirm_reset_password', 'confirm_reset_password');
    Route::post('verify_code', 'verify_code');
    Route::post('resend_code', 'resend_code');

    Route::group(['middleware'=>['auth:api']],function (){
    Route::post('complete_register', 'complete_register');
    Route::post('delete_account', 'delete_account');
    Route::post('change_password', 'change_password');
    Route::post('logout', 'logout');
});
});
Route::controller(ProfileController::class)->group(function () {
    Route::group(['middleware'=>['auth:api']],function (){
    Route::get('show_profile', 'show_profile');
    Route::post('update_personal_information', 'update_personal_information');
    Route::post('update_basic_information', 'update_basic_information');
    Route::post('update_target_job_information', 'update_target_job_information');
    Route::post('cv_information', 'cv_information');

    Route::post('add_experience', 'add_experience');
    Route::post('update_experience/{id}', 'update_experience');

    Route::post('add_traning_course', 'add_traning_course');
    Route::post('update_traning_course/{id}', 'update_traning_course');

    Route::post('add_reference', 'add_reference');
    Route::post('update_reference/{id}', 'update_reference');

    Route::post('add_link', 'add_link');
    Route::post('update_link/{id}', 'update_link');

    Route::post('upload_cv', 'upload_cv');
    Route::get('download_cv_file', 'download_cv_file');

    Route::post('delete_cv_file', 'delete_cv_file');
});
});
Route::controller(JobController::class)->group(function () {
    Route::group(['middleware'=>['auth:api']],function (){
    Route::get('my_jobs', 'my_jobs');
    Route::get('all_jobs', 'all_jobs');
    Route::post('filter', 'filter');
    Route::post('search', 'search');
    Route::get('job_details/{id}', 'job_details')->name('job_details');
    Route::get('show_questions/{id}', 'show_questions');
    Route::post('answer_and_applay/{id}', 'answer_and_applay');

});
});
Route::controller(CourseController::class)->group(function () {
    Route::group(['middleware'=>['auth:api']],function (){
    Route::get('courses', 'courses');
    Route::get('course_details/{id}', 'course_details')->name('course_details');
    Route::post('interested/{id}', 'interested');
    Route::post('filter_courses', 'filter_courses');
    Route::post('search_courses', 'search_courses');
});
});

Route::controller(SurveyController::class)->group(function () {
    Route::group(['middleware'=>['auth:api']],function (){
    Route::get('show_surveys', 'show_surveys');
    Route::get('survey_details/{id}', 'survey_details');
    Route::post('vote/{id}', 'vote');

});
});
Route::controller(CompanyController::class)->group(function () {
    Route::group(['middleware'=>['auth:api']],function (){
    Route::get('all_companies', 'all_companies');
    Route::get('followed_companies', 'followed_companies');
    Route::post('filter_companies', 'filter_companies');
    Route::post('search_for_companies', 'search_for_companies');
    Route::get('company_details/{id}', 'company_details');
    Route::post('follow_unfollow/{id}', 'follow_unfollow');
});
});
Route::controller(ConsulutionController::class)->group(function () {
    Route::group(['middleware'=>['auth:api']],function (){
    Route::get('get_topics', 'get_topics');
    Route::post('get_consultations', 'get_consultations')->name('consultations_details');
    Route::post('send_message', 'send_message');
    Route::post('review/{consulution_id}', 'review')->name('user_review_consulution');
});
});
