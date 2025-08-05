@props(['task'])

<div class="card border-0 shadow-sm rounded-3 h-100">
    <div class="card-body mb-0">
        <!-- Task Title -->
        <h5 class="card-title fw-bold text-dark">
            {{ $task->title }}
            <a href="{{ route('business.performance.tasks.progress', [ 'business' => $currentBusiness, 'task' => $task->slug]) }}" class="badge bg-primary">
                <i class="fa-solid fa-up-right-from-square me-1"></i> Open
            </a>
        </h5>

        <p class="text-muted" title="{{ $task->description ?? 'No description provided.' }}">
            {{ Str::limit($task->description ?? 'No description provided.', 100) }}
        </p>

        <!-- Divider -->
        <hr class="my-3">

        <!-- Task Details -->
        <div class="mb-3">
            <p class="mb-2"><strong>📅 Due Date:</strong> {{ $task->due_date ?? 'Not set' }}</p>
            <p class="mb-2"><strong>🚩 Status:</strong>
                <span
                    class="badge
                    @if ($task->status === 'completed') bg-success
                    @elseif($task->status === 'pending') bg-warning
                    @elseif($task->status === 'in_progress') bg-primary
                    @else bg-secondary @endif">
                    {{ formatStatus($task->status) }}
                </span>
            </p>

            <p class="mb-2">
                <strong>👥 Assigned Employees:</strong>
                @if ($task->employees->isNotEmpty())
                    {{ $task->employees->pluck('user.name')->implode(', ') }}
                @else
                    <span class="text-muted">None assigned</span>
                @endif
            </p>
        </div>

        <!-- Divider -->
        <hr class="my-3">
        title
        <!-- Footer: Created At & Buttons -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted small">🕒 Created {{ $task->created_at->diffForHumans() }}</span>
        </div>
        <div class="row g-1">
            <div class="col-md-6">
                <button type="button" class="btn btn-sm btn-warning w-100" data-task="{{ $task->slug }}"
                    onclick="editTask(this)">
                    <i class="bi bi-pencil-square me-2"></i> Edit
                </button>

            </div>
            <div class="col-md-6">
                <button type="button" class="btn btn-sm btn-danger w-100" data-task="{{ $task->slug }}"
                    onclick="deleteTask(this)">
                    <i class="bi bi-trash me-2"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>
