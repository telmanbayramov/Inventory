<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $user = User::find(1);
        // $role = Role::find(1);
        // $user->assignRole($role);

          Permission::create(['name' => 'view_room_permissions', 'guard_name' => 'api']);
          Permission::create(['name' => 'view_room_permission', 'guard_name' => 'api']);
          Permission::create(['name' => 'confirm_room_permission', 'guard_name' => 'api']);
        //   Permission::create(['name' => 'add_department', 'guard_name' => 'api']);
        //   Permission::create(['name' => 'view_department', 'guard_name' => 'api']);
        //   Permission::create(['name' => 'view_departments', 'guard_name' => 'api']);
        //   Permission::create(['name' => 'edit_department', 'guard_name' => 'api']);
        //   Permission::create(['name' => 'delete_department', 'guard_name' => 'api']);

        //   Permission::create(['name' => 'add_speciality', 'guard_name' => 'api']);
        //   Permission::create(['name' => 'view_speciality', 'guard_name' => 'api']);
        //   Permission::create(['name' => 'view_specialities', 'guard_name' => 'api']);
        //   Permission::create(['name' => 'edit_speciality', 'guard_name' => 'api']);
        //   Permission::create(['name' => 'delete_speciality', 'guard_name' => 'api']);

        //   Permission::create(['name' => 'add_course', 'guard_name' => 'api']);
        //   Permission::create(['name' => 'view_course', 'guard_name' => 'api']);
        //   Permission::create(['name' => 'view_courses', 'guard_name' => 'api']);
        //   Permission::create(['name' => 'edit_course', 'guard_name' => 'api']);
        //   Permission::create(['name' => 'delete_course', 'guard_name' => 'api']);

        $role = Role::find(1);
        $role->givePermissionTo(['view_room_permissions','view_room_permission','confirm_room_permission']);
        // // $adminRole = Role::create(['name' => 'admin']);
        // // $userRole = Role::create(['name' => 'user']);
    }
}
