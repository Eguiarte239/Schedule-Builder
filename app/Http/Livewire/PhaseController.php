<?php

namespace App\Http\Livewire;

use App\Models\Phase;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PhaseController extends CommonController
{
    public $phase;
    public $editPhase = false;

    protected function rules()
    {
        $rules = [
            "title" => ['required', 'string', 'max:255'],
            "start_date" => [Rule::excludeIf($this->phase && $this->start_date == $this->phase->start_date), 'required', 'date', 'after_or_equal:today'],
            "end_date" => ['required', 'date', 'after_or_equal:start_date'],
            "hour_estimate" => ['required', 'integer', 'min:1'],
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

        return view('livewire.phase', ['projects' => $projects, 'groupedPhases' => $groupedPhases, ])->layout('layouts.app');
    }

    public function setValues($id)
    {
        $this->phase = Phase::find($id);
        $this->title = $this->phase->title;
        $this->start_date = $this->phase->start_date;
        $this->end_date = $this->phase->end_date;
        $this->hour_estimate = $this->phase->hour_estimate;
        $this->content = $this->phase->content;
        $this->priority = $this->phase->priority;
        $this->project_id = $this->phase->project_id;
    }

    public function resetValues()
    {
        $this->phase = new Phase();
        $this->title = "";
        $this->start_date = now()->format('Y-m-d');
        $this->end_date = "";
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
        $this->phase->start_date = $this->start_date;
        $this->phase->end_date = $this->end_date;
        $this->phase->hour_estimate = $this->hour_estimate;
        $this->phase->content = $this->content;
        $this->phase->priority = $this->priority;
        $this->phase->project_id = $this->project_id;
        $this->phase->save();
        $this->openModal = false;
    }

    public function editPhase($id)
    {
        $this->validate();

        $this->phase = Phase::find($id);
        $this->phase->user_id = Auth::user()->id;
        $this->phase->title = $this->title;
        $this->phase->start_date = $this->start_date;
        $this->phase->end_date = $this->end_date;
        $this->phase->hour_estimate = $this->hour_estimate;
        $this->phase->content = $this->content;
        $this->phase->priority = $this->priority;
        $this->phase->save();
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
        $this->phase = Phase::find($id);
        if(Task::where('phase_id', $id)->count() > 0)
        {
            $tasks = Task::where('phase_id', $id)->get();
            $completedTasksCount = $tasks->filter(function ($task) {
                return $task->is_finished;
            })->count();
            if($completedTasksCount == $this->phase->task()->count()) {
                $this->phase->is_finished = true;
            }
            else{
                $this->phase->is_finished = false;
            }
            $this->phase->save();
            $percentage = ($completedTasksCount / $tasks->count()) * 100;
            return round($percentage) . '%';
        }
    }
}