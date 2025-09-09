<x-mail::message>
# New Leave Request Submitted

A new leave request has been submitted. Here are the details:

- **Employee:** {{ $leaveRequest->employee->user->name }} ({{ $leaveRequest->employee->user->email }})
- **Leave Type:** {{ $leaveRequest->leaveType->name }}
- **Start Date:** {{ $leaveRequest->start_date->format('d M Y') }}
- **End Date:** {{ $leaveRequest->end_date->format('d M Y') }}
- **Total Days:** {{ $leaveRequest->total_days }}
- **Reason:** {{ $leaveRequest->reason ?? 'N/A' }}

@if($leaveRequest->attachment)
- **Attachment:** [Download Attachment]({{ asset('storage/' . $leaveRequest->attachment) }})
@endif

<x-mail::button :url="url('/leave-requests/' . $leaveRequest->reference_number)">
View Request
</x-mail::button>

Thanks,  
{{ config('app.name') }}
</x-mail::message>
