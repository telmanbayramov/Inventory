<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Permission;

class PermissionPolicy
{
    public function views(User $user)
    {
        return $user->can('view_permissions');
    }
    public function view(User $user)
    {
        return  $user->can('view_permission');
    }

    public function create(User $user)
    {
        return  $user->can('add_permission', 'api');
    }

    public function update(User $user)
    {
        return  $user->can('edit_permission', 'api');
    }

    public function delete(User $user)
    {
        return $user->can('delete_permission', 'api');
    }
}
