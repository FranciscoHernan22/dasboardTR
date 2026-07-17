<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesionEntrenamiento extends Model
{
        protected $table = 'sesiones_entrenamiento';

        
    protected $fillable = ['user_id', 'fecha', 'completada'];

    protected $casts = [
        'fecha' => 'date',
        'completada' => 'boolean',
    ];

    public function cliente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}