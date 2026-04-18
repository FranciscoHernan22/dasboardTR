<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rutina;
use App\Models\User;
use App\Models\Ejercicio;

class RutinaApiController extends Controller
{
    public function ver($cliente, $semana, $dia)
    {
        // Obtener todas las rutinas del cliente para esa semana y día
        $rutinas = Rutina::where('user_id', $cliente)
            ->where('semana', $semana)
            ->where('dia', $dia)
            ->orderBy('orden')
            ->get()
            ->groupBy('grupo');

        // Mapear bloques y ejercicios
        $bloques = $rutinas->map(function ($grupo) {
            return [
                'tipo' => $grupo->first()->tipo,
                'orden' => $grupo->first()->orden,
                'ejercicios' => $grupo->map(function ($r) {
                    // 🔥 Obtener ejercicio real para acceder a la imagen
                $ejercicio = Ejercicio::where('nombre', $r->nombre)->first();
                
                    return [
                        'segmento' => $r->segmento,
                        'ejercicio' => $ejercicio->nombre ?? $r->nombre, // fallback si no existe
                        'series' => $r->series ?? 0,
                        'reps' => $r->reps ?? 0,
                        'imagen' => $ejercicio && $ejercicio->imagen
                            ? asset('storage/' . $ejercicio->imagen)
                            : null
                    ];
                })->values()
            ];
        })->values();

        // Retornar JSON
        return response()->json([
            'cliente' => optional(User::find($cliente))->name ?? 'Desconocido',
            'semana' => $semana,
            'dia' => $dia,
            'bloques' => $bloques
        ]);
    }
}