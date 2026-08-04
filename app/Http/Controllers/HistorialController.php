<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rutina;

class HIstorialController extends Controller
{
    private array $nombresMes = [
        1=>'Enero', 2=>'Febrero', 3=>'Marzo', 4=>'Abril',
        5=>'Mayo', 6=>'Junio', 7=>'Julio', 8=>'Agosto',
        9=>'Septiembre', 10=>'Octubre', 11=>'Noviembre', 12=>'Diciembre',
    ];

    public function anio(User $cliente)
    {
        $anio = (int) request('anio', date('Y'));

        $rutinas = Rutina::where('user_id', $cliente->id)
            ->whereNotNull('fecha')
            ->whereYear('fecha', $anio)
            ->select('fecha', 'series')
            ->get();

        // Solo cuentan las fechas donde el cliente realmente marcó
        // al menos una serie como completada en la app — no basta con
        // que el entrenador haya planificado/asignado la rutina.
        $fechasConEntrenamiento = $rutinas
            ->groupBy(fn ($r) => substr($r->fecha, 0, 10))
            ->filter(fn ($grupoDia) => $this->grupoTieneSerieCompletada($grupoDia))
            ->keys()
            ->flip()
            ->toArray();

        // Total por mes: cuántas fechas completadas caen en cada mes
        $mesesData = collect(range(1, 12))->map(function ($m) use ($anio, $fechasConEntrenamiento) {
            $totalMes = collect(array_keys($fechasConEntrenamiento))
                ->filter(fn ($f) => (int) substr($f, 5, 2) === $m)
                ->count();

            return [
                'nombre'  => $this->nombresMes[$m],
                'mes'     => $m,
                'diasMes' => cal_days_in_month(CAL_GREGORIAN, $m, $anio),
                'total'   => $totalMes,
                'fechas'  => $fechasConEntrenamiento,
            ];
        });

        return view('entrenador.historial.anio', compact('cliente', 'anio', 'mesesData'));
    }

    public function mes(User $cliente, int $anio, int $mes)
    {
        $rutinas = Rutina::where('user_id', $cliente->id)
            ->whereNotNull('fecha')
            ->whereYear('fecha', $anio)
            ->whereMonth('fecha', $mes)
            ->select('semana', 'dia', 'fecha', 'series')
            ->get();

        // Igual que en anio(): un día del calendario semanal solo se
        // marca si el cliente completó realmente al menos una serie.
        $rutinas = $rutinas->groupBy('semana')->map(function ($diasSemana) {
            return $diasSemana
                ->groupBy('dia')
                ->filter(fn ($grupoDia) => $this->grupoTieneSerieCompletada($grupoDia))
                ->keys()
                ->flip();
        });

        return view('entrenador.historial.mes', compact('cliente', 'anio', 'mes', 'rutinas'));
    }

    public function dia(User $cliente, int $anio, int $mes, int $sem, int $dia)
    {
        $rutinas = Rutina::where('user_id', $cliente->id)
            ->whereNotNull('fecha')
            ->whereYear('fecha', $anio)
            ->whereMonth('fecha', $mes)
            ->where('semana', $sem)
            ->where('dia', $dia)
            ->get();

        $bloques = $rutinas->groupBy('grupo')->map(function ($grupo) {
            return [
                'tipo'       => $grupo->first()->tipo,
                'ejercicios' => $grupo->map(fn ($r) => [
                    'nombre'   => $r->nombre,
                    'segmento' => $r->segmento,
                    'series'   => is_string($r->series)
                        ? json_decode($r->series, true)
                        : ($r->series ?? []),
                ])->values(),
            ];
        })->values();

        // "Planificada" (existe rutina asignada ese día) vs.
        // "completada" (el cliente realmente marcó series como hechas).
        $rutina    = $rutinas->isNotEmpty();
        $completada = $this->grupoTieneSerieCompletada($rutinas);

        return view('entrenador.historial.dia', compact(
            'cliente', 'anio', 'mes', 'sem', 'dia', 'rutina', 'bloques', 'completada'
        ));
    }

    /**
     * Recibe una colección de filas de Rutina (todas correspondientes al
     * mismo día) y determina si el cliente marcó al menos una serie como
     * completada ('completada' => true/'1' dentro del JSON de 'series').
     * Esto es lo que distingue "planificado" de "realmente entrenado".
     */
    private function grupoTieneSerieCompletada($rutinasDelDia): bool
    {
        foreach ($rutinasDelDia as $r) {
            $series = is_string($r->series)
                ? json_decode($r->series, true)
                : ($r->series ?? []);

            if (!is_array($series)) continue;

            foreach ($series as $serie) {
                $completada = $serie['completada'] ?? false;
                if ($completada === true || $completada === 1 || $completada === '1') {
                    return true;
                }
            }
        }

        return false;
    }
}