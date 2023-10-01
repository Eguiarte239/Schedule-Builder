<div>
    {{-- New note modal --}}
    <x-jet-dialog-modal wire:model="openModal">
        <x-slot name="title">
            @if($editModal)
                Edit project {{ $this->project->title }}
            @else
                Add new project
            @endif
        </x-slot>

        <x-slot name="content">
            @include('modal.modal_common_info')
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
</div>
