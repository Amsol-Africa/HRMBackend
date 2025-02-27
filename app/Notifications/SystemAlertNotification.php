<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage; // If you want to send it via email as well
use Illuminate\Notifications\Notification;

class SystemAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;
    protected $data;

    public function __construct($message, $data)
    {
        $this->message = $message;
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database', 'email'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->message,
            'data' => $this->data,
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)->line($this->message)->line('Thank you for using our application!');
    }
}
