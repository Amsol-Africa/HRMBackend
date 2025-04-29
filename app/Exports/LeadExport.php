<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

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
        ];
    }

    public function map($lead): array
    {
        return [
            $lead->id,
            $lead->name ?: 'Unknown',
            $lead->email,
            $lead->phone ?? 'N/A',
            $lead->country ?? 'N/A',
            ucfirst($lead->status),
            $lead->label ?? 'N/A',
            $lead->campaign ? $lead->campaign->name : 'N/A',
            $lead->source ?? 'Unknown',
        ];
    }
}
