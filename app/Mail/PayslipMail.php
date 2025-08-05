<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\EmployeePayroll;

class PayslipMail extends Mailable
{
    use Queueable, SerializesModels;

    public $employeePayroll;
    public $pdfPath;
    public $recipientName;

    public function __construct(EmployeePayroll $employeePayroll, string $pdfPath, string $recipientName)
    {
        $this->employeePayroll = $employeePayroll;
        $this->pdfPath = $pdfPath;
        $this->recipientName = $recipientName;
    }

    public function build()
    {
        $payrollPeriod = \Carbon\Carbon::create(
            $this->employeePayroll->payroll->payrun_year,
            $this->employeePayroll->payroll->payrun_month
        )->format('F Y');

        return $this->subject('Your Payslip for ' . $payrollPeriod)
            ->view('emails.payslip')
            ->with([
                'employeeName' => $this->recipientName,
                'payrollPeriod' => $payrollPeriod,
            ])
            ->attach($this->pdfPath, [
                'as' => 'payslip.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}