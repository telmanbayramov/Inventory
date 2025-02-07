<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasPermissions;
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Kullanıcının birincil anahtarını döndürür
    }

    public function getJWTCustomClaims()
    {
        return []; // İsteğe bağlı olarak özel talepler ekleyebilirsiniz
    }

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function departments()
    {
        return $this->belongsToMany(Department::class, 'teacher_departments', 'user_id', 'department_id');
    }


    public function faculty()
    {
        return $this->belongsTo(Faculty::class)->where('status', 1);
    }
}
