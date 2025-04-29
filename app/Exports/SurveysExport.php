<?php

namespace App\Exports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SurveysExport implements FromCollection, WithHeadings, WithMapping
{
    protected $leads;

    public function __construct($leads)
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
            'ID',
            'Name',
            'Email',
            'Country',
            'Feedback',
            'Status',
            'Submitted At',
        ];
    }

    public function map($lead): array
    {
        return [
            $lead->id,
            $lead->name,
            $lead->email,
            $lead->country,
            $lead->message,
            $lead->status,
            $lead->created_at->toDateTimeString(),
        ];
    }
}
