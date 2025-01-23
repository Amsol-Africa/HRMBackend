@props(['job_category'])

<div class="card shadow-sm border-0 h-100">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div class="icon me-3">
                <i class="bi bi-briefcase text-primary" style="font-size: 24px;"></i>
            </div>
            <div>
                <h5 class="card-title mb-0">{{ $job_category->name }}</h5>
                <small class="text-muted">{{ $job_category->slug }}</small>
            </div>
        </div>
        <p class="mt-3">{{ $job_category->description ?? 'No description provided.' }}</p>
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-sm btn-warning me-2" data-job-category="{{ $job_category->slug }}" onclick="editJobCategory(this)">
                <i class="bi bi-pencil-square"></i> Edit
            </button>
            <button type="button" class="btn btn-sm btn-danger" data-job-category="{{ $job_category->slug }}" onclick="deleteJobCategory(this)">
                <i class="bi bi-trash"></i> Delete
            </button>
        </div>
    </div>
</div>
