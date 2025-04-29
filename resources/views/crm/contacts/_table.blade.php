<table class="table table-hover table-striped" id="contactsTable">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Company</th>
            <th>Inquiry Type</th>
            <th>Status</th>
            <th>Source</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($submissions as $index => $submission)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ trim($submission->first_name . ' ' . $submission->last_name) ?: 'N/A' }}</td>
            <td>{{ $submission->email }}</td>
            <td>{{ $submission->phone ?? 'N/A' }}</td>
            <td>{{ $submission->company_name ?? 'N/A' }}</td>
            <td>{{ $submission->inquiry_type ?? 'N/A' }}</td>
            <td>
                <select class="form-select form-select-sm" data-submission="{{ $submission->id }}"
                    onchange="updateContactStatus(this)">
                    <option value="new" {{ $submission->status === 'new' ? 'selected' : '' }}>New</option>
                    <option value="contacted" {{ $submission->status === 'contacted' ? 'selected' : '' }}>Contacted
                    </option>
                    <option value="qualified" {{ $submission->status === 'qualified' ? 'selected' : '' }}>Qualified
                    </option>
                    <option value="closed" {{ $submission->status === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </td>
            <td>{{ $submission->source ?? 'Unknown' }}</td>
            <td>
                <div class="btn-group" role="group">
                    <a href="{{ route('business.crm.contacts.view', ['business' => $currentBusiness->slug, 'submission' => $submission->id]) }}"
                        class="btn btn-info btn-sm">
                        <i class="bi bi-eye"></i>
                    </a>
                    <button class="btn btn-danger btn-sm" data-submission="{{ $submission->id }}"
                        onclick="deleteContact(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>