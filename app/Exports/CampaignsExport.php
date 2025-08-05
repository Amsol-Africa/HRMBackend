<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class CampaignsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $campaigns;

    public function __construct(Collection $campaigns)
    {
        $this->campaigns = $campaigns;
    }

    public function collection()
    {
        return $this->campaigns;
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'UTM Source',
            'UTM Medium',
            'UTM Campaign',
            'Target URL',
            'Start Date',
            'End Date',
            'Status',
            'Has Survey',
            'Leads Count',
        ];
    }

    public function map($campaign): array
    {
        return [
            $campaign->id,
            $campaign->name,
            $campaign->utm_source,
            $campaign->utm_medium,
            $campaign->utm_campaign,
            $campaign->target_url,
            $campaign->start_date ? \Carbon\Carbon::parse($campaign->start_date)->format('Y-m-d') : 'N/A',
            $campaign->end_date ? \Carbon\Carbon::parse($campaign->end_date)->format('Y-m-d') : 'N/A',
            ucfirst($campaign->status),
            $campaign->has_survey ? 'Yes' : 'No',
            $campaign->leads()->count(),
        ];
    }
}
