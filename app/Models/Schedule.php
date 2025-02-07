<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status', 'faculty_id', 'department_id', 'group_id', 'corp_id', 'room_id', 'lesson_type_id', 'hour_id', 'semester_id', 'week_type_id', 'day_id', 'user_id', 'discipline_id', 'status', 'confirm_status'];
    public function faculty()
    {
        return $this->belongsTo(Faculty::class)->where('status', 1);
    }

    public function department()
    {
        return $this->belongsTo(Department::class)->where('status', 1);
    }

    public function group()
    {
        return $this->belongsTo(Group::class)->where('status', 1);
    }

    public function corp()
    {
        return $this->belongsTo(Corp::class)->where('status', 1);
    }

    public function room()
    {
        return $this->belongsTo(Room::class)->where('status', 1);
    }

    public function lessonType()
    {
        return $this->belongsTo(Lesson_Type::class)->where('status', 1);
    }

    public function hour()
    {
        return $this->belongsTo(Hour::class)->where('status', 1);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class)->where('status', 1);
    }

    public function weekType()
    {
        return $this->belongsTo(Week_Type::class)->where('status', 1);
    }

    public function day()
    {
        return $this->belongsTo(Day::class)->where('status', 1);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->where('status', 1);
    }

    public function discipline()
    {
        return $this->belongsTo(Discipline::class)->where('status', 1);
    }
}
