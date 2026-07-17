<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteNota extends Model
{
    protected $fillable = ['user_id', 'entrenador_id', 'contenido', 'etiqueta'];

    public function cliente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function entrenador()
    {
        return $this->belongsTo(User::class, 'entrenador_id');
    }
}