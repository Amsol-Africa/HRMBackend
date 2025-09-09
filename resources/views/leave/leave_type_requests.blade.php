<x-app-layout>
    <div class="row mb-3">
        <h2>Leave Requests for: {{ $leaveType->name }}</h2>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="leaveTypeRequestsTable">
                    <thead>
                        <tr>
                            <th>Ref</th>
                            <th>Employee</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Days</th>
                            <th>Status</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leaveType->leaveRequests as $req)
                            <tr>
                                <td>{{ $req->reference_number }}</td>
                                <td>{{ optional(optional($req->employee)->user)->name ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($req->start_date)->format('Y-m-d') }}</td>
                                <td>{{ \Carbon\Carbon::parse($req->end_date)->format('Y-m-d') }}</td>
                                <td>{{ $req->total_days }}</td>
                                <td>
                                    @if (!is_null($req->approved_by) && is_null($req->rejection_reason))
                                        <span class="badge bg-success">Approved</span>
                                    @elseif (is_null($req->approved_by) && is_null($req->rejection_reason))
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif (!is_null($req->rejection_reason) && is_null($req->approved_by))
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-secondary">Unknown</span>
                                    @endif
                                </td>
                                <td>{{ $req->reason ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(function () {
            $('#leaveTypeRequestsTable').DataTable({
                responsive: true,
                autoWidth: false
            });
        });
    </script>
    @endpush
</x-app-layout>
