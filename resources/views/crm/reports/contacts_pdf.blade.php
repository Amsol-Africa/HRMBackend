<!DOCTYPE html>
<html>

<head>
    <title>Contacts Report - {{ $business->company_name }}</title>
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
    <h1>Contacts Report - {{ $business->company_name }}</h1>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Company</th>
                <th>Country</th>
                <th>Inquiry Type</th>
                <th>Status</th>
                <th>Source</th>
                <th>UTM Source</th>
                <th>UTM Medium</th>
                <th>UTM Campaign</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($contacts as $index => $submission)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ trim($submission->first_name . ' ' . $submission->last_name) ?: 'Unknown' }}</td>
                <td>{{ $submission->email }}</td>
                <td>{{ $submission->phone ?? 'N/A' }}</td>
                <td>{{ $submission->company_name ?? 'N/A' }}</td>
                <td>{{ $submission->country ?? 'N/A' }}</td>
                <td>{{ $submission->inquiry_type }}</td>
                <td>{{ ucfirst($submission->status) }}</td>
                <td>{{ $submission->source ?? 'Unknown' }}</td>
                <td>{{ $submission->utm_source ?? 'N/A' }}</td>
                <td>{{ $submission->utm_medium ?? 'N/A' }}</td>
                <td>{{ $submission->utm_campaign ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>