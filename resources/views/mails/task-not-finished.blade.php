@component('mail::message')
# <center>Task not finished</center>

Hello {{ $task->user->name }}. The due date of one of your tasks has passed.
{{ $task->title }} with due date: {{ $task->getEndTaskAttribute() }}
Please complete and mark as "finished" your task as soon as possible.

Thanks,<br>
{{ config('app.name') }}
@endcomponent