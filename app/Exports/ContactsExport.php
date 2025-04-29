<?php

namespace App\Exports;

use App\Models\ContactSubmission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ContactsExport implements FromCollection, WithHeadings
{
    protected $contacts;

    public function __construct($contacts)
    {
        $this->contacts = $contacts;
    }

    public function collection()
    {
        return $this->contacts->map(function ($contact) {
            return [
                'Name' => $contact->name,
                'Email' => $contact->email,
                'Phone' => $contact->phone ?? 'N/A',
                'Status' => ucfirst($contact->status),
                'Source' => $contact->source ?? 'Unknown',
                'UTM Source' => $contact->utm_source ?? 'N/A',
                'UTM Medium' => $contact->utm_medium ?? 'N/A',
                'UTM Campaign' => $contact->utm_campaign ?? 'N/A',
                'Created At' => $contact->created_at->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return ['Name', 'Email', 'Phone', 'Status', 'Source', 'UTM Source', 'UTM Medium', 'UTM Campaign', 'Created At'];
    }
}
