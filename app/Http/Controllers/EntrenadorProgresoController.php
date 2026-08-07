<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ClienteMedida;
use App\Models\ClienteFoto;
use App\Models\ClienteVideo;
use App\Models\ClienteNota;
use App\Models\EjercicioRegistro;
use App\Models\SesionEntrenamiento;
use App\Models\Rutina;
use App\Models\EstimacionUnoRm;
use App\Models\EstimacionUnoRmHistorial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EntrenadorProgresoController extends Controller
{
    /** Semanas sin actualizar el 1RM para considerarlo "estancado". */
    private const SEMANAS_ESTANCAMIENTO = 4;

    /** Ventana de comparación para el % de cambio del 1RM. */
    private const DIAS_COMPARACION = 30;

    /**
     * Verifica que el cliente pertenezca al entrenador autenticado.
     */
    private function autorizarCliente(User $cliente): void
    {
        if ($cliente->entrenador_id !== Auth::id()) {
            abort(403);
        }
    }

    public function index($clienteId)
    {
        $cliente = User::findOrFail($clienteId);
        $this->autorizarCliente($cliente);

        // ── Medidas (Físico + Resumen) ──
        $medidas = ClienteMedida::where('user_id', $clienteId)
            ->orderBy('mes')
            ->get();

        $medidaMasReciente = $medidas->last();
        $medidaMasAntigua  = $medidas->first();

        // ── Fotos, agrupadas por mes ──
        $fotos = ClienteFoto::where('user_id', $clienteId)
            ->orderBy('mes')
            ->get()
            ->groupBy(fn($f) => $f->mes->format('Y-m'));

        // ── Videos, agrupados por ejercicio ──
        $videos = ClienteVideo::where('user_id', $clienteId)
            ->get()
            ->groupBy('ejercicio');

        // ── Rendimiento: ejercicio principal (el que tenga más registros) ──
        // Nota: EjercicioRegistro es un sistema aparte, sin uso confirmado
        // todavía. Se deja intacto, sin tocar.
        $ejercicioPrincipal = EjercicioRegistro::where('user_id', $clienteId)
            ->select('ejercicio')
            ->groupBy('ejercicio')
            ->orderByRaw('COUNT(*) DESC')
            ->value('ejercicio');

        $registrosEjercicio = $ejercicioPrincipal
            ? EjercicioRegistro::where('user_id', $clienteId)
                ->where('ejercicio', $ejercicioPrincipal)
                ->orderBy('fecha')
                ->get()
            : collect();

        $pesoMaximo = $registrosEjercicio->max('peso');
        $pesoInicial = $registrosEjercicio->first()?->peso;
        $seriesUltimaSemana = EjercicioRegistro::where('user_id', $clienteId)
            ->where('fecha', '>=', Carbon::now()->subDays(7))
            ->sum('series');
        $volumenTotal = EjercicioRegistro::where('user_id', $clienteId)
            ->get()
            ->sum(fn($r) => ($r->peso ?? 0) * ($r->series ?? 0) * ($r->reps ?? 0));

        // ── Rendimiento REAL: 1RM vigente + historial de pesos por ejercicio ──
        $estimaciones1RM = EstimacionUnoRm::where('user_id', $clienteId)
            ->with('ejercicio:id,nombre,segmento')
            ->orderByDesc('fecha_calculo')
            ->get();

        $historialPesoPorEjercicio = $this->construirHistorialPesoPorEjercicio($clienteId);

        $segmentosDisponibles = $estimaciones1RM
            ->map(fn ($e) => $e->ejercicio->segmento ?? 'Otro')
            ->merge(collect($historialPesoPorEjercicio)->pluck('segmento'))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $comparativaSegmentos = $this->construirComparativaSegmentos($clienteId, $estimaciones1RM);
        $estancamientos       = $this->detectarEstancamientos($clienteId, $estimaciones1RM);

        // ── Constancia ──
        $sesiones = SesionEntrenamiento::where('user_id', $clienteId)
            ->where('fecha', '>=', Carbon::now()->subWeeks(12))
            ->orderBy('fecha')
            ->get();

        $sesionesMes = SesionEntrenamiento::where('user_id', $clienteId)
            ->where('fecha', '>=', Carbon::now()->subDays(30))
            ->get();
        $porcentajeConstancia = $sesionesMes->count() > 0
            ? round($sesionesMes->where('completada', true)->count() / $sesionesMes->count() * 100)
            : null;

        $rachaSemanas = $this->calcularRacha($clienteId);

        // ── Notas ──
        $notas = ClienteNota::where('user_id', $clienteId)
            ->orderByDesc('created_at')
            ->get();

        return view('layouts.Progresocliente', compact(
            'cliente',
            'medidas',
            'medidaMasReciente',
            'medidaMasAntigua',
            'fotos',
            'videos',
            'ejercicioPrincipal',
            'registrosEjercicio',
            'pesoMaximo',
            'pesoInicial',
            'seriesUltimaSemana',
            'volumenTotal',
            'estimaciones1RM',
            'historialPesoPorEjercicio',
            'segmentosDisponibles',
            'comparativaSegmentos',
            'estancamientos',
            'sesiones',
            'porcentajeConstancia',
            'rachaSemanas',
            'notas'
        ));
    }

    /**
     * Borra el 1RM VIGENTE de un ejercicio para un cliente (ej. después
     * de una lesión, o si el entrenador considera que el dato ya no
     * representa la fuerza real del cliente). El historial de
     * candidatos NO se borra, queda para auditoría — el sistema vuelve
     * a construir el 1RM desde cero con las próximas series completadas.
     */
    public function resetear1RM($clienteId, $ejercicioId)
    {
        $cliente = User::findOrFail($clienteId);
        $this->autorizarCliente($cliente);

        $borrado = EstimacionUnoRm::where('user_id', $clienteId)
            ->where('ejercicio_id', $ejercicioId)
            ->delete();

        return back()->with('success', $borrado
            ? 'Se reinició el 1RM de ese ejercicio. Se recalculará con la próxima serie que el cliente complete.'
            : 'Ese ejercicio no tenía un 1RM registrado.');
    }

    /**
     * Construye, para cada ejercicio que el cliente haya tenido en su
     * rutina, un mapa fecha -> mejor peso registrado ese día (el más
     * alto entre todas las series completadas de ese ejercicio, sin
     * importar el método). Incluye el segmento (grupo muscular) del
     * ejercicio, tomado directo de la columna 'segmento' de Rutina
     * (no hace falta join, ya viene denormalizada ahí).
     *
     * @return array<int, array{nombre: string, segmento: string, puntos: array<string, float>}>
     */
    private function construirHistorialPesoPorEjercicio(int $clienteId): array
    {
        $rutinas = Rutina::where('user_id', $clienteId)
            ->whereNotNull('fecha')
            ->whereNotNull('ejercicio_id')
            ->get(['ejercicio_id', 'nombre', 'segmento', 'fecha', 'series']);

        $historial = [];

        foreach ($rutinas as $r) {
            $series = is_string($r->series) ? json_decode($r->series, true) : ($r->series ?? []);
            if (!is_array($series)) continue;

            $mejorDelDia = null;
            foreach ($series as $serie) {
                if (!is_array($serie)) continue;
                $peso = $this->mejorPesoDeSerie($serie);
                if ($peso !== null && ($mejorDelDia === null || $peso > $mejorDelDia)) {
                    $mejorDelDia = $peso;
                }
            }

            if ($mejorDelDia === null) continue;

            $ejercicioId = $r->ejercicio_id;
            if (!isset($historial[$ejercicioId])) {
                $historial[$ejercicioId] = [
                    'nombre'   => $r->nombre,
                    'segmento' => $r->segmento ?: 'Otro',
                    'puntos'   => [],
                ];
            }

            $fecha = substr($r->fecha, 0, 10);
            $actual = $historial[$ejercicioId]['puntos'][$fecha] ?? null;
            if ($actual === null || $mejorDelDia > $actual) {
                $historial[$ejercicioId]['puntos'][$fecha] = $mejorDelDia;
            }
        }

        foreach ($historial as &$data) {
            ksort($data['puntos']);
        }
        unset($data);

        return array_filter($historial, fn ($d) => count($d['puntos']) >= 1);
    }

    /**
     * Extrae el mejor peso registrado en una serie COMPLETADA, sin
     * importar el método. Solo cuenta si el cliente marcó la serie
     * como hecha (completada).
     */
    private function mejorPesoDeSerie(array $serie): ?float
    {
        if (empty($serie['completada'])) {
            return null;
        }

        $metodo = $serie['metodo'] ?? 'normal';

        $candidatos = match ($metodo) {
            'normal'    => [$serie['peso'] ?? null],
            'restpause' => [$serie['peso_rp'] ?? null],
            'forzadas'  => [$serie['peso_fz'] ?? null],
            '888'       => [$serie['peso1'] ?? null, $serie['peso2'] ?? null, $serie['peso3'] ?? null],
            '10_21'     => [$serie['peso_10'] ?? null, $serie['peso_21'] ?? null],
            '21s'       => [$serie['peso_21s'] ?? null],
            'isometria' => [$serie['peso_iso'] ?? null],
            'parciales' => [$serie['peso_pc'] ?? null],
            'negativas' => [$serie['peso_ng'] ?? null],
            default     => [],
        };

        $valores = [];
        foreach ($candidatos as $c) {
            if ($c === null || $c === '') continue;
            $v = (float) $c;
            if ($v > 0) $valores[] = $v;
        }

        return empty($valores) ? null : max($valores);
    }

    /**
     * Compara, por segmento (grupo muscular), la suma de 1RM actual vs.
     * la suma de hace ~30 días — solo entre los ejercicios que tienen
     * un dato de hace 30 días con el que comparar (si un ejercicio es
     * nuevo y no tiene historial de esa antigüedad, se excluye del
     * cálculo de % pero sí cuenta en el total "hoy").
     *
     * @return array<int, array{
     *   segmento: string, total_hoy_kg: float, cambio_pct: float|null,
     *   ejercicios: int, con_comparacion: int
     * }>
     */
    private function construirComparativaSegmentos(int $clienteId, $estimaciones1RM): array
    {
        if ($estimaciones1RM->isEmpty()) {
            return [];
        }

        $fechaCorte = Carbon::now()->subDays(self::DIAS_COMPARACION);
        $porSegmento = [];

        foreach ($estimaciones1RM as $est) {
            $segmento = $est->ejercicio->segmento ?? 'Otro';

            if (!isset($porSegmento[$segmento])) {
                $porSegmento[$segmento] = [
                    'hoy_total'      => 0.0,
                    'hoy_comparable' => 0.0,
                    'antes'          => 0.0,
                    'ejercicios'     => 0,
                    'con_baseline'   => 0,
                ];
            }

            $porSegmento[$segmento]['ejercicios']++;
            $porSegmento[$segmento]['hoy_total'] += $est->valor_1rm_kg;

            $antes = EstimacionUnoRmHistorial::where('user_id', $clienteId)
                ->where('ejercicio_id', $est->ejercicio_id)
                ->where('se_uso_como_vigente', true)
                ->where('fecha_calculo', '<=', $fechaCorte)
                ->orderByDesc('fecha_calculo')
                ->value('valor_1rm_kg');

            if ($antes !== null && $antes > 0) {
                $porSegmento[$segmento]['hoy_comparable'] += $est->valor_1rm_kg;
                $porSegmento[$segmento]['antes'] += $antes;
                $porSegmento[$segmento]['con_baseline']++;
            }
        }

        $resultado = [];
        foreach ($porSegmento as $segmento => $d) {
            $cambioPct = ($d['con_baseline'] > 0 && $d['antes'] > 0)
                ? round((($d['hoy_comparable'] - $d['antes']) / $d['antes']) * 100, 1)
                : null;

            $resultado[] = [
                'segmento'        => $segmento,
                'total_hoy_kg'    => round($d['hoy_total'], 1),
                'cambio_pct'      => $cambioPct,
                'ejercicios'      => $d['ejercicios'],
                'con_comparacion' => $d['con_baseline'],
            ];
        }

        usort($resultado, fn ($a, $b) => ($b['cambio_pct'] ?? -999) <=> ($a['cambio_pct'] ?? -999));

        return $resultado;
    }

    /**
     * Detecta ejercicios "estancados": el 1RM vigente no se actualiza
     * hace más de SEMANAS_ESTANCAMIENTO semanas, PERO el cliente ha
     * seguido entrenando ese ejercicio en ese período (si no lo ha
     * vuelto a hacer, no es estancamiento, es simplemente que no lo
     * entrena — no se marca como alerta).
     */
    private function detectarEstancamientos(int $clienteId, $estimaciones1RM): array
    {
        if ($estimaciones1RM->isEmpty()) {
            return [];
        }

        $fechaLimite = Carbon::now()->subWeeks(self::SEMANAS_ESTANCAMIENTO);
        $estancados = [];

        foreach ($estimaciones1RM as $est) {
            if (!$est->fecha_calculo || $est->fecha_calculo->gt($fechaLimite)) {
                continue; // se actualizó hace poco, no aplica
            }

            $entrenoDespues = Rutina::where('user_id', $clienteId)
                ->where('ejercicio_id', $est->ejercicio_id)
                ->where('fecha', '>', $est->fecha_calculo->toDateString())
                ->exists();

            if (!$entrenoDespues) {
                continue; // no lo ha vuelto a hacer, no es "estancamiento"
            }

            $estancados[] = [
                'ejercicio_id' => $est->ejercicio_id,
                'nombre'       => $est->ejercicio->nombre ?? 'Ejercicio eliminado',
                'segmento'     => $est->ejercicio->segmento ?? 'Otro',
                'semanas'      => (int) floor($est->fecha_calculo->diffInWeeks(Carbon::now())),
                'valor_1rm'    => $est->valor_1rm_kg,
            ];
        }

        usort($estancados, fn ($a, $b) => $b['semanas'] <=> $a['semanas']);

        return $estancados;
    }

    private function calcularRacha($clienteId): int
    {
        $sesiones = SesionEntrenamiento::where('user_id', $clienteId)
            ->where('completada', true)
            ->orderByDesc('fecha')
            ->pluck('fecha');

        if ($sesiones->isEmpty()) {
            return 0;
        }

        $racha = 0;
        $semanaActual = Carbon::now()->startOfWeek();

        while (true) {
            $inicioSemana = $semanaActual->copy()->subWeeks($racha);
            $finSemana = $inicioSemana->copy()->endOfWeek();

            $huboSesion = $sesiones->contains(
                fn($fecha) => $fecha->between($inicioSemana, $finSemana)
            );

            if (!$huboSesion) break;
            $racha++;
        }

        return $racha;
    }

    // ── Guardar medidas del mes ──
    public function storeMedida(Request $request, $clienteId)
    {
        $cliente = User::findOrFail($clienteId);
        $this->autorizarCliente($cliente);

        $request->validate([
            'mes'            => 'required|date',
            'peso'           => 'nullable|numeric|min:0|max:400',
            'cintura'        => 'nullable|numeric|min:0|max:300',
            'cadera'         => 'nullable|numeric|min:0|max:300',
            'pecho'          => 'nullable|numeric|min:0|max:300',
            'brazo'          => 'nullable|numeric|min:0|max:100',
            'muslo'          => 'nullable|numeric|min:0|max:150',
            'grasa_corporal' => 'nullable|numeric|min:0|max:100',
        ]);

        ClienteMedida::updateOrCreate(
            [
                'user_id' => $clienteId,
                'mes'     => Carbon::parse($request->mes)->startOfMonth()->toDateString(),
            ],
            $request->only(['peso', 'cintura', 'cadera', 'pecho', 'brazo', 'muslo', 'grasa_corporal'])
        );

        return back()->with('success', 'Medidas guardadas correctamente.');
    }

    // ── Subir foto de progreso ──
    public function storeFoto(Request $request, $clienteId)
    {
        $cliente = User::findOrFail($clienteId);
        $this->autorizarCliente($cliente);

        $request->validate([
            'mes'    => 'required|date',
            'foto'   => 'required|image|max:8192',
            'angulo' => 'nullable|string|max:50',
        ]);

        $ruta = $request->file('foto')->store('progreso/' . $clienteId, 'public');

        ClienteFoto::create([
            'user_id' => $clienteId,
            'mes'     => Carbon::parse($request->mes)->startOfMonth()->toDateString(),
            'ruta'    => $ruta,
            'angulo'  => $request->angulo,
        ]);

        return back()->with('success', 'Foto agregada correctamente.');
    }

    // ── Agregar nota ──
    public function storeNota(Request $request, $clienteId)
    {
        $cliente = User::findOrFail($clienteId);
        $this->autorizarCliente($cliente);

        $request->validate([
            'contenido' => 'required|string|max:1000',
            'etiqueta'  => 'nullable|string|max:50',
        ]);

        ClienteNota::create([
            'user_id'       => $clienteId,
            'entrenador_id' => Auth::id(),
            'contenido'     => $request->contenido,
            'etiqueta'      => $request->etiqueta,
        ]);

        return back()->with('success', 'Nota agregada correctamente.');
    }
}