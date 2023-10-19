<?php

namespace Database\Seeders;

use App\Models\Phase;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tasks = [
            [
                'user_id' => 2,
                'title' => 'Task A',
                'content' => 'This is task A',
                'start_date' => '2022-01-01',
                'end_date' => '2022-01-31',
                'priority' => 'High',
                'project_id' => Project::first()->id,
                'phase_id' => Phase::first()->id,
                'user_id_assigned' => User::first()->id,
                'predecessor_task' => null,
            ],
            [
                'user_id' => 2,
                'title' => 'Task B',
                'content' => 'This is task B',
                'start_date' => '2022-02-01',
                'end_date' => '2022-02-28',
                'priority' => 'Medium',
                'project_id' => Project::first()->id,
                'phase_id' => Phase::first()->id,
                'user_id_assigned' => User::first()->id,
                'predecessor_task' => null,
            ],
            [
                'user_id' => 2,
                'title' => 'Task C',
                'content' => 'This is task C',
                'start_date' => '2022-03-01',
                'end_date' => '2022-03-31',
                'priority' => 'Low',
                'project_id' => Project::first()->id,
                'phase_id' => Phase::first()->id,
                'user_id_assigned' => User::first()->id,
                'predecessor_task' => null,
            ],
        ];

        // Seed the tasks table with the defined tasks
        foreach ($tasks as $task) {
            Task::create($task);
        }
    }
}
