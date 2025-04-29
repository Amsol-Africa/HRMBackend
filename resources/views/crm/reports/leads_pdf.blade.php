<!DOCTYPE html>
<html>

<head>
    <title>Leads Report - {{ $business->company_name }}</title>
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
    <h1>Leads Report - {{ $business->company_name }}</h1>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Country</th>
                <th>Status</th>
                <th>Label</th>
                <th>Campaign</th>
                <th>Source</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($leads as $index => $lead)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $lead->name ?: 'Unknown' }}</td>
                <td>{{ $lead->email }}</td>
                <td>{{ $lead->phone ?? 'N/A' }}</td>
                <td>{{ $lead->country ?? 'N/A' }}</td>
                <td>{{ ucfirst($lead->status) }}</td>
                <td>{{ $lead->label ?? 'N/A' }}</td>
                <td>{{ $lead->campaign ? $lead->campaign->name : 'N/A' }}</td>
                <td>{{ $lead->source ?? 'Unknown' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>