<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Faculty;

class RoomPolicy
{
    /**
     * Check if the user can view the faculties.
     *
     * @param User $user
     * @return bool
     */
    public function views(User $user)
    {
        return $user->can('view_rooms');
    }

    /**
     * Check if the user can view a specific faculty.
     *
     * @param User $user
     * @return bool
     */
    public function view(User $user)
    {
        return $user->can('view_room');
    }

    /**
     * Check if the user can create a faculty.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can('add_room');
    }

    /**
     * Check if the user can update a specific faculty.
     *
     * @param User $user
     * @return bool
     */
    public function update(User $user)
    {
        return $user->can('edit_room');
    }

    /**
     * Check if the user can delete a specific faculty.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user)
    {
        return $user->can('delete_room');
    }
}
