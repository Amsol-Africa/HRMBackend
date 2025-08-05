<table class="table table-striped table-bordered" id="advancesTable">
    <thead>
        <tr>
            <th>#</th>
            <th>Code</th>
            <th>Name</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Note</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($advances as $key => $advance)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $advance->employee->employee_code }}</td>
                <td>{{ $advance->employee->user->name }}</td>
                <td>{{ number_format($advance->amount, 2) }}</td>
                <td>{{ $advance->date }}</td>
                <td>{{ $advance->note ?? 'N/A' }}</td>
                <td>
                    <button type="button" class="btn btn-primary btn-sm" data-advance="{{ $advance->id }}" onclick="editAdvance(this)">
                        <i class="bi bi-pencil-square"></i> Edit
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" data-advance="{{ $advance->id }}" onclick="deleteAdvance(this)">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
