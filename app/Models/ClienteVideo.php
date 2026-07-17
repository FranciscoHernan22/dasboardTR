<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteVideo extends Model
{
    protected $fillable = ['user_id', 'ejercicio', 'tipo', 'url'];

    public function cliente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}