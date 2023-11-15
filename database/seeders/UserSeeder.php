<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $password = 'KvLkPmQJAb4itzR';
        $user = User::create([
            'name' => 'Schedule-Admin',
            'email' => 'schedule-admin@schedule-builder.org',
            'password' => Hash::make($password),
        ])->assignRole('jetstream-user', 'admin-user');
        $user->markEmailAsVerified();
        Event::dispatch(new Verified($user));
    }
}
