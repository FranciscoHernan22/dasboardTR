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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EntrenadorProgresoController extends Controller
{
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
        // todavía. Se deja intacto (no se borra ni se toca) por si en algún
        // momento sí se usa, pero el peso real que se muestra en pantalla
        // ahora sale de Rutina + estimaciones_1rm (ver más abajo).
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
        // Esto sí viene del sistema de rutinas que realmente se usa.
        $estimaciones1RM = EstimacionUnoRm::where('user_id', $clienteId)
            ->with('ejercicio:id,nombre,segmento')
            ->orderByDesc('fecha_calculo')
            ->get();

        $historialPesoPorEjercicio = $this->construirHistorialPesoPorEjercicio($clienteId);

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
            'sesiones',
            'porcentajeConstancia',
            'rachaSemanas',
            'notas'
        ));
    }

    /**
     * Construye, para cada ejercicio que el cliente haya tenido en su
     * rutina, un mapa fecha -> mejor peso registrado ese día (el más
     * alto entre todas las series completadas de ese ejercicio, sin
     * importar el método). Se usa para graficar la evolución de peso
     * por ejercicio en la pestaña Rendimiento.
     *
     * @return array<int, array{nombre: string, puntos: array<string, float>}>
     */
    private function construirHistorialPesoPorEjercicio(int $clienteId): array
    {
        $rutinas = Rutina::where('user_id', $clienteId)
            ->whereNotNull('fecha')
            ->whereNotNull('ejercicio_id')
            ->get(['ejercicio_id', 'nombre', 'fecha', 'series']);

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
                $historial[$ejercicioId] = ['nombre' => $r->nombre, 'puntos' => []];
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

        // Solo interesan ejercicios con más de un punto (si no, no hay
        // "evolución" que graficar, solo un dato suelto).
        return array_filter($historial, fn ($d) => count($d['puntos']) >= 1);
    }

    /**
     * Extrae el mejor peso registrado en una serie COMPLETADA, sin
     * importar el método. Solo cuenta si el cliente marcó la serie
     * como hecha (completada) — series prescritas pero no confirmadas
     * no representan un registro real.
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