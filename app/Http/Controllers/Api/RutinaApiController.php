<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rutina;
use App\Models\User;
use App\Models\Ejercicio;
use App\Services\Calculador1RM;
use Illuminate\Http\Request;


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

        $bloques = $rutinas->map(function ($grupo) use ($cliente) {

            $ejercicios = $grupo->map(function ($r) use ($cliente) {

                $ejercicio = Ejercicio::find($r->ejercicio_id);

                $series = $r->series ?? [];
                if (is_string($series)) {
                    $series = json_decode($series, true) ?? [];
                }

                // Pasar todos los campos guardados tal cual, y agregar
                // 'peso_sugerido' cuando corresponda (ver método aparte).
                $seriesNormalizadas = collect($series)
                    ->map(fn ($s) => $this->conSugerenciaDePeso($s, (int) $cliente, (int) $r->ejercicio_id))
                    ->values()
                    ->toArray();

                return [
                    'nombre'   => $r->nombre,
                    'segmento' => $r->segmento,
                    'imagen' => $ejercicio && $ejercicio->imagen
                        ? env('AWS_URL') . '/' . $ejercicio->imagen
                        : null,
                    'video' => $ejercicio && $ejercicio->video
                        ? env('AWS_URL') . '/' . $ejercicio->video
                        : null,
                    'nota_ej'  => $r->nota_ej ?? '',
                    'series'   => $seriesNormalizadas,
                ];

            })->values();

            $descansosSerie = $grupo->first()->descansos_serie ?? [];
            if (is_string($descansosSerie)) {
                $descansosSerie = json_decode($descansosSerie, true) ?? [];
            }

            return [
                'tipo'            => strtoupper($grupo->first()->tipo),
                'orden'           => $grupo->first()->orden,
                'descansos_serie' => $descansosSerie,
                'ejercicios'      => $ejercicios,
            ];

        })->values();

        return response()->json([
            'cliente'     => optional(User::find($cliente))->name ?? 'Desconocido',
            'semana'      => $semana,
            'dia'         => $dia,
            'nota_sesion' => $notaSesion,
            'bloques'     => $bloques,
        ]);
    }

    /**
     * Punto de entrada: decide cómo sugerir peso según el método de la
     * serie. Solo se sugiere para métodos donde el 1RM de rango
     * completo es una base razonable: normal, rest-pause, forzadas
     * (mapeo directo), y 10+21s / 888 (derivados con una regla extra).
     * Los demás métodos (21s, parciales, isometría, negativas) no
     * tienen sugerencia todavía — la relación con el 1RM de rango
     * completo no es lo bastante confiable.
     */
    private function conSugerenciaDePeso(array $serie, int $clienteId, int $ejercicioId): array
    {
        $metodo = $serie['metodo'] ?? 'normal';

        return match ($metodo) {
            'normal'    => $this->conSugerenciaSimple($serie, $clienteId, $ejercicioId, 'peso', 'reps'),
            'restpause' => $this->conSugerenciaSimple($serie, $clienteId, $ejercicioId, 'peso_rp', 'reps_rp'),
            'forzadas'  => $this->conSugerenciaSimple($serie, $clienteId, $ejercicioId, 'peso_fz', 'reps_fz'),
            '10_21'     => $this->conSugerencia1021($serie, $clienteId, $ejercicioId),
            '888'       => $this->conSugerencia888($serie, $clienteId, $ejercicioId),
            default     => $serie,
        };
    }

    /**
     * Sugerencia directa: una sola serie a un número de reps fijo
     * (normal, rest-pause, forzadas). Solo sugiere si el campo de peso
     * correspondiente está vacío.
     */
    private function conSugerenciaSimple(array $serie, int $clienteId, int $ejercicioId, string $pesoKey, string $repsKey): array
    {
        $pesoActual = trim((string) ($serie[$pesoKey] ?? ''));
        if ($pesoActual !== '' && (float) $pesoActual > 0) {
            return $serie;
        }

        $reps = (int) ($serie[$repsKey] ?? 0);
        if ($reps <= 0) {
            return $serie;
        }

        $sugerencia = Calculador1RM::pesoSugeridoParaEjercicio($clienteId, $ejercicioId, $reps);
        if (!$sugerencia) {
            return $serie;
        }

        $serie['peso_sugerido']        = $sugerencia['peso_sugerido'];
        $serie['peso_sugerido_unidad'] = $sugerencia['unidad'];
        $serie['peso_sugerido_nivel']  = $sugerencia['nivel_confianza'];
        $serie['peso_sugerido_fecha']  = optional($sugerencia['fecha_calculo'])->toDateString();

        return $serie;
    }

    /**
     * Sugerencia para 10+21s: solo si el peso del primer tramo (×10)
     * está vacío — si el entrenador ya lo puso, el ×21s ya se calcula
     * automáticamente en el editor con la regla −40% existente.
     */
    private function conSugerencia1021(array $serie, int $clienteId, int $ejercicioId): array
    {
        $peso10Actual = trim((string) ($serie['peso_10'] ?? ''));
        if ($peso10Actual !== '' && (float) $peso10Actual > 0) {
            return $serie;
        }

        $sugerencia = Calculador1RM::sugerir1021($clienteId, $ejercicioId);
        if (!$sugerencia) {
            return $serie;
        }

        $serie['peso_10_sugerido']     = $sugerencia['peso_10_sugerido'];
        $serie['peso_21_sugerido']     = $sugerencia['peso_21_sugerido'];
        $serie['peso_sugerido_unidad'] = $sugerencia['unidad'];
        $serie['peso_sugerido_nivel']  = $sugerencia['nivel_confianza'];
        $serie['peso_sugerido_fecha']  = optional($sugerencia['fecha_calculo'])->toDateString();

        return $serie;
    }

    /**
     * Sugerencia para 888 (descendente): solo si el primer tramo (P1)
     * está vacío. Los tres pesos se derivan del mismo 1RM con la
     * heurística de dropset (−10% por tramo).
     */
    private function conSugerencia888(array $serie, int $clienteId, int $ejercicioId): array
    {
        $peso1Actual = trim((string) ($serie['peso1'] ?? ''));
        if ($peso1Actual !== '' && (float) $peso1Actual > 0) {
            return $serie;
        }

        $repsBase = (int) ($serie['reps_888'] ?? 8);
        $sugerencia = Calculador1RM::sugerir888($clienteId, $ejercicioId, $repsBase);
        if (!$sugerencia) {
            return $serie;
        }

        $serie['peso1_sugerido']       = $sugerencia['peso1_sugerido'];
        $serie['peso2_sugerido']       = $sugerencia['peso2_sugerido'];
        $serie['peso3_sugerido']       = $sugerencia['peso3_sugerido'];
        $serie['peso_sugerido_unidad'] = $sugerencia['unidad'];
        $serie['peso_sugerido_nivel']  = $sugerencia['nivel_confianza'];
        $serie['peso_sugerido_fecha']  = optional($sugerencia['fecha_calculo'])->toDateString();

        return $serie;
    }

    public function guardarPesos(Request $request, $clienteId, $semana, $dia)
    {
        $bloques = $request->input('bloques', []);

        foreach ($bloques as $bloqueData) {
            $orden = $bloqueData['orden'];

            foreach ($bloqueData['ejercicios'] ?? [] as $ejData) {
                $rutina = Rutina::where('user_id', $clienteId)
                    ->where('semana', $semana)
                    ->where('dia', $dia)
                    ->where('orden', $orden)
                    ->where('nombre', $ejData['nombre'])
                    ->first();

                if (!$rutina) continue;

                $seriesAnteriores = $rutina->series ?? [];
                if (is_string($seriesAnteriores)) {
                    $seriesAnteriores = json_decode($seriesAnteriores, true) ?? [];
                }

                $seriesNuevas = $ejData['series'] ?? [];

                // Solo registrar en el 1RM las series que ACABAN de pasar
                // a completada=true (evita recalcular en cada autosave,
                // ya que el cliente reenvía todo el bloque cada vez que
                // cambia cualquier campo).
                foreach ($seriesNuevas as $i => $serieNueva) {
                    $completadaAntes = !empty($seriesAnteriores[$i]['completada'] ?? null);
                    $completadaAhora = !empty($serieNueva['completada'] ?? null);

                    if ($completadaAhora && !$completadaAntes) {
                        $metodo = $serieNueva['metodo'] ?? 'normal';
                        [$peso, $reps, $unidad] = $this->extraerPesoRepsParaUnoRM($serieNueva, $metodo);

                        if ($peso !== null && $reps !== null) {
                            Calculador1RM::registrarSerie(
                                userId: (int) $clienteId,
                                ejercicioId: (int) $rutina->ejercicio_id,
                                metodo: $metodo,
                                peso: $peso,
                                reps: $reps,
                                unidad: $unidad
                            );
                        }
                    }
                }

                $rutina->series = $seriesNuevas;
                $rutina->save();
            }
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Extrae el peso/reps/unidad que representan mejor "una serie
     * normal a fallo" para cada método, para poder alimentar el 1RM.
     * Devuelve [null, null, 'kg'] para métodos que no aplican (el
     * caller no debe registrar nada en ese caso).
     */
    private function extraerPesoRepsParaUnoRM(array $serie, string $metodo): array
    {
        switch ($metodo) {
            case 'normal':
                return [
                    ((float) ($serie['peso'] ?? 0)) ?: null,
                    ((int)   ($serie['reps'] ?? 0)) ?: null,
                    $serie['unidad'] ?? 'kg',
                ];

            case 'restpause':
                return [
                    ((float) ($serie['peso_rp'] ?? 0)) ?: null,
                    ((int)   ($serie['reps_rp'] ?? 0)) ?: null,
                    $serie['unidad_rp'] ?? 'kg',
                ];

            case 'forzadas':
                // Solo las reps hechas SOLO (sin asistencia) reflejan
                // el esfuerzo real del cliente; las asistidas no cuentan.
                return [
                    ((float) ($serie['peso_fz'] ?? 0)) ?: null,
                    ((int)   ($serie['reps_fz'] ?? 0)) ?: null,
                    $serie['unidad_fz'] ?? 'kg',
                ];

            case '888':
                // Solo el PRIMER tramo (P1) es, en la práctica, una
                // serie normal a fallo. Los tramos 2 y 3 ya vienen con
                // fatiga acumulada del tramo anterior y no representan
                // la fuerza máxima real, así que no se usan.
                return [
                    ((float) ($serie['peso1'] ?? 0)) ?: null,
                    ((int)   ($serie['reps_888'] ?? 0)) ?: null,
                    $serie['unidad1'] ?? 'kg',
                ];

            default:
                return [null, null, 'kg'];
        }
    }
}