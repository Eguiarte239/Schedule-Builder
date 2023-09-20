<div>
    <div class="py-12 dark:bg-slate-800">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-400 overflow-hidden shadow-xl sm:rounded-lg p-6">
                @can('assign-leader')
                    <div>
                        <x-jet-button wire:click="newProject" class="mb-4">
                            + New project
                        </x-jet-button>
                    </div>
                @endcan

                <div>
                    <div>
                        <label for="Ask DB" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{$response}}</label>
                        <input wire:model.defer="ask" type="text" id="Ask DB" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Ask something" required>
                    </div>
                    
                    <x-jet-button wire:click="askDB" class="mt-4 mb-4">
                        Preguntar
                    </x-jet-button>
                </div>

                @include('livewire.search_bar', ['search' => $search, 'tasks' => false])

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
            <div>
                <label for="hour_estimate" class="block mb-2 text-sm font-medium text-gray-900">
                    Estimated hours
                </label>
                <input wire:model="hour_estimate" type="text" id="hour_estimate"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                    required>
                <x-jet-input-error for="hour_estimate"></x-jet-input-error>
            </div>
            <div class="mb-4">
                <label for="leader" class="block mb-2 text-sm font-medium text-gray-900">
                    Users
                </label>
                <select name="leader" id="leader"class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" wire:model="leader" @if($editModal) disabled @else required @endif>>
                    <option value="" hidden selected></option>
                    @foreach ($users as $user)
                        @if ($user->auth()->id !== $user->id)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endif
                    @endforeach
                </select>
                <x-jet-input-error for="leader"></x-jet-input-error>
            </div>
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
            
            @if ($editModal)
                <x-slot name="title">
                    Edit project
                </x-slot>
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
            
            Livewire.on('new-project-alert', function(message) {
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