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
        $rutinas = Rutina::where('user_id', $cliente)
            ->where('semana', $semana)
            ->where('dia', $dia)
            ->orderBy('orden')
            ->orderBy('id')
            ->get()
            ->groupBy('grupo');

        $bloques = $rutinas->map(function ($grupo) {

            $ejercicios = $grupo->map(function ($r) {

                $ejercicio = Ejercicio::find($r->ejercicio_id);

                // Normalizar series con todos los métodos
                $series = $r->series ?? [];
                if (is_string($series)) {
                    $series = json_decode($series, true) ?? [];
                }

                $seriesNormalizadas = collect($series)->map(function ($s) {
                    $metodo = $s['metodo'] ?? 'normal';
                    $base   = ['metodo' => $metodo];

                    switch ($metodo) {
                        case '888':
                            return array_merge($base, [
                                'peso1' => $s['peso1'] ?? 0,
                                'peso2' => $s['peso2'] ?? 0,
                                'peso3' => $s['peso3'] ?? 0,
                                'descripcion' => '8 reps × 3 pesos descendentes',
                            ]);
                        case 'restpause':
                            return array_merge($base, [
                                'reps'     => $s['reps']     ?? 0,
                                'peso'     => $s['peso']     ?? 0,
                                'descanso' => $s['descanso'] ?? 15,
                                'descripcion' => "Fallo → pausa {$s['descanso']}s → continuar",
                            ]);
                        case '21s':
                            return array_merge($base, [
                                'peso' => $s['peso'] ?? 0,
                                'descripcion' => '7 bajo + 7 alto + 7 completo',
                            ]);
                        case '10_21':
                            return array_merge($base, [
                                'peso_10' => $s['peso_10'] ?? 0,
                                'peso_21' => $s['peso_21'] ?? 0,
                                'descripcion' => '10 reps → −40% → 21s',
                            ]);
                        case 'isometria':
                            return array_merge($base, [
                                'peso'       => $s['peso']       ?? 0,
                                'reps_brazo' => $s['reps_brazo'] ?? 4,
                                'reps_ambos' => $s['reps_ambos'] ?? 8,
                                'descripcion' => 'Isometría + ROM completo',
                            ]);
                        case 'forzadas':
                            return array_merge($base, [
                                'reps'           => $s['reps']           ?? 0,
                                'reps_asistidas' => $s['reps_asistidas'] ?? 0,
                                'peso'           => $s['peso']           ?? 0,
                                'descripcion'    => 'Hasta fallo + reps asistidas',
                            ]);
                        case 'parciales':
                            return array_merge($base, [
                                'reps' => $s['reps'] ?? 0,
                                'peso' => $s['peso'] ?? 0,
                                'descripcion' => 'Rango parcial de movimiento',
                            ]);
                        case 'negativas':
                            return array_merge($base, [
                                'reps' => $s['reps'] ?? 0,
                                'peso' => $s['peso'] ?? 0,
                                'descripcion' => 'Fase excéntrica lenta',
                            ]);
                        default: // normal
                            return array_merge($base, [
                                'reps' => $s['reps'] ?? 0,
                                'peso' => $s['peso'] ?? 0,
                            ]);
                    }
                })->values()->toArray();

                return [
                    'nombre'   => $r->nombre,
                    'segmento' => $r->segmento,
                    'imagen'   => $ejercicio && $ejercicio->imagen
                                    ? asset('storage/' . $ejercicio->imagen)
                                    : null,
                    'series'   => $seriesNormalizadas,
                ];

            })->values();

            return [
                'tipo'       => strtoupper($grupo->first()->tipo),
                'orden'      => $grupo->first()->orden,
                'ejercicios' => $ejercicios,
            ];

        })->values();

        return response()->json([
            'cliente' => optional(User::find($cliente))->name ?? 'Desconocido',
            'semana'  => $semana,
            'dia'     => $dia,
            'bloques' => $bloques,
        ]);
    }
}
