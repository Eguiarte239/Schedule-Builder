<div>
    <div class="py-12 dark:bg-slate-800">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-400 overflow-hidden shadow-xl sm:rounded-lg p-6">
                @can('assign-employee')
                    <div>
                        <x-jet-button wire:click="$emit('new-phase-modal')" class="mb-4">
                            + New phase
                        </x-jet-button>
                    </div>
                @endcan
                
                @include('livewire.search_bar', ['search' => $search, 'tasks' => false])

                <div class="grid gap-2 md:grid-cols-4" wire:sortable="updateTaskOrder">
                    @foreach ($projects as $project)
                        @if (isset($groupedPhases[$project->id]) && $groupedPhases[$project->id]->isNotEmpty())
                            @include('project.project_info', ['project' => $project, 'projects' => false])
                            @foreach ($groupedPhases[$project->id] as $phase)
                                @include('phase.phase_info', ['phase' => $phase, 'phases' => true])
                            @endforeach
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @livewire('phase-modal')

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
            
            Livewire.on('new-phase-alert', function(message) {
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
