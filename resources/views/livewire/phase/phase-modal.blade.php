<div>
    {{-- New note modal --}}
    <x-jet-dialog-modal wire:model="openModal">
        <x-slot name="title">
            @if($editModal)
                {{__('Edit phase')}} {{ $this->phase->title }} 
            @else
                {{__('Add new phase')}}
            @endif
        </x-slot>

        <x-slot name="content">
            @include('modal.modal_common_info')
            @can('assign-employee')
                <div class="mb-4">
                    <label for="project_id" class="block mb-2 text-sm font-medium text-gray-900">
                        {{__('Projects')}}
                    </label>
                    <select name="project_id" id="project_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" wire:model="project_id" @if($editModal) disabled @else required @endif>>
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
                    {{__('Description')}}
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
                    wire:loading.attr="disabled" wire:click="deletePhase({{ $this->phase->id }})">
                    {{ __('Delete') }}
                </x-jet-secondary-button>
                <x-jet-button class="ml-3" wire:click="editPhase({{ $this->phase->id }})">
                    {{__('Save phase')}}
                </x-jet-button>
            @else
                <x-jet-button class="ml-3" wire:click="savePhase">
                    {{__('Save phase')}}
                </x-jet-button>
            @endif
        </x-slot>
    </x-jet-dialog-modal>
</div>
