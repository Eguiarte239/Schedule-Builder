<div wire:sortable.item="{{ $task->id }}" wire:key="task-{{ $task->id }}" class="mb-2 bg-white rounded-lg shadow-md p-2 border dark:bg-slate-600">
    <div class="px-2" wire:sortable.handle>
        <div class="flex flex-row justify-between">
            <div class="font-bold text-xl dark:text-white mb-2" >
                {{ $task->title }}
            </div> 
            <div>
                @can('assign-employee')
                        @if ($task->user_id === auth()->user()->id)
                            <button wire:click="editTaskNote({{ $task->id }})">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                class="w-6 h-6 dark:stroke-white">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </button>
                        @endif
                @endcan
                @if(($task->predecessor_task == null || $task->predecessorTask->is_finished == true) && (auth()->user()->id == $task->user_id_assigned || auth()->user()->id == $task->user_id))
                    <button wire:click="finishTask({{ $task->id }})">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 {{ $task->is_finished ? 'stroke-emerald-300' : 'stroke-red-300' }}">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 019 9v.375M10.125 2.25A3.375 3.375 0 0113.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 013.375 3.375M9 15l2.25 2.25L15 12" />
                        </svg> 
                    </button>                                                         
                @endif
            </div>
        </div>
        <p class="mb-3 text-lg text-gray-500 md:text-base dark:text-white">
            {{ $task->content }}
            <br>
                Responsible: {{ $task->user->name }}
            <br>
            @if($task->predecessor_task != null)
                Predecessor task: {{ $task->predecessor_task }}
            @else
                Predecessor task: NA
            @endif
            <br>
            <span class="bg-blue-100 text-blue-800 text-ms font-medium inline-flex px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-blue-400 border border-blue-400">
                <svg aria-hidden="true" class="w-4 h-6 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                Estimated hours: {{ $task->hour_estimate }}
            </span>
            <br>
            @if ($task->priority == 'Low')
                <span class="bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                    {{ $task->priority }}
                </span>
            @elseif ($task->priority == 'Medium')
                <span class="bg-green-100 text-green-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                    {{ $task->priority }}
                </span>
            @elseif ($task->priority == 'High')
                <span class="bg-yellow-100 text-yellow-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-orange-900 dark:text-yellow-300">
                    {{ $task->priority }}
                </span>
            @else
                <span class="bg-red-100 text-red-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                    {{ $task->priority }}
                </span>
            @endif
        </p>
    </div>
    <span
        class="flex flex-row bg-purple-200 rounded-lg px-3 py-1 text-sm font-semibold text-purple-800 mr-2 mt-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ $task->start_task }}
    </span>
    <span
        class="flex flex-row bg-red-200 rounded-lg px-3 py-1 text-sm font-semibold text-red-800 mr-2 mt-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ $task->end_task }}
    </span>
</div>