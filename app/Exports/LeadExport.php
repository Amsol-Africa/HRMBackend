<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LeadExport implements FromCollection, WithHeadings, WithMapping
{
    protected $leads;

    public function __construct(Collection $leads)
    {
        $this->leads = $leads;
    }

    public function collection()
    {
        return $this->leads;
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Email',
            'Phone',
            'Country',
            'Status',
            'Label',
            'Campaign',
            'Source',
            'Contact Submission ID',
            'Survey Responses',
            'Created At',
        ];
    }

    public function map($lead): array
    {
        $surveyResponses = $lead->survey_responses ? json_encode($lead->survey_responses) : 'N/A';
        if (is_array($lead->survey_responses)) {
            $surveyResponses = collect($lead->survey_responses)->map(function ($response, $key) {
                return "{$response['label']}: {$response['value']}";
            })->implode('; ');
        }

        return [
            $lead->id,
            $lead->name ?? 'Unknown',
            $lead->email ?? 'N/A',
            $lead->phone ?? 'N/A',
            $lead->country ?? $lead->user?->country ?? 'N/A',
            ucfirst($lead->status ?? 'N/A'),
            $lead->label ?? 'N/A',
            $lead->campaign ? $lead->campaign->name : 'N/A',
            $lead->source ?? 'Unknown',
            $lead->contact_submission_id ?? 'N/A',
            $surveyResponses,
            $lead->created_at ? $lead->created_at->format('Y-m-d H:i:s') : 'N/A',
        ];
    }
}