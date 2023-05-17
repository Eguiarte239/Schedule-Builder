<div>
    <div class="py-12 dark:bg-slate-800">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-400 overflow-hidden shadow-xl sm:rounded-lg p-6">
                @can('assign-employee')
                    <div>
                        <x-jet-button wire:click="newTask" class="mb-4">
                            + New task
                        </x-jet-button>
                    </div>
                @endcan
                
                @include('livewire.search_bar', ['search' => $search, 'tasks' => true])

                <div class="grid gap-2 md:grid-cols-4" wire:sortable="updateTaskOrder">
                    @foreach ($projects as $project)
                        @php
                            $hasTasks = false;
                        @endphp
                        @foreach ($project->phase as $phase)
                            @if($phase->task->isNotEmpty())
                                @if (!$hasTasks)
                                    @include('livewire.project_info', ['project' => $project, 'projects' => false])
                                    @php
                                        $hasTasks = true;
                                    @endphp
                                @endif
                                @include('livewire.phase_info', ['phase' => $phase, 'phases' => false])
                                @foreach ($phase->task as $task)
                                    @include('livewire.task_info', ['task' => $task])
                                @endforeach
                            @endif
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- New note modal --}}
    <x-jet-dialog-modal wire:model="openModal">
        <x-slot name="title">
            Add new task
        </x-slot>

        <x-slot name="content">
            @include('livewire.modal_common_info')
            <div class="mb-4">
                <label for="project_id" class="block mb-2 text-sm font-medium text-gray-900">
                    Projects
                </label>
                <select name="project_id" id="project_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" wire:model="project_id">
                    <option value="" hidden selected></option>
                    @foreach ($projects as $project)
                        @if ($project->leader_id_assigned === auth()->id())
                            <option value="{{ $project->id }}">{{ $project->title }}</option>
                        @endif
                    @endforeach
                </select>
                <x-jet-input-error for="project_id"></x-jet-input-error>
                @if( !is_null($project_id) )    
                    <label for="phase_id" class="block mb-2 text-sm font-medium text-gray-900">
                        Phases
                    </label>
                    <select name="phase_id" id="phase_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" wire:model="phase_id">
                        <option value="" hidden selected></option>
                        @foreach($projects->find($project_id)->phase as $phase)
                            <option value="{{ $phase->id }}">{{ $phase->title }}</option>
                        @endforeach
                    </select>
                    <x-jet-input-error for="phase_id"></x-jet-input-error>
                @endif
                <label for="predecessor_task" class="block mb-2 text-sm font-medium text-gray-900">
                    Predecessor task
                </label>
                <select name="predecessor_task" id="predecessor_task" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" wire:model="predecessor_task">
                    <option value="" hidden selected></option>
                    <option value="NA">No aplica</option>
                    @foreach ($predecessorTasks as $predecessorTask)
                        @if($predecessorTask->phase->project->id == $project_id)
                            @if($this->task->id !== $predecessorTask->id)
                                <option value="{{ $predecessorTask->id }}">{{ $predecessorTask->title }}</option>
                            @endif
                        @endif
                    @endforeach
                </select>
                <x-jet-input-error for="predecessor_task"></x-jet-input-error>
                <label for="user_id_assigned" class="block mb-2 text-sm font-medium text-gray-900">
                    Users
                </label>
                <select name="user_id_assigned" id="user_id_assigned" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" wire:model="user_id_assigned">
                    <option value="" hidden selected></option>
                    @foreach ($users as $user)
                        @if ($user->auth()->id !== $user->id)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endif
                    @endforeach
                </select>
                <x-jet-input-error for="predecessor_task"></x-jet-input-error>
            </div>
            <div wire:ignore>
                <label for="content" class="block mb-2 text-sm font-medium text-gray-900">
                </label>
                <textarea wire:model="content" name="editor" id="editor" cols="30" rows="10" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"></textarea>
            </div>
            <x-jet-input-error for="content"></x-jet-input-error>

        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('openModal')">
                {{ __('Cancel') }}
            </x-jet-secondary-button>
            
            @if ($editTask)
                <x-jet-secondary-button
                    class="ml-3 bg-red-500 text-white hover:text-white hover:bg-red-700 active:bg-red-50"
                    wire:click="deleteTask({{ $this->task->id }})">
                    {{ __('Delete') }}
                </x-jet-secondary-button>
                
                <x-jet-button class="ml-3" wire:click="editTask({{ $this->task->id }})">
                    Save tasks
                </x-jet-button>
            @else
                <x-jet-button class="ml-3" wire:click="saveTask">
                    Save tasks
                </x-jet-button>
            @endif
        </x-slot>
    </x-jet-dialog-modal>

    @push('js')
        <script src="https://cdn.jsdelivr.net/gh/livewire/sortable@v0.x.x/dist/livewire-sortable.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            const Swal = window.Swal;
            Livewire.on('alert', function(message, route) {
                Swal.fire({
                    imageUrl: 'https://media.tenor.com/2KK38LekJu0AAAAC/doki-doki-literature-club-mad.gif',
                    imageWidth: 250,
                    imageHeight: 250,
                    title: 'Oops...',
                    text: message,
                    timer: 5000,
                }).then(function () {
                    window.location.href = route;
                });
            })

            Livewire.on('predecessor', function(message, route) {
                Swal.fire({
                    imageUrl: 'https://media.tenor.com/2KK38LekJu0AAAAC/doki-doki-literature-club-mad.gif',
                    imageWidth: 250,
                    imageHeight: 250,
                    title: 'Oops...',
                    text: message,
                    timer: 5000,
                }).then(function () {
                    window.location.href = route;
                });
            })

            Livewire.on('new-task-alert', function(message) {
                Swal.fire({
                    imageUrl: 'https://stickershop.line-scdn.net/stickershop/v1/product/7458249/LINEStorePC/main.png',
                    imageWidth: 250,
                    imageHeight: 250,
                    title: 'Be careful',
                    text: message,
                })
            })
        </script>
    @endpush
</div>
