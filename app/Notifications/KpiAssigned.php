<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class KpiAssigned extends Notification
{
    use Queueable;

    protected $kpi;
    protected $assignmentLabel;

    public function __construct($kpi, $assignmentLabel)
    {
        $this->kpi = $kpi;
        $this->assignmentLabel = $assignmentLabel;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New KPI Assigned: ' . $this->kpi->name)
            ->line('A new KPI has been assigned to you.')
            ->line('**KPI Name:** ' . $this->kpi->name)
            ->line('**Assigned To:** ' . $this->assignmentLabel)
            ->line('**Description:** ' . ($this->kpi->description ?? 'No description'))
            ->line('**Target:** ' . ($this->kpi->target_value ? $this->kpi->target_value . ' ' . $this->kpi->comparison_operator : 'N/A'))
            ->action('View KPIs', url('/business/' . $this->kpi->business->slug . '/performance/kpis'));
    }
}
