<div>
    <div class="py-12 dark:bg-slate-800">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-400 overflow-hidden shadow-xl sm:rounded-lg p-6">
                @can('assign-employee')
                    <div>
                        <x-jet-button wire:click="newPhase" class="mb-4">
                            + New phase
                        </x-jet-button>
                    </div>
                @endcan
                
                @include('livewire.search_bar', ['search' => $search])

                <div class="grid gap-2 md:grid-cols-4" wire:sortable="updateTaskOrder">
                    @foreach ($projects as $project)
                        @include('livewire.project_info', ['project' => $project, 'projects' => false])
                        @if (isset($groupedPhases[$project->id]))
                            @foreach ($groupedPhases[$project->id] as $phase)
                                @include('livewire.phase_info', ['phase' => $phase])
                            @endforeach
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- New note modal --}}
    <x-jet-dialog-modal wire:model="openModal">
        <x-slot name="title">
            Add new phase
        </x-slot>

        <x-slot name="content">
            @include('livewire.modal_common_info')
            @can('assign-employee')
                <div class="mb-4">
                    <label for="project_id" class="block mb-2 text-sm font-medium text-gray-900">
                        Projects
                    </label>
                    <select name="project_id" id="project_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" wire:model="project_id">
                        <option value="" hidden selected></option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->title }}</option>
                        @endforeach
                    </select>
                    <x-jet-input-error for="project_id"></x-jet-input-error>
                </div>
            @endcan
            <div wire:ignore>
                <label for="content" class="block mb-2 text-sm font-medium text-gray-900">
                    Description
                </label>
                <textarea wire:model="content" name="editor" id="editor" cols="30" rows="10" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"></textarea>
            </div>
            <x-jet-input-error for="content"></x-jet-input-error>

        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('openModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>
            
            @if ($editPhase)
                <x-jet-secondary-button
                    class="ml-3 bg-red-500 text-white hover:text-white hover:bg-red-700 active:bg-red-50"
                    wire:loading.attr="disabled" wire:click="deletePhase({{ $this->phase->id }})">
                    {{ __('Delete') }}
                </x-jet-secondary-button>
                <x-jet-button class="ml-3" wire:click="editPhase({{ $this->phase->id }})">
                    Save phase
                </x-jet-button>
            @else
                <x-jet-button class="ml-3" wire:click="savePhase">
                    Save phase
                </x-jet-button>
            @endif
        </x-slot>
    </x-jet-dialog-modal>

    @push('js')
        <script src="https://cdn.jsdelivr.net/gh/livewire/sortable@v0.x.x/dist/livewire-sortable.js"></script>
    @endpush
</div>
