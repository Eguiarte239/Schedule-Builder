@component('mail::message')
# <center>Task finished</center>

Hello {{ $task->leader->name }}. One of the tasks you've assigned has been completed late.
{{ $task->title }} from {{ $task->project->title }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent