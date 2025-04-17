<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationStageUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $interview;

    public function __construct(Application $application, $interview = null)
    {
        $this->application = $application;
        $this->interview = $interview;
    }

    public function build()
    {
        return $this->subject('Application Stage Updated')
            ->view('emails.application_stage_updated')
            ->with([
                'applicantName' => $this->application->applicant->user->name,
                'jobTitle' => $this->application->jobPost->title,
                'stage' => ucfirst($this->application->stage),
            ]);
    }
}