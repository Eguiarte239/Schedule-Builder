<?php

namespace App\Http\Livewire;

use App\Models\Phase;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PhaseController extends Component
{
    use AuthorizesRequests;

    protected $middleware = ['web', 'livewire:protect'];

    public $search = '';

    public $classMap = [
        'Low' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        'Medium' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'High' => 'bg-yellow-100 text-yellow-800 dark:bg-orange-900 dark:text-yellow-300',
        'Urgent' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
    ];

    protected $listeners = ['refresh' => '$refresh'];

    public function render()
    {   
        $projects = Project::where('leader_id', Auth::user()->id)->get();
        $phases = Phase::with('project')
                    ->where('user_id', Auth::user()->id)
                    ->where('title', 'like', '%'.$this->search.'%')
                    ->orderBy('id', 'asc')
                    ->get();
        
        $adminUsers = User::role('admin-user')->get();
        
        if($adminUsers->contains(auth()->user())){
            $projects = Project::all();
            $phases = Phase::with('project')
                    ->where('title', 'like', '%'.$this->search.'%')
                    ->orderBy('id', 'asc')
                    ->get();
        }

        $groupedPhases = $phases->groupBy('project_id');

        return view('livewire.phase.phase', ['projects' => $projects, 'groupedPhases' => $groupedPhases ])->layout('layouts.app');
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