<?php

namespace App\Http\Livewire;

use App\Models\Phase;
use App\Models\Project;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PhaseController extends Component
{
    use AuthorizesRequests, WithPagination;

    protected $middleware = ['web', 'livewire:protect'];

    public $phase;
    public $openModal = false;
    public $editPhase = false;

    public $title;
    public $start_time;
    public $end_time;
    public $hour_estimate;
    public $content;
    public $priority;
    public $project_id;

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
        ];
        
        return $rules;
    }

    protected $rules = [];

    public function mount(){
        $this->rules = $this->rules();
    }

    public function getPhasesProperty()
    {
        return Phase::where(function ($query) {
            $query->where('user_id', Auth::user()->id);
        })
        ->where('title', 'like', '%'.$this->search.'%')
        ->orderBy('order_position', 'asc')
        ->get();
    }

    public function render()
    {
        //$role = Role::where('name', 'admin-user')->first();
        $phases = $this->phases;
        $projects = Project::all()->where('leader_id_assigned', Auth::user()->id);
        return view('livewire.phase', ['phases' => $phases, 'projects' => $projects])->layout('layouts.app');
    }

    public function setValues($id)
    {
        $this->phase = Phase::find($id);
        $this->title = $this->phase->title;
        $this->start_time = $this->phase->start_time;
        $this->end_time = $this->phase->end_time;
        $this->hour_estimate = $this->phase->hour_estimate;
        $this->content = $this->phase->content;
        $this->priority = $this->phase->priority;
    }

    public function resetValues()
    {
        $this->phase = new Phase();
        $this->title = "";
        $this->start_time = now()->format('Y-m-d');
        $this->end_time = "";
        $this->hour_estimate = "";
        $this->content = "";
        $this->priority = null;
    }

    public function newPhase()
    {
        $this->resetValues();
        $this->resetValidation();
        $this->editPhase = false;
        $this->openModal = true;
    }

    public function editPhaseNote($id)
    {
        $this->setValues($id);
        $this->editPhase = true;
        $this->openModal = true;
    }

    public function savePhase()
    {
        $this->validate();

        $this->phase = new Phase();
        $this->phase->user_id = Auth::user()->id;
        $this->phase->title = $this->title;
        $this->phase->start_time = $this->start_time;
        $this->phase->end_time = $this->end_time;
        $this->phase->hour_estimate = $this->hour_estimate;
        $this->phase->content = $this->content;
        $this->phase->priority = $this->priority;
        $this->phase->project_id = $this->project_id;
        $this->phase->save();
        $this->openModal = false;
        return redirect()->route('phases');
    }

    public function editPhase($id)
    {
        $this->validate();

        $this->phase = Phase::find($id);
        $this->phase->user_id = Auth::user()->id;
        $this->phase->title = $this->title;
        $this->phase->start_time = $this->start_time;
        $this->phase->end_time = $this->end_time;
        $this->phase->hour_estimate = $this->hour_estimate;
        $this->phase->content = $this->content;
        $this->phase->priority = $this->priority;
        $this->phase->save();
        $this->openModal = false;
    }

    public function deletePhase($id)
    {
        Phase::destroy($id);
        $this->openModal = false;
        return redirect()->route('phases');
    }

    public function updateTaskOrder($items)
    {
        foreach($items as $item)
        {
            $phase = Phase::find($item['value']);
            $phase->order_position = $item['order'];
            $phase->save();
        }
    }
}
