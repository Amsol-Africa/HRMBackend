<?php

namespace App\Mail;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Payroll;

class PayslipMail extends Mailable
{
    use Queueable, SerializesModels;

    public $payslip;

    public function __construct(Payroll $payslip)
    {
        $this->payslip = $payslip;
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
            with: ['payslip' => $this->payslip]
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('payroll._payslip_pdf', ['payslip' => $this->payslip]);
        $pdfPath = storage_path("app/payslips/Payslip_{$this->payslip->employee->employee_code}.pdf");

        file_put_contents($pdfPath, $pdf->output());

        return [
            Attachment::fromPath($pdfPath)->as("Payslip_{$this->payslip->employee->employee_code}.pdf")->withMime('application/pdf'),
        ];
    }
}
