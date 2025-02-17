<?php

namespace App\Http\Controllers;

use App\Models\Task;
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
        $tasks = Task::with('employees')->get();
        $task_cards = view('tasks._cards', compact('tasks'))->render();
        return RequestResponse::ok('Tasks fetched successfully.', $task_cards);
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
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'required|in:pending,in_progress,completed',
            'due_date'    => 'required|date',
            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $task = Task::create([
                'title'       => $validatedData['title'],
                'description' => $validatedData['description'] ?? null,
                'status'      => $validatedData['status'],
                'due_date'    => $validatedData['due_date'] ?? null,
            ]);

            if (!empty($validatedData['employee_ids'])) {
                $task->employees()->attach($validatedData['employee_ids']);
            }

            return RequestResponse::created('Task created successfully.');
        });
    }

    // Edit a task
    public function edit(Request $request)
    {
        $validatedData = $request->validate([
            'task_id' => 'required|exists:tasks,id',
        ]);

        $task = Task::with('employees')->findOrFail($validatedData['task_id']);
        $employees = Employee::all();
        $task_form = view('tasks._form', compact('task', 'employees'))->render();

        return RequestResponse::ok('Task found.', $task_form);
    }

    // Update a task
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'task_id'     => 'required|exists:tasks,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'required|in:pending,in_progress,completed',
            'due_date'    => 'nullable|date',
            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $task = Task::findOrFail($validatedData['task_id']);

            $task->update([
                'title'       => $validatedData['title'],
                'description' => $validatedData['description'] ?? null,
                'status'      => $validatedData['status'],
                'due_date'    => $validatedData['due_date'] ?? null,
            ]);

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
            'task_id'     => 'required|exists:tasks,id',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        return $this->handleTransaction(function () use ($validatedData) {
            $task = Task::findOrFail($validatedData['task_id']);
            $task->employees()->sync($validatedData['employee_ids']);

            return RequestResponse::ok('Employees assigned successfully.');
        });
    }
}