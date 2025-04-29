<table id="leadsDataTable" class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Campaign</th>
            <th>Status</th>
            <th>Label</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($leads as $lead)
        <tr>
            <td>{{ $lead->name }}</td>
            <td>{{ $lead->email }}</td>
            <td>{{ $lead->campaign ? $lead->campaign->name : 'N/A' }}</td>
            <td>
                <span
                    class="badge bg-{{ $lead->status === 'new' ? 'info' : ($lead->status === 'contacted' ? 'primary' : ($lead->status === 'qualified' ? 'warning' : ($lead->status === 'converted' ? 'success' : 'danger'))) }}">
                    {{ ucfirst($lead->status) }}
                </span>
            </td>
            <td>
                <span
                    class="badge bg-{{ $lead->label === 'High Priority' ? 'danger' : ($lead->label === 'Low Priority' ? 'secondary' : ($lead->label === 'Follow Up' ? 'info' : ($lead->label === 'Hot Lead' ? 'warning' : ($lead->label === 'Cold Lead' ? 'primary' : 'dark')))) }}">
                    {{ $lead->label ?? 'N/A' }}
                </span>
            </td>
            <td>
                <a href="{{ route('business.crm.leads.view', ['business' => session('active_business_slug'), 'lead' => $lead->id]) }}"
                    class="btn btn-sm btn-outline-primary">View</a>
                <button class="btn btn-sm btn-outline-danger delete-lead" data-id="{{ $lead->id }}">Delete</button>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center">No leads available</td>
        </tr>
        @endforelse
    </tbody>
</table>