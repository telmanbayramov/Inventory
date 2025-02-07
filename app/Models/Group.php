<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'faculty_id','student_amount','group_type','course_id','speciality_id','group_level', 'status'];

    // Department'in fakülte ile ilişkisi
    public function faculty()
    {
        return $this->belongsTo(Faculty::class)->where('status', 1);
    }
    public function speciality()
    {
        return $this->belongsTo(Speciality::class)->where('status', 1);
    }
    public function course()
    {
        return $this->belongsTo(Course::class)->where('status', 1);
    }
}
