<?php

namespace App\Http\Livewire;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class ParentController extends Component
{
    use AuthorizesRequests;

    protected $middleware = ['web', 'livewire:protect'];

    public $openModal = false;
    public $editModal = false;

    public $title;
    public $start_date;
    public $end_date;
    public $hour_estimate;
    public $content;
    public $priority;

    public $search = '';

    public $classMap = [
        'Low' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        'Medium' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'High' => 'bg-yellow-100 text-yellow-800 dark:bg-orange-900 dark:text-yellow-300',
        'Urgent' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
    ];

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function setGeneralValues($instance){
        $this->title = $instance->title;
        $this->start_date = $instance->start_date;
        $this->end_date = $instance->end_date;
        $this->hour_estimate = $instance->hour_estimate;
        $this->content = $instance->content;
        $this->priority = $instance->priority;
    }

    public function resetGeneralValues(){
        $this->title = "";
        $this->start_date = now()->format('Y-m-d');
        $this->end_date = "";
        $this->hour_estimate = "";
        $this->content = "";
        $this->priority = "";
    }

    public function newInstance(){
        $this->editModal = false;
        $this->openModal = true;
    }

    //instance variable takes the instance type that was sended (project, phase or task)
    public function saveInstanceGeneralValues($instance){
        $instance->title = $this->title;
        $instance->start_date = $this->start_date;
        $instance->end_date = $this->end_date;
        $instance->hour_estimate = $this->hour_estimate;
        $instance->content = $this->content;
        $instance->priority = $this->priority;
    }

    public function editInstanceGeneralValues($instance){
        $instance->title = $this->title;
        $instance->hour_estimate = $this->hour_estimate;
        $instance->content = $this->content;
        $instance->priority = $this->priority;
    }

}
