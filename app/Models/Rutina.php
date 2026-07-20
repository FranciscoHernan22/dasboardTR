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
    'ejercicio_id',
    'series',
    'dia',
    'semana',
    'mes',
    'anio',
    'usuario',
    'entrenador',
    'orden',
    'descanso_valor',
    'descanso_unidad',
        'descansos_serie',   // ⭐ AGREGAR ESTA LÍNEA

    'nota_sesion',
    'nota_ej',
    'fecha',        // ← agregar esta línea
];

    protected $casts = [
        'series' => 'array',
            'descansos_serie' => 'array',   // ⭐ AGREGAR

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}