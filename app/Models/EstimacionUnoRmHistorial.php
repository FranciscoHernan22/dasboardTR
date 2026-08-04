<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Log de TODOS los candidatos de 1RM calculados a lo largo del tiempo,
 * hayan reemplazado o no al valor vigente en EstimacionUnoRm.
 */
class EstimacionUnoRmHistorial extends Model
{
    protected $table = 'estimaciones_1rm_historial';

    protected $fillable = [
        'user_id',
        'ejercicio_id',
        'valor_1rm_kg',
        'nivel_confianza',
        'reps_base',
        'peso_base',
        'unidad_base',
        'se_uso_como_vigente',
        'fecha_calculo',
    ];

    protected $casts = [
        'valor_1rm_kg'         => 'float',
        'peso_base'            => 'float',
        'reps_base'            => 'integer',
        'se_uso_como_vigente'  => 'boolean',
        'fecha_calculo'        => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ejercicio()
    {
        return $this->belongsTo(Ejercicio::class);
    }
}