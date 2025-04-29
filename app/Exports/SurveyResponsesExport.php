<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use App\Models\Lead;

class SurveyResponsesExport implements FromCollection, WithHeadings
{
    protected $lead;

    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    public function collection()
    {
        $data = [
            'name' => $this->lead->name,
            'email' => $this->lead->email,
            'country' => $this->lead->country,
        ];

        // Add survey responses dynamically
        if ($this->lead->survey_responses) {
            foreach ($this->lead->survey_responses as $field) {
                $data[$field['label']] = $field['value'] ?? 'N/A';
            }
        }

        return new Collection([$data]);
    }

    public function headings(): array
    {
        $headings = ['Name', 'Email', 'Country'];

        // Add survey response field labels
        if ($this->lead->survey_responses) {
            foreach ($this->lead->survey_responses as $field) {
                $headings[] = $field['label'];
            }
        }

        return $headings;
    }
}
