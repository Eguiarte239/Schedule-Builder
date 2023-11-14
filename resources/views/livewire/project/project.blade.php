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
                        <div class="flex flex-col items-start px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 mb-4">
                            <label id="response" for="Ask DB" class="mb-2 ml-5 text-sm font-medium text-gray-900 dark:text-white">{{__($response)}}</label>
                            <div class="w-full flex items-center">
                                <textarea id="Ask DB" wire:model.defer="ask" rows="1" class="flex-grow mx-4 p-2.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="{{__('Ask me what you need')}}"></textarea>
                                <button wire:loading.attr="disabled" wire:click="askDB" class="tooltip inline-flex justify-center pb-1 mr-1 text-blue-600 rounded-full cursor-pointer hover:bg-blue-100 dark:text-blue-500 dark:hover:bg-gray-600" title="{{__('Ask Schedule Assistant')}}">
                                    <svg wire:loading.remove wire:target="askDB" class="w-5 h-5 rotate-90 rtl:-rotate-90" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 18 20">
                                        <path d="m17.914 18.594-8-18a1 1 0 0 0-1.828 0l-8 18a1 1 0 0 0 1.157 1.376L8 18.281V9a1 1 0 0 1 2 0v9.281l6.758 1.689a1 1 0 0 0 1.156-1.376Z"/>
                                    </svg>
                                    <span class="sr-only">{{__('Ask Schedule Assistant')}}</span>
                                </button>
                                <svg wire:loading wire:target="askDB" aria-hidden="true" class="w-8 h-8 mr-2 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor" />
                                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill" />
                                </svg>
                            </div>
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