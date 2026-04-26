<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rutina;
use App\Models\User;
use App\Models\Ejercicio;
use Illuminate\Support\Facades\Auth;

class EntrenadorRutinaController extends Controller
{
    public function menu(User $cliente)
    {
        if ($cliente->entrenador_id !== Auth::id()) {
            abort(403);
        }
        return view('rutina.menu', compact('cliente'));
    }

    public function editar(User $cliente, $semana, $dia)
    {
        if ($cliente->entrenador_id !== Auth::id()) {
            abort(403);
        }

        $ejercicios = Ejercicio::all()->keyBy('id');

        $ejerciciosPorGrupo = Ejercicio::select('id', 'nombre', 'segmento', 'imagen')
            ->get()
            ->groupBy('segmento');

        $bloques = Rutina::where('user_id', $cliente->id)
            ->where('semana', $semana)
            ->where('dia', $dia)
            ->orderBy('orden')
            ->orderBy('id')
            ->get()
            ->groupBy('grupo');

        return view('layouts.editar-rutina', compact(
            'cliente', 'semana', 'dia', 'bloques', 'ejerciciosPorGrupo', 'ejercicios'
        ));
    }

    public function guardar(Request $request, User $cliente, $semana, $dia)
    {
        if ($cliente->entrenador_id !== Auth::id()) {
            abort(403);
        }

        // ── Decodificar el JSON enviado desde el blade ──
        $raw   = $request->input('datos_json', '{}');
        $datos = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || empty($datos['bloques'])) {
            return back()->withErrors(['error' => 'Error al procesar los datos. Intenta de nuevo.']);
        }

        Rutina::where('user_id', $cliente->id)
            ->where('semana', $semana)
            ->where('dia', $dia)
            ->delete();

        $orden = 0;

        foreach ($datos['bloques'] as $grupo => $bloque) {
            foreach ($bloque['ejercicios'] ?? [] as $ej) {

                if (empty($ej['ejercicio_id'])) continue;

                $ejercicio = Ejercicio::find($ej['ejercicio_id']);
                if (!$ejercicio) continue;

                $series = [];

                foreach ($ej['series'] ?? [] as $s) {
                    $metodo = $s['metodo'] ?? 'normal';
                    $serie  = ['metodo' => $metodo];

                    switch ($metodo) {

                        case '888':
                            $serie['reps_888'] = (int)   ($s['reps_888'] ?? 8);
                            $serie['peso1']    = (float) ($s['peso1']    ?? 0);
                            $serie['unidad1']  =          $s['unidad1']  ?? 'kg';
                            $serie['peso2']    = (float) ($s['peso2']    ?? 0);
                            $serie['unidad2']  =          $s['unidad2']  ?? 'kg';
                            $serie['peso3']    = (float) ($s['peso3']    ?? 0);
                            $serie['unidad3']  =          $s['unidad3']  ?? 'kg';
                            break;

                        case 'restpause':
                            $serie['reps']      = (int)   ($s['reps_rp']  ?? 0);
                            $serie['peso']      = (float) ($s['peso_rp']  ?? 0);
                            $serie['unidad_rp'] =          $s['unidad_rp'] ?? 'kg';
                            $serie['descanso']  = (int)   ($s['descanso'] ?? 15);
                            break;

                        case '21s':
                            $serie['reps_21s']   = (int)   ($s['reps_21s']  ?? 7);
                            $serie['peso_21s']   = (float) ($s['peso_21s']  ?? 0);
                            $serie['unidad_21s'] =          $s['unidad_21s'] ?? 'kg';
                            break;

                        case '10_21':
                            $serie['peso_10']   = (float) ($s['peso_10']  ?? 0);
                            $serie['unidad_10'] =          $s['unidad_10'] ?? 'kg';
                            $serie['peso_21']   = (float) ($s['peso_21']  ?? 0);
                            $serie['unidad_21'] =          $s['unidad_21'] ?? 'kg';
                            break;

                        case 'isometria':
                            $serie['peso']       = (float) ($s['peso_iso']   ?? 0);
                            $serie['unidad_iso'] =          $s['unidad_iso']  ?? 'kg';
                            $serie['reps_brazo'] = (int)   ($s['reps_brazo'] ?? 4);
                            $serie['reps_ambos'] = (int)   ($s['reps_ambos'] ?? 8);
                            break;

                        case 'forzadas':
                            $serie['reps']           = (int)   ($s['reps_fz']        ?? 0);
                            $serie['reps_asistidas'] = (int)   ($s['reps_asistidas'] ?? 0);
                            $serie['peso']           = (float) ($s['peso_fz']        ?? 0);
                            $serie['unidad_fz']      =          $s['unidad_fz']       ?? 'kg';
                            break;

                        case 'parciales':
                            $serie['reps']      = (int)   ($s['reps_pc'] ?? 0);
                            $serie['peso']      = (float) ($s['peso_pc'] ?? 0);
                            $serie['unidad_pc'] =          $s['unidad_pc'] ?? 'kg';
                            break;

                        case 'negativas':
                            $serie['reps']      = (int)   ($s['reps_ng'] ?? 0);
                            $serie['peso']      = (float) ($s['peso_ng'] ?? 0);
                            $serie['unidad_ng'] =          $s['unidad_ng'] ?? 'kg';
                            break;

                        default: // normal
                            $serie['reps']   = (int)   ($s['reps']   ?? 0);
                            $serie['peso']   = (float) ($s['peso']   ?? 0);
                            $serie['unidad'] =          $s['unidad'] ?? 'kg';
                    }

                    $series[] = $serie;
                }

                Rutina::create([
                    'user_id'      => $cliente->id,
                    'semana'       => $semana,
                    'dia'          => $dia,
                    'grupo'        => $grupo,
                    'tipo'         => $bloque['tipo'],
                    'orden'        => $orden,
                    'ejercicio_id' => $ejercicio->id,
                    'segmento'     => $ejercicio->segmento,
                    'nombre'       => $ejercicio->nombre,
                    'series'       => $series,
                ]);
            }
            $orden++;
        }

        return back()->with('success', 'Rutina guardada correctamente');
    }
}
