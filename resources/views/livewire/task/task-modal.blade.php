<div>
    {{-- New note modal --}}
    <x-jet-dialog-modal wire:model="openModal">
        <x-slot name="title">
            @if($editModal)
                {{__('Edit task')}} {{ $this->task->title }}
            @else
                {{__('Add new task')}}
            @endif
        </x-slot>

        <x-slot name="content">
            @include('modal.modal_common_info')
            <div class="mb-4">
                <label for="project_id" class="block mb-2 text-sm font-medium text-gray-900">
                    {{__('Projects')}}
                </label>
                <select name="project_id" id="project_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" wire:model="project_id">
                    <option value="" hidden selected></option>
                    @foreach ($projects as $project)
                        @if ($project->leader_id === auth()->id())
                            <option value="{{ $project->id }}">{{ $project->title }}</option>
                        @endif
                    @endforeach
                </select>
                <x-jet-input-error for="project_id"></x-jet-input-error>
                @if( !is_null($project_id) )    
                    <label for="phase_id" class="block mb-2 text-sm font-medium text-gray-900">
                        {{__('Phases')}}
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
                    {{__('Predecessor task')}}
                </label>
                <select name="predecessor_task" id="predecessor_task" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" wire:model="predecessor_task">
                    <option value="" hidden selected></option>
                    <option value="NA">{{__('Not Applicable')}}</option>
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
                    {{__('Users')}}
                </label>
                <select name="user_id_assigned" id="user_id_assigned" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" wire:model="user_id_assigned">
                    <option value="" hidden selected></option>
                    @foreach ($users as $user)
                        @if ($user->auth()->id !== $user->id)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endif
                    @endforeach
                </select>
                <x-jet-input-error for="user_id_assigned"></x-jet-input-error>
            </div>
            <div wire:ignore>
                <label for="content" class="block mb-2 text-sm font-medium text-gray-900">
                    {{__('Description')}}
                </label>
                <textarea wire:model="content" name="editor" id="editor" cols="30" rows="10" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"></textarea>
            </div>
            <x-jet-input-error for="content"></x-jet-input-error>

        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('openModal')">
                {{ __('Cancel') }}
            </x-jet-secondary-button>
            
            @if ($editModal)
                <x-jet-secondary-button
                    class="ml-3 bg-red-500 text-white hover:text-white hover:bg-red-700 active:bg-red-50"
                    wire:click="deleteTask({{ $this->task->id }})">
                    {{ __('Delete') }}
                </x-jet-secondary-button>
                
                <x-jet-button class="ml-3" wire:click="editTask({{ $this->task->id }})">
                    {{__('Save task')}}
                </x-jet-button>
            @else
                <x-jet-button class="ml-3" wire:click="saveTask">
                    {{__('Save task')}}
                </x-jet-button>
            @endif
        </x-slot>
    </x-jet-dialog-modal>
</div>
