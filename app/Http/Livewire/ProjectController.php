<?php

namespace App\Http\Livewire;

use App\Models\AskDB;
use App\Models\Phase;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ProjectController extends Component
{
    use AuthorizesRequests;

    protected $middleware = ['web', 'livewire:protect'];

    public $ask;
    public $response;

    public $search = '';

    public $classMap = [
        'Low' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
        'Medium' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'High' => 'bg-yellow-100 text-yellow-800 dark:bg-orange-900 dark:text-yellow-300',
        'Urgent' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
    ];

    protected $listeners = ['refresh' => '$refresh'];

    public function getProjectsProperty()
    {
        return Project::where(function ($query) {
            $query->where('user_id', Auth::user()->id)
                  ->orWhere('leader_id', 'LIKE', '%'.Auth::user()->id.'%');
        })
        ->where('title', 'like', '%'.$this->search.'%')
        ->orderBy('id', 'asc')
        ->get();        
    }

    public function render()
    {
        $projects = $this->projects;
        $response = $this->response;
        return view('livewire.project.project', ['projects' => $projects, 'response' => $response])->layout('layouts.app');
    }

    public function askDB(){
        DB::connection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        $this->response = AskDB::ask($this->ask);
        // $this->response = AskDB::evaluateQuery('SELECT * FROM projects WHERE end_date = (SELECT min(end_date) FROM projects);');
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
