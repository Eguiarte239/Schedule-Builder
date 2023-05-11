<div class="mb-6">
    <label for="title" class="block mb-2 text-sm font-medium text-gray-900">
        Title
    </label>
    <input wire:model="title" type="text" id="first_name"
        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
        required>
    <x-jet-input-error for="title"></x-jet-input-error>
</div>
<div class="grid gap-6 mb-6 md:grid-cols-4">
    <div>
        <label for="priority" class="block mb-2 text-sm font-medium text-gray-900">
            Priority
        </label>
        <select name="priority" id="priority" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" wire:model="priority">
            <option value="" hidden selected></option>
            <option value="Low">Low</option>
            <option value="Medium">Medium</option>
            <option value="High">High</option>
            <option value="Urgent">Urgent</option>
        </select>
        <x-jet-input-error for="priority"></x-jet-input-error>
    </div>
    <div>
        <label for="start_time" class="block mb-2 text-sm font-medium text-gray-900">
            Start time
        </label>
        <input wire:model="start_time" id="start_time" type="date"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
            required>
        <x-jet-input-error for="start_time"></x-jet-input-error>
    </div>
    <div>
        <label for="end_time" class="block mb-2 text-sm font-medium text-gray-900">
            End time
        </label>
        <input wire:model="end_time" type="date" id="end_time"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
            required>
        <x-jet-input-error for="end_time"></x-jet-input-error>
    </div>
    <div>
        <label for="hour_estimate" class="block mb-2 text-sm font-medium text-gray-900">
            Estimated hours
        </label>
        <input wire:model="hour_estimate" type="text" id="hour_estimate"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
            required>
        <x-jet-input-error for="hour_estimate"></x-jet-input-error>
    </div>
</div>