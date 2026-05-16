<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $table = 'planes';

    protected $fillable = [
        'user_id',
        'semanas',
        'semana_inicio',
        'fecha_inicio',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}