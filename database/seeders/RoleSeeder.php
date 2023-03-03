<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminUser = Role::create(['name' => 'admin-user']);
        $jetstreamUser = Role::create(['name' => 'jetstream-user']);
        $googleUser = Role::create(['name' => 'google-user']);

        $changePasswordPermission = Permission::create(['name' => 'change-password']);
        $changePasswordPermission->assignRole($jetstreamUser);

        $assignUsersToTask = Permission::create(['name' => 'assign-to-user']);
        $assignUsersToTask->assignRole($adminUser);
    }
}
