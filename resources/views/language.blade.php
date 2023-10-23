<div class="ml-3 relative" x-data="{ open: false }">
    <div class="hidden lg:flex lg:items-center lg:ml-6">
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

    <div class="-mr-2 flex items-center lg:hidden">
        <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-white hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition dark:hover:text-white dark:hover:bg-gray-900">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden lg:hidden">

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="flex items-center px-4">
                <div>
                    <div class="font-medium text-base text-gray-800 dark:text-white">{{ __('Change language') }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Account Management -->
                <x-jet-responsive-nav-link href="{{ route('locale', 'en') }}" class="dark:text-white">
                    {{ __('EN') }}
                </x-jet-responsive-nav-link>

                <x-jet-responsive-nav-link href="{{ route('locale', 'es') }}" class="dark:text-white">
                    {{ __('ES') }}
                </x-jet-responsive-nav-link>

                <x-jet-responsive-nav-link href="{{ route('locale', 'fr') }}" class="dark:text-white">
                    {{ __('FR') }}
                </x-jet-responsive-nav-link>
            </div>
        </div>
    </div>
</div>