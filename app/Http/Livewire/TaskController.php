<?php

namespace App\Http\Livewire;

use App\Mail\TaskFinished;
use App\Mail\TaskFinishedLate;
use App\Models\Phase;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Rules\EstimatedTaskHoursRule;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class TaskController extends Component
{
    use AuthorizesRequests, WithPagination;

    protected $middleware = ['web', 'livewire:protect'];

    public $search = '';

    protected $listeners = ['refresh' => '$refresh'];

    public function render()
    {
        $user = Auth::user();

        $projectIds = Phase::where('user_id', $user->id)
                    ->orWhereHas('task', function ($query) use ($user) {
                        $query->where('user_id_assigned', 'LIKE', '%'.$user->id.'%');
                    })
                    ->groupBy('project_id')
                    ->pluck('project_id');

        $projects = Project::whereIn('id', $projectIds)->with(['phase.task' => function($query) use ($user) {
            $query->where('user_id', $user->id)->orWhere('user_id_assigned', 'LIKE', '%'.$user->id.'%')->orderBy('id', 'asc');
        }])->where('title', 'like', '%'.$this->search.'%')->get();

        $adminUsers = User::role('admin-user')->get();

        if($adminUsers->contains(auth()->user())){
            $projectIds = Phase::WhereHas('task')
                    ->groupBy('project_id')
                    ->pluck('project_id');

            $projects = Project::whereIn('id', $projectIds)->with('phase.task')->orderBy('id', 'asc')->where('title', 'like', '%'.$this->search.'%')->get();
        }

        return view('livewire.task.task', ['projects' => $projects])->layout('layouts.app');
    }

    public function finishTask($id)
    {
        $task = Task::find($id);
        $isPredecessor = Task::where('predecessor_task', $task->id)->exists();
        $dependentTasks = Task::where('predecessor_task', $task->id)->get();
        $allSuccessorsMarked = true;
        foreach ($dependentTasks as $dependentTask) {
            if ($dependentTask->is_finished == false) {
                $allSuccessorsMarked = false;
                break;
            }
        }
        if($isPredecessor == true && $allSuccessorsMarked == true){
            $this->emit('predecessor', [
                'message' => trans("You can't uncheck a task which is predecessor of another until you uncheck that one"), 
                'route' => route('tasks')
            ]);
        }
        else {
            if($task->is_finished == false && Carbon::now()->greaterThan(Carbon::parse($task->end_date)) && Auth::user()->id == $task->user_id_assigned){
                app()->setLocale($task->leader->locale);
                Mail::to($task->leader->email)->queue(new TaskFinishedLate($task));
            } 
            else if($task->is_finished == false && Carbon::now()->lessThan(Carbon::parse($task->end_date)) && Auth::user()->id == $task->user_id_assigned){
                app()->setLocale($task->leader->locale);
                Mail::to($task->leader->email)->queue(new TaskFinished($task));
            }
            $task->is_finished = !$task->is_finished;
            $task->save();
        }
    }
}
