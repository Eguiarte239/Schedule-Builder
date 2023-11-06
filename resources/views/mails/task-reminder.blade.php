@component('mail::message')
# <center>{{__('Task reminder')}}</center>

{{__('Hello')}}, {{ $task->user->name }}. {{__('The due date of one of your tasks is close.')}}
{{ $task->title }} {{__('with due date')}}: {{ $task->getEndTaskAttribute() }}</p>

{{__('Thanks')}},<br>
{{ config('app.name') }}
@endcomponent