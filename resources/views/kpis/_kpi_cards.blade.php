<div class="row g-4">
    @forelse($kpis as $kpi)
    <div class="col-md-4">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">{{ $kpi->name }}</h5>
                <p class="card-text mb-2"><strong>Type:</strong> {{ class_basename($kpi->model_type) }}</p>
                <p class="card-text mb-2"><strong>Assigned:</strong>
                    @if($kpi->employee_id)
                    Employee: {{ $kpi->employee->user->name ?? 'N/A' }}
                    @elseif($kpi->department_id)
                    Department: {{ $kpi->department->name ?? 'N/A' }}
                    @elseif($kpi->job_category_id)
                    Job Category: {{ $kpi->jobCategory->name ?? 'N/A' }}
                    @elseif($kpi->location_id)
                    Location: {{ $kpi->location->name ?? 'N/A' }}
                    @else
                    Business: {{ $kpi->business->company_name ?? 'N/A' }}
                    @endif
                </p>
                <p class="card-text mb-2"><strong>Target:</strong>
                    {{ $kpi->target_value ? $kpi->target_value . ' ' . $kpi->comparison_operator : 'N/A' }}
                </p>
                <p class="card-text mb-2"><strong>Method:</strong>
                    {{ $kpi->calculation_method ? ucfirst($kpi->calculation_method) : 'N/A' }}
                </p>
                <p class="card-text mb-2"><strong>Description:</strong> {{ $kpi->description ?? 'No description' }}</p>
            </div>
            <div class="card-footer bg-transparent border-0 d-flex gap-2">
                <button class="btn btn-sm btn-info view-btn" data-kpi="{{ $kpi->id }}"
                    onclick="viewResults(this)">View</button>
                <button class="btn btn-sm btn-primary edit-btn" data-kpi="{{ $kpi->id }}"
                    onclick="editKPI(this)">Edit</button>
                <button class="btn btn-sm btn-danger delete-btn" data-kpi="{{ $kpi->id }}"
                    onclick="deleteKPI(this)">Delete</button>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <p class="text-center text-muted">No KPIs available.</p>
    </div>
    @endforelse
</div>