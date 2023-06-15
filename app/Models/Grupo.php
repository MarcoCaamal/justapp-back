<?php

namespace App\Models;

use App\Models\Justificacion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'semestre',
        'carrera',
        'aula'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'grupo_user', 'grupo_id', 'user_id');
    }
}
