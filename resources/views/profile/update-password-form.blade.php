@can('change-password')
    <x-jet-form-section submit="updatePassword">
        <x-slot name="title">
            <div class="dark:text-gray-100">
                {{ __('Update Password') }}
            </div>
        </x-slot>

        <x-slot name="description">
            <div class="dark:text-gray-400">
                {{ __('Ensure your account is using a long, random password to stay secure.') }}
            </div>
        </x-slot>

        <x-slot name="form">
            <div class="col-span-6 sm:col-span-4">
                <x-jet-label for="current_password" value="{{ __('Current Password') }}" />
                <x-jet-input id="current_password" type="password" class="mt-1 block w-full" wire:model.defer="state.current_password" autocomplete="current-password" />
                <x-jet-input-error for="current_password" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <x-jet-label for="password" value="{{ __('New Password') }}" />
                <x-jet-input id="password" type="password" class="mt-1 block w-full" wire:model.defer="state.password" autocomplete="new-password" />
                <x-jet-input-error for="password" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <x-jet-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-jet-input id="password_confirmation" type="password" class="mt-1 block w-full" wire:model.defer="state.password_confirmation" autocomplete="new-password" />
                <x-jet-input-error for="password_confirmation" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="actions">
            <x-jet-action-message class="mr-3" on="saved">
                {{ __('Saved.') }}
            </x-jet-action-message>

            <x-jet-button>
                {{ __('Save') }}
            </x-jet-button>
        </x-slot>
    </x-jet-form-section>
@else
    <x-jet-action-section>
        <x-slot name="title">
            <div class="dark:text-gray-100">
                {{ __('Update Password') }}
            </div>
        </x-slot>

        <x-slot name="description">
            <div class="dark:text-gray-400">
                {{ __('Ensure your account is using a long, random password to stay secure.') }}
            </div>
        </x-slot>

        <x-slot name="content">
            <div class="max-w-xl text-sm text-gray-600 dark:text-gray-200">
                {{ __('This option is only available if you registered using the web app.') }}
            </div>
        </x-slot>
    </x-jet-action-section>
@endcan