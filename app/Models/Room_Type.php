<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room_Type extends Model
{
    use HasFactory;
    protected $table = 'room_types';
    protected $fillable = ['name', 'status'];
    protected $hidden = ['created_at', 'updated_at'];
    public $timestamps = false;
    // // Faculty'nin departmanlarla ilişkisi
    // public function departments()
    // {
    //     return $this->hasMany(Department::class)->where('status', 1);
    // }

    
}
