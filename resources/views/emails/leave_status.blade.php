<x-mail::message>
# Leave Request Update

Hello {{ $leave->employee->name }},

Your leave request from **{{ $leave->start_date }}** to **{{ $leave->end_date }}**
has been **{{ strtoupper($leave->status) }}**.

<x-mail::panel>
Reason: {{ $leave->reason }}
</x-mail::panel>

Thanks,  
{{ config('app.name') }}
</x-mail::message>
