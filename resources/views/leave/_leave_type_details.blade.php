<div class="row">
    <div class="col-md-6">
        <h6 class="mb-3">Basic Information</h6>
        <table class="table table-bordered">
            <tr>
                <th>Name</th>
                <td>{{ $leaveType->name }}</td>
            </tr>
            <tr>
                <th>Description</th>
                <td>{{ $leaveType->description ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Paid Leave</th>
                <td>
                    <span class="badge {{ $leaveType->is_paid ? 'bg-success' : 'bg-warning' }}">
                        {!! $leaveType->is_paid ? '<i class="bi bi-check-circle"></i> Yes' : '<i class="bi bi-check-circle"></i> No' !!}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Requires Approval</th>
                <td>
                    <span class="badge {{ $leaveType->requires_approval ? 'bg-info' : 'bg-secondary' }}">
                        {!! $leaveType->requires_approval ? '<i class="bi bi-check-circle"></i> Yes' : '<i class="bi bi-check-circle"></i> No' !!}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Allows Half Day</th>
                <td>
                    <span class="badge {{ $leaveType->allows_half_day ? 'bg-success' : 'bg-secondary' }}">
                        {!! $leaveType->allows_half_day ? '<i class="bi bi-check-circle"></i> Yes' : '<i class="bi bi-check-circle"></i> No' !!}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Requires Attachment</th>
                <td>
                    <span class="badge {{ $leaveType->requires_attachment ? 'bg-info' : 'bg-secondary' }}">
                        {!! $leaveType->requires_attachment ? '<i class="bi bi-check-circle"></i> Yes' : '<i class="bi bi-check-circle"></i> No' !!}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Max Continuous Days</th>
                <td>{{ $leaveType->max_continuous_days.' Days' ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Min Notice Days</th>
                <td>{{ $leaveType->min_notice_days. ' Days' }}</td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <h6 class="mb-3">Policy Details</h6>
        @if ($leaveType->leavePolicies->first())
            @php
                $policy = $leaveType->leavePolicies->first();
            @endphp
            <table class="table table-bordered">
                <tr>
                    <th>Department</th>
                    <td>{{ $policy->department->name }}</td>
                </tr>
                <tr>
                    <th>Job Category</th>
                    <td>{{ $policy->jobCategory->name }}</td>
                </tr>
                <tr>
                    <th>Gender Applicable</th>
                    <td>{{ ucfirst($policy->gender_applicable) }}</td>
                </tr>
                <tr>
                    <th>Default Days</th>
                    <td>{{ $policy->default_days.' Days' }}</td>
                </tr>
                <tr>
                    <th>Accrual Frequency</th>
                    <td>{{ ucfirst($policy->accrual_frequency) }}</td>
                </tr>
                <tr>
                    <th>Accrual Amount</th>
                    <td>{{ $policy->accrual_amount }}</td>
                </tr>
                <tr>
                    <th>Max Carryover Days</th>
                    <td>{{ $policy->max_carryover_days.' Days' }}</td>
                </tr>
                <tr>
                    <th>Min Service Days</th>
                    <td>{{ $policy->minimum_service_days_required.' Days' }}</td>
                </tr>
                <tr>
                    <th>Effective Date</th>
                    <td>{{ $policy->effective_date->format('Y-m-d') }}</td>
                </tr>
                <tr>
                    <th>End Date</th>
                    <td>{{ $policy->end_date ? $policy->end_date->format('Y-m-d') : 'N/A' }}</td>
                </tr>
            </table>
        @else
            <p class="text-muted">No policy details available</p>
        @endif
    </div>
</div>
