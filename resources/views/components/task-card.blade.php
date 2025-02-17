@props(['task'])

<div class="card border-0 shadow-sm rounded-3 h-100">
    <div class="card-body">
        <!-- Task Title -->
        <h5 class="card-title fw-bold text-dark">{{ $task->title }}</h5>
        <p class="text-muted">{{ $task->description ?? 'No description provided.' }}</p>

        <!-- Divider -->
        <hr class="my-3">

        <!-- Task Details -->
        <div class="mb-3">
            <p class="mb-2"><strong>📅 Due Date:</strong> {{ $task->due_date ?? 'Not set' }}</p>
            <p class="mb-2"><strong>🚩 Status:</strong>
                <span class="badge 
                    @if($task->status === 'completed') bg-success 
                    @elseif($task->status === 'pending') bg-warning 
                    @elseif($task->status === 'in_progress') bg-primary 
                    @else bg-secondary @endif">
                    {{ $task->status === 'in_progress' ? 'In Progress' : ucfirst($task->status ?? 'Not set') }}
                </span>
            </p>

            <p class="mb-2"><strong>👥 Assigned Employees:</strong>
                @if ($task->employees && count($task->employees) > 0)
                {{ implode(', ', $task->employees->pluck('name')->toArray()) }}
                @else
                <span class="text-muted">None assigned</span>
                @endif
            </p>
        </div>

        <!-- Divider -->
        <hr class="my-3">

        <!-- Footer: Created At & Buttons -->
        <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted small">🕒 Created {{ $task->created_at->diffForHumans() }}</span>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-warning" data-task="{{ $task->id }}"
                    onclick="editTask(this)">
                    <i class="bi bi-pencil-square"></i> Edit
                </button>
                <button type="button" class="btn btn-sm btn-danger" data-task="{{ $task->id }}"
                    onclick="deleteTask(this)">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>