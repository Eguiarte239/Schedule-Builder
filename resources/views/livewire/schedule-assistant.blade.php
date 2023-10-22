<div>    
    {{-- New note modal --}}
    <x-jet-dialog-modal wire:model="openHelp">
        <x-slot name="title">
            {{__('How to use Schedule-Assistant')}}
        </x-slot>

        <x-slot name="content">
            <p class="mb-3 text-gray-500 dark:text-gray-800">
                {{__('The way to use the Schedule Assistant is straightforward. Simply navigate to the text box labeled Ask me what you need. Once here, just enter the query you require, though there are certain limitations at the moment.')}}
            </p>
            <p class="mb-3 text-gray-500 dark:text-gray-800">
                {{__('1: It must be as specific as possible. Due to the current state of the Schedule Assistant, it is necessary to be clear and specific about what you want. An example of this would be as follows:')}}
            </p>
            <ul class="mb-3 ml-8 text-gray-500 list-disc list-inside dark:text-gray-400">
                <li>
                    {{__('When wanting to obtain information about the phases of a project, you can enter Give me the titles of the phases associated with the Sample Project.')}}
                </li>
            </ul>
            <p class="mb-3 text-gray-500 dark:text-gray-800">
                {{__('2: Queries should be simple. For instance, if you want to retrieve information about a project (for example) then you will have to specify what you need. Otherwise, if you need to obtain information about multiple projects then you will have to be as specific and clear as possible. Here is an example:')}}
            </p>
            <ul class="mb-3 ml-8 text-gray-500 list-inside dark:text-gray-400">
                <li>
                    <i class="fa-solid fa-circle-check" style="color: #17ab50;"></i>
                    {{__('Give me the titles of the phases associated with the Sample project.')}}
                </li>
                <li>
                    <i class="fa-solid fa-circle-check" style="color: #17ab50;"></i>
                    {{__('Give me the titles of the currently existing projects.')}}
                </li>
                <li>
                    <i class="fa-solid fa-circle-check" style="color: #17ab50;"></i>
                    {{__('Give me the titles of the phases associated with the Sample and Second Sample projects and tell me which project each one belongs to.')}}
                </li>
            </ul>
            <p class="mb-3 text-gray-500 dark:text-gray-800">
                {{__('3: The query at the moment cannot involve projects, phases, and tasks all at once. If you wish to obtain information in this manner, it is advisable to make two or more separate queries.')}}
            </p>
            <p class="mb-3 text-gray-500 dark:text-gray-800">
                {{__('Since the current status of Schedule Assistant it may not work as expected sometimes. In this case please rephrase your question in a more specific and clearer way.')}}
            </p>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('openHelp')" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-jet-secondary-button>
        </x-slot>
    </x-jet-dialog-modal>
</div>
