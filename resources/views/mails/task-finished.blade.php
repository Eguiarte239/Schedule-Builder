@component('mail::message')
# <center>{{__('Task finished')}}</center>

{{__('Hello')}}, {{ $task->leader->name }}. {{__("One of the tasks you've assigned has been finished")}}.
{{ $task->title }} {{__('from')}} {{ $task->project->title }}.

{{__('Thanks')}},<br>
{{ config('app.name') }}
@endcomponent