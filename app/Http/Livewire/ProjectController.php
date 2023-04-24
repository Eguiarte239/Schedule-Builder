<?php

namespace App\Http\Livewire;

use App\Models\Project;
use App\Models\User;
use App\Rules\UniqueTitleForUser;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use Livewire\WithFileUploads;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class ProjectController extends Component
{
    use WithFileUploads, AuthorizesRequests, WithPagination;

    protected $middleware = ['web', 'livewire:protect'];

    public $project;
    public $openModal = false;
    public $editTask = false;

    public $title;
    public $start_time;
    public $end_time;
    public $hour_estimate;
    public $content;
    public $priority;
    public $leader_id_assigned;

    public $search = '';


    protected $listeners = ['refreshComponent' => '$refresh'];

    protected function rules()
    {
        $rules = [
            "title" => ['required', 'string', 'max:255', new UniqueTitleForUser],
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
                  ->orWhereJsonContains('leader_id_assigned', Auth::user()->id);
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
        $this->editTask = false;
        $this->openModal = true;
    }

    public function editNote($id)
    {
        $this->setValues($id);
        $this->editTask = true;
        $this->openModal = true;
    }

    public function saveTask()
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

    public function editTask($id)
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

    public function deleteTask($id)
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
