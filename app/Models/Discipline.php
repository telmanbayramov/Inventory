<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discipline extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status', 'department_id'];

    // Faculty'nin departmanlarla ilişkisi
    public function department()
    {
        return $this->belongsTo(Department::class)->where('status', 1);
    }

    // public function specialities()
    // {
    //     return $this->hasMany(Speciality::class)->where('status', 1);
    // }
}
