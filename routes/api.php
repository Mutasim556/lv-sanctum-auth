<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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



Route::controller(AuthController::class)->group(function(){
    Route::group(['prefix'=>'auth'],function(){
        Route::post('register','register');
        Route::post('login','login');
        

        Route::middleware('auth:sanctum')->group(function(){
            Route::post('logout','logout');
            Route::post('updateprofile','UpdateProfile');
            Route::post('updatepassword','UpdatePassword');
            Route::post('resend-verify-email','ResendVerifyEmail');
        });
        Route::post('forgot-password','ForgetPassword');
        Route::post('reset-password','ResetPassword');
        Route::get('test',function(){
            return "Hello";
        });
        Route::get('verify-email','VerifyEmail')->name('verification.verify');
        
    });
    
});
