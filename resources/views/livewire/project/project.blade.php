<div>
    <div class="py-12 dark:bg-slate-800">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-400 overflow-hidden shadow-xl sm:rounded-lg p-6">
                @can('assign-leader')
                    <div>
                        <x-jet-button wire:click="$emit('new-project-modal')" class="mb-4">
                            + New project
                        </x-jet-button>
                    </div>
                    
                    <div>
                        <div>
                            <label for="Ask DB" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{$response}}</label>
                            <input wire:model.defer="ask" type="text" id="Ask DB" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Ask me what you need" required>
                        </div>
                        <x-jet-input-error for="ask"></x-jet-input-error>
                        
                        <x-jet-button wire:click="askDB" class="mt-4 mb-4">
                            Ask Schedule Assistant
                        </x-jet-button>
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