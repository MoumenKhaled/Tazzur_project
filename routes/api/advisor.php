<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Advisor\ConsulutionCompanyController;
use App\Http\Controllers\Advisor\ConsulutionController;
use App\Http\Controllers\Advisor\AuthController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::controller(AuthController::class)->group(function () {
    Route::post('add_addvisor', 'add_addvisor');
    Route::post('login', 'login');
    Route::post('logout', 'logout')->middleware('auth:advisors');
});

Route::middleware(['api','auth:advisors'])->group(function ()
{
    Route::controller(ConsulutionController::class)->group(function () {
        Route::get('show_consultation_requests', 'show_consultation_requests')->name('show_consultation_requests');
        Route::get('show_user_messages/{user_id}', 'show_user_messages');
        Route::get('show_company_messages/{company_id}', 'show_company_messages');

        Route::post('reply_to_user/{consultation_id}', 'reply_to_user');
        Route::post('reply_to_company/{consultation_id}', 'reply_to_company');

        Route::get('show_user_information/{user_id}', 'show_user_information');
        Route::get('show_company_profile/{company_id}', 'show_company_profile');

        
    });


});
