<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegisterUserMail extends Mailable
{
    use Queueable, SerializesModels;
    protected $user,$token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$token)
    {
        $this->user=$user;
        $this->token=$token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $code=$this->token;
        $user=$this->user;
        return $this->from(address:'example@example.com')->view(view:'emails.registerUser')->with([
         'code'=>$code,
         'email'=>$user->email
        ]);
    }
}
