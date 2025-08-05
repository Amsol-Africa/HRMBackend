<?php

namespace App\Exports;

use App\Models\Roster;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RosterExport implements FromCollection, WithHeadings, WithMapping
{
    protected $businessId;
    protected $filters;

    public function __construct($businessId, $filters)
    {
        $this->businessId = $businessId;
        $this->filters = $filters;
    }

    public function collection()
    {
        return Roster::where('business_id', $this->businessId)
            ->with([
                'assignments.employee.user',
                'assignments.department',
                'assignments.jobCategory',
                'assignments.location',
                'assignments.shift',
                'assignments.leave'
            ])
            ->when($this->filters['department_id'] ?? null, fn($q) => $q->whereHas('assignments', fn($q) => $q->where('department_id', $this->filters['department_id'])))
            ->when($this->filters['job_category_id'] ?? null, fn($q) => $q->whereHas('assignments', fn($q) => $q->where('job_category_id', $this->filters['job_category_id'])))
            ->when($this->filters['location_id'] ?? null, fn($q) => $q->whereHas('assignments', fn($q) => $q->where('location_id', $this->filters['location_id'])))
            ->when($this->filters['employee_id'] ?? null, fn($q) => $q->whereHas('assignments', fn($q) => $q->where('employee_id', $this->filters['employee_id'])))
            ->get()
            ->flatMap->assignments;
    }

    public function headings(): array
    {
        return [
            'Roster Name',
            'Employee',
            'Department',
            'Job Category',
            'Location',
            'Date',
            'Shift',
            'Leave',
            'Status',
            'Overtime Hours',
            'Notes',
        ];
    }

    public function map($assignment): array
    {
        return [
            $assignment->roster->name,
            $assignment->employee->user->first_name . ' ' . $assignment->employee->user->last_name,
            $assignment->department->name,
            $assignment->jobCategory->name,
            $assignment->location->name,
            $assignment->date->format('Y-m-d'),
            $assignment->shift ? $assignment->shift->name : 'N/A',
            $assignment->leave ? $assignment->leave->name : 'N/A',
            ucfirst($assignment->status),
            $assignment->overtime_hours,
            $assignment->notes ?? 'N/A',
        ];
    }
}