<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Assigned To</th>
                <th>Model Type</th>
                <th>Target</th>
                <th>Progress</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kpis as $kpi)
            <tr>
                <td>{{ $kpi->name }}</td>
                <td>
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
                </td>
                <td>{{ class_basename($kpi->model_type) }}</td>
                <td>{{ $kpi->target_value ? $kpi->target_value . ' ' . $kpi->comparison_operator : 'N/A' }}</td>
                <td>
                    @if($kpi->model_type !== 'manual')
                    <div class="progress">
                        <div class="progress-bar" role="progressbar"
                            style="width: {{ $kpi->getProgressPercentage() }}%;"
                            aria-valuenow="{{ $kpi->getProgressPercentage() }}" aria-valuemin="0" aria-valuemax="100">
                            {{ $kpi->getProgressPercentage() }}%
                        </div>
                    </div>
                    @else
                    N/A
                    @endif
                </td>
                <td>
                    <button class="btn btn-sm btn-info view-btn" data-kpi="{{ $kpi->id }}"
                        onclick="viewResults(this)">View</button>
                    <button class="btn btn-sm btn-danger delete-btn" data-kpi="{{ $kpi->id }}"
                        onclick="deleteKPI(this)">Delete</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">No KPIs available.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>