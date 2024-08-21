<?php

namespace App\Mail;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CompanyStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $status;

    public function __construct(Company $company, $status)
    {
        $this->company = $company;
        $this->status = $status;
    }

    public function build()
    {
        return $this->view('emails.company_status')
                    ->subject("Status Update: " . $this->status)
                    ->with(['company' => $this->company, 'status' => $this->status]);
    }
}
