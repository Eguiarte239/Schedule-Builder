<div>
    <div class="py-12 dark:bg-slate-800">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-400 overflow-hidden shadow-xl sm:rounded-lg p-6">
                @can('assign-employee')
                    <div>
                        <x-jet-button wire:click="$emit('new-task-modal')" class="mb-4">
                            + New task
                        </x-jet-button>
                    </div>
                @endcan
                
                @include('livewire.search_bar', ['search' => $search, 'tasks' => true])

                <div class="grid gap-2 md:grid-cols-4" wire:sortable="updateTaskOrder">
                    @foreach ($projects as $project)
                        @php
                            $hasTasks = false;
                        @endphp
                        @foreach ($project->phase as $phase)
                            @if($phase->task->isNotEmpty())
                                @if (!$hasTasks)
                                    @include('project.project_info', ['project' => $project, 'projects' => false])
                                    @php
                                        $hasTasks = true;
                                    @endphp
                                @endif
                                @include('phase.phase_info', ['phase' => $phase, 'phases' => false])
                                @foreach ($phase->task as $task)
                                    @include('task.task_info', ['task' => $task])
                                @endforeach
                            @endif
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @livewire('task-modal')

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

            Livewire.on('predecessor', function(message, route) {
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

            Livewire.on('new-task-alert', function(message) {
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
