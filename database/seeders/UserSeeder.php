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
        $password = 'password';
        $user = User::create([
            'name' => 'Abraham Israel Lopez Eguiarte',
            'email' => 'admin@example.com',
            'password' => Hash::make($password),
        ])->assignRole('jetstream-user', 'admin-user');
        $user->markEmailAsVerified();
        Event::dispatch(new Verified($user));

        $user = User::create([
            'name' => 'employee',
            'email' => 'employee@example.com',
            'password' => Hash::make($password),
        ])->assignRole('jetstream-user', 'employee-user');
        $user->markEmailAsVerified();
        Event::dispatch(new Verified($user));
    }
}
