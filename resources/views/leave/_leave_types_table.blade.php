<div class="card">
    <div class="card-header">
        <h5>Leave Types</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="leaveTypesTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Paid</th>
                        <th>Requires Approval</th>
                        <th>Max Continuous Days</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($leaveTypes as $leaveType)
                        <tr>
                            <td>{{ $leaveType->name }}</td>
                            <td>
                                <span class="badge {{ $leaveType->is_paid ? 'bg-success' : 'bg-warning' }}">
                                    {!! $leaveType->is_paid ? '<i class="bi bi-check-circle"></i> Paid' : '<i class="bi bi-x-circle"></i> Unpaid' !!}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $leaveType->requires_approval ? 'bg-info' : 'bg-secondary' }}">
                                    {!! $leaveType->requires_approval ? '<i class="bi bi-check-circle"></i> Yes' : '<i class="bi bi-x-circle"></i> No' !!}
                                </span>
                            </td>
                            <td>{{ $leaveType->max_continuous_days.' Days' ?? 'N/A' }}</td>
                            <td>
                                <button class="btn btn-sm btn-info" data-leave-type="{{ $leaveType->slug }}" onclick="viewLeaveType(this)">
                                    <i class="bi bi-view-list"></i> Details
                                </button>
                                <a href="" class="btn btn-sm btn-primary"><i class="bi bi-question-circle"></i> Requests</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
