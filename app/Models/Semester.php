<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;
    protected $fillable = ['year','semester_num', 'status'];

    // // Faculty'nin departmanlarla ilişkisi
    // public function departments()
    // {
    //     return $this->hasMany(Department::class)->where('status', 1);
    // }

    
}
