<table class="table table-striped table-hover" id="rostersTable">
    <thead class="table-light">
        <tr>
            <th><input type="checkbox" id="selectAll"></th>
            <th>#</th>
            <th>Roster</th>
            <th>Employee</th>
            <th>Department</th>
            <th>Job Category</th>
            <th>Location</th>
            <th>Date</th>
            <th>Shift</th>
            <th>Leave</th>
            <th>Status</th>
            <th>Overtime (Hrs)</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rosters as $roster)
        @foreach ($roster->assignments as $index => $assignment)
        <tr data-id="{{ $assignment->id }}">
            <td><input type="checkbox" class="selectRow" data-id="{{ $assignment->id }}"></td>
            <td>{{ $index + 1 }}</td>
            <td>{{ $roster->name }}</td>
            <td>{{ $assignment->employee->user->name }}</td>
            <td>{{ $assignment->department->name }}</td>
            <td>{{ $assignment->jobCategory->name }}</td>
            <td>{{ $assignment->location->name }}</td>
            <td>{{ $assignment->date->format('Y-m-d') }}</td>
            <td>{{ $assignment->shift ? $assignment->shift->name : 'N/A' }}</td>
            <td>{{ $assignment->leave ? $assignment->leave->name : 'N/A' }}</td>
            <td><span
                    class="badge bg-{{ $assignment->status === 'draft' ? 'secondary' : ($assignment->status === 'published' ? 'success' : 'danger') }}">{{ ucfirst($assignment->status) }}</span>
            </td>
            <td>{{ number_format($assignment->overtime_hours, 2) }}</td>
            <td>
                <div class="btn-group">
                    <button class="btn btn-outline-info btn-sm edit-roster" data-roster="{{ $roster->slug }}"
                        title="Edit Roster">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-outline-danger btn-sm delete-roster" data-roster="{{ $roster->slug }}"
                        title="Delete Roster">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
        @endforeach
        @endforeach
    </tbody>
</table>

@push('scripts')
<script>
$(document).ready(function() {
    const table = $('#rostersTable').DataTable({
        dom: '<"d-flex justify-content-between align-items-center mb-3"lBf>rt<"d-flex justify-content-between mt-3"ip>',
        order: [
            [7, 'desc']
        ],
        lengthMenu: [
            [5, 10, 20, 50, 100],
            [5, 10, 20, 50, 100]
        ],
        pageLength: 10,
        buttons: [{
                extend: 'copy',
                className: 'btn btn-outline-primary btn-sm',
                exportOptions: {
                    columns: ':not(:first-child, :last-child)'
                }
            },
            {
                extend: 'csv',
                className: 'btn btn-outline-secondary btn-sm',
                exportOptions: {
                    columns: ':not(:first-child, :last-child)'
                }
            },
            {
                extend: 'excel',
                className: 'btn btn-outline-success btn-sm',
                exportOptions: {
                    columns: ':not(:first-child, :last-child)'
                }
            },
            {
                extend: 'pdf',
                className: 'btn btn-outline-danger btn-sm',
                exportOptions: {
                    columns: ':not(:first-child, :last-child)'
                }
            },
            {
                extend: 'print',
                className: 'btn btn-outline-info btn-sm',
                exportOptions: {
                    columns: ':not(:first-child, :last-child)'
                }
            },
            {
                text: '<i class="fas fa-bell"></i> Notify Selected',
                className: 'btn btn-outline-warning btn-sm',
                action: window.notifySelectedAssignments
            }
        ]
    });

    table.on('click', 'tbody tr', function(e) {
        if (!$(e.target).closest('.btn').length) {
            $(this).toggleClass('selected');
            $(this).find('.selectRow').prop('checked', $(this).hasClass('selected'));
        }
    });

    $('#selectAll').on('change', function() {
        const isChecked = $(this).is(':checked');
        table.rows().nodes().to$().find('.selectRow').prop('checked', isChecked).closest('tr')
            .toggleClass('selected', isChecked);
    });
});
</script>
@endpush