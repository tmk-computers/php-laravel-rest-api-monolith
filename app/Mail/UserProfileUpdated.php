<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserProfileUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct($data)
    {
        $this->data=$data;
    }

    public function build()
    {
        //return $this->subject('Shop Registered')markdown('')->with('data',$this->data);

        return $this->subject('Your profile is updated successfully')
                     ->markdown('emails.user.profileupdated')
                     ->with('data',$this->data);

    }
}
