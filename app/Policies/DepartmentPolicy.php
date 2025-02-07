<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Department;

class DepartmentPolicy
{
    /**
     * Check if the user can view the departments.
     *
     * @param User $user
     * @return bool
     */
    public function views(User $user)
    {
        return $user->can('view_departments');
    }

    /**
     * Check if the user can view a specific department.
     *
     * @param User $user
     * @param Department $department
     * @return bool
     */
    public function view(User $user)
    {
        return $user->can('view_department');
    }

    /**
     * Check if the user can create a department.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can('add_department');
    }

    /**
     * Check if the user can update a specific department.
     *
     * @param User $user
     * @param Department $department
     * @return bool
     */
    public function update(User $user)
    {
        return $user->can('edit_department');
    }

    /**
     * Check if the user can delete a specific department.
     *
     * @param User $user
     * @param Department $department
     * @return bool
     */
    public function delete(User $user)
    {
        return $user->can('delete_department');
    }
}
