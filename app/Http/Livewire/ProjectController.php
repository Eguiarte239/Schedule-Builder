<?php

namespace App\Http\Livewire;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProjectController extends Component
{
    use AuthorizesRequests;

    protected $middleware = ['web', 'livewire:protect'];

    public $project;
    public $openModal = false;
    public $editProject = false;

    public $title;
    public $start_time;
    public $end_time;
    public $hour_estimate;
    public $content;
    public $priority;
    public $leader_id_assigned;

    public $search = '';

    public $classMap = [
        'Low' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        'Medium' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'High' => 'bg-yellow-100 text-yellow-800 dark:bg-orange-900 dark:text-yellow-300',
        'Urgent' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
    ];

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
            'leader_id_assigned' => 'required',
            'leader_id_assigned.*' => 'required|exists:users,id',
        ];
        
        return $rules;
    }

    protected $rules = [];

    public function mount(){
        $this->rules = $this->rules();
    }

    public function getProjectsProperty()
    {
        return Project::where(function ($query) {
            $query->where('user_id', Auth::user()->id)
                  ->orWhere('leader_id_assigned', 'LIKE', '%'.Auth::user()->id.'%');
        })
        ->where('title', 'like', '%'.$this->search.'%')
        ->orderBy('order_position', 'asc')
        ->get();        
    }

    public function render()
    {
        $projects = $this->projects;
        $users = User::all();
        return view('livewire.project', ['projects' => $projects, 'users' => $users])->layout('layouts.app');
    }

    public function setValues($id)
    {
        $this->project = Project::find($id);
        $this->title = $this->project->title;
        $this->start_time = $this->project->start_time;
        $this->end_time = $this->project->end_time;
        $this->hour_estimate = $this->project->hour_estimate;
        $this->content = $this->project->content;
        $this->priority = $this->project->priority;
    }

    public function resetValues()
    {
        $this->project = new Project();
        $this->title = "";
        $this->start_time = now()->format('Y-m-d');
        $this->end_time = "";
        $this->hour_estimate = "";
        $this->content = "";
        $this->priority = null;
    }

    public function newNote()
    {
        $this->resetValues();
        $this->resetValidation();
        $this->editProject = false;
        $this->openModal = true;
    }

    public function editProjectNote($id)
    {
        $this->setValues($id);
        $this->editProject = true;
        $this->openModal = true;
    }

    public function saveProject()
    {
        if(isset($this->leader_id_assigned)){
            User::find(intval($this->leader_id_assigned))->assignRole('leader-user');
        }

        $this->project = new Project();
        $this->project->user_id = Auth::user()->id;
        $this->project->title = $this->title;
        $this->project->start_time = $this->start_time;
        $this->project->end_time = $this->end_time;
        $this->project->hour_estimate = $this->hour_estimate;
        $this->project->content = $this->content;
        $this->project->priority = $this->priority;
        $this->project->leader_id_assigned = $this->leader_id_assigned;
        $this->project->save();
        $this->openModal = false;
        return redirect()->route('projects');
    }

    public function editProject($id)
    {
        $this->validate();

        $this->project = Project::find($id);
        $this->project->user_id = Auth::user()->id;
        $this->project->title = $this->title;
        $this->project->start_time = $this->start_time;
        $this->project->end_time = $this->end_time;
        $this->project->hour_estimate = $this->hour_estimate;
        $this->project->content = $this->content;
        $this->project->priority = $this->priority;
        $this->project->save();
        $this->openModal = false;
    }

    public function deleteProject($id)
    {
        Project::destroy($id);
        $this->openModal = false;
        return redirect()->route('projects');
    }

    public function updateTaskOrder($items)
    {
        foreach($items as $item)
        {
            $project = Project::find($item['value']);
            $project->order_position = $item['order'];
            $project->save();
        }
    }
}
