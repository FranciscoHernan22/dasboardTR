<?php

namespace App\Services;

use App\Models\EstimacionUnoRm;
use App\Models\EstimacionUnoRmHistorial;
use Carbon\Carbon;

/**
 * Servicio de cálculo y mantenimiento del 1RM estimado por (cliente, ejercicio).
 *
 * Reglas de negocio (definidas junto con el usuario):
 * - Se calcula a partir de series que mecánicamente equivalen a "una
 *   serie normal a fallo": método 'normal', 'restpause', 'forzadas'
 *   (solo las reps hechas sin asistencia), y el PRIMER tramo del
 *   método '888' (los tramos 2 y 3 ya vienen con fatiga acumulada y
 *   no representan la fuerza máxima real, así que no se usan).
 *   El caller (RutinaApiController) es responsable de extraer el
 *   peso/reps correctos según el método antes de llamar a
 *   registrarSerie() — este servicio no conoce la estructura de cada
 *   método, solo recibe un peso y unas reps ya resueltos.
 * - Niveles de confianza por rango de repeticiones:
 *     A (1-6 reps)   -> más confiable
 *     B (7-12 reps)  -> confiable
 *     C (13-20 reps) -> menos confiable, se usa solo si no hay algo mejor
 * - Un candidato de mejor nivel SIEMPRE reemplaza al vigente, aunque el
 *   valor sea menor.
 * - Dentro del mismo nivel, reemplaza solo si el valor es igual o mayor
 *   (nuevo PR real).
 * - Un candidato de peor nivel nunca reemplaza al vigente, pero se
 *   guarda igual en el historial para analítica/auditoría.
 * - El 1RM vigente se guarda siempre en kg (unidad canónica); se
 *   convierte a la unidad que se necesite al leer/sugerir.
 */
class Calculador1RM
{
    /** Métodos cuya serie (ya resuelta por el caller) puede alimentar el 1RM. */
    private const METODOS_CONFIABLES = ['normal', 'restpause', 'forzadas', '888'];

    /** Rango de repeticiones válido para calcular/estimar (fuera de esto, no se usa). */
    private const REPS_MIN = 1;
    private const REPS_MAX = 20;

    /** Rango de reps por nivel de confianza. */
    private const NIVELES_RANGOS = [
        'A' => [1, 6],
        'B' => [7, 12],
        'C' => [13, 20],
    ];

    /** Ranking numérico de nivel, para comparar "mejor que" sin importar el valor. */
    private const NIVEL_RANGO = ['A' => 3, 'B' => 2, 'C' => 1];

    /**
     * Tabla %1RM por número de repeticiones (referencia tipo NSCA/ACSM).
     * %1RM(reps) = porcentaje del 1RM que representa una serie a fallo
     * (o cercana a fallo) con ese número de repeticiones.
     */
    private const TABLA_PORCENTAJE = [
        1  => 100.0,
        2  => 95.0,
        3  => 93.0,
        4  => 90.0,
        5  => 87.0,
        6  => 85.0,
        7  => 83.0,
        8  => 80.0,
        9  => 77.0,
        10 => 75.0,
        11 => 73.0,
        12 => 70.0,
        13 => 68.0,
        14 => 66.0,
        15 => 65.0,
        16 => 63.0,
        17 => 62.0,
        18 => 61.0,
        19 => 60.5,
        20 => 60.0,
    ];

    private const KG_POR_LB = 0.45359237;
    private const LB_POR_KG = 2.20462262;

    /* ─────────────────────────────────────────────
       Conversión de unidades
    ───────────────────────────────────────────── */

    public static function aKg(float $valor, string $unidad): float
    {
        return strtolower($unidad) === 'lb' ? $valor * self::KG_POR_LB : $valor;
    }

    public static function deKg(float $valorKg, string $unidad): float
    {
        return strtolower($unidad) === 'lb' ? $valorKg * self::LB_POR_KG : $valorKg;
    }

    /**
     * Redondea a un incremento realista de carga: 2.5 en kg, 5 en lb.
     * (Coincide con los pasos usados en el selector de pesos de la app.)
     */
    public static function redondear(float $valor, string $unidad): float
    {
        $paso = strtolower($unidad) === 'lb' ? 5 : 2.5;
        return round($valor / $paso) * $paso;
    }

    /* ─────────────────────────────────────────────
       Tabla %1RM / niveles de confianza
    ───────────────────────────────────────────── */

    public static function nivelParaReps(int $reps): ?string
    {
        foreach (self::NIVELES_RANGOS as $nivel => [$min, $max]) {
            if ($reps >= $min && $reps <= $max) {
                return $nivel;
            }
        }
        return null; // fuera de rango (0, o más de 20)
    }

    public static function porcentajeParaReps(int $reps): ?float
    {
        if ($reps < self::REPS_MIN || $reps > self::REPS_MAX) {
            return null;
        }
        return self::TABLA_PORCENTAJE[$reps];
    }

    /**
     * Estima el 1RM (en la misma unidad que $peso) a partir de un peso
     * y repeticiones reales. Devuelve null si las reps están fuera del
     * rango soportado (1-20).
     */
    public static function estimar1RM(float $peso, int $reps): ?float
    {
        $porcentaje = self::porcentajeParaReps($reps);
        if ($porcentaje === null || $porcentaje <= 0) {
            return null;
        }
        return $peso / ($porcentaje / 100);
    }

    /**
     * A partir de un 1RM, calcula el peso esperado para un número de
     * repeticiones objetivo (misma unidad que el 1RM recibido).
     */
    public static function pesoParaReps(float $unoRM, int $repsObjetivo): ?float
    {
        $porcentaje = self::porcentajeParaReps($repsObjetivo);
        if ($porcentaje === null) {
            return null;
        }
        return $unoRM * ($porcentaje / 100);
    }

    private static function rangoNivel(string $nivel): int
    {
        return self::NIVEL_RANGO[$nivel] ?? 0;
    }

    /* ─────────────────────────────────────────────
       Registro de una serie completada
    ───────────────────────────────────────────── */

    /**
     * Punto de entrada principal. Se llama cada vez que el cliente
     * confirma/completa una serie de un método "confiable" (ver
     * METODOS_CONFIABLES) con peso > 0. El caller debe haber resuelto
     * ya el peso/reps correctos según el método (ej. para '888' se
     * pasa el peso y reps del PRIMER tramo, no los tres).
     *
     * No hace nada (retorna null) si:
     * - el método no está en la lista de confiables
     * - el peso o las reps no son válidos
     * - las reps quedan fuera del rango 1-20 (no se puede clasificar por nivel)
     *
     * @return array{
     *   candidato_1rm_kg: float,
     *   nivel: string,
     *   reemplazo_vigente: bool,
     *   vigente_1rm_kg: float
     * }|null
     */
    public static function registrarSerie(
        int $userId,
        int $ejercicioId,
        string $metodo,
        float $peso,
        int $reps,
        string $unidad = 'kg'
    ): ?array {
        if (!in_array($metodo, self::METODOS_CONFIABLES, true) || $peso <= 0 || $reps <= 0) {
            return null;
        }

        $nivel = self::nivelParaReps($reps);
        if ($nivel === null) {
            return null;
        }

        $pesoKg = self::aKg($peso, $unidad);
        $candidato1RMKg = self::estimar1RM($pesoKg, $reps);
        if ($candidato1RMKg === null) {
            return null;
        }

        $vigente = EstimacionUnoRm::where('user_id', $userId)
            ->where('ejercicio_id', $ejercicioId)
            ->first();

        $reemplaza = self::debeReemplazar($vigente, $nivel, $candidato1RMKg);
        $ahora = Carbon::now();

        // El historial siempre se registra, haya reemplazado o no.
        EstimacionUnoRmHistorial::create([
            'user_id'              => $userId,
            'ejercicio_id'         => $ejercicioId,
            'valor_1rm_kg'         => round($candidato1RMKg, 2),
            'nivel_confianza'      => $nivel,
            'reps_base'            => $reps,
            'peso_base'            => $peso,
            'unidad_base'          => $unidad,
            'se_uso_como_vigente'  => $reemplaza,
            'fecha_calculo'        => $ahora,
        ]);

        if ($reemplaza) {
            EstimacionUnoRm::updateOrCreate(
                ['user_id' => $userId, 'ejercicio_id' => $ejercicioId],
                [
                    'valor_1rm_kg'    => round($candidato1RMKg, 2),
                    'nivel_confianza' => $nivel,
                    'reps_base'       => $reps,
                    'peso_base'       => $peso,
                    'unidad_base'     => $unidad,
                    'fecha_calculo'   => $ahora,
                ]
            );
        }

        return [
            'candidato_1rm_kg'  => round($candidato1RMKg, 2),
            'nivel'             => $nivel,
            'reemplazo_vigente' => $reemplaza,
            'vigente_1rm_kg'    => $reemplaza ? round($candidato1RMKg, 2) : ($vigente->valor_1rm_kg ?? round($candidato1RMKg, 2)),
        ];
    }

    private static function debeReemplazar(?EstimacionUnoRm $vigente, string $nivelCandidato, float $valorCandidatoKg): bool
    {
        if (!$vigente) {
            return true;
        }

        $rankCandidato = self::rangoNivel($nivelCandidato);
        $rankVigente   = self::rangoNivel($vigente->nivel_confianza);

        // Mejor nivel siempre gana, aunque el valor sea menor.
        if ($rankCandidato > $rankVigente) {
            return true;
        }

        // Mismo nivel: gana si es igual o mayor (nuevo PR real).
        if ($rankCandidato === $rankVigente && $valorCandidatoKg >= $vigente->valor_1rm_kg) {
            return true;
        }

        // Peor nivel: nunca reemplaza.
        return false;
    }

    /* ─────────────────────────────────────────────
       Lectura / sugerencia
    ───────────────────────────────────────────── */

    /**
     * Devuelve el 1RM vigente de un ejercicio para un cliente, en la
     * unidad solicitada, o null si todavía no hay ninguna estimación.
     */
    public static function obtenerVigente(int $userId, int $ejercicioId, string $unidadSalida = 'kg'): ?array
    {
        $vigente = EstimacionUnoRm::where('user_id', $userId)
            ->where('ejercicio_id', $ejercicioId)
            ->first();

        if (!$vigente) {
            return null;
        }

        return [
            'valor_1rm'       => self::redondear(self::deKg($vigente->valor_1rm_kg, $unidadSalida), $unidadSalida),
            'unidad'          => $unidadSalida,
            'nivel_confianza' => $vigente->nivel_confianza,
            'reps_base'       => $vigente->reps_base,
            'peso_base'       => $vigente->peso_base,
            'unidad_base'     => $vigente->unidad_base,
            'fecha_calculo'   => $vigente->fecha_calculo,
        ];
    }

    /**
     * Sugiere el peso esperado para un ejercicio + número de reps
     * objetivo, a partir del 1RM vigente del cliente. Devuelve null si
     * no hay 1RM registrado aún, o si repsObjetivo está fuera de 1-20.
     *
     * Si no se especifica $unidadSalida, se usa la misma unidad con la
     * que el cliente registró el 1RM vigente (unidad_base) — así la
     * sugerencia sale en la unidad que el cliente realmente usa para
     * ese ejercicio (por ejemplo, si solo tiene mancuernas en lb, la
     * sugerencia sale en lb aunque la serie vacía tenga 'kg' por
     * defecto), en vez de forzar una unidad arbitraria.
     */
    public static function pesoSugeridoParaEjercicio(
        int $userId,
        int $ejercicioId,
        int $repsObjetivo,
        ?string $unidadSalida = null
    ): ?array {
        $vigente = EstimacionUnoRm::where('user_id', $userId)
            ->where('ejercicio_id', $ejercicioId)
            ->first();

        if (!$vigente) {
            return null;
        }

        $unidadSalida = $unidadSalida ?? $vigente->unidad_base;

        $pesoKgSugerido = self::pesoParaReps($vigente->valor_1rm_kg, $repsObjetivo);
        if ($pesoKgSugerido === null) {
            return null;
        }

        $pesoSugerido = self::redondear(self::deKg($pesoKgSugerido, $unidadSalida), $unidadSalida);

        return [
            'peso_sugerido'   => $pesoSugerido,
            'unidad'          => $unidadSalida,
            'reps_objetivo'   => $repsObjetivo,
            'basado_en_1rm'   => self::redondear(self::deKg($vigente->valor_1rm_kg, $unidadSalida), $unidadSalida),
            'nivel_confianza' => $vigente->nivel_confianza,
            'fecha_calculo'   => $vigente->fecha_calculo,
        ];
    }

    /**
     * Sugerencia para el método '10+21s': el primer tramo (10 reps
     * completas) se sugiere igual que una serie normal; el segundo
     * tramo (21s) aplica la misma regla de −40% que ya usa el editor
     * web para calcularlo automáticamente a partir del primero.
     */
    public static function sugerir1021(
        int $userId,
        int $ejercicioId,
        int $repsBase = 10,
        ?string $unidadSalida = null
    ): ?array {
        $sugerenciaBase = self::pesoSugeridoParaEjercicio($userId, $ejercicioId, $repsBase, $unidadSalida);
        if (!$sugerenciaBase) {
            return null;
        }

        $unidad = $sugerenciaBase['unidad'];
        $peso21 = self::redondear($sugerenciaBase['peso_sugerido'] * 0.60, $unidad);

        return [
            'peso_10_sugerido' => $sugerenciaBase['peso_sugerido'],
            'peso_21_sugerido' => $peso21,
            'unidad'           => $unidad,
            'nivel_confianza'  => $sugerenciaBase['nivel_confianza'],
            'fecha_calculo'    => $sugerenciaBase['fecha_calculo'],
        ];
    }

    /**
     * Sugerencia para el método descendente/888: el primer tramo se
     * sugiere igual que una serie normal a las reps prescritas; los
     * siguientes dos tramos aplican una caída típica de dropset del
     * 10% por tramo (heurística de referencia, no medida con datos
     * propios del cliente — por eso no se usa para calcular/actualizar
     * el 1RM, solo para sugerir).
     */
    public static function sugerir888(
        int $userId,
        int $ejercicioId,
        int $repsBase = 8,
        ?string $unidadSalida = null
    ): ?array {
        $sugerenciaBase = self::pesoSugeridoParaEjercicio($userId, $ejercicioId, $repsBase, $unidadSalida);
        if (!$sugerenciaBase) {
            return null;
        }

        $unidad = $sugerenciaBase['unidad'];
        $peso1  = $sugerenciaBase['peso_sugerido'];
        $peso2  = self::redondear($peso1 * 0.90, $unidad);
        $peso3  = self::redondear($peso2 * 0.90, $unidad);

        return [
            'peso1_sugerido'  => $peso1,
            'peso2_sugerido'  => $peso2,
            'peso3_sugerido'  => $peso3,
            'unidad'          => $unidad,
            'nivel_confianza' => $sugerenciaBase['nivel_confianza'],
            'fecha_calculo'   => $sugerenciaBase['fecha_calculo'],
        ];
    }
}