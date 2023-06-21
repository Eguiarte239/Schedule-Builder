<?php

namespace App\Http\Livewire;

use App\Models\Phase;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Rules\CurrentEstimatedHoursRule;
use App\Rules\EstimatedPhaseHoursRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PhaseController extends ParentController
{
    public $phase;

    public $project_id;

    protected function rules()
    {
        $rules = [
            "title" => ['required', 'string', 'max:255'],
            "start_date" => [
                Rule::when(!$this->editModal, function () {
                    return ['required', 'date', 'after_or_equal:today'];
                }),
            ],
            "end_date" => ['required', 'date', 'after_or_equal:start_date', 'before_or_equal:'.Phase::projectEndDate($this->project_id)],
            "hour_estimate" => ['required', 'integer',],
            "content" => ['required', 'string', 'max:500'],
            "priority" => ['required', 'in:Low,Medium,High,Urgent'],
            'project_id' => 'required',
            'project_id.*' => 'required|exists:projects,id',
        ];

        if($this->editModal){
            $rules['hour_estimate'][] = new EstimatedPhaseHoursRule($this->project_id, $this->hour_estimate, $this->editModal, $this->phase->hour_estimate);
            if($this->phase->task()->exists()){
                $rules['hour_estimate'][] = new CurrentEstimatedHoursRule($this->phase->hour_estimate);
            }
        } 
        else{
            $rules['hour_estimate'][] = new EstimatedPhaseHoursRule($this->project_id, $this->hour_estimate, $this->editModal, 0);
        }
        
        return $rules;
    }

    protected $rules = [];

    public function mount(){
        $this->rules = $this->rules();
    }

    public function render()
    {
        $projects = Project::where('leader_id_assigned', Auth::user()->id)->get();
        
        $phases = Phase::with('project')
                    ->where('user_id', Auth::user()->id)
                    ->where('title', 'like', '%'.$this->search.'%')
                    ->orderBy('order_position', 'asc')
                    ->get();
        
        $adminUsers = User::role('admin-user')->get();
        if($adminUsers->contains(auth()->user())){
            $projects = Project::all();
            $phases = Phase::with('project')
                    ->where('title', 'like', '%'.$this->search.'%')
                    ->orderBy('order_position', 'asc')
                    ->get();
        }

        $groupedPhases = $phases->groupBy('project_id');

        return view('livewire.phase', ['projects' => $projects, 'groupedPhases' => $groupedPhases ])->layout('layouts.app');
    }

    public function setPhaseValues($id)
    {
        $this->phase = Phase::find($id);
        $this->setGeneralValues($this->phase);
        $this->project_id = $this->phase->project_id;
    }

    public function resetPhaseValues()
    {
        $this->resetGeneralValues();
        $this->project_id = "";
    }

    public function newPhase()
    {
        $this->resetPhaseValues();
        $this->resetValidation();
        $this->newInstance();
        $this->emit('new-phase-alert', "Once you save your phase, its start and end date, and the project won't be able to be changed. Its hour estimate can only be changed to a lower value as long as it has no assigned tasks");
    }

    public function savePhase()
    {
        $this->validate();
        $this->phase = new Phase();
        $this->phase->user_id = Auth::user()->id;
        $this->saveInstanceGeneralValues($this->phase);
        $this->phase->project_id = $this->project_id;
        $this->phase->save();
        $this->openModal = false;
    }

    public function editPhaseNote($id)
    {
        $this->setPhaseValues($id);
        $this->editModal = true;
        $this->openModal = true;
    }

    public function editPhase($id)
    {
        $this->validate();

        $this->phase = Phase::find($id);
        $this->phase->user_id = Auth::user()->id;
        $this->editInstanceGeneralValues($this->phase);
        $this->phase->update();
        $this->openModal = false;
    }

    public function deletePhase($id)
    {
        if(Task::where('phase_id', $id)->count() == 0){
            Phase::destroy($id);
            $this->openModal = false;
            return redirect()->route('phases');
        }
        else{
            $this->emit('alert', "You can't delete a phase that already has tasks assigned", route('phases'));
        }
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

    public function getProgressPercentage($id)
    {
        $phase = Phase::find($id);
        if(Task::where('phase_id', $id)->count() > 0)
        {
            $tasks = Task::where('phase_id', $id)->get();
            $completedTasksCount = $tasks->filter(function ($task) {
                return $task->is_finished;
            })->count();
            if($completedTasksCount == $phase->task()->count()){
                $phase->is_finished = true;
            }
            else{
                $phase->is_finished = false;
            }
            $phase->save();
            $percentage = ($completedTasksCount / $tasks->count()) * 100;
            return round($percentage) . '%';
        }
    }
}