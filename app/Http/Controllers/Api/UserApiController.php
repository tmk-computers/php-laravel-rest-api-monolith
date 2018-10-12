<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\MailSender;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Response;

class UserApiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | UserApi Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
     */

    public function index($id = null)
    {
        if ($id == null) {
            return Device::orderBy('id', 'asc')->get();
        } else {
            return $this->show($id);
        }
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function checkEmailVerified(Request $request)
    {
        $user = User::where('email', $request['email'])->first();
        if ($user != null) {
            if ($user->email_verified == 1) {
                return response(array(
                    'error' => false,
                    'message' => 'Email is already verified',
                ), 200);
            } else {
                return response(array(
                    'error' => true,
                    'message' => 'Email Verification is pending',
                ), 400);
            }
        } else {
            return response(array(
                'error' => true,
                'message' => 'Email Not Found',
            ), 404);
        }
    }

    public function checkMobileNumberVerified(Request $request)
    {
        $user = User::where('email', $request['email'])->first();
        if ($user != null) {
            if ($user->mobile_number_verified == 1) {
                return response(array(
                    'error' => false,
                    'message' => 'Mobile Number is verified',
                ), 200);
            } else {
                return response(array(
                    'error' => true,
                    'message' => 'Mobile Number Verification is pending',
                ), 400);
            }
        } else {
            return response(array(
                'error' => true,
                'message' => 'Email Not Found',
            ), 404);
        }
    }

    public function checkEmailIdExist(Request $request)
    {
        $user = User::where('email', $request['email'])->first();
        if ($user != null) {
            return response(array(
                'error' => false,
                'message' => 'email id exists',
            ), 200);
        } else {
            return response(array(
                'error' => true,
                'message' => 'email Not Found',
            ), 404);
        }
    }

    public function GoogleLogin(Request $request)
    {
        $client = new \Google_Client([
            'client_id' => 'yourclientid.apps.googleusercontent.com',
        ]);
        $payload = $client->verifyIdToken($request['idToken']);

        return $payload;

        // {
        //     // These six fields are included in all Google ID Tokens.
        //     "iss": "https://accounts.google.com",
        //     "sub": "110169548443743867276334", //Unique Google Account Id
        //     "azp": "100871994270978-hb624n2dstb40o4579d4feuo2ukqmcc6381.apps.googleusercontent.com",
        //     "aud": "100871994270978-hb624n2dstb40o4579d4feuo2ukqmcc6381.apps.googleusercontent.com",
        //     "iat": "143397835213",
        //     "exp": "143398781953",
        //     // These seven fields are only included when the user has granted the "profile" and
        //     // "email" OAuth scopes to the application.
        //     "email": "testuser@gmail.com",
        //     "email_verified": "true",
        //     "name" : "Test User",
        //     "picture": "https://lh4.googleusercontent.com/-kYgzyAWptZzJ/ABCDEFGBHI/AAAHJKLMNOP/tIXL29Ir44LE/s979-c/photo1.jpg",
        //     "given_name": "Test",
        //     "family_name": "User",
        //     "locale": "en"
        //  }
    }

    public function FaceBookLogin(Request $request)
    {
        $fb = new \Facebook\Facebook([
            'app_id' => 'fbappid',
            'app_secret' => 'fbappsecret',
            'default_graph_version' => 'v2.12',
        ]);
        try {
            $fbresponse = $fb->get('/me?fields=id,name,email', $request['idToken']);
            $me = $fbresponse->getGraphUser();
            $userId = $me->getId();
            $email = $me->getEmail();
            $name = $me['name']; //$me->getName();
            return $me;
            // return Response::json($me);

        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            //Handle this error, return a failed request to the app with either 401 or 500
            return response(array(
                'error' => true,
                'message' => 'Handle this error, return a failed request to the app with either 401 or 500',
            ), 401);
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            //Handle this error, return a 500 error – something is wrong with your code
            return response(array(
                'error' => true,
                'message' => 'Handle this error, return a 500 error – something is wrong with your code',
            ), 500);
        }
    }

    public function SocialLogin(Request $request)
    {
        $defaultSocialPassword = 'defaultpassword';

        $user = User::where('email', $request['email'])->first();
        if ($user != null) {
            if ($user->isSocialLogin == 1) {
                if (Auth::attempt(['email' => request('email'), 'password' => $defaultSocialPassword])) {
                    $user = Auth::user();
                    $tokenResult = $user->createToken('Personal Access Token');
                    $token = $tokenResult->token;

                    if ($request->remember_me) {
                        $token->expires_at = Carbon::now()->addWeeks(1);
                    }

                    $token->save();

                    return response()->json([
                        'access_token' => $tokenResult->accessToken,
                        'token_type' => 'Bearer',
                        'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
                    ]);
                } else {
                    return response()->json(['error' => 'Unauthorised'], 401);
                }
            } else {
                return response(array(
                    'error' => false,
                    'message' => 'You are registered with password',
                ), 200);
            }
        } else {
            //create user with a default password
            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => bcrypt($defaultSocialPassword),

                'school_name' => '',
                'phone_number' => '',
                'city' => '',
                'passing_year' => 2018,                
                'email_otp' => 0,
                'mobile_otp' => 0,
                'isSocialLogin' => 1,
                'socialLoginProvider' => $request['socialLoginProvider'],
            ]);
            if ($user->exists) {
                if (Auth::attempt(['email' => request('email'), 'password' => $defaultSocialPassword])) {
                    $user = Auth::user();
                    $tokenResult = $user->createToken('Personal Access Token');
                    $token = $tokenResult->token;

                    if ($request->remember_me) {
                        $token->expires_at = Carbon::now()->addWeeks(1);
                    }

                    $token->save();

                    return response()->json([
                        'access_token' => $tokenResult->accessToken,
                        'token_type' => 'Bearer',
                        'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
                    ]);
                } else {
                    return response()->json(['error' => 'Unauthorised'], 401);
                }
            } else {
                return response(array(
                    'error' => true,
                    'message' => 'Problem in creating user',
                ), 400);
            }
        }
    }

    public function signup(Request $request)
    {
        $user = User::where('email', $request['email'])->first();
        if ($user != null) {
            return response(array(
                'error' => true,
                'message' => 'Same email id already exists',
            ), 400);
        }

        $six_digit_random_email_otp = mt_rand(100000, 999999);
        $six_digit_random_mobile_otp = mt_rand(100000, 999999);

        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),

            'school_name' => $request['school_name'],
            'phone_number' => $request['phone_number'],
            'city' => $request['city'],
            'passing_year' => $request['passing_year'],            
            'email_otp' => $six_digit_random_email_otp,
            'mobile_otp' => $six_digit_random_mobile_otp,
            'isSocialLogin' => 0,
            'socialLoginProvider' => '',
        ]);

        if ($user->exists) {
            // success
            return response()->json($user, 201);
        } else {
            return response(array(
                'error' => true,
                'message' => 'Problem in creating user',
            ), 400);
        }
    }

    public function sendOTPSMS(Request $request)
    {
        $user = User::where('email', $request['email'])->first();
        if ($user != null) {

            $six_digit_random_number = mt_rand(100000, 999999);

            $user->update(array('mobile_otp' => $six_digit_random_number));

            $mobileNumber = $user->phone_number;
            $msg = 'Your OTP for Mobile Number Verification is ' . $six_digit_random_number;
            $msg = str_replace(' ', '%20', $msg);
            $url = 'http://starbulksms.net/sendsms?uname=uname&pwd=pwd&senderid=senderid&to=' . $mobileNumber . '&msg=' . $msg . '&route=T';

            //echo $url;
            //Initialize cURL.
            $ch = curl_init();

            //Set the URL that you want to GET by using the CURLOPT_URL option.
            curl_setopt($ch, CURLOPT_URL, $url);

            //Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            //Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

            //Execute the request.
            $data = curl_exec($ch);

            //Close the cURL handle.
            curl_close($ch);
            return response(array(
                'error' => false,
                'message' => 'OTP SMS is Sent Successfully',
            ), 200);

        } else {
            return response(array(
                'error' => true,
                'message' => 'Email Not Found',
            ), 404);
        }

    }

    public function sendSMS(Request $request)
    {
        $mobileNumber = $request['mobilenumber'];
        $msg = $request['msg'];
        $msg = str_replace(' ', '%20', $msg);
        $url = 'http://starbulksms.net/sendsms?uname=uname&pwd=pwd&senderid=senderid&to=' . $mobileNumber . '&msg=' . $msg . '&route=T';

        //Initialize cURL.
        $ch = curl_init();

        //Set the URL that you want to GET by using the CURLOPT_URL option.
        curl_setopt($ch, CURLOPT_URL, $url);

        //Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        //Execute the request.
        $data = curl_exec($ch);

        //Close the cURL handle.
        curl_close($ch);
        return response(array(
            'error' => false,
            'message' => 'SMS is Sent Successfully',
        ), 200);
    }

    public function update_mobile_number(Request $request)
    {
        $user = User::where('email', $request['email'])->first();
        if ($user != null) {

            $user->update(array(
                'phone_number' => $request['phone_number'],
                'mobile_number_verified' => 0,
            ));

            return response(array(
                'error' => false,
                'message' => 'Mobile number Updated Successfully',
            ), 200);
        } else {
            return response(array(
                'error' => true,
                'message' => 'email Not Found',
            ), 404);
        }
    }

    public function update_profile(Request $request)
    {
        $user = User::where('email', $request['email'])->first();
        if ($user != null) {

            $user->update(array(
                'name' => $request['name'],
                'school_name' => $request['school_name'],
                'phone_number' => $request['phone_number'],
                'city' => $request['city'],
                'passing_year' => $request['passing_year']                
            ));

            return response(array(
                'error' => false,
                'message' => 'Profile Updated Successfully',
            ), 200);
        } else {
            return response(array(
                'error' => true,
                'message' => 'email Not Found',
            ), 404);
        }
    }

    public function verify_email(Request $request)
    {
        $code = $request['code'];
        $email = $request['email'];
        $user = User::where('email', $email)->first();
        if ($user != null) {
            if (strcmp($code, $user->email_otp) == 0) {
                $user->update(array('email_verified' => 1));
                return response(array(
                    'error' => false,
                    'message' => 'Your Account is activated',
                ), 200);
            } else {
                return response(array(
                    'error' => true,
                    'message' => 'Invalid Code',
                ), 400);
            }
        } else {
            return response(array(
                'error' => true,
                'message' => 'Invalid User',
            ), 404);
        }
    }

    public function verify_mobile_otp(Request $request)
    {
        $user = User::where('email', $request['email'])->first();
        if ($user != null) {

            $otp = $request['otp'];

            if (strcmp($otp, $user->mobile_otp) == 0) {
                $user->update(array('mobile_number_verified' => 1));
                return response(array(
                    'error' => false,
                    'message' => 'Mobile Number is verified Successfully',
                ), 200);
            } else {
                return response(array(
                    'error' => true,
                    'message' => 'Invalid Otp',
                ), 400);
            }

        } else {
            return response(array(
                'error' => true,
                'message' => 'email Not Found',
            ), 404);
        }
    }

    public function change_password(Request $request)
    {
        $user = User::where('email', $request['email'])->first();
        if ($user != null) {

            if (Hash::check($request['currentPassword'], $user->password)) {
                $user->update(array('password' => bcrypt($request['newPassword'])));

                return response(array(
                    'error' => false,
                    'message' => 'Password Updated Successfully',
                ), 200);
            } else {
                return response(array(
                    'error' => true,
                    'message' => 'Current Password does not match',
                ), 404);
            }
        } else {
            return response(array(
                'error' => true,
                'message' => 'email Not Found',
            ), 404);
        }
    }

    public function sendEmail_VerificationCode(Request $request)
    {
        $user = User::where('email', $request['email'])->first();
        if ($user != null) {

            //$six_digit_random_number = mt_rand(100000, 999999);

            //$user->update(array('email_otp' => $six_digit_random_number));

            $data = array(
                'name' => $user->name,
                'email' => $request['email'],
                // 'token' => base64_encode($rc->token),
                'code' => $user->email_otp,
            );
            try {

                $result = (new MailSender)->user_registerd($data);
                if ($result == 0) {
                    return response(array(
                        'error' => true,
                        'message' => 'Problem in sending activation Email',
                    ), 400);
                } else {
                    return response(array(
                        'error' => false,
                        'message' => 'Email sent Successfully',
                    ), 200);
                }
            } catch (Exception $e) {
                return response(array(
                    'error' => true,
                    'message' => 'Problem in sending Email',
                ), 400);
            }
        } else {
            return response(array(
                'error' => true,
                'message' => 'email Not Found',
            ), 404);
        }
    }

    public function sendEmail_ProfileUpdated(Request $request)
    {
        $data = array(
            'name' => $request['name'],
            'email' => $request['email'],
        );

        try {
            (new MailSender)->user_profile_updated($data);
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    public function sendEmail_EmailVerified(Request $request)
    {
        $data = array(
            'name' => $request['name'],
            'email' => $request['email'],
        );

        try {
            (new MailSender)->user_welcome($data);
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    public function sendEmail_PasswordChanged(Request $request)
    {

        $data = array(
            'name' => $request['name'],
            'email' => $request['email'],
        );
        try {
            (new MailSender)->user_password_changed($data);
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    public function forgot_password(Request $request)
    {
        $user = User::where('email', $request['email'])->first();
        if ($user != null) {

            if ($user->isSocialLogin == 1) {
                return response(array(
                    'error' => true,
                    'message' => 'You are registered with Social Login',
                ), 200);
            }

            $randomNewPassword = str_random(10);
            $user->update(array('password' => bcrypt($randomNewPassword)));

            $data = array(
                'name' => $user->name,
                'email' => $request['email'],
                'randomNewPassword' => $randomNewPassword,
            );

            try {
                (new MailSender)->user_forgot_password($data);
            } catch (Exception $e) {
                report($e);
                return false;
            }

            return response(array(
                'error' => false,
                'message' => 'Your new password is generated and sent to your mail',
            ), 200);
            //
        } else {
            //
            return response(array(
                'error' => true,
                'message' => 'email Not Found',
            ), 404);
        }
    }

    // public function contactus(Request $request)
    // {
    //     // $validator = Validator::make($request->all(), [
    //     //     'name' => 'required',
    //     //     'email' => 'required|email',
    //     //     'mobile_number' => 'required|Numeric',
    //     //     'subject' => 'required',
    //     //     'message' => 'required',
    //     // ]);

    //     // if ($validator->fails()) {
    //     //     return redirect::back()->withErrors($validator)->withInput();
    //     // } else {
    //     //     $data = array(
    //     //         'name' => $request->Input('name'),
    //     //         'email' => $request->Input('email'),
    //     //         'mobile' => $request->Input('mobile_nmber'),
    //     //         'subject' => $request->Input('subject'),
    //     //         'message' => $request->Input('message'),
    //     //     );

    //     //     Session::put('data', $data);

    //     //     (new SendMailController)->user_contactus();

    //     //     Session::forget('data');

    //     //     Session::flash('success', 'Thank you for your enquiry. We will be dealt with as soon as possible.');
    //     //     return back();
    //     // }
    // }
}
