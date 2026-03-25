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
    ];

    protected $hidden = [
        'password',
    ];

    public function users()
{
    return $this->hasMany(User::class, 'entrenador_id');
}
}

