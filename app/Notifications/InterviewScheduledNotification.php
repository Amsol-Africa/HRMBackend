<?php

namespace App\Notifications;

use App\Models\Interview;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class InterviewScheduledNotification extends Notification implements ShouldQueue
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
            'message' => 'Your interview has been scheduled.',
            'interview_id' => $this->interview->id,
            'scheduled_at' => $this->interview->scheduled_at->toDateTimeString(),
            'type' => $this->interview->type,
            'location' => $this->interview->location,
            'meeting_link' => $this->interview->meeting_link,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Interview Scheduled')
            ->line('Your interview has been scheduled on ' . $this->interview->scheduled_at->format('M d, Y H:i'))
            ->action('View Interview', url('/interviews/' . $this->interview->id));
    }
}
