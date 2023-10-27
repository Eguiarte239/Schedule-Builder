<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Schedule-Builder') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @include('sweetalert::alert')

        <!-- Styles -->
        @livewireStyles
        <script src="to/prefers-dark.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    </head>
    <body class="font-sans antialiased mode-dark">
        <x-jet-banner />

        <div class="min-h-screen bg-gray-100 dark:bg-slate-800">
            
            @livewire('navigation-menu')

            <!-- Page Content -->
            <main>
                @livewire('schedule-assistant')
                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        @livewireScripts
        
        @stack('js')
    </body>

</html>
