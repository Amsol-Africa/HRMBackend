<!DOCTYPE html>
<html>

<head>
    <title>Campaigns Report - {{ $business->company_name }}</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
    }

    h1 {
        text-align: center;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th,
    td {
        border: 1px solid #000;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }
    </style>
</head>

<body>
    <h1>Campaigns Report - {{ $business->company_name }}</h1>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>UTM Source</th>
                <th>UTM Medium</th>
                <th>UTM Campaign</th>
                <th>Target URL</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Has Survey</th>
                <th>Leads Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($campaigns as $index => $campaign)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $campaign->name }}</td>
                <td>{{ $campaign->utm_source }}</td>
                <td>{{ $campaign->utm_medium }}</td>
                <td>{{ $campaign->utm_campaign }}</td>
                <td>{{ $campaign->target_url }}</td>
                <td>{{ $campaign->start_date ? \Carbon\Carbon::parse($campaign->start_date)->format('Y-m-d') : 'N/A' }}
                </td>
                <td>{{ $campaign->end_date ? \Carbon\Carbon::parse($campaign->end_date)->format('Y-m-d') : 'N/A' }}</td>
                <td>{{ ucfirst($campaign->status) }}</td>
                <td>{{ $campaign->has_survey ? 'Yes' : 'No' }}</td>
                <td>{{ $campaign->leads()->count() }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>