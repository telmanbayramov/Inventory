<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'faculty_id', 'status'];
    protected $hidden = ['created_at', 'updated_at'];
    public $timestamps = false;
    public function faculty()
    {
        return $this->belongsTo(Faculty::class)->where('status', 1);
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'teacher_departments', 'department_id', 'user_id');
    }
}
