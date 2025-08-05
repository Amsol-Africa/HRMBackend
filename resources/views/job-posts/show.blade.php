<x-app-layout title="Job Post Details">
    <div class="row g-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $jobPost->title }}</h5>
                    <span
                        class="badge bg-{{ $jobPost->status == 'open' ? 'success' : 'secondary' }}">{{ ucfirst($jobPost->status) }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Type:</strong> {{ ucfirst($jobPost->employment_type) }}</p>
                            <p><strong>Location:</strong> {{ $jobPost->place }}</p>
                            <p><strong>Salary Range:</strong> {{ $jobPost->salary_range ?? 'Not specified' }}</p>
                            <p><strong>Positions:</strong> {{ $jobPost->number_of_positions }}</p>
                            <p><strong>Closing Date:</strong>
                                {{ $jobPost->closing_date ? $jobPost->closing_date->format('d M Y') : 'N/A' }}
                            </p>
                            <p><strong>Public:</strong> <span
                                    class="badge bg-{{ $jobPost->is_public ? 'success' : 'warning' }}">{{ $jobPost->is_public ? 'Yes' : 'No' }}</span>
                            </p>
                            <p><strong>Created By:</strong> {{ $jobPost->creator->name ?? 'Unknown' }}</p>
                            <p><strong>Created:</strong> {{ $jobPost->created_at->format('d M Y H:i') }}</p>
                            <p><strong>Updated:</strong> {{ $jobPost->updated_at->format('d M Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Description</h6>
                            <div class="border p-3 rounded">{!! $jobPost->description !!}</div>
                            @if($jobPost->requirements)
                            <h6 class="mt-3">Requirements</h6>
                            <div class="border p-3 rounded">{!! $jobPost->requirements !!}</div>
                            @endif
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('business.recruitment.jobs.edit', ['business' => session('active_business_slug'), 'jobpost' => $jobPost->slug]) }}"
                            class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="{{ route('business.recruitment.jobs.index', session('active_business_slug')) }}"
                            class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>