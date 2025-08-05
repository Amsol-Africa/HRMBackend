<table class="table table-hover" id="visitsDataTable">
    <thead>
        <tr>
            <th>IP Address</th>
            <th>Browser</th>
            <th>OS</th>
            <th>Device Type</th>
            <th>Country</th>
            <th>Visited At</th>
        </tr>
    </thead>
    <tbody>
        @foreach($visits as $visit)
        <tr>
            <td>{{ $visit->ip_address ?? 'N/A' }}</td>
            <td>{{ $visit->browser ?? 'N/A' }}</td>
            <td>{{ $visit->os ?? 'N/A' }}</td>
            <td>{{ $visit->device_type ?? 'N/A' }}</td>
            <td>{{ $visit->country ?? 'N/A' }}</td>
            <td>{{ $visit->created_at->format('Y-m-d H:i:s') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>