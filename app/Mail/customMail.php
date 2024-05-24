<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;

class customMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct()
    {
    }
     
    public function build(request $request)
    {
        return $this->subject('Hello World')->view('mail',['request'=>$request]); 
    }
}