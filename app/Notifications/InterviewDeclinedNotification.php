<?php

namespace App\Notifications;

use App\Models\Interview;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class InterviewDeclinedNotification extends Notification
{
    use Queueable;
    protected $interview;

    public function __construct(Interview $interview)
    {
        $this->interview = $interview;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => 'An applicant declined an interview.',
            'interview_id' => $this->interview->id,
            'decline_reason' => $this->interview->decline_reason,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Interview Declined')
            ->line('An applicant has declined the interview scheduled on ' . $this->interview->scheduled_at->format('M d, Y H:i'))
            ->line('Reason: ' . $this->interview->decline_reason)
            ->action('View Interview', url('/interviews/' . $this->interview->id));
    }
}
