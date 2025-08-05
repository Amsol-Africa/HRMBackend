<?php

namespace App\Notifications;

use App\Models\Interview;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class InterviewRescheduledNotification extends Notification implements ShouldQueue
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
            'message' => 'Your interview has been rescheduled.',
            'interview_id' => $this->interview->id,
            'new_date' => $this->interview->scheduled_at->toDateTimeString(),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Interview Rescheduled')
            ->line('Your interview has been rescheduled to ' . $this->interview->scheduled_at->format('M d, Y H:i'))
            ->action('View Interview', url('/interviews/' . $this->interview->id));
    }
}
