<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plantilla;
use App\Models\Ejercicio;
use App\Models\Rutina;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PlantillaController extends Controller
{
    public function index()
    {
        $plantillas = Plantilla::where('entrenador_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('entrenador.plantillas.index', compact('plantillas'));
    }

    public function crear()
    {
        $ejerciciosPorGrupo = Ejercicio::select('id', 'nombre', 'segmento', 'imagen')
            ->get()
            ->groupBy('segmento');
        return view('entrenador.plantillas.crear', compact('ejerciciosPorGrupo'));
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'nombre'     => 'required|string|max:100',
            'datos_json' => 'required|string',
        ]);

        $datos = json_decode($request->datos_json, true);

        if (json_last_error() !== JSON_ERROR_NONE || empty($datos['dias'])) {
            return back()->withErrors(['error' => 'Error al procesar los datos.']);
        }

        Plantilla::create([
            'entrenador_id' => Auth::id(),
            'nombre'        => $request->nombre,
            'descripcion'   => $request->descripcion,
            'bloques'       => $datos['dias'], // guardamos dias como bloques
        ]);

        return redirect()->route('entrenador.plantillas.index')
            ->with('success', 'Plantilla guardada correctamente');
    }

    public function editar(Plantilla $plantilla)
    {
        if ($plantilla->entrenador_id !== Auth::id()) abort(403);

        $ejerciciosPorGrupo = Ejercicio::select('id', 'nombre', 'segmento', 'imagen')
            ->get()
            ->groupBy('segmento');

        return view('entrenador.plantillas.editar', compact('plantilla', 'ejerciciosPorGrupo'));
    }

    public function actualizar(Request $request, Plantilla $plantilla)
    {
        if ($plantilla->entrenador_id !== Auth::id()) abort(403);

        $request->validate([
            'nombre'     => 'required|string|max:100',
            'datos_json' => 'required|string',
        ]);

        $datos = json_decode($request->datos_json, true);

        if (json_last_error() !== JSON_ERROR_NONE || empty($datos['dias'])) {
            return back()->withErrors(['error' => 'Error al procesar los datos.']);
        }

        $plantilla->update([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'bloques'     => $datos['dias'],
        ]);

        return redirect()->route('entrenador.plantillas.index')
            ->with('success', 'Plantilla actualizada correctamente');
    }

    public function eliminar(Plantilla $plantilla)
    {
        if ($plantilla->entrenador_id !== Auth::id()) abort(403);
        $plantilla->delete();
        return back()->with('success', 'Plantilla eliminada');
    }

    // ── Aplicar plantilla a un cliente ──
    public function aplicar(Request $request, Plantilla $plantilla)
    {
        if ($plantilla->entrenador_id !== Auth::id()) abort(403);

        $clienteId   = $request->cliente_id;
        $semanaInicio = (int) $request->semana_inicio;
        $diaInicio    = (int) $request->dia_inicio;
        $solodia      = $request->solo_dia; // null = todos, número = día específico de la plantilla

        $cliente = User::findOrFail($clienteId);
        $plan    = $cliente->plan;

        $dias = $plantilla->bloques ?? [];

        if ($solodia !== null) {
            // Aplicar solo un día específico de la plantilla
            $diaPlantilla = $dias[$solodia] ?? null;
            if (!$diaPlantilla) return back()->withErrors(['error' => 'Día no encontrado']);

            $fecha = $plan && $plan->fecha_inicio
                ? Carbon::parse($plan->fecha_inicio)->addWeeks($semanaInicio - 1)->addDays($diaInicio - 1)->toDateString()
                : now()->toDateString();

            $this->guardarDiaRutina($cliente, $semanaInicio, $diaInicio, $diaPlantilla['bloques'] ?? [], $fecha);
        } else {
            // Aplicar todos los días consecutivamente
            $semana = $semanaInicio;
            $dia    = $diaInicio;

            foreach ($dias as $diaData) {
                if ($dia > 7) { $dia = 1; $semana++; }

                $fecha = $plan && $plan->fecha_inicio
                    ? Carbon::parse($plan->fecha_inicio)->addWeeks($semana - 1)->addDays($dia - 1)->toDateString()
                    : now()->toDateString();

                $this->guardarDiaRutina($cliente, $semana, $dia, $diaData['bloques'] ?? [], $fecha);
                $dia++;
            }
        }

        return redirect()->route('entrenador.rutina.menu', $cliente->id)
            ->with('success', 'Plantilla aplicada correctamente');
    }

    private function guardarDiaRutina(User $cliente, int $semana, int $dia, array $bloques, string $fecha)
    {
        // Borra lo que había ese día
        Rutina::where('user_id', $cliente->id)
            ->where('semana', $semana)
            ->where('dia', $dia)
            ->delete();

        $orden = 0;
        foreach ($bloques as $grupo => $bloque) {
            foreach ($bloque['ejercicios'] ?? [] as $ej) {
                if (empty($ej['ejercicio_id'])) continue;
                $ejercicio = \App\Models\Ejercicio::find($ej['ejercicio_id']);
                if (!$ejercicio) continue;

                Rutina::create([
                    'user_id'         => $cliente->id,
                    'semana'          => $semana,
                    'dia'             => $dia,
                    'fecha'           => $fecha,
                    'grupo'           => $grupo,
                    'tipo'            => $bloque['tipo'],
                    'orden'           => $orden,
                    'ejercicio_id'    => $ejercicio->id,
                    'segmento'        => $ejercicio->segmento,
                    'nombre'          => $ejercicio->nombre,
                    'series'          => $ej['series'] ?? [],
                    'descanso_valor'  => $bloque['descanso_valor']  ?? '',
                    'descanso_unidad' => $bloque['descanso_unidad'] ?? 'seg',
                    'nota_sesion'     => '',
                    'nota_ej'         => $ej['nota_ej'] ?? '',
                ]);
            }
            $orden++;
        }
    }
}