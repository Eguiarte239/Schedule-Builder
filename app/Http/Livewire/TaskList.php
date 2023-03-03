<?php

namespace App\Http\Livewire;

use App\Models\Task;
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

class TaskList extends Component
{
    use WithFileUploads, AuthorizesRequests, WithPagination;

    protected $middleware = ['web', 'livewire:protect'];

    public $task;
    public $openModal = false;
    public $editTask = false;

    public $title;
    public $start_time;
    public $end_time;
    public $hour_estimate;
    public $content;
    public $image;
    public $priority;
    public $assigned_to;
    
    public $urls = [];
    public $path;

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
            "image.*" => ['nullable', 'mimes:jpeg,png,gif', 'max:2048'],
            "priority" => ['required', 'in:Low,Medium,High,Urgent'],
            'assigned_to' => 'nullable',
            'assigned_to.*' => 'nullable|exists:users,id',
        ];
        
        return $rules;
    }

    protected $rules = [];

    public function mount(){
        $this->path = public_path('/images');
        $this->rules = $this->rules();
    }

    public function getTasksProperty()
    {
        //return Task::where('user_id', Auth::user()->id)->where('title', 'like', '%'.$this->search.'%')->orderBy('order_position', 'asc')->get();
        /*$assignedTasks = Auth::user()->assignedTasks()->where('title', 'like', '%'.$this->search.'%')->orderBy('order_position', 'asc')->get();
        $userTasks = Task::where('user_id', Auth::user()->id)->where('title', 'like', '%'.$this->search.'%')->orderBy('order_position', 'asc')->get();

        return $assignedTasks->merge($userTasks);*/
        return Task::where(function ($query) {
            $query->where('user_id', Auth::user()->id)
                  ->orWhereJsonContains('assigned_to', Auth::user()->id);
        })
        ->where('title', 'like', '%'.$this->search.'%')
        ->orderBy('order_position', 'asc')
        ->get();
    }

    public function render()
    {
        $tasks = $this->tasks;
        $users = User::all();
        return view('livewire.task-list', ['tasks' => $tasks, 'users' => $users])->layout('layouts.app');
    }

    public function setValues($id)
    {
        $this->task = Task::find($id);
        $this->title = $this->task->title;
        $this->start_time = $this->task->start_time;
        $this->end_time = $this->task->end_time;
        $this->hour_estimate = $this->task->hour_estimate;
        $this->content = $this->task->content;
        $this->priority = $this->task->priority;
    }

    public function resetValues()
    {
        $this->task = new Task();
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
        $this->validate();
        if(!File::exists($this->path)) {
            Storage::disk('public')->makeDirectory('images');
        }

        $this->task = new Task();
        $this->task->user_id = Auth::user()->id;
        $this->task->title = $this->title;
        $this->task->start_time = $this->start_time;
        $this->task->end_time = $this->end_time;
        $this->task->hour_estimate = $this->hour_estimate;
        $this->task->content = $this->content;
        $this->task->priority = $this->priority;
        if(!empty($this->image)){
            foreach ($this->image as $image) {
                $name =  $image->getClientOriginalName();
                $route = storage_path().'\app\public\images/'.$name;
                Image::make($image)->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->encode('jpg')->save($route);
                $urls[] = '/storage/images/'.$name;
            }
            $this->task->image = Crypt::encrypt(json_encode($urls));
        }
        else{
            $this->task->image = null;
        }
        $this->task->assigned_to = $this->assigned_to;
        $this->task->save();
        $this->openModal = false;

        return redirect()->route('notes.index');
    }

    public function editTask($id)
    {
        $this->validate();

        $this->task = Task::find($id);
        $this->task->user_id = Auth::user()->id;
        $this->task->title = $this->title;
        $this->task->start_time = $this->start_time;
        $this->task->end_time = $this->end_time;
        $this->task->hour_estimate = $this->hour_estimate;
        $this->task->content = $this->content;
        $this->task->priority = $this->priority;
        if(!empty($this->image)){
            foreach ($this->image as $image) {
                $name =  $image->getClientOriginalName();
                $route = storage_path().'\app\public\images/'.$name;
                Image::make($image)->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->encode('jpg')->save($route);
                $urls[] = '/storage/images/'.$name;
            }
            $this->task->image = Crypt::encrypt(json_encode($urls));
        }
        else{
            $this->task->image = null;
        }
        $this->task->save();
        $this->openModal = false;
    }

    public function deleteTask($id)
    {
        Task::destroy($id);
        $this->openModal = false;
        return redirect()->route('notes.index');
    }

    public function updateTaskOrder($items)
    {
        foreach($items as $item)
        {
            $task = Task::find($item['value']);
            $task->order_position = $item['order'];
            $task->save();
        }
    }
}
