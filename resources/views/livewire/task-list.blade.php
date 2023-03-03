<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 ">
            To Do App
        </h2>
    </x-slot>

    <div class="py-12 dark:bg-slate-800">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-400 overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div>
                    <x-jet-button wire:click="newNote" class="mb-4">
                        + New task
                    </x-jet-button>
                </div>

                <div class="flex items-center mb-4">   
                    <label for="simple-search" class="sr-only">Search</label>
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
                        </div>
                        <input type="search" wire:model="search" id="simple-search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search" required>
                    </div>
                </div>

                <div class="grid gap-2 md:grid-cols-4" wire:sortable="updateTaskOrder">
                    @foreach ($tasks as $task)
                        <div wire:sortable.item="{{ $task->id }}" wire:key="task-{{ $task->id }}" class="mb-2 bg-white rounded-lg shadow-md p-2 border dark:bg-slate-600">
                            <div class="px-2" wire:sortable.handle>
                                <div class="flex flex-row justify-between">
                                    <div class="font-bold text-xl dark:text-white mb-2" >
                                        {{ $task->title }}
                                        @if(!empty($task->image))
                                            @foreach (json_decode(Crypt::decrypt($task->image)) as $image)
                                                <img src="{{ $image }}" alt="Image"  class="object-cover w-48 py-5">
                                            @endforeach                                               
                                        @else
                                            <p>No image</p>
                                        @endif
                                    </div>    
                                    <div>
                                        @can('assign-to-user')
                                            <button wire:click="editNote({{ $task->id }})">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="w-6 h-6 dark:stroke-white">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                </svg>
                                            </button>
                                        @endcan
                                        <button>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 dark:stroke-white">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>                                              
                                        </button>
                                    </div>
                                </div>
                                    <p class="mb-3 font-normal text-gray-700 dark:text-white">
                                        {{ $task->content }}
                                    <br>
                                    <span class="bg-blue-100 text-blue-800 text-ms font-medium inline-flex px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-blue-400 border border-blue-400">
                                        <svg aria-hidden="true" class="w-4 h-6 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                                        Estimated hours: {{ $task->hour_estimate }}
                                    </span>
                                    <br>
                                    @if ($task->priority == 'Low')
                                        <span class="bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                            {{ $task->priority }}
                                        </span>
                                    @elseif ($task->priority == 'Medium')
                                        <span class="bg-green-100 text-green-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                            {{ $task->priority }}
                                        </span>
                                    @elseif ($task->priority == 'High')
                                        <span class="bg-yellow-100 text-yellow-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-orange-900 dark:text-yellow-300">
                                            {{ $task->priority }}
                                        </span>
                                    @else
                                        <span class="bg-red-100 text-red-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                            {{ $task->priority }}
                                        </span>
                                    @endif
                                    
                                </p>
                            </div>
                            <span
                                class="flex flex-row bg-purple-200 rounded-lg px-3 py-1 text-sm font-semibold text-purple-800 mr-2 mt-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $task->start_task }}
                            </span>
                            <span
                                class="flex flex-row bg-red-200 rounded-lg px-3 py-1 text-sm font-semibold text-red-800 mr-2 mt-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $task->end_task }}
                            </span>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>

    {{-- New note modal --}}
    <x-jet-dialog-modal wire:model="openModal">
        <x-slot name="title">
            Add new note
        </x-slot>

        <x-slot name="content">
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
                    <select name="priority" id="priority"class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" wire:model="priority">
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
            @can('assign-to-user')
                <div class="mb-4">
                    <label for="assigned_to" class="block mb-2 text-sm font-medium text-gray-900">
                        Users
                    </label>
                    <select name="assigned_to" id="assigned_to"class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" wire:model="assigned_to">
                        <option value="" hidden selected></option>
                        @foreach ($users as $user)
                            @if ($user->auth()->id !== $user->id)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endif
                        @endforeach
                    </select>
                    <x-jet-input-error for="assigned_to"></x-jet-input-error>
                </div>
            @endcan
            <input type="file" wire:model="image" multiple value="">
            <x-jet-input-error for="image"></x-jet-input-error>
            <div wire:ignore>
                <label for="content" class="block mb-2 text-sm font-medium text-gray-900">
                </label>
                <textarea wire:model="content" name="editor" id="editor" cols="30" rows="10" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"></textarea>
            </div>
            <x-jet-input-error for="content"></x-jet-input-error>

        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('openModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>
            
            @if ($editTask)
                <x-jet-secondary-button
                    class="ml-3 bg-red-500 text-white hover:text-white hover:bg-red-700 active:bg-red-50"
                    wire:loading.attr="disabled" wire:click="deleteTask({{ $this->task->id }})">
                    {{ __('Delete') }}
                </x-jet-secondary-button>
                <x-jet-button class="ml-3" wire:click="editTask({{ $this->task->id }})" wire:loading.attr="disabled" wire:target="save, image">
                    Save task
                </x-jet-button>
            @else
                <x-jet-button class="ml-3" wire:click="saveTask" wire:loading.attr="disabled" wire:target="save, image">
                    Save task
                </x-jet-button>
            @endif
        </x-slot>
    </x-jet-dialog-modal>

    @push('js')
        <script src="https://cdn.jsdelivr.net/gh/livewire/sortable@v0.x.x/dist/livewire-sortable.js"></script>
    @endpush
</div>
