<?php

namespace Database\Seeders;

use App\Models\Phase;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PhaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $phases = [
            [
                'title' => 'Phase A',
                'content' => 'This is phase A',
                'start_date' => '2022-01-01',
                'end_date' => '2022-01-31',
                'priority' => 'High',
                'project_id' => Project::first()->id,
                'user_id' => User::first()->id,
            ],
            [
                'title' => 'Phase B',
                'content' => 'This is phase B',
                'start_date' => '2022-02-01',
                'end_date' => '2022-02-28',
                'priority' => 'Medium',
                'project_id' => Project::first()->id,
                'user_id' => User::first()->id,
            ],
            [
                'title' => 'Phase C',
                'content' => 'This is phase C',
                'start_date' => '2022-03-01',
                'end_date' => '2022-03-31',
                'priority' => 'Low',
                'project_id' => Project::first()->id,
                'user_id' => User::first()->id,
            ],
        ];

        // Seed the phases table with the defined phases
        foreach ($phases as $phase) {
            Phase::create($phase);
        }
    }
}
