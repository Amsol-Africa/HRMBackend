<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class ContactsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $contacts;

    public function __construct(Collection $contacts)
    {
        $this->contacts = $contacts;
    }

    public function collection()
    {
        return $this->contacts;
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Email',
            'Phone',
            'Company',
            'Country',
            'Inquiry Type',
            'Status',
            'Source',
            'UTM Source',
            'UTM Medium',
            'UTM Campaign',
        ];
    }

    public function map($submission): array
    {
        $name = trim($submission->first_name . ' ' . $submission->last_name);
        return [
            $submission->id,
            $name ?: 'Unknown',
            $submission->email,
            $submission->phone ?? 'N/A',
            $submission->company_name ?? 'N/A',
            $submission->country ?? 'N/A',
            $submission->inquiry_type,
            ucfirst($submission->status),
            $submission->source ?? 'Unknown',
            $submission->utm_source ?? 'N/A',
            $submission->utm_medium ?? 'N/A',
            $submission->utm_campaign ?? 'N/A',
        ];
    }
}
