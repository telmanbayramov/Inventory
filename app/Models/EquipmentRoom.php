<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentRoom extends Model
{
    use HasFactory;
    protected $table = 'equipment_rooms';
    protected $fillable = ['equipment_id', 'room_id', 'quantity','status','equipment_status'];
}
