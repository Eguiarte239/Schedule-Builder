<?php

namespace App\Console\Commands;

use App\Mail\TaskReminder;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ReminderEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:send';

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
            if(Carbon::parse($task->end_date)->endOfDay()->diffInDays(Carbon::now()->startOfDay()) == 1 && $task->is_finished == false){
                Mail::to($task->user->email)->queue(new TaskReminder($task));
            }
        }
    }
}
