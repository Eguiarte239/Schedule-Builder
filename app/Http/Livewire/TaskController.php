<?php

namespace App\Http\Livewire;

use App\Models\Phase;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TaskController extends Component
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
    public $project_id;
    public $phase_id;
    public $user_id_assigned;
    public $predecessor_task;

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
            'project_id' => 'required',
            'project_id.*' => 'required|exists:projects,id',
            'phase_id' => 'required',
            'phase_id.*' => 'required|exists:phases,id',
            'user_id_assigned' => 'required',
            'user_id_assigned.*' => 'required|exists:users,id',
            'predecessor_task' => 'nullable',
            'predecessor_task.*' => 'nullable|exists:tasks,id',
        ];
        
        return $rules;
    }

    protected $rules = [];

    public function mount(){
        $this->rules = $this->rules();
    }

    public function render()
    {
        $user = Auth::user();

        $projectIds = Phase::where('user_id', $user->id)
                    ->orWhereHas('task', function ($query) use ($user) {
                        $query->where('user_id_assigned', 'LIKE', '%'.$user->id.'%');
                    })
                    ->groupBy('project_id')
                    ->pluck('project_id');

        $projects = Project::whereIn('id', $projectIds)->with(['phase.task' => function($query) use ($user) {
                $query->where('user_id', $user->id)->orWhere('user_id_assigned', 'LIKE', '%'.$user->id.'%')
                    ->orderBy('order_position', 'asc');
            }
        ])->get();

        $users = User::all();
        $predecessorTasks = Task::all();

        return view('livewire.task', ['projects' => $projects, 'users' => $users, 'predecessorTasks' => $predecessorTasks])->layout('layouts.app');
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

        if(isset($this->user_id_assigned)){
            User::find(intval($this->user_id_assigned))->assignRole('employee-user');
        }

        $this->task = new Task();
        $this->task->user_id = Auth::user()->id;
        $this->task->title = $this->title;
        $this->task->start_time = $this->start_time;
        $this->task->end_time = $this->end_time;
        $this->task->hour_estimate = $this->hour_estimate;
        $this->task->content = $this->content;
        $this->task->priority = $this->priority;
        $this->task->project_id = $this->project_id;
        $this->task->phase_id = $this->phase_id;
        $this->task->user_id_assigned = $this->user_id_assigned;
        $this->task->predecessor_task = $this->predecessor_task;
        $this->task->save();
        $this->openModal = false;
        return redirect()->route('tasks');
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
        $this->task->project_id = $this->project_id;
        $this->task->phase_id = $this->phase_id;
        $this->task->user_id_assigned = $this->user_id_assigned;
        $this->task->predecessor_task = $this->predecessor_task;
        $this->task->save();
        $this->openModal = false;
    }

    public function deleteTask($id)
    {
        $task = Task::find($id);
        $hasPredecessor = Task::where('predecessor_task', $id)->exists();
        if ($hasPredecessor)
        {
            $this->emit('alert', "You can't delete a task which is a predecessor of another", route('tasks'));
        } 
        else
        {
            $task->destroy($id);
            $this->openModal = false;
            return redirect()->route('tasks');
        }
    }

    public function finishTask($id)
    {
        $this->task = Task::find($id);
        $isPredecessor = Task::where('predecessor_task', $this->task->id)->exists();
        $dependentTasks = Task::where('predecessor_task', $this->task->id)->get();
        $allSuccessorsMarked = true;
        foreach ($dependentTasks as $dependentTask) {
            if ($dependentTask->is_finished == false) {
                $allSuccessorsMarked = false;
                break;
            }
        }
        if($isPredecessor == true && $allSuccessorsMarked == true){
            $this->emit('predecessor', "You can't unmark a task which is predecessor of another until you unmark that one", route('tasks'));
        }
        else{
            $this->task->is_finished = !$this->task->is_finished;
            $this->task->save();
        }
        //condicional
        //$taskLeader = User::find($this->task->user_id);
        //Mail::to($taskLeader->email)->queue(new TaskReminder);
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
