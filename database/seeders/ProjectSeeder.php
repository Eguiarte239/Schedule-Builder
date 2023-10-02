<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $projects = [
            [
                'user_id' => User::first()->id,
                'title' => 'Project A',
                'content' => 'This is project A',
                'hour_estimate' => 100,
                'start_date' => '2022-01-01',
                'end_date' => '2022-01-31',
                'priority' => 'High',
                'leader_id' => 2,
            ],
            [
                'user_id' => User::first()->id,
                'title' => 'Project B',
                'content' => 'This is project B',
                'hour_estimate' => 200,
                'start_date' => '2022-02-01',
                'end_date' => '2022-02-28',
                'priority' => 'Medium',
                'leader_id' => 2,
            ],
            [
                'user_id' => User::first()->id,
                'title' => 'Project C',
                'content' => 'This is project C',
                'hour_estimate' => 300,
                'start_date' => '2022-03-01',
                'end_date' => '2022-03-31',
                'priority' => 'Low',
                'leader_id' => 2,
            ],
        ];
        
        foreach ($projects as $project) {
            Project::create($project);
        }
    }
}
