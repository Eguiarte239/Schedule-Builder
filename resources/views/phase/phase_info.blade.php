@if ($__data['phases'])
    <div wire:sortable.item="{{ $phase->id }}" wire:key="phase-{{ $phase->id }}" class="mb-2 bg-neutral-50 rounded-lg shadow-md p-2 border dark:bg-slate-600">
        <div class="px-2" wire:sortable.handle>
            <div class="flex flex-row justify-between">
                <div class="font-bold text-xl dark:text-white mb-2" >
                    {{ $phase->title }}
                </div>
                <div>
                    @can('assign-employee')
                        <button wire:click="$emit('edit-phase-modal' ,{{ $phase->id }})">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                class="w-6 h-6 dark:stroke-white">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                        </button>
                    @endcan
                </div>
            </div>
            @if($this->getProgressPercentage($phase->id) > 0)   
                <div class="w-full bg-gray-200 rounded-full dark:bg-gray-700">
                    <div class="bg-blue-600 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full" style="width: {{ $this->getProgressPercentage($phase->id) }}"> {{ $this->getProgressPercentage($phase->id) }}</div>
                </div>
            @endif
            <p class="mb-3 text-lg text-gray-800 md:text-base dark:text-white">
                {{ $phase->content }}
                <br>
                @php
                    $class = $classMap[$phase->priority];
                @endphp
                <span class="{{ $class }} text-sm font-medium mr-2 px-2.5 py-0.5 rounded">
                    {{ __($phase->priority) }}
                </span> 
            </p>
        </div>
        <span
            class="flex flex-row bg-purple-200 rounded-lg px-3 py-1 text-sm font-semibold text-purple-800 mr-2 mt-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ $phase->start_task }}
        </span>
        <span
            class="flex flex-row bg-red-200 rounded-lg px-3 py-1 text-sm font-semibold text-red-800 mr-2 mt-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ $phase->end_task }}
        </span>
    </div>
@else
    <div class="col-span-4">
        <h1 class="text-2xl font-extrabold dark:text-white">{{ $phase->title }}</h1>
    </div>
@endif