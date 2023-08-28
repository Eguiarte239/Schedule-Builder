<?php

namespace App\Http\Livewire;

use App\Models\Phase;
use App\Models\Project;
use App\Models\User;
use App\Rules\CurrentEstimatedHoursRule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ProjectController extends Component
{
    use AuthorizesRequests;

    protected $middleware = ['web', 'livewire:protect'];

    public $project;
    public $openModal = false;
    public $editModal = false;

    public $title;
    public $start_date;
    public $end_date;
    public $hour_estimate;
    public $content;
    public $priority;
    public $leader;
    public $ask;
    public $response;

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
            'leader' => 'required',
            'leader.*' => 'required|exists:users,id',
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
                  ->orWhere('leader_id', 'LIKE', '%'.Auth::user()->id.'%');
        })
        ->where('title', 'like', '%'.$this->search.'%')
        ->orderBy('order_position', 'asc')
        ->get();        
    }

    public function render()
    {
        $projects = $this->projects;
        $users = User::all();
        $response = $this->response;
        return view('livewire.project', ['projects' => $projects, 'users' => $users, 'response' => $response])->layout('layouts.app');
    }

    public function askDB(){
        DB::connection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        //$this->response = DB::askForQuery($this->ask);
        $this->response = DB::ask($this->ask);
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
