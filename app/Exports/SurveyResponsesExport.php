<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use App\Models\Lead;
use Illuminate\Support\Facades\Log;

class SurveyResponsesExport implements FromCollection, WithHeadings
{
    protected $lead;

    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    public function collection()
    {
        if (!$this->lead) {
            Log::error("Lead is null in SurveyResponsesExport");
            return new Collection([]);
        }

        $data = [
            'name' => $this->lead->name ?? 'N/A',
            'email' => $this->lead->email ?? 'N/A',
            'country' => $this->lead->country ?? 'N/A',
        ];

        if (is_array($this->lead->survey_responses)) {
            foreach ($this->lead->survey_responses as $field) {
                $data[$field['label'] ?? 'Unknown'] = $field['value'] ?? 'N/A';
            }
        } else {
            Log::warning("survey_responses is not an array for lead ID: {$this->lead->id}");
        }

        Log::info("SurveyResponsesExport data for lead ID: {$this->lead->id}: " . json_encode($data));
        return new Collection([$data]);
    }

    public function headings(): array
    {
        $headings = ['Name', 'Email', 'Country'];

        if (is_array($this->lead->survey_responses)) {
            foreach ($this->lead->survey_responses as $field) {
                $headings[] = $field['label'] ?? 'Unknown';
            }
        }

        return $headings;
    }
}