<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 1RM estimado VIGENTE de un cliente para un ejercicio.
 * Solo existe una fila por (user_id, ejercicio_id).
 */
class EstimacionUnoRm extends Model
{
    protected $table = 'estimaciones_1rm';

    protected $fillable = [
        'user_id',
        'ejercicio_id',
        'valor_1rm_kg',
        'nivel_confianza',
        'reps_base',
        'peso_base',
        'unidad_base',
        'fecha_calculo',
    ];

    protected $casts = [
        'valor_1rm_kg'  => 'float',
        'peso_base'     => 'float',
        'reps_base'     => 'integer',
        'fecha_calculo' => 'datetime',
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