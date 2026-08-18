<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Entrenador extends Authenticatable
{
    use HasFactory;

    protected $table = 'entrenadores';

    protected $fillable = [
        'nombre',
        'username',
        'email',
        'password',
        'role',
        'activo',
        'ultimo_pago',
        'vence_el',
        'notas_pago',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'activo'      => 'boolean',
        'ultimo_pago' => 'date',
        'vence_el'    => 'date',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'entrenador_id');
    }
}