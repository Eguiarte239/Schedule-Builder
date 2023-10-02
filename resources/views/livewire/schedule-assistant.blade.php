<div>    
    {{-- New note modal --}}
    <x-jet-dialog-modal wire:model="openHelp">
        <x-slot name="title">
            How to use the Schedule Assistant
        </x-slot>

        <x-slot name="content">
            <p class="mb-3 text-gray-500 dark:text-gray-800">
                The way to use the Schedule Assistant is straightforward. Simply navigate to the text box labeled "Ask me what you need." Once here, just enter the query you require, though there are certain limitations at the moment.
            </p>
            <p class="mb-3 text-gray-500 dark:text-gray-800">
                1: It must be as specific as possible. Due to the current state of the Schedule Assistant, it is necessary to be clear and specific about what you want. An example of this would be as follows:
            </p>
            <ul class="mb-3 ml-8 text-gray-500 list-disc list-inside dark:text-gray-400">
                <li>
                    When wanting to obtain information about the phases of a project, you can enter "Give me the titles of the phases associated with the 'Sample Project.'"
                </li>
            </ul>
            <p class="mb-3 text-gray-500 dark:text-gray-800">
                2: Queries should be simple. For instance, if you want to retrieve information about a project, you will only be able to obtain data related to that project if your query involves phases or tasks. Otherwise, if you want to get information about multiple projects, you won't be able to get data that involves the phases or tasks of those projects. Here's an example:
            </p>
            <ul class="mb-3 ml-8 text-gray-500 list-inside dark:text-gray-400">
                <li>
                    <i class="fa-solid fa-circle-check" style="color: #17ab50;"></i>
                    Give me the titles of the phases associated with the "Sample" project.
                </li>
                <li>
                    <i class="fa-solid fa-circle-check" style="color: #17ab50;"></i>
                    Give me the titles of the currently existing projects.
                </li>
                <li>
                    <i class="fa-solid fa-circle-xmark" style="color: #e81111;"></i>
                    Give me the titles of the phases associated with the "Sample" and "Second Sample" projects.
                </li>
            </ul>
            <p class="mb-3 text-gray-500 dark:text-gray-800">
                3: The query at the moment cannot involve projects, phases, and tasks all at once. If you wish to obtain information in this manner, it is advisable to make two or more separate queries.
            </p>
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$toggle('openHelp')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>
        </x-slot>
    </x-jet-dialog-modal>
</div>
