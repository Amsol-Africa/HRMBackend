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
                            <a href="" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> View
                            </a>
                            @if (is_null($request->approved_by) && auth()->user()->hasRole('admin'))
                                <button type="button" onclick="approve(this)" data-leave="{{ $request->id }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-check"></i> Approve
                                </button>

                                <button type="button" onclick="reject(this)" data-leave="{{ $request->id }}" class="btn btn-sm btn-danger">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- {{ $leaveRequests->links() }} --}}
    </div>
</div>
