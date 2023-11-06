@component('mail::message')
# <center>{{__('Task not finished')}}</center>

{{__('Hello')}}, {{ $task->user->name }}. {{__('The due date of one of your tasks has passed')}}.
{{ $task->title }} {{__('with due date')}}: {{ $task->getEndTaskAttribute() }}
{{__('Please complete and mark as finished your task as soon as possible')}}.

{{__('Thanks')}},<br>
{{ config('app.name') }}
@endcomponent