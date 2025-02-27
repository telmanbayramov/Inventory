<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;
    protected $table = 'devices';
    protected $fillable = ['room_id', 'device_type_id','quantity'];
    protected $hidden = ['created_at', 'updated_at'];
    public $timestamps = false;
    public function deviceType()
    {
        return $this->belongsTo(DeviceType::class, 'device_type_id');
    }
}
