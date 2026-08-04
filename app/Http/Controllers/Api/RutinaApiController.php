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
     * Si la serie es de método 'normal' y el entrenador NO puso un peso
     * objetivo explícito (el campo viene vacío o en 0), se busca una
     * sugerencia a partir del 1RM vigente del cliente para ese ejercicio,
     * usando las reps prescritas en la serie. Se agrega como campo extra
     * 'peso_sugerido' (y metadatos), sin tocar el campo 'peso' original
     * — la app decide cómo mostrarlo/prellenarlo.
     */
    private function conSugerenciaDePeso(array $serie, int $clienteId, int $ejercicioId): array
    {
        $metodo = $serie['metodo'] ?? 'normal';
        if ($metodo !== 'normal') {
            return $serie;
        }

        $pesoActual = trim((string) ($serie['peso'] ?? ''));
        $tienePesoExplicito = $pesoActual !== '' && (float) $pesoActual > 0;
        if ($tienePesoExplicito) {
            return $serie;
        }

        $reps = (int) ($serie['reps'] ?? 0);
        if ($reps <= 0) {
            return $serie;
        }

        $unidad = $serie['unidad'] ?? 'kg';

        $sugerencia = Calculador1RM::pesoSugeridoParaEjercicio($clienteId, $ejercicioId, $reps, $unidad);
        if (!$sugerencia) {
            return $serie; // sin 1RM registrado todavía para este ejercicio
        }

        $serie['peso_sugerido']         = $sugerencia['peso_sugerido'];
        $serie['peso_sugerido_unidad']  = $sugerencia['unidad'];
        $serie['peso_sugerido_nivel']   = $sugerencia['nivel_confianza'];
        $serie['peso_sugerido_fecha']   = optional($sugerencia['fecha_calculo'])->toDateString();

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
                        $peso   = (float) ($serieNueva['peso'] ?? 0);
                        $reps   = (int)   ($serieNueva['reps'] ?? 0);
                        $unidad = $serieNueva['unidad'] ?? 'kg';

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

                $rutina->series = $seriesNuevas;
                $rutina->save();
            }
        }

        return response()->json(['ok' => true]);
    }
}