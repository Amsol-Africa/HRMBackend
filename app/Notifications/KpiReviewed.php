<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class KpiReviewed extends Notification
{
    use Queueable;

    protected $kpi;
    protected $ratingValue;
    protected $status;
    protected $comment;

    public function __construct($kpi, $ratingValue, $status, $comment)
    {
        $this->kpi = $kpi;
        $this->ratingValue = $ratingValue;
        $this->status = $status;
        $this->comment = $comment;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('KPI Review: ' . $this->kpi->name)
            ->line('Your KPI has been reviewed.')
            ->line('**KPI Name:** ' . $this->kpi->name)
            ->line('**Rating Value:** ' . $this->ratingValue)
            ->line('**Status:** ' . $this->status)
            ->line('**Comment:** ' . ($this->comment ?? 'No comment'))
            ->action('View KPIs', url('/business/' . $this->kpi->business->slug . '/performance/kpis'));
    }
}
