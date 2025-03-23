<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;

class PayslipMail extends Mailable
{
    use Queueable, SerializesModels;

    public $employeePayroll;
    public $filePath; // Path to the temporary PDF file
    public $employeeName;

    public function __construct($employeePayroll, $filePath, $employeeName)
    {
        $this->employeePayroll = $employeePayroll;
        $this->filePath = $filePath;
        $this->employeeName = $employeeName;
    }

    public function build()
    {
        $email = $this->subject('Your Payslip for ' . now()->format('F Y'))
            ->view('emails.payslip')
            ->with([
                'employeeName' => $this->employeeName,
            ])
            ->attach($this->filePath, [
                'as' => 'payslip.pdf',
                'mime' => 'application/pdf',
            ]);

        // Delete the temporary file after attaching
        $this->afterCommit(function () {
            if (File::exists($this->filePath)) {
                File::delete($this->filePath);
            }
        });

        return $email;
    }
}
