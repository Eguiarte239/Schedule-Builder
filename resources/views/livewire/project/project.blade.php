<div>
    <div class="py-12 dark:bg-slate-800">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-400 overflow-hidden shadow-xl sm:rounded-lg p-6">
                @can('assign-leader')
                    <div>
                        <x-jet-button wire:click="$emit('new-project-modal')" class="mb-4">
                            + {{__('New project')}}
                        </x-jet-button>
                    </div>
                    
                    <div>
                        <div class="flex items-center px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-700">
                            <textarea id="chat" rows="1" class="block mx-4 p-2.5 w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="{{__('Ask me what you need')}}"></textarea>
                            <button wire:click="askDB" class="tooltip inline-flex justify-center p-2 text-blue-600 rounded-full cursor-pointer hover:bg-blue-100 dark:text-blue-500 dark:hover:bg-gray-600" title="{{__('Ask Schedule Assistant')}}">
                                <svg class="w-5 h-5 rotate-90 rtl:-rotate-90" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                    <path d="m17.914 18.594-8-18a1 1 0 0 0-1.828 0l-8 18a1 1 0 0 0 1.157 1.376L8 18.281V9a1 1 0 0 1 2 0v9.281l6.758 1.689a1 1 0 0 0 1.156-1.376Z"/>
                                </svg>
                                <span class="sr-only">{{__('Ask Schedule Assistant')}}</span>
                            </button>
                        </div>
                        <div>
                            <label for="Ask DB" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{__($response)}}</label>
                        </div>
                        <x-jet-input-error for="ask"></x-jet-input-error>
                    </div>
                @endcan
                
                @include('livewire.search_bar', ['search' => $search, 'tasks' => false])

                <div class="grid gap-2 md:grid-cols-4" wire:sortable="updateTaskOrder">
                    @foreach ($projects as $project)
                        @include('project.project_info', ['project' => $project, 'projects' => true])
                    @endforeach

                </div>
            </div>
        </div>
    </div>

    @livewire('project-modal')

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            const Swal = window.Swal;
            Livewire.on('alert', function(data) {
                Swal.fire({
                    imageUrl: 'https://media.tenor.com/2KK38LekJu0AAAAC/doki-doki-literature-club-mad.gif',
                    imageWidth: 250,
                    imageHeight: 250,
                    title: 'Oops...',
                    text: data.message,
                    timer: 5000,
                }).then(function () {
                    window.location.href = data.route;
                });
            })
            
            Livewire.on('new-project-alert', function(data) {
                Swal.fire({
                    imageUrl: 'https://stickershop.line-scdn.net/stickershop/v1/product/7458249/LINEStorePC/main.png',
                    imageWidth: 250,
                    imageHeight: 250,
                    title: data.title,
                    text: data.message,
                })
            })
        </script>
    @endpush
</div>