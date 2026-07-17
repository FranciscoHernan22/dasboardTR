<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteFoto extends Model
{
    protected $fillable = ['user_id', 'mes', 'ruta', 'angulo'];

    protected $casts = [
        'mes' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->ruta);
    }
}