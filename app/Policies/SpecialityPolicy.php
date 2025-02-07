<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Speciality;

class SpecialityPolicy
{
    /**
     * Check if the user can view the specialitys.
     *
     * @param User $user
     * @return bool
     */
    public function views(User $user)
    {
        return $user->can('view_specialities');
    }

    /**
     * Check if the user can view a specific speciality.
     *
     * @param User $user
     * @param Speciality $speciality
     * @return bool
     */
    public function view(User $user)
    {
        return $user->can('view_speciality');
    }

    /**
     * Check if the user can create a speciality.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can('add_speciality');
    }

    /**
     * Check if the user can update a specific speciality.
     *
     * @param User $user
     * @param Speciality $speciality
     * @return bool
     */
    public function update(User $user)
    {
        return $user->can('edit_speciality');
    }

    /**
     * Check if the user can delete a specific speciality.
     *
     * @param User $user
     * @param Speciality $speciality
     * @return bool
     */
    public function delete(User $user)
    {
        return $user->can('delete_speciality');
    }
}
