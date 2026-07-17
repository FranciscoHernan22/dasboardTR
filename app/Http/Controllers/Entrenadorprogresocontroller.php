<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ClienteMedida;
use App\Models\ClienteFoto;
use App\Models\ClienteVideo;
use App\Models\ClienteNota;
use App\Models\EjercicioRegistro;
use App\Models\SesionEntrenamiento;
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
            'sesiones',
            'porcentajeConstancia',
            'rachaSemanas',
            'notas'
        ));
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