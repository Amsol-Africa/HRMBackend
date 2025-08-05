<?php

namespace App\Notifications;

use App\Models\Interview;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class InterviewCanceledNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected $interview;
    protected $reason;

    public function __construct(Interview $interview, $reason)
    {
        $this->interview = $interview;
        $this->reason = $reason;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => 'Your interview has been canceled.',
            'interview_id' => $this->interview->id,
            'reason' => $this->reason,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Interview Canceled')
            ->line('Unfortunately, your interview has been canceled.')
            ->line('Reason: ' . $this->reason)
            ->action('View Details', url('/interviews/' . $this->interview->id));
    }
}
