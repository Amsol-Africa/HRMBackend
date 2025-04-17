<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class LoginNotification extends Notification
{
    use Queueable;

    protected $loginLog;

    public function __construct($loginLog)
    {
        $this->loginLog = $loginLog;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $actionUrl = URL::temporarySignedRoute(
            'auth.suspicious-login',
            now()->addHours(24),
            ['user' => $notifiable->id, 'login' => $this->loginLog->id]
        );

        return (new MailMessage)
            ->subject('New Login to Your Account')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We detected a new login to your business account.')
            ->line('**Details**:')
            ->line('- **Time**: ' . $this->loginLog->login_at->setTimezone('Africa/Nairobi')->format('d M Y, h:i A'))
            ->line('- **Location**: ' . ($this->loginLog->location ?: 'Unknown'))
            ->line('- **Browser**: ' . ($this->loginLog->browser ?: 'Unknown'))
            ->line('- **Device**: ' . ($this->loginLog->device ?: 'Unknown'))
            ->line('- **IP Address**: ' . $this->loginLog->ip_address)
            ->line('- **Network**: ' . ($this->loginLog->network ?: 'Unknown'))
            ->action('That Was Not Me', $actionUrl)
            ->line('If this was you, you can ignore this email.')
            ->salutation('Regards, ' . config('app.name'));
    }
}
