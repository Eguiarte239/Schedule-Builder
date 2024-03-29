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
        $projectLeaderUser = Role::create(['name' => 'leader-user']);
        $employeeUser = Role::create(['name' => 'employee-user']);
        $jetstreamUser = Role::create(['name' => 'jetstream-user']);
        $googleUser = Role::create(['name' => 'google-user']);

        $changePasswordPermission = Permission::create(['name' => 'change-password']);
        $changePasswordPermission->assignRole($jetstreamUser);

        $deleteUserPermission = Permission::create(['name' => 'delete-user']);
        $deleteUserPermission->assignRole($jetstreamUser);

        $assignUsersToProject = Permission::create(['name' => 'assign-leader']);
        $assignUsersToProject->assignRole($adminUser);

        $assignUsersToTask = Permission::create(['name' => 'assign-employee']);
        $assignUsersToTask->assignRole($projectLeaderUser);
        $finishTask = Permission::create(['name' => 'task-finished']);
        $finishTask->assignRole($employeeUser);
    }
}
