<div class="ml-3 relative">
    <x-jet-dropdown align="left" width="48">
        <x-slot name="trigger">
            <span class="inline-flex rounded-md">
                <button type="button" class="inline-flex items-center px-3 py-2 border border-neutral-200 text-sm leading-4 font-medium rounded-md text-gray-500 bg-neutral-50 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition">
                    {{__('Language')}}
                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
            </span>
        </x-slot>

        <x-slot name="content">
            <!-- Account Management -->
            <div class="block px-4 py-2 text-xs text-gray-400">
                {{ __('Change language') }}
            </div>

            <x-jet-dropdown-link href="{{ route('locale', 'en') }}">
                {{ __('EN') }}
            </x-jet-dropdown-link>

            <x-jet-dropdown-link href="{{ route('locale', 'es') }}">
                {{ __('ES') }}
            </x-jet-dropdown-link>

            <x-jet-dropdown-link href="{{ route('locale', 'fr') }}">
                {{ __('FR') }}
            </x-jet-dropdown-link>
        </x-slot>
    </x-jet-dropdown>
</div>