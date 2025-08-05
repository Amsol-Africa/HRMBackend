<?php

namespace App\Exports;

use App\Models\Applicant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ApplicantsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Applicant::with('user')->get()->map(function ($applicant) {
            return [
                'Name' => $applicant->user->name,
                'Email' => $applicant->user->email,
                'Phone' => $applicant->user->phone,
                'Location' => "{$applicant->city}, {$applicant->country}",
                'Experience Level' => $applicant->experience_level,
                'Current Job Title' => $applicant->current_job_title,
                'Applications' => $applicant->applications->count(),
            ];
        });
    }

    public function headings(): array
    {
        return ['Name', 'Email', 'Phone', 'Location', 'Experience Level', 'Current Job Title', 'Applications'];
    }
}