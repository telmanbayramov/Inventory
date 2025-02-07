<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Group;

class GroupPolicy
{
    /**
     * Check if the user can view the groups.
     *
     * @param User $user
     * @return bool
     */
    public function views(User $user)
    {
        return $user->can('view_groups');
    }

    /**
     * Check if the user can view a specific Group.
     *
     * @param User $user
     * @param Group $Group
     * @return bool
     */
    public function view(User $user)
    {
        return $user->can('view_group');
    }

    /**
     * Check if the user can create a Group.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can('add_group');
    }

    /**
     * Check if the user can update a specific group.
     *
     * @param User $user
     * @return bool
     */
    public function update(User $user)
    {
        return $user->can('edit_group');
    }

    /**
     * Check if the user can delete a specific group.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user)
    {
        return $user->can('delete_group');
    }
}
