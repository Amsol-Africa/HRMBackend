<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BusinessChangedNotification extends Notification
{
    use Queueable;

    protected $business;
    protected $user;
    protected $action;

    public function __construct($business, $user, $action = 'updated')
    {
        $this->business = $business;
        $this->user = $user;
        $this->action = $action;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $actionText = $this->action === 'created' ? 'created' : 'updated';

        return (new MailMessage)
            ->subject("Your Business Has Been {$actionText}")
            ->line("Your business, {$this->business->company_name}, has been {$actionText}.")
            ->line("Action by: {$this->user->name}")
            ->action('View Business', route('business.index', $this->business->slug))
            ->line('If this was not you, please contact support.');
    }

    public function toArray($notifiable)
    {
        $actionText = $this->action === 'created' ? 'created' : 'updated';

        return [
            'message' => "Business {$this->business->company_name} {$actionText} by {$this->user->name}",
            'business_id' => $this->business->id,
            'user_id' => $this->user->id,
        ];
    }
}
