<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Faculty;

class SemesterPolicy
{
    /**
     * Check if the user can view the faculties.
     *
     * @param User $user
     * @return bool
     */
    public function views(User $user)
    {
        return $user->can('view_semesters');
    }

    /**
     * Check if the user can view a specific faculty.
     *
     * @param User $user
     * @return bool
     */
    public function view(User $user)
    {
        return $user->can('view_semester');
    }

    /**
     * Check if the user can create a faculty.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can('add_semester');
    }

    /**
     * Check if the user can update a specific faculty.
     *
     * @param User $user
     * @return bool
     */
    public function update(User $user)
    {
        return $user->can('edit_semester');
    }

    /**
     * Check if the user can delete a specific faculty.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user)
    {
        return $user->can('delete_semester');
    }
}
