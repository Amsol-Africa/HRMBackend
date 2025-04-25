<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class WelcomeEmployeeNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $token;

    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $this->user->email,
        ], false));

        return (new MailMessage)
            ->subject('Welcome to ' . config('app.name') . ' - Set Up Your Account')
            ->greeting('Hello ' . $this->user->name . ',')
            ->line('Welcome to ' . config('app.name') . '! Your account has been created.')
            ->line('To get started, please set your password by clicking the button below:')
            ->action('Set Password', $resetUrl)
            ->line('This link will expire in ' . config('auth.passwords.users.expire') . ' minutes.')
            ->line('If you did not expect this email, please contact our support team.')
            ->salutation('Best regards, ' . config('app.name'));
    }
}