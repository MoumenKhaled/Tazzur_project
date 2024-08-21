<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StatusCompanyMail extends Mailable
{
    use Queueable, SerializesModels;
    protected $company;
    protected $status;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($company,$status)
    {
        $this->company=$company;
        $this->status=$status;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $company=$this->company;
        $status=$this->status;
        return $this->from(address:'example@example.com')->view(view:'emails.statusCompany')->with([
         'company'=>$company,
         'status'=>$status
        ]);
    }
}
