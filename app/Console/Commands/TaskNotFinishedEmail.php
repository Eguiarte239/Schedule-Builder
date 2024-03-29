<?php

namespace App\Console\Commands;

use App\Mail\TaskNotFinished;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TaskNotFinishedEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'not_finished:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tasks = Task::all();
        foreach($tasks as $task){
            if(Carbon::now()->startOfDay()->greaterThan(Carbon::parse($task->end_date)) && $task->is_finished == false){
                app()->setLocale($task->user->locale);
                Mail::to($task->user->email)->queue(new TaskNotFinished($task));
            }
        }
    }
}