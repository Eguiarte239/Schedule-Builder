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

class TaskModal extends Component
{
    use AuthorizesRequests, WithPagination;

    protected $middleware = ['web', 'livewire:protect'];
    
    public $task;
    public $openModal = false;
    public $editModal = false;

    public $title;
    public $start_date;
    public $end_date;
    public $content;
    public $priority;
    public $project_id;
    public $phase_id;
    public $user_id_assigned;
    public $predecessor_task;

    protected $listeners = ['refresh' => '$refresh',
                            'new-task-modal' => 'newTask',
                            'edit-task-modal' => 'editTaskNote',
                        ];

    protected function rules()
    {
        $rules = [
            "title" => ['required', 'string', 'max:255'],
            "start_date" => ['after_or_equal:'.Task::phaseStartDate($this->phase_id), 'before_or_equal:'.Task::phaseEndDate($this->phase_id)],
            "end_date" => ['required', 'date', 'after_or_equal:start_date', 'before_or_equal:'.Task::phaseEndDate($this->phase_id)],
            "content" => ['required', 'string', 'max:500'],
            "priority" => ['required', 'in:Low,Medium,High,Urgent'],
            'project_id' => 'required',
            'project_id.*' => 'required|exists:projects,id',
            'phase_id' => 'required',
            'phase_id.*' => 'required|exists:phases,id',
            'user_id_assigned' => 'required',
            'user_id_assigned.*' => 'required|exists:users,id',
            'predecessor_task' => 'required',
            'predecessor_task.*' => 'required|nullable|exists:tasks,id|in:No aplica',
        ];
        
        return $rules;
    }

    protected $rules = [];

    public function mount(){
        $this->rules = $this->rules();
    }

    public function render()
    {
        $projects = Project::where('leader_id', Auth::user()->id)->get();
        $users = User::all();
        $predecessorTasks = Task::all();
        
        return view('livewire.task.task-modal', ['projects' => $projects, 'users' => $users, 'predecessorTasks' => $predecessorTasks]);
    }

    public function setValues($id)
    {
        $this->task = Task::find($id);
        $this->title = $this->task->title;
        $this->start_date = $this->task->start_date;
        $this->end_date = $this->task->end_date;
        $this->content = $this->task->content;
        $this->priority = $this->task->priority;
        $this->project_id = $this->task->project_id;
        $this->phase_id = $this->task->phase_id;
        $this->predecessor_task = $this->task->predecessor_task;
        $this->user_id_assigned = $this->task->user_id_assigned;
        $this->predecessor_task = $this->task->predecessor_task;
    }

    public function resetValues()
    {
        $this->task = new Task();
        $this->title = "";
        $this->start_date = now()->format('Y-m-d');
        $this->end_date = "";
        $this->content = "";
        $this->priority = "";
        $this->project_id = null;
        $this->phase_id = null;
        $this->predecessor_task = "";
        $this->user_id_assigned = "";
        $this->predecessor_task = "";
    }

    public function newTask()
    {
        $this->resetValues();
        $this->resetValidation();
        $this->editModal = false;
        $this->openModal = true;
        $this->emit('new-task-alert', [
            'title' => trans("Be careful"),
            'message' => trans("Once you save your task, its start and end date won't be able to be changed")
        ]);
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
        $this->task->start_date = $this->start_date;
        $this->task->end_date = $this->end_date;
        $this->task->content = $this->content;
        $this->task->priority = $this->priority;
        $this->task->project_id = $this->project_id;
        $this->task->phase_id = $this->phase_id;
        $this->task->user_id_assigned = $this->user_id_assigned;
        if($this->predecessor_task == 'NA'){
            $this->task->predecessor_task = null;
        }
        else{
            $this->task->predecessor_task = $this->predecessor_task;
        }
        $this->task->save();
        $this->openModal = false;
        $this->emit('refresh');
    }

    public function editTaskNote($id)
    {
        $this->setValues($id);
        $this->editModal = true;
        $this->openModal = true;
    }

    public function editTask($id)
    {
        $this->validate();
        $this->task = Task::find($id);
        $this->task->user_id = Auth::user()->id;
        $this->task->title = $this->title;
        $this->task->content = $this->content;
        $this->task->priority = $this->priority;
        $this->task->project_id = $this->project_id;
        $this->task->phase_id = $this->phase_id;
        $this->task->user_id_assigned = $this->user_id_assigned;
        if($this->predecessor_task == 'NA'){
            $this->task->predecessor_task = null;
        }
        else{
            $this->task->predecessor_task = $this->predecessor_task;
        }
        $this->task->update();
        $this->openModal = false;
        $this->emit('refresh');
    }

    public function deleteTask($id)
    {
        $hasPredecessor = Task::where('predecessor_task', $id)->exists();
        if ($hasPredecessor)
        {
            $this->emit('alert', [
                'message' => trans("You can't delete a task which is a predecessor of another"), 
                'route' => route('tasks')
            ]);
        } 
        else
        {
            Task::destroy($id);
            $this->openModal = false;
            return redirect()->route('tasks');
        }
    }
}
