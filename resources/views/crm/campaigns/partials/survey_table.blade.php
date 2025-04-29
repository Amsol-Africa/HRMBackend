<table class="table table-hover" id="surveyDataTable">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Message</th>
            <th>Status</th>
            <th>Submitted At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($leads as $lead)
        <tr>
            <td>{{ $lead->name }}</td>
            <td>{{ $lead->email }}</td>
            <td>{{ $lead->phone ?? 'N/A' }}</td>
            <td>{{ Str::limit($lead->message, 50) }}</td>
            <td>{{ ucfirst($lead->status) }}</td>
            <td>{{ $lead->created_at->format('Y-m-d H:i:s') }}</td>
            <td>
                <a href="{{ route('business.crm.leads.view', ['business' => Auth::user()->currentBusiness->slug, 'lead' => $lead->id]) }}"
                    class="btn btn-sm btn-outline-primary">View</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>