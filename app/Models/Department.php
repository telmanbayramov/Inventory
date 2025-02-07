<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'faculty_id', 'status'];

    // Department'in fakülte ile ilişkisi
    public function faculty()
    {
        return $this->belongsTo(Faculty::class)->where('status', 1);
    }
    public function disciplines()
    {
        return $this->hasMany(Discipline::class)->where('status', 1);
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'teacher_departments', 'department_id', 'user_id');
    }
}
