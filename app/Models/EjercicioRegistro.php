<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EjercicioRegistro extends Model
{
    protected $fillable = ['user_id', 'ejercicio', 'peso', 'series', 'reps', 'fecha'];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}