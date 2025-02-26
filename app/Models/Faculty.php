<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status'];

    // Faculty'nin departmanlarla ilişkisi
    public function departments()
    {
        return $this->hasMany(Department::class)->where('status', 1);
    }

}
