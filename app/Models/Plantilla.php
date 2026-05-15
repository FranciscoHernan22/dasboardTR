<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plantilla extends Model
{
    protected $fillable = ['entrenador_id', 'nombre', 'descripcion', 'bloques'];

    protected $casts = ['bloques' => 'array'];

    public function entrenador()
    {
        return $this->belongsTo(Entrenador::class, 'entrenador_id');
    }
}