<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Campaign;
use App\Models\Lead;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SurveyResponsesExport;

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
        // Generate XLSX file
        $fileName = "survey_responses_{$this->lead->id}.xlsx";
        $filePath = storage_path("app/public/{$fileName}");
        Excel::store(new SurveyResponsesExport($this->lead), "public/{$fileName}");

        return $this->subject('Thank You for Your Feedback!')
            ->view('emails.survey_confirmation')
            ->with([
                'campaign_name' => $this->campaign->name,
                'name' => $this->lead->name,
                'responses' => $this->lead->survey_responses,
            ])
            ->attach($filePath, [
                'as' => $fileName,
                'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
    }
}
