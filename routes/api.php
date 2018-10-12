<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth:api')->get('/userinfo', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->get('/logout', function (Request $request) {
    $request->user()->token()->revoke();

    return response(array(
        'error' => false,
        'message' => 'You are logged out',
    ), 200);
});

Route::middleware(['auth:api'])->group(function () {
    Route::get('/v1/employees/{id?}', 'Api\EmployeeController@index');
    Route::post('/v1/employees', 'Api\EmployeeController@store');
    Route::post('/v1/employees/{id}', 'Api\EmployeeController@update');
    Route::delete('/v1/employees/{id}', 'Api\EmployeeController@destroy');

    Route::post('/user/changepassword', 'Api\UserApiController@change_password');
    Route::post('/user/updateprofile', 'Api\UserApiController@update_profile');
    Route::post('/user/updatemobilenumber', 'Api\UserApiController@update_mobile_number');
    Route::post('/user/sendEmailPasswordChanged', 'Api\UserApiController@sendEmail_PasswordChanged');
    Route::post('/user/sendEmailProfileUpdated', 'Api\UserApiController@sendEmail_ProfileUpdated');
});

Route::post('/user/checkEmailVerified', 'Api\UserApiController@checkEmailVerified');
Route::post('/user/checkMobileNumberVerified', 'Api\UserApiController@checkMobileNumberVerified');
Route::post('/user/checkEmailIdExist', 'Api\UserApiController@checkEmailIdExist');

Route::post('/user/signup', 'Api\UserApiController@signup');
Route::post('/user/socialLogin', 'Api\UserApiController@SocialLogin');

Route::post('/user/googleLogin', 'Api\UserApiController@GoogleLogin');
Route::post('/user/fbLogin', 'Api\UserApiController@FaceBookLogin');

Route::post('/user/sendOtp', 'Api\UserApiController@sendOTPSMS');
Route::post('/user/sendSMS', 'Api\UserApiController@sendSMS');

Route::post('/user/verifymobileotp', 'Api\UserApiController@verify_mobile_otp');
Route::post('/user/verifyemail', 'Api\UserApiController@verify_email');

Route::post('/user/forgotpassword', 'Api\UserApiController@forgot_password');
Route::post('/user/sendEmailVerificationCode', 'Api\UserApiController@sendEmail_VerificationCode');
Route::post('/user/sendEmailVerified', 'Api\UserApiController@sendEmail_EmailVerified');
