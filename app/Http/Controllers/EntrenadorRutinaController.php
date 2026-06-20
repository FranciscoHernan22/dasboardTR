<?php
// DESTINO: app/Http/Controllers/EntrenadorRutinaController.php
// Esta es tu versión ORIGINAL, antes de los cambios de "Ejercicios por entrenador".
// Reemplaza tu controlador actual con este.

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rutina;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Ejercicio;
use Illuminate\Support\Facades\Auth;

class EntrenadorRutinaController extends Controller
{
  public function menu(User $cliente)
{
    if ($cliente->entrenador_id !== Auth::id()) {
        abort(403);
    }

    $plan = $cliente->plan;

    // Si no tiene plan → regresa a clientes con modal
    if (!$plan) {
        return redirect()->route('entrenador.clientes')
            ->with('sin_plan_cliente', $cliente->name);
    }

    $semanaInicio = $plan->semana_inicio;
    $semanaFin    = $plan->semana_inicio + $plan->semanas - 1;

    return view('rutina.menu', compact('cliente', 'semanaInicio', 'semanaFin'));
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

    $notaSesion = Rutina::where('user_id', $cliente->id)
        ->where('semana', $semana)
        ->where('dia', $dia)
        ->value('nota_sesion') ?? '';

    $plantillas = \App\Models\Plantilla::where('entrenador_id', Auth::id())
        ->orderBy('nombre')
        ->get();

    // ── Navegador: días que ya tienen rutina ──
    $diasConRutina = Rutina::where('user_id', $cliente->id)
        ->selectRaw('CONCAT(semana, "-", dia) as clave')
        ->pluck('clave')
        ->unique()
        ->values()
        ->toArray();

    return view('layouts.editar-rutina', compact(
        'cliente', 'semana', 'dia', 'bloques',
        'ejerciciosPorGrupo', 'ejercicios',
        'notaSesion', 'plantillas',
        'diasConRutina'
    ));
}
    public function guardar(Request $request, User $cliente, $semana, $dia)
    {
        if ($cliente->entrenador_id !== Auth::id()) {
            abort(403);
        }

        $raw   = $request->input('datos_json', '{}');
        $datos = json_decode($raw, true);

        $nota_sesion = trim($datos['nota_sesion'] ?? '');

        if (json_last_error() !== JSON_ERROR_NONE || empty($datos['bloques'])) {
            return back()->withErrors(['error' => 'Error al procesar los datos. Intenta de nuevo.']);
        }

        // Calcular fecha real — siempre desde fecha_inicio de semana 1
        $plan = $cliente->plan;
        if ($plan && $plan->fecha_inicio) {
            $fecha = Carbon::parse($plan->fecha_inicio)
                ->addDays(($semana - 1) * 7 + ($dia - 1))
                ->toDateString();
        } else {
            $fecha = now()->toDateString();
        }

        Rutina::where('user_id', $cliente->id)
            ->where('semana', $semana)
            ->where('dia', $dia)
            ->delete();

        $orden = 0;

        foreach ($datos['bloques'] as $grupo => $bloque) {

            $descanso_valor  = $bloque['descanso_valor']  ?? '';
            $descanso_unidad = $bloque['descanso_unidad'] ?? 'seg';

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
                            $serie['reps_rp']   = (int)   ($s['reps_rp']   ?? 0);
                            $serie['peso_rp']   = (float) ($s['peso_rp']   ?? 0);
                            $serie['unidad_rp'] =          $s['unidad_rp'] ?? 'kg';
                            $serie['descanso']  = (int)   ($s['descanso']  ?? 15);
                            break;

                        case '21s':
                            $serie['reps_21s']   = (int)   ($s['reps_21s']   ?? 7);
                            $serie['peso_21s']   = (float) ($s['peso_21s']   ?? 0);
                            $serie['unidad_21s'] =          $s['unidad_21s'] ?? 'kg';
                            break;

                        case '10_21':
                            $serie['peso_10']   = (float) ($s['peso_10']   ?? 0);
                            $serie['unidad_10'] =          $s['unidad_10'] ?? 'kg';
                            $serie['peso_21']   = (float) ($s['peso_21']   ?? 0);
                            $serie['unidad_21'] =          $s['unidad_21'] ?? 'kg';
                            break;

                        case 'isometria':
                            $serie['peso_iso']   = (float) ($s['peso_iso']   ?? 0);
                            $serie['unidad_iso'] =          $s['unidad_iso'] ?? 'kg';
                            $serie['reps_brazo'] = (int)   ($s['reps_brazo'] ?? 4);
                            $serie['reps_ambos'] = (int)   ($s['reps_ambos'] ?? 8);
                            break;

                        case 'forzadas':
                            $serie['reps_fz']        = (int)   ($s['reps_fz']        ?? 0);
                            $serie['reps_asistidas'] = (int)   ($s['reps_asistidas'] ?? 0);
                            $serie['peso_fz']        = (float) ($s['peso_fz']        ?? 0);
                            $serie['unidad_fz']      =          $s['unidad_fz']      ?? 'kg';
                            break;

                        case 'parciales':
                            $serie['reps_pc']   = (int)   ($s['reps_pc']   ?? 0);
                            $serie['peso_pc']   = (float) ($s['peso_pc']   ?? 0);
                            $serie['unidad_pc'] =          $s['unidad_pc'] ?? 'kg';
                            break;

                        case 'negativas':
                            $serie['reps_ng']   = (int)   ($s['reps_ng']   ?? 0);
                            $serie['peso_ng']   = (float) ($s['peso_ng']   ?? 0);
                            $serie['unidad_ng'] =          $s['unidad_ng'] ?? 'kg';
                            break;

                        default:
                            $serie['reps']   = (int)   ($s['reps']   ?? 0);
                            $serie['peso']   = (float) ($s['peso']   ?? 0);
                            $serie['unidad'] =          $s['unidad'] ?? 'kg';
                    }

                    if (!empty($s['tempo_activo']) && $s['tempo_activo'] === '1') {
                        $serie['tempo_activo']      = '1';
                        $serie['tempo_excentrica']  = $s['tempo_excentrica']  ?? '0';
                        $serie['tempo_pausa']       = $s['tempo_pausa']       ?? '0';
                        $serie['tempo_concentrica'] = $s['tempo_concentrica'] ?? '0';
                    }

                    if (!empty($s['rir_activo']) && $s['rir_activo'] === '1') {
                        $serie['rir_activo'] = '1';
                        $serie['rir_modo']   = $s['rir_modo']  ?? 'rir';
                        $serie['rir_valor']  = $s['rir_valor'] ?? '';
                    }

                    $series[] = $serie;
                }

                Rutina::create([
                    'user_id'         => $cliente->id,
                    'semana'          => $semana,
                    'dia'             => $dia,
                    'fecha'           => $fecha,
                    'grupo'           => $grupo,
                    'tipo'            => $bloque['tipo'],
                    'orden'           => $orden,
                    'ejercicio_id'    => $ejercicio->id,
                    'segmento'        => $ejercicio->segmento,
                    'nombre'          => $ejercicio->nombre,
                    'series'          => $series,
                    'descanso_valor'  => $descanso_valor,
                    'descanso_unidad' => $descanso_unidad,
                    'nota_sesion'     => $nota_sesion,
                    'nota_ej'         => trim($ej['nota_ej'] ?? ''),
                ]);
            }
            $orden++;
        }

        return back()->with('success', 'Rutina guardada correctamente');
    }
}