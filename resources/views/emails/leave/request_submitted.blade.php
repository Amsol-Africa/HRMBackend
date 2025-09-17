<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Leave Request</title>
</head>
<body style="font-family: Arial, sans-serif; line-height:1.5; color:#222;">
    <h2 style="margin-bottom:8px;">New Leave Request Submitted</h2>

    <p>A new leave request has been submitted. Here are the details:</p>

    <ul>
        <li><strong>Employee:</strong> {{ $leaveRequest->employee->user->name }} ({{ $leaveRequest->employee->user->email }})</li>
        <li><strong>Leave Type:</strong> {{ $leaveRequest->leaveType->name }}</li>
        <li><strong>Start Date:</strong> {{ optional($leaveRequest->start_date)->format('d M Y') }}</li>
        <li><strong>End Date:</strong> {{ optional($leaveRequest->end_date)->format('d M Y') }}</li>
        <li><strong>Total Days:</strong> {{ $leaveRequest->total_days }}</li>
        <li><strong>Reason:</strong> {{ $leaveRequest->reason ?? 'N/A' }}</li>
        @if($leaveRequest->attachment)
            <li><strong>Attachment:</strong>
                <a href="{{ asset('storage/' . $leaveRequest->attachment) }}" target="_blank">Download</a>
            </li>
        @endif
    </ul>

    @isset($showUrl)
        <p style="margin:24px 0;">
            <a href="{{ $showUrl }}"
               style="display:inline-block;padding:10px 16px;background:#0d6efd;color:#fff;text-decoration:none;border-radius:6px;">
                View Request
            </a>
        </p>
    @endisset

    <p style="color:#666;">Thanks,<br>{{ config('app.name') }}</p>
</body>
</html>
