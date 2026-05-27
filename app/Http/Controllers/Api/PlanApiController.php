<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use App\Models\Rutina;
use App\Models\DiaCompletado;
use Carbon\Carbon;

class PlanApiController extends Controller
{
    // GET /api/cliente/{id}/semana-actual
    public function semanaActual($clienteId)
    {
        $cliente = User::findOrFail($clienteId);
        $plan    = Plan::where('user_id', $clienteId)->first();

        if (!$plan) {
            return response()->json(['error' => 'Sin plan asignado'], 404);
        }

        $semanaFin = $plan->semana_inicio + $plan->semanas - 1;

        // ── Semana por progreso real ──────────────────────────────────
        // Recorre desde la primera semana y avanza mientras estén completas
        $semanaActual = $plan->semana_inicio;

        for ($s = $plan->semana_inicio; $s <= $semanaFin; $s++) {
            $diasConRutina = Rutina::where('user_id', $clienteId)
                ->where('semana', $s)
                ->distinct()
                ->pluck('dia')
                ->toArray();

            // Si la semana no tiene rutinas la saltamos
            if (empty($diasConRutina)) {
                $semanaActual = $s + 1 <= $semanaFin ? $s + 1 : $s;
                continue;
            }

            $diasCompletados = DiaCompletado::where('user_id', $clienteId)
                ->where('semana', $s)
                ->pluck('dia')
                ->toArray();

            $semanaCompleta = count(array_diff($diasConRutina, $diasCompletados)) === 0;

            if ($semanaCompleta) {
                // Todos los días con rutina hechos → puede estar en la siguiente
                $semanaActual = $s + 1 <= $semanaFin ? $s + 1 : $s;
            } else {
                // Primera semana incompleta → el usuario está aquí
                $semanaActual = $s;
                break;
            }
        }

        // ── Días completados de la semana actual ──────────────────────
        $completados = DiaCompletado::where('user_id', $clienteId)
            ->where('semana', $semanaActual)
            ->pluck('dia')
            ->flip()
            ->toArray();

        // ── Días con rutina en la semana actual ───────────────────────
        $diasConRutinaActual = Rutina::where('user_id', $clienteId)
            ->where('semana', $semanaActual)
            ->distinct()
            ->pluck('dia')
            ->toArray();

        // ── Primer día pendiente (solo días con rutina) ───────────────
        $primerPendiente = null;
        for ($d = 1; $d <= 7; $d++) {
            if (in_array($d, $diasConRutinaActual) && !isset($completados[$d])) {
                $primerPendiente = $d;
                break;
            }
        }

        // ── Construir los 7 días ──────────────────────────────────────
        $dias = [];
        for ($d = 1; $d <= 7; $d++) {
            $tieneRutina = in_array($d, $diasConRutinaActual);

            $notaSesion = $tieneRutina
                ? Rutina::where('user_id', $clienteId)
                    ->where('semana', $semanaActual)
                    ->where('dia', $d)
                    ->value('nota_sesion') ?? ''
                : '';

            $fechaDia = Carbon::parse($plan->fecha_inicio)
                ->addDays(($semanaActual - 1) * 7 + ($d - 1))
                ->toDateString();

            if (!$tieneRutina) {
                $status = 'rest';
            } elseif (isset($completados[$d])) {
                $status = 'done';
            } elseif ($d === $primerPendiente) {
                $status = 'pending';
            } else {
                $status = 'locked';
            }

            $dias[] = [
                'dia'          => $d,
                'nombre'       => $notaSesion ?: ($tieneRutina ? "Día $d" : 'Descanso'),
                'fecha'        => $fechaDia,
                'status'       => $status,
                'tiene_rutina' => $tieneRutina,
            ];
        }

        return response()->json([
            'cliente'       => $cliente->name,
            'semana_actual' => $semanaActual,
            'semana_inicio' => $plan->semana_inicio,
            'semana_fin'    => $semanaFin,
            'total_semanas' => $plan->semanas,
            'dias'          => $dias,
        ]);
    }

    // POST /api/cliente/{id}/semana/{semana}/dia/{dia}/completar
    public function completarDia($clienteId, $semana, $dia)
    {
        DiaCompletado::firstOrCreate([
            'user_id' => $clienteId,
            'semana'  => $semana,
            'dia'     => $dia,
        ]);

        // Verificar si la semana quedó completa
        $diasConRutina = Rutina::where('user_id', $clienteId)
            ->where('semana', $semana)
            ->distinct()
            ->pluck('dia')
            ->toArray();

        $diasCompletados = DiaCompletado::where('user_id', $clienteId)
            ->where('semana', $semana)
            ->pluck('dia')
            ->toArray();

        $semanaCompleta = count($diasConRutina) > 0
            && count(array_diff($diasConRutina, $diasCompletados)) === 0;

        return response()->json([
            'ok'              => true,
            'semana_completa' => $semanaCompleta,
        ]);
    }

    // GET /api/cliente/{id}/semanas
public function todasLasSemanas($clienteId)
{
    $cliente = User::findOrFail($clienteId);
    $plan    = Plan::where('user_id', $clienteId)->first();

    if (!$plan) {
        return response()->json(['error' => 'Sin plan asignado'], 404);
    }

    $semanaFin = $plan->semana_inicio + $plan->semanas - 1;

    // Calcular semana actual por progreso (misma lógica que semanaActual)
    $semanaActual = $plan->semana_inicio;

    for ($s = $plan->semana_inicio; $s <= $semanaFin; $s++) {
        $diasConRutina = Rutina::where('user_id', $clienteId)
            ->where('semana', $s)
            ->distinct()
            ->pluck('dia')
            ->toArray();

        if (empty($diasConRutina)) {
            $semanaActual = $s + 1 <= $semanaFin ? $s + 1 : $s;
            continue;
        }

        $diasCompletados = DiaCompletado::where('user_id', $clienteId)
            ->where('semana', $s)
            ->pluck('dia')
            ->toArray();

        $semanaCompleta = count(array_diff($diasConRutina, $diasCompletados)) === 0;

        if ($semanaCompleta) {
            $semanaActual = $s + 1 <= $semanaFin ? $s + 1 : $s;
        } else {
            $semanaActual = $s;
            break;
        }
    }

    // Construir resumen de todas las semanas
    $semanas = [];

    for ($s = $plan->semana_inicio; $s <= $semanaFin; $s++) {
        $diasConRutina = Rutina::where('user_id', $clienteId)
            ->where('semana', $s)
            ->distinct()
            ->pluck('dia')
            ->toArray();

        $diasCompletados = DiaCompletado::where('user_id', $clienteId)
            ->where('semana', $s)
            ->pluck('dia')
            ->toArray();

        $hechos = count(array_intersect($diasConRutina, $diasCompletados));
        $total  = count($diasConRutina);

        if ($s < $semanaActual) {
            $estado = 'completa';
        } elseif ($s === $semanaActual) {
            $estado = 'actual';
        } else {
            $estado = 'futura';
        }

        $fechaInicioSemana = Carbon::parse($plan->fecha_inicio)
            ->addDays(($s - $plan->semana_inicio) * 7)
            ->toDateString();

        $fechaFinSemana = Carbon::parse($plan->fecha_inicio)
            ->addDays(($s - $plan->semana_inicio) * 7 + 6)
            ->toDateString();

        // Días de la semana con su status
        $dias = [];
        for ($d = 1; $d <= 7; $d++) {
            $tieneRutina = in_array($d, $diasConRutina);
            $completado  = in_array($d, $diasCompletados);

            $notaSesion = $tieneRutina
                ? Rutina::where('user_id', $clienteId)
                    ->where('semana', $s)
                    ->where('dia', $d)
                    ->value('nota_sesion') ?? ''
                : '';

            if (!$tieneRutina) {
                $status = 'rest';
            } elseif ($completado) {
                $status = 'done';
            } else {
                $status = $estado === 'futura' ? 'locked' : 'pending';
            }

            $dias[] = [
                'dia'          => $d,
                'nombre'       => $notaSesion ?: ($tieneRutina ? "Día $d" : 'Descanso'),
                'status'       => $status,
                'tiene_rutina' => $tieneRutina,
            ];
        }

        $semanas[] = [
            'semana'       => $s,
            'estado'       => $estado,
            'fecha_inicio' => $fechaInicioSemana,
            'fecha_fin'    => $fechaFinSemana,
            'hechos'       => $hechos,
            'total'        => $total,
            'dias'         => $dias,
        ];
    }

    return response()->json([
        'cliente'       => $cliente->name,
        'semana_actual' => $semanaActual,
        'semanas'       => $semanas,
    ]);
}


}