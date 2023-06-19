<?php

namespace App\Http\Livewire;

use App\Models\Phase;
use App\Models\Project;
use App\Models\User;
use App\Rules\CurrentEstimatedHoursRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProjectController extends ParentController
{
    public $project;

    public $leader_id_assigned;

    protected function rules()
    {
        $rules = [
            "title" => ['required', 'string', 'max:255'],
            "start_date" => [
                Rule::when(!$this->editModal, function () {
                    return ['required', 'date', 'after_or_equal:today'];
                }),
            ],
            "end_date" => ['required', 'date', 'after_or_equal:start_date'],
            "end_date" => ['required', 'date', 'after_or_equal:start_date'],
            "hour_estimate" => ['required', 'integer', 'between:0,500',],
            "content" => ['required', 'string', 'max:500'],
            "priority" => ['required', 'in:Low,Medium,High,Urgent'],
            'leader_id_assigned' => 'required',
            'leader_id_assigned.*' => 'required|exists:users,id',
        ];

        if($this->editModal){
            if($this->project->phase()->exists()){
                $rules['hour_estimate'][] = new CurrentEstimatedHoursRule($this->project->hour_estimate);
            }
        } 
        
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
        $this->start_date = $this->project->start_date;
        $this->end_date = $this->project->end_date;
        $this->hour_estimate = $this->project->hour_estimate;
        $this->content = $this->project->content;
        $this->priority = $this->project->priority;
        $this->leader_id_assigned = $this->project->leader_id_assigned;
    }

    public function resetValues()
    {
        $this->project = new Project();
        $this->title = "";
        $this->start_date = now()->format('Y-m-d');
        $this->end_date = "";
        $this->hour_estimate = "";
        $this->content = "";
        $this->priority = "";
        $this->leader_id_assigned = "";
    }

    public function newProject()
    {
        $this->resetValues();
        $this->resetValidation();
        $this->editModal = false;
        $this->openModal = true;
        $this->emit('new-project-alert', "Once you save your project, its start and end date, and the leader project won't be able to be changed. Its hour estimate can only be changed to a lower value as long as it has no assigned phases");
    }

    public function saveProject()
    {
        $class_type = get_class($this);
        $this->validate();
        if(isset($this->leader_id_assigned)){
            User::find(intval($this->leader_id_assigned))->assignRole('leader-user');
        }
        $this->project = new Project();
        $this->project->user_id = Auth::user()->id;
        $this->save($class_type, $this->project);
        $this->project->leader_id_assigned = $this->leader_id_assigned;
        $this->project->save();
        $this->openModal = false;
    }

    public function editProjectNote($id)
    {
        $this->setValues($id);
        $this->editModal = true;
        $this->openModal = true;
    }

    public function editProject($id)
    {
        $this->validate();
        $this->project = Project::find($id);
        $this->project->user_id = Auth::user()->id;
        $this->project->title = $this->title;
        $this->project->hour_estimate = $this->hour_estimate;
        $this->project->content = $this->content;
        $this->project->priority = $this->priority;
        $this->project->update();
        $this->openModal = false;
    }

    public function deleteProject($id)
    {
        if(Phase::where('project_id', $id)->count() == 0){
            Project::destroy($id);
            $this->openModal = false;
            return redirect()->route('projects');
        }
        else{
            $this->emit('alert', "You can't delete a project that already has phases assigned", route('projects'));
        }
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

    public function getProgressPercentage($id)
    {
        if(Phase::where('project_id', $id)->count() > 0)
        {
            $phases = Phase::where('project_id', $id)->get();
            $completedPhasessCount = $phases->filter(function ($phase) {
                return $phase->is_finished;
            })->count();
            $percentage = ($completedPhasessCount / $phases->count()) * 100;
            return round($percentage) . '%';
        }
    }
}
