<div class="mb-6">
    <label for="title" class="block mb-2 text-sm font-medium text-gray-900">
        {{__('Title')}}
    </label>
    <input wire:model="title" type="text" id="title"
        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
        required>
    <x-jet-input-error for="title"></x-jet-input-error>
</div>
<div class="grid gap-6 mb-6 md:grid-cols-4">
    <div>
        <label for="priority" class="block mb-2 text-sm font-medium text-gray-900">
            {{__('Priority')}}
        </label>
        <select name="priority" id="priority" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" wire:model="priority">
            <option value="" hidden selected></option>
            <option value="Low">{{__('Low')}}</option>
            <option value="Medium">{{__('Medium')}}</option>
            <option value="High">{{__('High')}}</option>
            <option value="Urgent">{{__('Urgent')}}</option>
        </select>
        <x-jet-input-error for="priority"></x-jet-input-error>
    </div>
    <div>
        <label for="start_date" class="block mb-2 text-sm font-medium text-gray-900">
            {{__('Start date')}}
        </label>
        <input wire:model="start_date" id="start_date" type="date"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
            @if($editModal) disabled @else required @endif>
        <x-jet-input-error for="start_date"></x-jet-input-error>
    </div>
    <div>
        <label for="end_date" class="block mb-2 text-sm font-medium text-gray-900">
            {{__('End date')}}
        </label>
        <input wire:model="end_date" type="date" id="end_date"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
            @if($editModal) disabled @else required @endif>
        <x-jet-input-error for="end_date"></x-jet-input-error>
    </div> 
    @if(isset($routeProject) && $routeProject == true)
        <div>
            <label for="hour_estimate" class="block mb-2 text-sm font-medium text-gray-900">
                {{__('Estimated hours')}}
            </label>
            <input wire:model="hour_estimate" type="text" id="hour_estimate"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                required>
            <x-jet-input-error for="hour_estimate"></x-jet-input-error>
        </div>  
    @endif  
</div>