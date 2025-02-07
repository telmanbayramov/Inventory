<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Faculty;

class Room_TypePolicy
{
    /**
     * Check if the user can view the faculties.
     *
     * @param User $user
     * @return bool
     */
    public function views(User $user)
    {
        return $user->can('view_room_types');
    }

    /**
     * Check if the user can view a specific faculty.
     *
     * @param User $user
     * @return bool
     */
    public function view(User $user)
    {
        return $user->can('view_room_type');
    }

    /**
     * Check if the user can create a faculty.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can('add_room_type');
    }

    /**
     * Check if the user can update a specific faculty.
     *
     * @param User $user
     * @return bool
     */
    public function update(User $user)
    {
        return $user->can('edit_room_type');
    }

    /**
     * Check if the user can delete a specific faculty.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user)
    {
        return $user->can('delete_room_type');
    }
}
