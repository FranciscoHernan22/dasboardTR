<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rutina;
use App\Models\User;
use App\Models\Ejercicio;
use Illuminate\Http\Request;  // ← esta línea


class RutinaApiController extends Controller
{
    public function ver($cliente, $semana, $dia)
    {
        $rutinas = Rutina::where('user_id', $cliente)
            ->where('semana', $semana)
            ->where('dia', $dia)
            ->orderBy('orden')
            ->orderBy('id')
            ->get()
            ->groupBy('grupo');

        // ── Nota de sesión ──
        $notaSesion = Rutina::where('user_id', $cliente)
            ->where('semana', $semana)
            ->where('dia', $dia)
            ->value('nota_sesion') ?? '';

        $bloques = $rutinas->map(function ($grupo) {

            $ejercicios = $grupo->map(function ($r) {

                $ejercicio = Ejercicio::find($r->ejercicio_id);

                $series = $r->series ?? [];
                if (is_string($series)) {
                    $series = json_decode($series, true) ?? [];
                }

                // Pasar todos los campos guardados tal cual
                $seriesNormalizadas = collect($series)
                    ->map(fn($s) => $s)
                    ->values()
                    ->toArray();

                return [
                    'nombre'   => $r->nombre,
                    'segmento' => $r->segmento,
                    'imagen' => $ejercicio && $ejercicio->imagen
    ? env('AWS_URL') . '/' . $ejercicio->imagen
    : null,
                    'nota_ej'  => $r->nota_ej ?? '',   // ← nuevo
                    'series'   => $seriesNormalizadas,
                ];

            })->values();

            return [
                'tipo'            => strtoupper($grupo->first()->tipo),
                'orden'           => $grupo->first()->orden,
                'descanso_valor'  => $grupo->first()->descanso_valor  ?? '',
                'descanso_unidad' => $grupo->first()->descanso_unidad ?? 'seg',
                'ejercicios'      => $ejercicios,
            ];

        })->values();

        return response()->json([
            'cliente'     => optional(User::find($cliente))->name ?? 'Desconocido',
            'semana'      => $semana,
            'dia'         => $dia,
            'nota_sesion' => $notaSesion,   // ← nuevo
            'bloques'     => $bloques,
        ]);
    }


   public function guardarPesos(Request $request, $clienteId, $semana, $dia)
{
    $bloques = $request->input('bloques', []);

    foreach ($bloques as $bloqueData) {
        $orden = $bloqueData['orden'];

        foreach ($bloqueData['ejercicios'] ?? [] as $ejData) {
            $rutina = \App\Models\Rutina::where('user_id', $clienteId)
                ->where('semana', $semana)
                ->where('dia', $dia)
                ->where('orden', $orden)
                ->where('nombre', $ejData['nombre'])
                ->first();

            if ($rutina) {
                $rutina->series = $ejData['series'];
                $rutina->save();
            }
        }
    }

    return response()->json(['ok' => true]);
}


}