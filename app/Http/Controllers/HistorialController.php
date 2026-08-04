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
        ->select('fecha')
        ->distinct()
        ->get();

    // Fechas con entrenamiento como set
    $fechasConEntrenamiento = $rutinas->pluck('fecha')->map(fn($f) => substr($f, 0, 10))->flip()->toArray();

    // Total por mes
    $mesesData = collect(range(1, 12))->map(fn($m) => [
        'nombre'    => $this->nombresMes[$m],
        'mes'       => $m,
        'diasMes'   => cal_days_in_month(CAL_GREGORIAN, $m, $anio),
        'total'     => $rutinas->filter(fn($r) => (int)date('n', strtotime($r->fecha)) === $m)->count(),
        'fechas'    => $fechasConEntrenamiento,
    ]);

    return view('entrenador.historial.anio', compact('cliente', 'anio', 'mesesData'));
}

    public function mes(User $cliente, int $anio, int $mes)
    {
        $rutinas = Rutina::where('user_id', $cliente->id)
            ->whereNotNull('fecha')
            ->whereYear('fecha', $anio)
            ->whereMonth('fecha', $mes)
            ->select('semana', 'dia', 'fecha')
            ->distinct()
            ->get()
            ->groupBy('semana')
            ->map(fn($s) => $s->pluck('dia')->flip());

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
                'ejercicios' => $grupo->map(fn($r) => [
                    'nombre'   => $r->nombre,
                    'segmento' => $r->segmento,
                    'series'   => is_string($r->series)
                        ? json_decode($r->series, true)
                        : ($r->series ?? []),
                ])->values(),
            ];
        })->values();

        $rutina = $rutinas->isNotEmpty();

        return view('entrenador.historial.dia', compact(
            'cliente', 'anio', 'mes', 'sem', 'dia', 'rutina', 'bloques'
        ));
    }
}