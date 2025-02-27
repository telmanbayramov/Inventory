<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Equipment;

class DeviceTypePolicy
{
      /**
     * Check if the user can view any devices.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user) 
    {
        return $user->can('view_devices');
    }
    

    /**
     * Check if the user can view a specific device.
     *
     * @param User $user
     * @param Equipment $equipment
     * @return bool
     */
    public function view(User $user)
    {
        return $user->can('view_device');
    }

    /**
     * Check if the user can create a device.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can('add_device');
    }

    /**
     * Check if the user can update a specific device.
     *
     * @param User $user
     * @param Equipment $device
     * @return bool
     */
    public function update(User $user)
    {
        return $user->can('edit_device');
    }

    /**
     * Check if the user can delete a specific device.
     *
     * @param User $user
     * @param Equipment $device
     * @return bool
     */
    public function delete(User $user)
    {
        return $user->can('delete_device');
    }
}
