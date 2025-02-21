<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Business;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\RequestResponse;
use App\Traits\HandleTransactions;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    use HandleTransactions;

    // Fetch all tasks
    public function fetch(Request $request)
    {
        $business = Business::findBySlug(session('active_business_slug'));
        $tasks = $business->tasks()->with('employees')->get();
        $task_cards = view('tasks._cards', compact('tasks'))->render();
        return RequestResponse::ok('Tasks fetched successfully.', $task_cards);
    }

    public function timelines(Request $request)
    {
        $taskprogresses = Task::where('slug', $request->task_slug)->with('employees')->get();
        $timelines = view('tasks._timelines', compact('taskprogresses'))->render();
        return RequestResponse::ok('Ok.', $timelines);
    }

    public function create()
    {
        $employees = Employee::all();
        $task_form = view('tasks._form', compact('employees'))->render();
        return RequestResponse::ok('Task form loaded.', $task_form);
    }

    // Store a new task
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'required|date',
            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $business = Business::findBySlug(session('active_business_slug'));
            $task = Task::create([
                'business_id' => $business->id,
                'title' => $validatedData['title'],
                'description' => $validatedData['description'] ?? null,
                'due_date' => $validatedData['due_date'] ?? null,
            ])->setStatus($validatedData['status'] ?? 'pending');

            if (!empty($validatedData['employee_ids'])) {
                $task->employees()->sync($validatedData['employee_ids']);
            }

            return RequestResponse::created('Task created successfully.');
        });
    }
    public function progress(Request $request)
    {
        $validatedData = $request->validate([
            'task_slug' => 'required|exists:tasks,slug',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $task = Task::findBySlug($validatedData['task_slug']);
            $task->setStatus($validatedData['status'], $validatedData['description']);

            if (!empty($validatedData['employee_ids'])) {
                $task->employees()->sync($validatedData['employee_ids']);
            }

            return RequestResponse::created('Task created successfully.');
        });
    }

    // Edit a task
    public function edit(Request $request)
    {
        $validatedData = $request->validate([
            'task_slug' => 'required|exists:tasks,slug',
        ]);

        $task = Task::where('slug', $validatedData['task_slug'])->with('employees')->firstOrFail();
        $employees = Employee::all();
        $task_form = view('tasks._form', compact('task', 'employees'))->render();

        return RequestResponse::ok('Task found.', $task_form);
    }

    // Update a task
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'task_slug' => 'required|exists:tasks,slug',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $task = Task::findBySlug($validatedData['task_slug']);

            $task->update([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'] ?? null,
                'status' => $validatedData['status'],
                'due_date' => $validatedData['due_date'] ?? null,
            ])->setStatus($validatedData['status']);

            if (isset($validatedData['employee_ids'])) {
                $task->employees()->sync($validatedData['employee_ids']);
            }

            return RequestResponse::ok('Task updated successfully.');
        });
    }

    // Assign employees to a task
    public function assignEmployees(Request $request)
    {
        $validatedData = $request->validate([
            'task_slug' => 'required|exists:tasks,slug',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $task = Task::findBySlug($validatedData['task_slug']);
            $task->employees()->sync($validatedData['employee_ids']);

            return RequestResponse::ok('Employees assigned successfully.');
        });
    }

    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'task_slug' => 'required|exists:tasks,slug',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $task = Task::findBySlug($validatedData['task_slug']);
            $task->delete();
            return RequestResponse::ok('Task deleted & de-assigned.');
        });
    }
}
