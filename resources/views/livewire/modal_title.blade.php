@if ($__data['create'])
    <x-slot name="title">
        Create
    </x-slot>
@else
    <x-slot name="title">
        Edit
    </x-slot>
@endif