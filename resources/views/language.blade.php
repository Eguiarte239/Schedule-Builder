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
                    <span class="fi fi-us mr-2"></span>
                    {{ __('English') }}
                </x-jet-dropdown-link>
        
                <x-jet-dropdown-link href="{{ route('locale', 'es') }}">
                    <span class="fi fi-mx mr-2"></span>
                    {{ __('Español') }}
                </x-jet-dropdown-link>
        
                <x-jet-dropdown-link href="{{ route('locale', 'fr') }}">
                    <span class="fi fi-fr mr-2"></span>
                    {{ __('Français') }}
                </x-jet-dropdown-link>

                <x-jet-dropdown-link href="{{ route('locale', 'it') }}">
                    <span class="fi fi-it mr-2"></span>
                    {{ __('Italiano') }}
                </x-jet-dropdown-link>

                <x-jet-dropdown-link href="{{ route('locale', 'pt') }}">
                    <span class="fi fi-br mr-2"></span>
                    {{ __('Português') }}
                </x-jet-dropdown-link>

                <x-jet-dropdown-link href="{{ route('locale', 'de') }}">
                    <span class="fi fi-de mr-2"></span>
                    {{ __('Deutsch') }}
                </x-jet-dropdown-link>

                <x-jet-dropdown-link href="{{ route('locale', 'ja') }}">
                    <span class="fi fi-jp mr-2"></span>
                    {{ __('日本語 ') }}
                </x-jet-dropdown-link>

                <x-jet-dropdown-link href="{{ route('locale', 'zh') }}">
                    <span class="fi fi-cn mr-2"></span>
                    {{ __('中文简体') }}
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


        <div :class="{'block': open, 'hidden': ! open}" class="hidden lg:hidden">
            <!-- Account Language -->
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="flex items-center px-4">
                    <div>
                        <div class="font-medium text-base text-gray-800 dark:text-white">{{__('Language')}}</div>
                    </div>
                </div>
                <x-jet-responsive-nav-link href="{{ route('locale', 'en') }}" :active="app()->isLocale('en')" x-bind:class="{'dark:text-white':{{ !app()->isLocale('en') }}}">
                    <span class="fi fi-us mr-2"></span>
                    
                    {{ __('English') }}
                </x-jet-responsive-nav-link>
                
                <x-jet-responsive-nav-link href="{{ route('locale', 'es') }}" :active="app()->isLocale('es')" x-bind:class="{'dark:text-white':{{ !app()->isLocale('es') }}}">
                    <span class="fi fi-mx mr-2"></span>
                    
                    {{ __('Español') }}
                </x-jet-responsive-nav-link>
                
                <x-jet-responsive-nav-link href="{{ route('locale', 'fr') }}" :active="app()->isLocale('fr')" x-bind:class="{'dark:text-white':{{ !app()->isLocale('fr') }}}">
                    <span class="fi fi-fr mr-2"></span>
                    
                    {{ __('Français') }}
                </x-jet-responsive-nav-link>

                <x-jet-responsive-nav-link href="{{ route('locale', 'it') }}" :active="app()->isLocale('it')" x-bind:class="{'dark:text-white':{{ !app()->isLocale('it') }}}">
                    <span class="fi fi-it mr-2"></span>
                    
                    {{ __('Italiano') }}
                </x-jet-responsive-nav-link>

                <x-jet-responsive-nav-link href="{{ route('locale', 'pt') }}" :active="app()->isLocale('pt')" x-bind:class="{'dark:text-white':{{ !app()->isLocale('pt') }}}">
                    <span class="fi fi-br mr-2"></span>
                    
                    {{ __('Português') }}
                </x-jet-responsive-nav-link>

                <x-jet-responsive-nav-link href="{{ route('locale', 'de') }}" :active="app()->isLocale('de')" x-bind:class="{'dark:text-white':{{ !app()->isLocale('de') }}}">
                    <span class="fi fi-de mr-2"></span>
                    
                    {{ __('Deutsch') }}
                </x-jet-responsive-nav-link>

                <x-jet-responsive-nav-link href="{{ route('locale', 'ja') }}" :active="app()->isLocale('ja')" x-bind:class="{'dark:text-white':{{ !app()->isLocale('ja') }}}">
                    <span class="fi fi-jp mr-2"></span>
                    
                    {{ __('日本語') }}
                </x-jet-responsive-nav-link>

                <x-jet-responsive-nav-link href="{{ route('locale', 'zh') }}" :active="app()->isLocale('zh')" x-bind:class="{'dark:text-white':{{ !app()->isLocale('zh') }}}">
                    <span class="fi fi-cn mr-2"></span>
                    
                    {{ __('中文简体') }}
                </x-jet-responsive-nav-link>
            </div>
        </div>

</div>