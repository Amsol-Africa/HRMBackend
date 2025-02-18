<table class="table table-striped table-hover" id="overtimeTable">
    <thead>
        <tr>
            <th>Employee</th>
            <th>Date</th>
            <th>Hours</th>
            <th>Description</th>
            <th>Approved By</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($overtimes as $overtime)
            <tr>
                <td>{{ $overtime->employee->user->name ?? 'N/A' }}</td>
                <td>{{ $overtime->date->format('jS M Y') ?? 'N/A' }}</td>
                <td>{{ $overtime->overtime_hours }}</td>
                <td>{{ $overtime->description ?? '-' }}</td>
                <td>{{ $overtime->approvedBy->name ?? 'N/A' }}</td>
                <td>
                    <a href="" onclick="editOvertime(this)" data-overtime="{{ $overtime->id }}"  class="btn btn-sm btn-primary">  Edit</a>
                    <button type="button" onclick="deleteOvertime(this)"  data-overtime="{{ $overtime->id }}" class="btn btn-sm btn-danger">Delete</button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
