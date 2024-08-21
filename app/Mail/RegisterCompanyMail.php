<?php

namespace App\Mail;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegisterCompanyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $code;

    public function __construct(Company $company, $code)
    {
        $this->company = $company;
        $this->code = $code;
    }

    public function build()
    {
        return $this->from('example@example.com')
                    ->view('emails.registerCompany')
                    ->with([
                        'code' => $this->code,
                    ]);
    }
}
