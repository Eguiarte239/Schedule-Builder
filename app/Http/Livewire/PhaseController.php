<?php

namespace App\Http\Livewire;

use App\Models\Phase;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Rules\CurrentEstimatedHoursRule;
use App\Rules\EstimatedPhaseHoursRule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class PhaseController extends Component
{
    use AuthorizesRequests;

    protected $middleware = ['web', 'livewire:protect'];

    public $phase;
    public $openModal = false;
    public $editModal = false;

    public $title;
    public $start_date;
    public $end_date;
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
        $projects = Project::where('leader_id', Auth::user()->id)->get();
        
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
        $this->priority = "";
        $this->project_id = "";
    }

    public function newPhase()
    {
        $this->resetValues();
        $this->resetValidation();
        $this->editModal = false;
        $this->openModal = true;
        $this->emit('new-phase-alert', "Once you save your phase, its start and end date, and the project won't be able to be changed. Its hour estimate can only be changed to a lower value as long as it has no assigned tasks");
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

    public function editPhaseNote($id)
    {
        $this->setValues($id);
        $this->editModal = true;
        $this->openModal = true;
    }

    public function editPhase($id)
    {
        $this->validate();

        $this->phase = Phase::find($id);
        $this->phase->user_id = Auth::user()->id;
        $this->phase->title = $this->title;
        $this->phase->hour_estimate = $this->hour_estimate;
        $this->phase->content = $this->content;
        $this->phase->priority = $this->priority;
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