<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Speciality extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'faculty_id', 'status'];

    // Department'in fakülte ile ilişkisi
    public function faculty()
    {
        return $this->belongsTo(Faculty::class)->where('status', 1);
    }
    public function groups()
    {
        return $this->hasMany(Group::class)->where('status', 1);
    }

   
}
