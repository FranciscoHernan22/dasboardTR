<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteMedida extends Model
{
    protected $fillable = [
        'user_id', 'mes', 'peso', 'cintura', 'cadera', 'pecho', 'brazo', 'muslo', 'grasa_corporal',
    ];

    protected $casts = [
        'mes' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}