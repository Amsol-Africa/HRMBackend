@component('mail::message')

# Task Assignment Notification

You have been assigned to a new task: **{{ $task->title }}**

## Task Details
- **Business**: {{ $business->company_name }}
- **Description**: {{ $task->description ?? 'No description provided' }}
- **Priority**: {{ ucfirst($task->priority) }}
- **Due Date**: {{ $task->due_date->format('F j, Y') }}
- **Status**: {{ ucfirst($task->status) }}

@if($task->links)
## Links
@foreach($task->links as $link)
- [{{ $link }}]({{ $link }})
@endforeach
@endif

@if($task->getMedia('attachments')->count())
## Attachments
@foreach($task->getMedia('attachments') as $attachment)
- [{{ $attachment->file_name }}]({{ $attachment->getUrl() }})
@endforeach
@endif

Please review the task details and take necessary actions.

@component('mail::button', ['url' => url('/tasks/' . $task->slug)])
View Task
@endcomponent

Thanks,
{{ config('app.name') }}

@endcomponent