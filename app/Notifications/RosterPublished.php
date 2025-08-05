<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class RosterPublished extends Notification
{
    use Queueable;

    protected $assignment;

    public function __construct($assignment)
    {
        $this->assignment = $assignment;
    }

    public function via($notifiable)
    {
        return [$this->assignment->notification_type === 'email' ? 'mail' : 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Roster Schedule')
            ->line('You have been assigned a new roster schedule.')
            ->line('Date: ' . $this->assignment->date->format('Y-m-d'))
            ->line('Shift: ' . ($this->assignment->shift ? $this->assignment->shift->name : 'N/A'))
            ->line('Leave: ' . ($this->assignment->leave ? $this->assignment->leave->name : 'N/A'))
            ->line('Location: ' . $this->assignment->location->name)
            ->action('View Roster', url('/business/' . $this->assignment->roster->business->slug . '/roster'));
    }

    public function toArray($notifiable)
    {
        return [
            'roster_id' => $this->assignment->roster_id,
            'date' => $this->assignment->date->format('Y-m-d'),
            'shift' => $this->assignment->shift ? $this->assignment->shift->name : 'N/A',
            'leave' => $this->assignment->leave ? $this->assignment->leave->name : 'N/A',
            'location' => $this->assignment->location->name,
        ];
    }
}