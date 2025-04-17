<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function build()
    {
        return $this->subject('Application Received')
            ->view('emails.application_received')
            ->with([
                'applicantName' => $this->application->applicant->user->name,
                'jobTitle' => $this->application->jobPost->title,
                'applicationDate' => $this->application->created_at->format('d M, Y'),
            ]);
    }
}