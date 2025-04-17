<?php

namespace App\Mail;

use App\Models\Application;
use App\Models\Interview;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InterviewScheduled extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $interview;

    public function __construct(Application $application, Interview $interview)
    {
        $this->application = $application;
        $this->interview = $interview;
    }

    public function build()
    {
        return $this->subject('Interview Scheduled')
            ->view('emails.interview_scheduled')
            ->with([
                'applicantName' => $this->application->applicant->user->name,
                'jobTitle' => $this->application->jobPost->title,
                'interviewDate' => $this->interview->interview_date,
                'interviewTime' => $this->interview->interview_time,
                'location' => $this->interview->location,
                'type' => ucfirst($this->interview->type),
                'meetingLink' => $this->interview->meeting_link,
            ]);
    }
}