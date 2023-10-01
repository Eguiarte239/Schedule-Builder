<?php

namespace App\Http\Livewire;

use App\Models\Phase;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PhaseModal extends Component
{
    use AuthorizesRequests;

    protected $middleware = ['web', 'livewire:protect'];

    public $phase;
    public $openModal = false;
    public $editModal = false;

    public $title;
    public $start_date;
    public $end_date;
    public $content;
    public $priority;
    public $project_id;

    protected $listeners = ['refresh' => '$refresh',
                            'new-phase-modal' => 'newPhase',
                            'edit-phase-modal' => 'editPhaseNote',
                        ];

    protected function rules()
    {
        $rules = [
            "title" => ['required', 'string', 'max:255'],
            "start_date" => ['after_or_equal:'.Phase::projectStartDate($this->project_id), 'before_or_equal:'.Phase::projectEndDate($this->project_id)],
            "end_date" => ['required', 'date', 'after_or_equal:start_date', 'before_or_equal:'.Phase::projectEndDate($this->project_id)],
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
        $projects = Project::where('leader_id', Auth::user()->id)->get();
        return view('livewire.phase.phase-modal', compact('projects'));
    }

    public function setValues($id)
    {
        $this->phase = Phase::find($id);
        $this->title = $this->phase->title;
        $this->start_date = $this->phase->start_date;
        $this->end_date = $this->phase->end_date;
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
        $this->emit('new-phase-alert', "Once you save your phase, its start and end date, and the project won't be able to be changed");
    }

    public function savePhase()
    {
        $this->validate();

        $this->phase = new Phase();
        $this->phase->user_id = Auth::user()->id;
        $this->phase->title = $this->title;
        $this->phase->start_date = $this->start_date;
        $this->phase->end_date = $this->end_date;
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
}
