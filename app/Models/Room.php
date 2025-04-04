<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'department_id', 'room_capacity', 'room_type_id', 'status', 'corp_id'];
    protected $hidden = ['created_at', 'updated_at'];
    public $timestamps = false;
    public function department()
    {
        return $this->belongsTo(Department::class)->where('status', 1);
    }
    public function room_type()
    {
        return $this->belongsTo(Room_Type::class)->where('status', 1);
    }
    public function corp()
    {
        return $this->belongsTo(Corp::class)->where('status', 1);
    }
    public function devices()
    {
        return $this->hasMany(Device::class);
    }
    
}
