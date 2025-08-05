<?php

namespace App\Http\Controllers;

use App\Enum\Status;
use App\Models\Roster;
use App\Models\RosterAssignment;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Notification;
use App\Notifications\RosterPublished;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RosterExport;

class RosterController extends Controller
{
    use HandleTransactions;

    public function index(Request $request)
    {
        $page = 'Work Rosters';
        $description = 'Manage and schedule employee rosters across departments and locations.';
        $business = Business::findBySlug(session('active_business_slug'));

        $departments = $business->departments;
        $jobCategories = $business->job_categories;
        $locations = $business->locations;
        $employees = $business->employees()->with('user')->get();
        $shifts = $business->shifts;
        $leaveTypes = $business->leaveTypes;

        return view('roster.index', compact(
            'page',
            'description',
            'departments',
            'jobCategories',
            'locations',
            'employees',
            'shifts',
            'leaveTypes'
        ));
    }

    public function fetch(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $rosters = Roster::where('business_id', $business->id)
            ->with([
                'assignments.employee.user',
                'assignments.department',
                'assignments.jobCategory',
                'assignments.location',
                'assignments.shift',
                'assignments.leave'
            ])
            ->when($request->department_id, fn($q) => $q->whereHas('assignments', fn($q) => $q->where('department_id', $request->department_id)))
            ->when($request->job_category_id, fn($q) => $q->whereHas('assignments', fn($q) => $q->where('job_category_id', $request->job_category_id)))
            ->when($request->location_id, fn($q) => $q->whereHas('assignments', fn($q) => $q->where('location_id', $request->location_id)))
            ->when($request->employee_id, fn($q) => $q->whereHas('assignments', fn($q) => $q->where('employee_id', $request->employee_id)))
            ->get();

        $viewType = $request->view_type ?? 'table';
        $view = $viewType === 'cards' ? 'roster._cards' : 'roster._table';
        $renderedView = view($view, compact('rosters'))->render();

        return RequestResponse::ok('Ok', $renderedView);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:draft,published,closed',
            'assignments' => 'required|array|min:1',
            'assignments.*.employee_id' => 'required|exists:employees,id',
            'assignments.*.department_id' => 'required|exists:departments,id',
            'assignments.*.job_category_id' => 'required|exists:job_categories,id',
            'assignments.*.location_id' => 'required|exists:locations,id',
            'assignments.*.date' => 'required|date|between:' . $request->start_date . ',' . $request->end_date,
            'assignments.*.shift_id' => 'nullable|exists:shifts,id',
            'assignments.*.leave_id' => 'nullable|exists:leave_types,id',
            'assignments.*.overtime_hours' => 'nullable|numeric|min:0',
            'assignments.*.notes' => 'nullable|string|max:1000',
            'assignments.*.notification_type' => 'required|in:email,in_app,none',
        ]);

        return $this->handleTransaction(function () use ($request, $validatedData) {
            $business = Business::findBySlug(session('active_business_slug'));

            $roster = $business->rosters()->create([
                'name' => $validatedData['name'],
                'start_date' => $validatedData['start_date'],
                'end_date' => $validatedData['end_date'],
                'status' => $validatedData['status'],
                'created_by' => $request->user()->id,
            ]);

            foreach ($validatedData['assignments'] as $assignment) {
                $roster->assignments()->create([
                    'employee_id' => $assignment['employee_id'],
                    'department_id' => $assignment['department_id'],
                    'job_category_id' => $assignment['job_category_id'],
                    'location_id' => $assignment['location_id'],
                    'date' => $assignment['date'],
                    'shift_id' => $assignment['shift_id'] ?? null,
                    'leave_id' => $assignment['leave_id'] ?? null,
                    'overtime_hours' => $assignment['overtime_hours'] ?? 0,
                    'notes' => $assignment['notes'] ?? null,
                    'notification_type' => $assignment['notification_type'],
                    'notification_status' => 'pending',
                ]);
            }

            if ($roster->status === 'published') {
                $this->sendNotifications($roster);
            }

            return RequestResponse::created('Roster created successfully.');
        });
    }

    public function edit(Request $request)
    {
        $validatedData = $request->validate([
            'slug' => 'required|string|exists:rosters,slug',
        ]);

        $roster = Roster::findBySlug($validatedData['slug'])->load([
            'assignments.employee.user',
            'assignments.department',
            'assignments.jobCategory',
            'assignments.location',
            'assignments.shift',
            'assignments.leave'
        ]);

        $business = Business::findBySlug(session('active_business_slug'));
        $departments = $business->departments;
        $jobCategories = $business->job_categories;
        $locations = $business->locations;
        $employees = $business->employees()->with('user')->get();
        $shifts = $business->shifts;
        $leaveTypes = $business->leaveTypes;

        $rosterForm = view('roster._form', compact(
            'roster',
            'departments',
            'jobCategories',
            'locations',
            'employees',
            'shifts',
            'leaveTypes'
        ))->render();

        return RequestResponse::ok('Roster found', $rosterForm);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'roster_slug' => 'required|exists:rosters,slug',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:draft,published,closed',
            'assignments' => 'required|array|min:1',
            'assignments.*.employee_id' => 'required|exists:employees,id',
            'assignments.*.department_id' => 'required|exists:departments,id',
            'assignments.*.job_category_id' => 'required|exists:job_categories,id',
            'assignments.*.location_id' => 'required|exists:locations,id',
            'assignments.*.date' => 'required|date|between:' . $request->start_date . ',' . $request->end_date,
            'assignments.*.shift_id' => 'nullable|exists:shifts,id',
            'assignments.*.leave_id' => 'nullable|exists:leave_types,id',
            'assignments.*.overtime_hours' => 'nullable|numeric|min:0',
            'assignments.*.notes' => 'nullable|string|max:1000',
            'assignments.*.notification_type' => 'required|in:email,in_app,none',
        ]);

        return $this->handleTransaction(function () use ($request, $validatedData) {
            $roster = Roster::findBySlug($validatedData['roster_slug']);
            $roster->update([
                'name' => $validatedData['name'],
                'start_date' => $validatedData['start_date'],
                'end_date' => $validatedData['end_date'],
                'status' => $validatedData['status'],
                'updated_by' => $request->user()->id,
            ]);

            $roster->assignments()->delete();

            foreach ($validatedData['assignments'] as $assignment) {
                $roster->assignments()->create([
                    'employee_id' => $assignment['employee_id'],
                    'department_id' => $assignment['department_id'],
                    'job_category_id' => $assignment['job_category_id'],
                    'location_id' => $assignment['location_id'],
                    'date' => $assignment['date'],
                    'shift_id' => $assignment['shift_id'] ?? null,
                    'leave_id' => $assignment['leave_id'] ?? null,
                    'overtime_hours' => $assignment['overtime_hours'] ?? 0,
                    'notes' => $assignment['notes'] ?? null,
                    'notification_type' => $assignment['notification_type'],
                    'notification_status' => 'pending',
                ]);
            }

            if ($roster->status === 'published') {
                $this->sendNotifications($roster);
            }

            return RequestResponse::ok('Roster updated successfully.');
        });
    }

    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'slug' => 'required|exists:rosters,slug',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $roster = Roster::findBySlug($validatedData['slug']);
            $roster->assignments()->delete();
            $roster->delete();
            return RequestResponse::ok('Roster deleted successfully.');
        });
    }

    public function notify(Request $request)
    {
        $validatedData = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:roster_assignments,id',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $assignments = RosterAssignment::whereIn('id', $validatedData['ids'])->get();
            foreach ($assignments as $assignment) {
                if ($assignment->notification_type !== 'none') {
                    Notification::send($assignment->employee->user, new RosterPublished($assignment));
                    $assignment->update(['notification_status' => 'sent']);
                }
            }
            return RequestResponse::ok('Notifications sent successfully.');
        });
    }

    public function reports(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $filters = $request->only(['department_id', 'job_category_id', 'location_id', 'employee_id', 'type']);

        $rosters = Roster::where('business_id', $business->id)
            ->with([
                'assignments.employee.user',
                'assignments.department',
                'assignments.jobCategory',
                'assignments.location',
                'assignments.shift',
                'assignments.leave'
            ])
            ->when($filters['department_id'] ?? null, fn($q) => $q->whereHas('assignments', fn($q) => $q->where('department_id', $filters['department_id'])))
            ->when($filters['job_category_id'] ?? null, fn($q) => $q->whereHas('assignments', fn($q) => $q->where('job_category_id', $filters['job_category_id'])))
            ->when($filters['location_id'] ?? null, fn($q) => $q->whereHas('assignments', fn($q) => $q->where('location_id', $filters['location_id'])))
            ->when($filters['employee_id'] ?? null, fn($q) => $q->whereHas('assignments', fn($q) => $q->where('employee_id', $filters['employee_id'])))
            ->get();

        $reportView = view('roster._report', compact('rosters', 'filters'))->render();
        return RequestResponse::ok('Report generated', $reportView);
    }

    public function export(Request $request)
    {
        $validatedData = $request->validate([
            'format' => 'required|in:csv,excel,pdf',
            'department_id' => 'nullable|exists:departments,id',
            'job_category_id' => 'nullable|exists:job_categories,id',
            'location_id' => 'nullable|exists:locations,id',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $business = Business::findBySlug(session('active_business_slug'));
        $filters = $request->only(['department_id', 'job_category_id', 'location_id', 'employee_id']);

        if ($validatedData['format'] === 'pdf') {
            $rosters = Roster::where('business_id', $business->id)
                ->with([
                    'assignments.employee.user',
                    'assignments.department',
                    'assignments.jobCategory',
                    'assignments.location',
                    'assignments.shift',
                    'assignments.leave'
                ])
                ->when($filters['department_id'] ?? null, fn($q) => $q->whereHas('assignments', fn($q) => $q->where('department_id', $filters['department_id'])))
                ->when($filters['job_category_id'] ?? null, fn($q) => $q->whereHas('assignments', fn($q) => $q->where('job_category_id', $filters['job_category_id'])))
                ->when($filters['location_id'] ?? null, fn($q) => $q->whereHas('assignments', fn($q) => $q->where('location_id', $filters['location_id'])))
                ->when($filters['employee_id'] ?? null, fn($q) => $q->whereHas('assignments', fn($q) => $q->where('employee_id', $filters['employee_id'])))
                ->get();

            $pdf = Pdf::loadView('roster._pdf', compact('rosters', 'filters'));
            return $pdf->download('roster-report.pdf');
        }

        return Excel::download(new RosterExport($business->id, $filters), 'roster-report.' . $validatedData['format']);
    }

    public function calendar(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $filters = $request->only(['department_id', 'job_category_id', 'location_id', 'employee_id', 'start', 'end']);

        $assignments = RosterAssignment::whereHas('roster', fn($q) => $q->where('business_id', $business->id))
            ->with(['employee.user', 'department', 'jobCategory', 'location', 'shift', 'leave'])
            ->when($filters['department_id'] ?? null, fn($q) => $q->where('department_id', $filters['department_id']))
            ->when($filters['job_category_id'] ?? null, fn($q) => $q->where('job_category_id', $filters['job_category_id']))
            ->when($filters['location_id'] ?? null, fn($q) => $q->where('location_id', $filters['location_id']))
            ->when($filters['employee_id'] ?? null, fn($q) => $q->where('employee_id', $filters['employee_id']))
            ->when($filters['start'] ?? null, fn($q) => $q->whereDate('date', '>=', $filters['start']))
            ->when($filters['end'] ?? null, fn($q) => $q->whereDate('date', '<=', $filters['end']))
            ->get();

        $events = $assignments->map(function ($assignment) {
            return [
                'id' => $assignment->id,
                'title' => $assignment->employee->user->first_name . ' ' . $assignment->employee->user->last_name,
                'start' => $assignment->date->format('Y-m-d'),
                'extendedProps' => [
                    'employeeName' => $assignment->employee->user->first_name . ' ' . $assignment->employee->user->last_name,
                    'departmentName' => $assignment->department->name,
                    'shiftName' => $assignment->shift ? $assignment->shift->name : null,
                    'leaveName' => $assignment->leave ? $assignment->leave->name : null,
                    'overtimeHours' => $assignment->overtime_hours,
                ],
            ];
        });

        return RequestResponse::ok('Calendar events fetched', ['events' => $events]);
    }

    protected function sendNotifications(Roster $roster)
    {
        foreach ($roster->assignments as $assignment) {
            if ($assignment->notification_type !== 'none' && $assignment->notification_status !== 'sent') {
                Notification::send($assignment->employee->user, new RosterPublished($assignment));
                $assignment->update(['notification_status' => 'sent']);
            }
        }
    }
}