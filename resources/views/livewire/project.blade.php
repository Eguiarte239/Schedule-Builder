<div>
    <div class="py-12 dark:bg-slate-800">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-400 overflow-hidden shadow-xl sm:rounded-lg p-6">
                @can('assign-leader')
                    <div>
                        <x-jet-button wire:click="newNote" class="mb-4">
                            + New project
                        </x-jet-button>
                    </div>
                @endcan

                @include('livewire.search_bar', ['search' => $search])

                <div class="grid gap-2 md:grid-cols-4" wire:sortable="updateTaskOrder">
                    @foreach ($projects as $project)
                        @include('livewire.project_info', ['project' => $project, 'projects' => true])
                    @endforeach

                </div>
            </div>
        </div>
    </div>

    @can('assign-leader')
    {{-- New note modal --}}
    <x-jet-dialog-modal wire:model="openModal">
        <x-slot name="title">
            Add new project
        </x-slot>

        <x-slot name="content">
            @include('livewire.modal_common_info')
            <div class="mb-4">
                <label for="leader_id_assigned" class="block mb-2 text-sm font-medium text-gray-900">
                    Users
                </label>
                <select name="leader_id_assigned" id="leader_id_assigned"class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" wire:model="leader_id_assigned">
                    <option value="" hidden selected></option>
                    @foreach ($users as $user)
                        @if ($user->auth()->id !== $user->id)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endif
                    @endforeach
                </select>
                <x-jet-input-error for="leader_id_assigned"></x-jet-input-error>
            </div>
            
            <x-jet-input-error for="content"></x-jet-input-error>

        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('openModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>
            
            @if ($editProject)
                <x-jet-secondary-button
                    class="ml-3 bg-red-500 text-white hover:text-white hover:bg-red-700 active:bg-red-50"
                    wire:loading.attr="disabled" wire:click="deleteProject({{ $this->project->id }})">
                    {{ __('Delete') }}
                </x-jet-secondary-button>
                <x-jet-button class="ml-3" wire:click="editProject({{ $this->project->id }})">
                    Save project
                </x-jet-button>
            @else
                <x-jet-button class="ml-3" wire:click="saveProject">
                    Save project
                </x-jet-button>
            @endif
        </x-slot>
    </x-jet-dialog-modal>
    @endcan

    @push('js')
        <script src="https://cdn.jsdelivr.net/gh/livewire/sortable@v0.x.x/dist/livewire-sortable.js"></script>
    @endpush
</div>