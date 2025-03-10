<?php

namespace App\Mail;

use App\Models\Payroll;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Spatie\LaravelPdf\Facades\Pdf;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Contracts\Queue\ShouldQueue;

class PayslipMail extends Mailable
{
    use Queueable, SerializesModels;
    public $employee;
    public $payslipData;
    public $pdfPath;

    public function __construct($employee, $payslipData, $pdfPath)
    {
        $this->employee = $employee;
        $this->payslipData = $payslipData;
        $this->pdfPath = $pdfPath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Payslip',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payslip-email',
            with: ['payslipData' => $this->payslipData]
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)->as("payslip_{$this->employee->employee_code}.pdf")->withMime('application/pdf'),
        ];
    }
}
