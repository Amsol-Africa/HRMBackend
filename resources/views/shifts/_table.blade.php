<table class="table table-striped table-hover" id="shiftsTable">
    <thead>
        <tr>
            <th>#</th>
            <th>Shift Name</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Date Created</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($shifts as $index => $shift)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $shift->name }}</td>
                <td>{{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }}</td>
                <td>{{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}</td>
                <td>{{ \Carbon\Carbon::parse($shift->created_at)->diffForHumans() }}</td>
                <td>
                    <div class="btn-group" role="group">
                        <button class="btn btn-info edit-shift" onclick="editShift(this)" data-shift="{{ $shift->slug }}"
                            data-bs-toggle="tooltip" title="Edit Shift">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger delete-shift" onclick="deleteShift(this)"
                            data-shift="{{ $shift->slug }}" data-bs-toggle="tooltip" title="Delete Shift">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
