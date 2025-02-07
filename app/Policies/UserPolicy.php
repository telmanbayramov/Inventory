<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function views(User $user)
    {
        return $user->can('view_users');
    }
    public function view(User $user)
    {
        return $user->can('view_user');
    }

    public function create(User $user)
    {
        return $user->can('add_user', 'api');
    }

    public function update(User $user)
    {
        return $user->can('edit_user', 'api');
    }

    public function delete(User $user)
    {
        return $user->can('delete_user', 'api');
    }
}
