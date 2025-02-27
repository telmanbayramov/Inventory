<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Equipment;

class EquipmentPolicy
{
      /**
     * Check if the user can view any equipments.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user) 
    {
        return $user->can('view_equipment_rooms');
    }
    

    /**
     * Check if the user can view a specific equipment.
     *
     * @param User $user
     * @param Equipment $equipment
     * @return bool
     */
    public function view(User $user)
    {
        return $user->can('view_equipment_room');
    }

    /**
     * Check if the user can create a equipment.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->can('add_equipment_room');
    }

    /**
     * Check if the user can update a specific equipment.
     *
     * @param User $user
     * @param Equipment $equipment
     * @return bool
     */
    public function update(User $user)
    {
        return $user->can('edit_equipment_room');
    }

    /**
     * Check if the user can delete a specific equipment.
     *
     * @param User $user
     * @param Equipment $equipment
     * @return bool
     */
    public function delete(User $user)
    {
        return $user->can('delete_equipment_room');
    }
}
