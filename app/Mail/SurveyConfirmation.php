<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Campaign;
use App\Models\Lead;

class SurveyConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $campaign;
    public $lead;

    public function __construct(Campaign $campaign, Lead $lead)
    {
        $this->campaign = $campaign;
        $this->lead = $lead;
    }

    public function build()
    {
        return $this->subject('Thank You for Your Feedback!')
            ->view('emails.survey_confirmation')
            ->with([
                'campaign_name' => $this->campaign->name,
                'name' => $this->lead->name,
                'responses' => $this->lead->survey_responses,
            ]);
    }
}