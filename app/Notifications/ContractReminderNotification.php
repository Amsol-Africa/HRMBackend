<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ContractReminderNotification extends Notification
{
    use Queueable;

    protected $contractAction;

    public function __construct($contractAction)
    {
        $this->contractAction = $contractAction;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Contract Expiry Reminder')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('This is a reminder that your contract is nearing its expiration date.')
            ->line('**Details**:')
            ->line('Reason: ' . $this->contractAction->reason)
            ->line('Description: ' . ($this->contractAction->description ?? 'N/A'))
            ->line('Please contact HR for further details or to discuss renewal options.')
            ->action('View Profile', url('/myaccount/' . $this->contractAction->business->slug))
            ->salutation('Best regards, HR Team');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Contract Expiry Reminder',
            'message' => 'Your contract is nearing its expiration date.',
            'action_url' => url('/myaccount/' . $this->contractAction->business->slug),
        ];
    }
}
