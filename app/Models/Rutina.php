<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rutina extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tipo',
        'grupo',
        'segmento',
        'nombre',
            'ejercicio_id',  // 👈 ¿está esto?
        'series',
        'dia',
        'semana',
        'mes',
        'anio',
        'usuario',
        'entrenador',
    ];

    protected $casts = [
        'series' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}