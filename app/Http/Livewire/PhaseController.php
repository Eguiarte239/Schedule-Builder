<?php

namespace App\Http\Livewire;

use App\Models\Phase;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PhaseController extends Component
{
    use AuthorizesRequests;

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

        $groupedPhases = $phases->groupBy('project_id');

        return view('livewire.phase', ['projects' => $projects, 'groupedPhases' => $groupedPhases, ])->layout('layouts.app');
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

    public function getProgressPercentage($id)
    {
        if(Task::where('phase_id', $id)->count() > 0)
        {
            $tasks = Task::where('phase_id', $id)->get();
            $completedTasksCount = $tasks->filter(function ($task) {
                return $task->is_finished;
            })->count();
            $percentage = ($completedTasksCount / $tasks->count()) * 100;
            return round($percentage) . '%';
        }
    }
}