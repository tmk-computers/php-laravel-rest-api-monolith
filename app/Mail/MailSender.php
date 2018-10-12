<?php

namespace App\Mail;
use App\Mail\EmailVerification;
use App\Mail\PdfAttachment;
use App\Mail\UserContactUs;
use App\Mail\UserForgotPassword;
use App\Mail\UserPasswordChanged;
use App\Mail\UserProfileUpdated;
use App\Mail\UserWelcome;
use Mail;

class MailSender
{

    public function send_pdf($data)
    {
        if ($data != "") {
            $receiverAddress = $data['email'];

            Mail::to($receiverAddress)->send(new PdfAttachment($data));

            return 1;
        } else {
            return 0;
        }
    }

    public function user_registerd($data)
    {
        if ($data != "") {
            //echo $data['email']; exit();

            $receiverAddress = $data['email']; //$data['email'];

            Mail::to($receiverAddress)->send(new EmailVerification($data));

            return 1;
        } else {
            return 0;
        }
    }

    public function user_forgot_password($data)
    {
        if ($data != "") {
            //echo $data['email']; exit();

            $receiverAddress = $data['email']; //$data['email'];

            Mail::to($receiverAddress)->send(new UserForgotPassword($data));

            return 1;
        } else {
            return 0;
        }
    }

    public function user_welcome($data)
    {
        if ($data != "") {
            //echo $data['email']; exit();

            $receiverAddress = $data['email']; //$data['email'];

            Mail::to($receiverAddress)->send(new UserWelcome($data));

            return 1;
        } else {
            return 0;
        }
    }

    public function user_password_changed($data)
    {
        if ($data != "") {

            $receiverAddress = $data['email']; //$data['email'];

            Mail::to($receiverAddress)->send(new UserPasswordChanged($data));

            return 1;
        } else {
            return 0;
        }
    }

    public function user_profile_updated($data)
    {
        if ($data != "") {

            $receiverAddress = $data['email']; //$data['email'];

            Mail::to($receiverAddress)->send(new UserProfileUpdated($data));

            return 1;
        } else {
            return 0;
        }
    }

    public function user_contactus($data)
    {
        if (count($data) > 0) {

            $receiverAddress = 'info@mobisecure.co.in'; //$data['email'];

            Mail::to($receiverAddress)->send(new UserContactUs($data));

            return 1;
        } else {
            return 0;
        }
    }
}
