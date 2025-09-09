<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class LeaveStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $leave;

    public function __construct(LeaveRequest $leave)
    {
        $this->leave = $leave;
    }

    // Which channels to notify
    public function via($notifiable)
    {
        return ['mail', 'database']; // Email + in-app
    }

    // Email content
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject("Your Leave Request has been {$this->leave->status}")
                    ->greeting("Hello {$this->leave->employee->user->name},")
                    ->line("Your leave request from {$this->leave->start_date->format('Y-m-d')} to {$this->leave->end_date->format('Y-m-d')} has been **{$this->leave->status}**.")
                    ->line("Reason: {$this->leave->reason}")
                    ->line('Thank you for using our HR system.');
    }

    // In-app / database notification
    public function toDatabase($notifiable)
    {
        return [
            'leave_id'   => $this->leave->id,
            'status'     => $this->leave->status,
            'start_date' => $this->leave->start_date->format('Y-m-d'),
            'end_date'   => $this->leave->end_date->format('Y-m-d'),
            'reason'     => $this->leave->reason,
            'message'    => "Your leave request from {$this->leave->start_date->format('Y-m-d')} to {$this->leave->end_date->format('Y-m-d')} has been {$this->leave->status}."
        ];
    }
}
