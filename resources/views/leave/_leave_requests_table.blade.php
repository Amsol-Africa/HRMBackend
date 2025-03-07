<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Leave Requests</h5>


        @if (auth()->user()->hasRole('business-admin'))
            <a href="{{ route('business.leave.create', $currentBusiness->slug) }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus"></i> Create Leave Request
            </a>
        @else
            <a href="{{ route('myaccount.leave.requests.create', $currentBusiness->slug) }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus"></i> Request Leave
            </a>
        @endif

    </div>

    <div class="card-body">
        <table class="table table-bordered" style="width: 100%" id="{{ $status }}LeaveRequestsTable">
            <thead>
                <tr>
                    <th>Ref. No.</th>
                    <th>Employee</th>
                    <th>Leave Type</th>
                    <th>Start Date</th>
                    <th>Days</th>
                    <th>End Date</th>
                    <th>Remaining</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($leaveRequests as $request)
                    <tr>
                        <td>{{ $request->reference_number }}</td>
                        <td>{{ $request->employee->user->name }}</td>
                        <td class="text-white @if ($request->leaveType->name == 'Sick Leave') bg-danger
                            @elseif ($request->leaveType->name == 'Annual Leave') bg-primary
                            @else bg-secondary @endif">
                            {{ $request->leaveType->name }}
                        </td>

                        <td class="fw-bold text-primary">{{ $request->start_date->format('Y-m-d') }}</td>
                        <td>{{ $request->total_days }}</td>
                        <td class="fw-bold text-danger">{{ $request->end_date->format('Y-m-d') }}</td>
                        <td class="fw-bold
                            @if ($request->end_date->isPast()) text-danger
                            @elseif ($request->end_date->diffInDays(today()) <= 2) text-warning
                            @else text-success
                            @endif">
                            {{ max($request->end_date->diffInDays(today()), 0) }}
                        </td>

                        <td>
                            @if (is_null($request->approved_by))
                                <span class="badge bg-warning">
                                    <i class="fa-solid me-1 fa-clock"></i> Pending
                                </span>
                            @elseif (!is_null($request->approved_by))
                                <span class="badge bg-success">
                                    <i class="fa-solid me-1 fa-check-circle"></i> Approved
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="fa-solid me-1 fa-times-circle"></i> Rejected
                                </span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <a href="" class="btn btn-primary">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @if (is_null($request->approved_by) && auth()->user()->hasRole('business-admin'))
                                    <button type="button" onclick="manageLeave(this)" data-action="approve" data-leave="{{ $request->reference_number }}" class="btn btn-success">
                                        <i class="fa-solid fa-check"></i>
                                    </button>

                                    <button type="button" onclick="manageLeave(this)" data-action="reject" data-leave="{{ $request->reference_number }}" class="btn btn-danger">
                                        <i class="fa-solid fa-ban"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- {{ $leaveRequests->links() }} --}}
    </div>
</div>
