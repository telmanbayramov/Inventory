<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Department;
use App\Models\Equipment;
use App\Models\Faculty;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Speciality;
use App\Models\User;
use App\Policies\DepartmentPolicy;
use App\Policies\EquipmentPolicy;
use App\Policies\FacultyPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use App\Policies\SpecialityPolicy;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class=>UserPolicy::class,
        Role::class => RolePolicy::class,
        Permission::class=>PermissionPolicy::class,
        Faculty::class=> FacultyPolicy::class,
        Department::class=> DepartmentPolicy::class,
        Equipment::class=> EquipmentPolicy::class 
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
