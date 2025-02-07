<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Role;

class RolePolicy
{
    public function views(User $user)
    {
        return $user->can('view_roles');
    }
    public function view(User $user)
    {
        return  $user->can('view_role');
    }

    public function create(User $user)
    {
        return  $user->can('add_role', 'api');
    }

    public function update(User $user)
    {
        return  $user->can('edit_role', 'api');
    }

    public function delete(User $user)
    {
        return $user->can('delete_role', 'api');
    }
}
