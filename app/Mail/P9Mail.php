<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class P9Mail extends Mailable
{
    use Queueable, SerializesModels;

    public $employeePayroll;
    public $pdfPath;

    public function __construct($employeePayroll, $pdfPath)
    {
        $this->employeePayroll = $employeePayroll;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        return $this->subject('Your P9 Form')
            ->view('emails.p9')
            ->attach($this->pdfPath);
    }
}
