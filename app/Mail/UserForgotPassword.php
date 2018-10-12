<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct($data)
    {
        $this->data=$data;
    }

    public function build()
    {
        //return $this->subject('Shop Registered')markdown('')->with('data',$this->data);

        return $this->subject('New Password For Your Account')
                     ->markdown('emails.user.forgotpassword')
                     ->with('data',$this->data);

    }
}
