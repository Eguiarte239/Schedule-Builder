<?php

namespace App\Http\Livewire;

use App\Models\Phase;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Validation\Rule;

class ProjectModal extends Component
{
    use AuthorizesRequests;
    
    public $project;
    public $openModal = false;
    public $editModal = false;
    public $routeProject = false;

    public $title;
    public $start_date;
    public $end_date;
    public $hour_estimate;
    public $content;
    public $priority;
    public $leader;

    protected $listeners = ['refresh' => '$refresh',
                            'new-project-modal' => 'newProject',
                            'edit-project-modal' => 'editProjectNote',    
                        ];

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
            "hour_estimate" => ['required', 'integer', 'between:0,500'],
            "content" => ['required', 'string', 'max:500'],
            "priority" => ['required', 'in:Low,Medium,High,Urgent'],
            'leader' => 'required',
            'leader.*' => 'required|exists:users,id',
        ];
        
        return $rules;
    }

    protected $rules = [];

    public function mount(){
        $this->rules = $this->rules();
    }

    public function render()
    {
        $users = User::all();
        return view('livewire.project.project-modal', compact('users'));
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
        $this->leader = $this->project->leader_id;
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
        $this->leader = "";
    }

    public function newProject()
    {
        $this->resetValues();
        $this->resetValidation();
        $this->editModal = false;
        $this->openModal = true;
        $this->routeProject = true;
        $this->emit('new-project-alert', "Once you save your project, its start and end date, and the leader project won't be able to be changed. Its hour estimate can only be changed to a lower value as long as it has no assigned phases");
    }

    public function saveProject()
    {
        $this->validate();
        if(isset($this->leader)){
            User::find(intval($this->leader))->assignRole('leader-user');
        }
        $this->project = new Project();
        $this->project->user_id = Auth::user()->id;
        $this->project->title = $this->title;
        $this->project->start_date = $this->start_date;
        $this->project->end_date = $this->end_date;
        $this->project->hour_estimate = $this->hour_estimate;
        $this->project->content = $this->content;
        $this->project->priority = $this->priority;
        $this->project->leader_id = $this->leader;
        $this->project->save();
        $this->openModal = false;
        $this->routeProject = false;
        $this->emit('refresh');
    }

    public function editProjectNote($id)
    {
        $this->setValues($id);
        $this->editModal = true;
        $this->openModal = true;
        $this->routeProject = true;
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
        $this->routeProject = false;
        $this->emit('refresh');
    }

    public function deleteProject($id)
    {
        if(Phase::where('project_id', $id)->count() == 0){
            Project::destroy($id);
            $this->openModal = false;
            $this->routeProject = false;
            return redirect()->route('projects');
        }
        else{
            $this->emit('alert', "You can't delete a project that already has phases assigned", route('projects'));
        }
    }
}
