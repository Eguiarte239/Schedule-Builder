<?php

namespace App\Http\Livewire;

use App\Models\Phase;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TaskList extends Component
{
    use AuthorizesRequests, WithPagination;

    protected $middleware = ['web', 'livewire:protect'];

    public $task;
    public $openModal = false;
    public $editTask = false;

    public $title;
    public $start_time;
    public $end_time;
    public $hour_estimate;
    public $content;
    public $priority;
    public $assigned_to_phase;
    public $assigned_to_task;

    public $search = '';


    protected $listeners = ['refreshComponent' => '$refresh'];

    protected function rules()
    {
        $rules = [
            "title" => ['required', 'string', 'max:255'],
            "start_time" => ['required', 'date', 'after_or_equal:today'],
            "end_time" => ['required', 'date', 'after_or_equal:start_time'],
            "hour_estimate" => ['required', 'integer', 'between:0,100.99'],
            "content" => ['required', 'string', 'max:500'],
            "priority" => ['required', 'in:Low,Medium,High,Urgent'],
            'assigned_to_phase' => 'required',
            'assigned_to_phase.*' => 'required|exists:phases,id',
            'assigned_to_task' => 'required',
            'assigned_to_task.*' => 'required|exists:users,id',
        ];
        
        return $rules;
    }

    protected $rules = [];

    public function mount(){
        $this->rules = $this->rules();
    }

    public function getTasksProperty()
    {
        return Task::where(function ($query) {
            $query->where('user_id', Auth::user()->id)
                    ->orWhereJsonContains('assigned_to_task', Auth::user()->id);
        })
        ->where('title', 'like', '%'.$this->search.'%')
        ->orderBy('order_position', 'asc')
        ->get();
    }

    public function render()
    {
        $tasks = $this->tasks;
        $phases = Phase::where('user_id', Auth::user()->id)->get();
        $users = User::all();
        return view('livewire.task', ['tasks' => $tasks, 'phases' => $phases, 'users' => $users])->layout('layouts.app');
    }

    public function setValues($id)
    {
        $this->task = Task::find($id);
        $this->title = $this->task->title;
        $this->start_time = $this->task->start_time;
        $this->end_time = $this->task->end_time;
        $this->hour_estimate = $this->task->hour_estimate;
        $this->content = $this->task->content;
        $this->priority = $this->task->priority;
    }

    public function resetValues()
    {
        $this->task = new Task();
        $this->title = "";
        $this->start_time = now()->format('Y-m-d');
        $this->end_time = "";
        $this->hour_estimate = "";
        $this->content = "";
        $this->priority = null;
    }

    public function newTask()
    {
        $this->resetValues();
        $this->resetValidation();
        $this->editTask = false;
        $this->openModal = true;
    }

    public function editTaskNote($id)
    {
        $this->setValues($id);
        $this->editTask = true;
        $this->openModal = true;
    }

    public function saveTask()
    {
        $this->validate();

        if(isset($this->assigned_to_task)){
            User::find(intval($this->assigned_to_task))->assignRole('employee-user');
        }

        $this->task = new Task();
        $this->task->user_id = Auth::user()->id;
        $this->task->title = $this->title;
        $this->task->start_time = $this->start_time;
        $this->task->end_time = $this->end_time;
        $this->task->hour_estimate = $this->hour_estimate;
        $this->task->content = $this->content;
        $this->task->priority = $this->priority;
        $this->task->assigned_to_phase = $this->assigned_to_phase;
        $this->task->assigned_to_task = $this->assigned_to_task;
        $this->task->save();
        $this->openModal = false;
        return redirect()->route('phases');
    }

    public function editTask($id)
    {
        $this->validate();

        $this->task = Task::find($id);
        $this->task->user_id = Auth::user()->id;
        $this->task->title = $this->title;
        $this->task->start_time = $this->start_time;
        $this->task->end_time = $this->end_time;
        $this->task->hour_estimate = $this->hour_estimate;
        $this->task->content = $this->content;
        $this->task->priority = $this->priority;
        $this->task->save();
        $this->openModal = false;
    }

    public function deleteTask($id)
    {
        Task::destroy($id);
        $this->openModal = false;
        return redirect()->route('phases');
    }

    public function updateTaskOrder($items)
    {
        foreach($items as $item)
        {
            $task = Task::find($item['value']);
            $task->order_position = $item['order'];
            $task->save();
        }
    }
}
