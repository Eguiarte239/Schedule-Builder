<?php

namespace App\Console\Commands;

use App\Models\Phase;
use Illuminate\Console\Command;

class FinishPhase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phases:send';

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
        $phases = Phase::all();
        foreach ($phases as $phase) {
            $completedTasksCount = $phase->task()->where('is_finished', true)->count();
            if ($completedTasksCount == $phase->task()->count()) {
                $phase->is_finished = true;
            } else {
                $phase->is_finished = false;
            }
            $phase->save();
        }
    }
}
