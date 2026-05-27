<?php
// app/Models/DiaCompletado.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiaCompletado extends Model
{
    protected $table    = 'dias_completados';
    protected $fillable = ['user_id', 'semana', 'dia', 'fecha_completado'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}