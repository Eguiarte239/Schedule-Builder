<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ScheduleAssistant extends Component
{
    public $openHelp = false;

    protected $listeners = ['refresh' => '$refresh',
                            'open-help' => 'open',  
                        ];
    public function render()
    {
        return view('livewire.schedule-assistant');
    }

    public function open(){
        $this->openHelp = true;
    }
}
