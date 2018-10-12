<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PdfAttachment extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct($data)
    {
        $this->data=$data;
    }

    public function build()
    {
        //return $this->subject('Shop Registered')markdown('')->with('data',$this->data);

        return $this->subject('Basepaper is sent as attachment')
                     ->markdown('emails.user.sendpdf')
                     ->with('data',$this->data)
                     ->attach($this->data['path'], [
                        'as' => 'name.pdf',
                        'mime' => 'application/pdf',
                    ]);

    }
}
