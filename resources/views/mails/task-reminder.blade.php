@component('mail::message')
# <center>Task reminder</center>

Hello {{ $task->user->name }}. The due date of one of your tasks is close.
{{ $task->title }} with due date: {{ $task->getEndTaskAttribute() }}</p>

Thanks,<br>
{{ config('app.name') }}
@endcomponent