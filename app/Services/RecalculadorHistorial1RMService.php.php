<?php

namespace App\Services;

use App\Models\EstimacionUnoRm;
use App\Models\EstimacionUnoRmHistorial;
use App\Models\Rutina;
use Carbon\Carbon;

/**
 * RecalculadorHistorial1RMService
 * ─────────────────────────────────
 * Reconstruye desde cero el 1RM vigente y el historial de estimaciones
 * de un (userId, ejercicioId), reproduciendo Calculador1RM::registrarSerie()
 * sobre TODAS las series completadas de ese ejercicio, en todas las
 * filas de Rutina de ese cliente, en orden cronológico.
 *
 * Se usa cuando el cliente edita el peso de una serie que YA estaba
 * completada=true (ver el bug en RutinaApiController::guardarPesos:
 * ese caso hoy no dispara ningún registro en el 1RM). Como el
 * resultado del 1RM depende del orden en que se compararon los
 * niveles/valores, no alcanza con "actualizar el dato editado" — hay
 * que volver a correr Calculador1RM::registrarSerie() desde el
 * principio con los valores ya corregidos.
 *
 * ⚠️ Supuesto restante: dentro del array `series` de una fila, el
 * índice del array (0, 1, 2...) representa el orden cronológico en
 * que esas series se completaron ese día (S1 antes que S2, etc.).
 * Si eso no es así en algún método (ej. circuitos donde se alternan
 * ejercicios), avisame para ajustar el orden dentro del día.
 */
class RecalculadorHistorial1RMService
{
    /** Debe coincidir con Calculador1RM::METODOS_CONFIABLES (privada allá). */
    private const METODOS_CONFIABLES = ['normal', 'restpause', 'forzadas', '888'];

    /**
     * @return array{ejercicio_id:int, cambio:bool, valor_1rm_anterior:?float, valor_1rm_nuevo:?float}
     */
    public function recalcular(int $userId, int $ejercicioId): array
    {
        $vigenteAntes = EstimacionUnoRm::where('user_id', $userId)
            ->where('ejercicio_id', $ejercicioId)
            ->first();
        $valorAnterior = $vigenteAntes?->valor_1rm_kg;

        // Se borra el vigente y todo el historial de ESTE ejercicio para
        // este cliente: se regenera desde cero. (Cambia los IDs de las
        // filas de historial — si algo más los referencia por ID directo,
        // hay que revisarlo antes de usar esto en prod.)
        EstimacionUnoRm::where('user_id', $userId)
            ->where('ejercicio_id', $ejercicioId)
            ->delete();

        EstimacionUnoRmHistorial::where('user_id', $userId)
            ->where('ejercicio_id', $ejercicioId)
            ->delete();

        // Todas las filas de Rutina de este cliente que contienen este
        // ejercicio, en orden cronológico real por fecha de sesión.
        $filas = Rutina::where('user_id', $userId)
            ->where('ejercicio_id', $ejercicioId)
            ->whereNotNull('fecha')
            ->orderBy('fecha')
            ->orderBy('orden')
            ->get();

        foreach ($filas as $fila) {
            $series = $fila->series ?? [];
            if (is_string($series)) {
                $series = json_decode($series, true) ?? [];
            }

            // Fecha aproximada de esta sesión — ver supuesto #1. Si no hay
            // columna de fecha real en Rutina, se usa una fecha sintética
            // basada en (semana, dia) solo para poder ordenar/mostrar algo
            // razonable en "hace X" — NO es una fecha calendario real.
            $fechaAprox = $this->fechaAproximada($fila);

            // Orden dentro del día — ver supuesto #2 (índice = cronología)
            foreach ($series as $serie) {
                if (empty($serie['completada'])) {
                    continue;
                }

                $metodo = $serie['metodo'] ?? 'normal';
                if (!in_array($metodo, self::METODOS_CONFIABLES, true)) {
                    continue;
                }

                [$peso, $reps, $unidad] = $this->extraerPesoRepsParaUnoRM($serie, $metodo);
                if ($peso === null || $reps === null) {
                    continue;
                }

                Calculador1RM::registrarSerie(
                    userId: $userId,
                    ejercicioId: $ejercicioId,
                    metodo: $metodo,
                    peso: $peso,
                    reps: $reps,
                    unidad: $unidad,
                    fechaEvento: $fechaAprox, // requiere el parche en Calculador1RM
                );
            }
        }

        $vigenteDespues = EstimacionUnoRm::where('user_id', $userId)
            ->where('ejercicio_id', $ejercicioId)
            ->first();
        $valorNuevo = $vigenteDespues?->valor_1rm_kg;

        return [
            'ejercicio_id'       => $ejercicioId,
            'cambio'             => $this->difiere($valorAnterior, $valorNuevo),
            'valor_1rm_anterior' => $valorAnterior,
            'valor_1rm_nuevo'    => $valorNuevo,
        ];
    }

    /**
     * MISMA lógica de extracción que ya usa RutinaApiController
     * (extraerPesoRepsParaUnoRM) — duplicada acá a propósito para no
     * acoplar este servicio al controlador. Si tenés esa lógica en
     * un solo lugar compartido, mejor: reemplazá esto por una llamada
     * a ese método común.
     */
    private function extraerPesoRepsParaUnoRM(array $serie, string $metodo): array
    {
        return match ($metodo) {
            'normal' => [
                ((float) ($serie['peso'] ?? 0)) ?: null,
                ((int)   ($serie['reps'] ?? 0)) ?: null,
                $serie['unidad'] ?? 'kg',
            ],
            'restpause' => [
                ((float) ($serie['peso_rp'] ?? 0)) ?: null,
                ((int)   ($serie['reps_rp'] ?? 0)) ?: null,
                $serie['unidad_rp'] ?? 'kg',
            ],
            'forzadas' => [
                ((float) ($serie['peso_fz'] ?? 0)) ?: null,
                ((int)   ($serie['reps_fz'] ?? 0)) ?: null,
                $serie['unidad_fz'] ?? 'kg',
            ],
            '888' => [
                ((float) ($serie['peso1'] ?? 0)) ?: null,
                ((int)   ($serie['reps_888'] ?? 0)) ?: null,
                $serie['unidad1'] ?? 'kg',
            ],
            default => [null, null, 'kg'],
        };
    }

    /**
     * Fecha real de la sesión, tal como ya usa HistorialController
     * (whereYear('fecha', ...), whereMonth('fecha', ...)) — cada fila
     * de Rutina trae su propia fecha de sesión, no hace falta inferir
     * nada a partir de (semana, dia).
     */
    private function fechaAproximada(Rutina $fila): Carbon
    {
        return Carbon::parse($fila->fecha);
    }

    private function difiere(?float $a, ?float $b): bool
    {
        if ($a === null && $b === null) {
            return false;
        }
        if ($a === null || $b === null) {
            return true;
        }
        return abs($a - $b) > 0.001;
    }
}