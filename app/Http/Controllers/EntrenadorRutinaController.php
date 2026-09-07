<?php
// DESTINO: app/Http/Controllers/EntrenadorRutinaController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rutina;
use App\Models\Plan;
use App\Models\User;
use App\Models\DiaCompletado;
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

        // Asegura que este entrenador ya tenga su propio set de ejercicios
        // (la primera vez le clonamos el catálogo default; las siguientes no hace nada)
        Ejercicio::asegurarDefaultsPara(Auth::id());

        $ejercicios = Ejercicio::where('entrenador_id', Auth::id())
            ->get()
            ->keyBy('id');

        $ejerciciosPorGrupo = Ejercicio::select('id', 'nombre', 'segmento', 'imagen')
            ->where('entrenador_id', Auth::id())
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
        ->addDays(($semana - $plan->semana_inicio) * 7 + ($dia - 1))
        ->toDateString();
} else {
    $fecha = now()->toDateString();
}


        Rutina::where('user_id', $cliente->id)
            ->where('semana', $semana)
            ->where('dia', $dia)
            ->delete();

        // El entrenador está reescribiendo este día desde el editor:
        // si el cliente lo había marcado como completado antes, ese
        // estado ya no corresponde a la rutina nueva.
        DiaCompletado::where('user_id', $cliente->id)
            ->where('semana', $semana)
            ->where('dia', $dia)
            ->delete();

        $orden = 0;

        foreach ($datos['bloques'] as $grupo => $bloque) {

            $descansos_serie = $bloque['descansos_serie'] ?? [];

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
                    'descansos_serie' => $descansos_serie,

                    'nota_sesion'     => $nota_sesion,
                    'nota_ej'         => trim($ej['nota_ej'] ?? ''),
                ]);
            }
            $orden++;
        }

        return back()->with('success', 'Rutina guardada correctamente');
    }

    /**
     * Intercambia el contenido completo (bloques, ejercicios, series, nota
     * de sesión y estado de día completado) entre dos días de la misma
     * semana. Se usa para el drag & drop del nav de días en el editor.
     */
    public function moverDia(Request $request, User $cliente)
    {
        if ($cliente->entrenador_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'semana'      => 'required|integer|min:1',
            'dia_origen'  => 'required|integer|min:1|max:7',
            'dia_destino' => 'required|integer|min:1|max:7',
        ]);

        $semana = (int) $request->semana;
        $diaA   = (int) $request->dia_origen;
        $diaB   = (int) $request->dia_destino;

        if ($diaA === $diaB) {
            return response()->json(['success' => false, 'message' => 'Selecciona dos días distintos.'], 422);
        }

        $plan = $cliente->plan;
$fechaFor = function ($dia) use ($plan, $semana) {
    if ($plan && $plan->fecha_inicio) {
        return Carbon::parse($plan->fecha_inicio)
            ->addDays(($semana - $plan->semana_inicio) * 7 + ($dia - 1))
            ->toDateString();
    }
    return now()->toDateString();
};

        // Día temporal (-1) para poder intercambiar A <-> B sin colisiones
        // de la restricción semana+dia mientras se hace el swap.
        Rutina::where('user_id', $cliente->id)
            ->where('semana', $semana)
            ->where('dia', $diaA)
            ->update(['dia' => -1]);

        Rutina::where('user_id', $cliente->id)
            ->where('semana', $semana)
            ->where('dia', $diaB)
            ->update(['dia' => $diaA, 'fecha' => $fechaFor($diaA)]);

        Rutina::where('user_id', $cliente->id)
            ->where('semana', $semana)
            ->where('dia', -1)
            ->update(['dia' => $diaB, 'fecha' => $fechaFor($diaB)]);

        // Lo mismo para el estado de "día completado" marcado por el cliente,
        // para que ese estado viaje junto con el contenido que le corresponde.
        DiaCompletado::where('user_id', $cliente->id)
            ->where('semana', $semana)
            ->where('dia', $diaA)
            ->update(['dia' => -1]);

        DiaCompletado::where('user_id', $cliente->id)
            ->where('semana', $semana)
            ->where('dia', $diaB)
            ->update(['dia' => $diaA]);

        DiaCompletado::where('user_id', $cliente->id)
            ->where('semana', $semana)
            ->where('dia', -1)
            ->update(['dia' => $diaB]);

        return response()->json(['success' => true]);
    }

    /**
     * Copia todos los días de una semana (con sus bloques/ejercicios/series)
     * hacia otra semana del mismo cliente. Sobrescribe lo que exista en destino.
     */
    public function copiarSemana(Request $request, User $cliente)
    {
        if ($cliente->entrenador_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'semana_origen'  => 'required|integer|min:1|max:52',
            'semana_destino' => 'required|integer|min:1|max:52',
        ]);

        $origen  = (int) $request->semana_origen;
        $destino = (int) $request->semana_destino;

        if ($origen === $destino) {
            return back()->withErrors(['error' => 'La semana origen y destino no pueden ser la misma.']);
        }

        $rutinasOrigen = Rutina::where('user_id', $cliente->id)
            ->where('semana', $origen)
            ->orderBy('orden')
            ->orderBy('id')
            ->get();

        if ($rutinasOrigen->isEmpty()) {
            return back()->withErrors(['error' => "La semana {$origen} no tiene rutinas para copiar."]);
        }

        $plan = $cliente->plan;

        // Limpia lo que ya exista en la semana destino antes de pegar
        Rutina::where('user_id', $cliente->id)
            ->where('semana', $destino)
            ->delete();

        // La semana destino es un plan nuevo para esos días: si el
        // cliente ya los había marcado como completados antes, ese
        // estado no debe seguir aplicando a lo que se está pegando ahora.
        DiaCompletado::where('user_id', $cliente->id)
            ->where('semana', $destino)
            ->delete();

        foreach ($rutinasOrigen as $r) {
    $fecha = $r->fecha;
    if ($plan && $plan->fecha_inicio) {
        $fecha = Carbon::parse($plan->fecha_inicio)
            ->addDays(($destino - $plan->semana_inicio) * 7 + ($r->dia - 1))
            ->toDateString();
    }
 
            $series          = is_string($r->series) ? json_decode($r->series, true) : $r->series;
            $descansosSerie  = is_string($r->descansos_serie) ? json_decode($r->descansos_serie, true) : $r->descansos_serie;

            // No arrastrar el flag 'completada' de la semana origen: la
            // copia es una rutina nueva, no una que el cliente ya hizo.
            $series = collect($series ?? [])->map(function ($serie) {
                unset($serie['completada']);
                return $serie;
            })->all();

            Rutina::create([
                'user_id'         => $cliente->id,
                'semana'          => $destino,
                'dia'             => $r->dia,
                'fecha'           => $fecha,
                'grupo'           => $r->grupo,
                'tipo'            => $r->tipo,
                'orden'           => $r->orden,
                'ejercicio_id'    => $r->ejercicio_id,
                'segmento'        => $r->segmento,
                'nombre'          => $r->nombre,
                'series'          => $series,
                'descansos_serie' => $descansosSerie ?? [],
                'nota_sesion'     => $r->nota_sesion,
                'nota_ej'         => $r->nota_ej,
            ]);
        }

        return back()->with('success', "Semana {$origen} copiada a la semana {$destino} correctamente.");
    }

    /**
     * Borra TODO el historial de rutinas de un cliente (todas las semanas/días)
     * y también su registro de días completados, para que un plan nuevo
     * empiece realmente desde cero.
     * Requiere que el entrenador escriba el nombre del cliente como confirmación.
     */
    public function borrarHistorial(Request $request, User $cliente)
    {
        if ($cliente->entrenador_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'confirmar_nombre' => 'required|string',
        ]);

        if (trim($request->confirmar_nombre) !== trim($cliente->name)) {
            return back()->withErrors(['error' => 'El nombre no coincide. No se borró nada.']);
        }

        Rutina::where('user_id', $cliente->id)->delete();
        DiaCompletado::where('user_id', $cliente->id)->delete();

        return redirect()->route('entrenador.rutina.editar', [$cliente->id, 1, 1])
            ->with('success', 'Historial de entrenamientos borrado correctamente.');
    }

    /**
     * Vacía únicamente una semana específica de un cliente (todos sus días)
     * y su registro de días completados en esa semana.
     */
    public function vaciarSemana(Request $request, User $cliente, $semana)
    {
        if ($cliente->entrenador_id !== Auth::id()) {
            abort(403);
        }

        Rutina::where('user_id', $cliente->id)
            ->where('semana', $semana)
            ->delete();

        DiaCompletado::where('user_id', $cliente->id)
            ->where('semana', $semana)
            ->delete();

        return back()->with('success', "Semana {$semana} vaciada correctamente.");
    }
}