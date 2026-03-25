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
        'series',
        'reps',
        'dia',
        'semana',
        'mes',
        'anio',
        'usuario',
        'entrenador',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
