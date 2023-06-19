<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use App\Models\Grupo;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
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

    // Relaciones

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'grupo_user', 'user_id', 'grupo_id')->withTimestamps()->withPivot(['is_active']);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    public function justificaciones()
    {
        if (
            User::whereHas('roles', function ($query) {
                $query->where('role_name', 'profesor');
            })->exists()
        ) {
            return $this->hasMany(Justificacion::class, 'profesor_id');
        }
        if (
            User::whereHas('roles', function ($query) {
                $query->where('role_name', 'alumno');
            })->exists()
        ) {
            return $this->hasMany(Justificacion::class, 'alumno_id');
        }
    }
}
