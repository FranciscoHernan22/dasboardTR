<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rutina;
use App\Models\User;

class RutinaApiController extends Controller
{
  public function ver($cliente, $semana, $dia)
{
    $rutinas = Rutina::where('user_id', $cliente)
        ->where('semana', $semana)
        ->where('dia', $dia)
        ->orderBy('orden')
        ->get()
        ->groupBy('grupo');

    $bloques = $rutinas->map(function ($grupo) {
        return [
            'tipo' => $grupo->first()->tipo,
            'orden' => $grupo->first()->orden,
            'ejercicios' => $grupo->map(function ($r) {
                return [
                    'segmento' => $r->segmento,
                    'ejercicio' => $r->nombre, // 👈 AQUÍ
                    'series' => $r->series,
                    'reps' => $r->reps,
                ];
            })->values()
        ];
    })->values();

    return response()->json([
        'cliente' => optional(User::find($cliente))->name,
        'semana' => $semana,
        'dia' => $dia,
        'bloques' => $bloques
    ]);
}

}
