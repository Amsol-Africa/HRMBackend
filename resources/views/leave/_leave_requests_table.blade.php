<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Leave Requests</h5>
        <a href="" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i> Create Leave Request
        </a>
    </div>

    <div class="card-body">
        <table class="table table-bordered" style="width: 100%" id="{{ $status }}LeaveRequestsTable">
            <thead>
                <tr>
                    <th>Ref. No.</th>
                    <th>Employee</th>
                    <th>Leave Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Days</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($leaveRequests as $request)
                    <tr>
                        <td>{{ $request->reference_number }}</td>
                        <td>{{ $request->employee->user->name }}</td>
                        <td>{{ $request->leaveType->name }}</td>
                        <td>{{ $request->start_date->format('Y-m-d') }}</td>
                        <td>{{ $request->end_date->format('Y-m-d') }}</td>
                        <td>{{ $request->total_days }}</td>
                        <td>
                            @if (is_null($request->approved_by))
                                <span class="badge badge-warning">Pending</span>
                            @elseif (!is_null($request->approved_by))
                                <span class="badge badge-success">Approved</span>
                            @else
                                <span class="badge badge-danger">Rejected</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('leave_requests.show', $request->reference_number) }}"
                                class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                            @if (is_null($request->approved_by))
                                <a href="{{ route('leave_requests.approve', $request->reference_number) }}"
                                    class="btn btn-sm btn-success">
                                    <i class="fas fa-check"></i> Approve
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal"
                                    data-target="#rejectModal{{ $request->id }}">
                                    <i class="fas fa-times"></i> Reject
                                </button>

                                <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1"
                                    role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="rejectModalLabel">Reject Leave Request</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form
                                                action="{{ route('leave_requests.reject', $request->reference_number) }}"
                                                method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="rejection_reason">Rejection Reason</label>
                                                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-danger">Reject</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- {{ $leaveRequests->links() }} --}}
    </div>
</div>
