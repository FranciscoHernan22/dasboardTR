<?php
// DESTINO: app/Http/Controllers/PlantillaController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plantilla;
use App\Models\Ejercicio;
use App\Models\Rutina;
use App\Models\User;
use App\Models\DiaCompletado;
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
        // Clonar catálogo default si es la primera vez que entra este entrenador
        Ejercicio::asegurarDefaultsPara(Auth::id());

        $ejerciciosPorGrupo = Ejercicio::select('id', 'nombre', 'segmento', 'imagen')
            ->where('entrenador_id', Auth::id())
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
            // Una plantilla es un diseño reutilizable, nunca debe cargar
            // estado de progreso (peso registrado / serie marcada) de un
            // cliente real. Se limpia antes de guardar.
            'bloques'       => $this->limpiarDiasPlantilla($datos['dias']),
        ]);

        return redirect()->route('entrenador.plantillas.index')
            ->with('success', 'Plantilla guardada correctamente');
    }

    public function editar(Plantilla $plantilla)
    {
        if ($plantilla->entrenador_id !== Auth::id()) abort(403);

        Ejercicio::asegurarDefaultsPara(Auth::id());

        $mapaEjercicios = $this->construirMapaEjercicios(Auth::id());

        if (!empty($mapaEjercicios)) {
            $bloques = $plantilla->bloques ?? [];
            $bloquesRemapeados = [];

            foreach ($bloques as $diaIdx => $dia) {
                $bloquesRemapeados[$diaIdx] = $dia;
                foreach ($dia['bloques'] ?? [] as $grupo => $bloque) {
                    $ejerciciosRemapeados = [];
                    foreach ($bloque['ejercicios'] ?? [] as $ejIdx => $ej) {
                        $idViejo = $ej['ejercicio_id'] ?? null;
                        if ($idViejo && isset($mapaEjercicios[$idViejo])) {
                            $ej['ejercicio_id'] = $mapaEjercicios[$idViejo];
                        }
                        $ejerciciosRemapeados[$ejIdx] = $ej;
                    }
                    $bloquesRemapeados[$diaIdx]['bloques'][$grupo]['ejercicios'] = $ejerciciosRemapeados;
                }
            }

            $plantilla->bloques = $bloquesRemapeados;
        }

        $ejerciciosPorGrupo = Ejercicio::select('id', 'nombre', 'segmento', 'imagen')
            ->where('entrenador_id', Auth::id())
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
            'bloques'     => $this->limpiarDiasPlantilla($datos['dias']),
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

        $clienteId    = $request->cliente_id;
        $semanaInicio = (int) $request->semana_inicio;
        $diaInicio    = (int) $request->dia_inicio;
        $solodia      = $request->solo_dia;

        $cliente = User::findOrFail($clienteId);
        $plan    = $cliente->plan;

        // Construir mapa de remapeo: id_default → id_clonado para este entrenador
        // Sirve para plantillas guardadas con IDs viejos (del catálogo default)
        $mapaEjercicios = $this->construirMapaEjercicios(Auth::id());

        $dias = $plantilla->bloques ?? [];

        if ($solodia !== null) {
            $diaPlantilla = $dias[$solodia] ?? null;
            if (!$diaPlantilla) return back()->withErrors(['error' => 'Día no encontrado']);

            $fecha = $plan && $plan->fecha_inicio
                ? Carbon::parse($plan->fecha_inicio)->addWeeks($semanaInicio - 1)->addDays($diaInicio - 1)->toDateString()
                : now()->toDateString();

            $this->guardarDiaRutina($cliente, $semanaInicio, $diaInicio, $diaPlantilla['bloques'] ?? [], $fecha, $mapaEjercicios);
        } else {
            $semana = $semanaInicio;
            $dia    = $diaInicio;

            foreach ($dias as $diaData) {
                if ($dia > 7) { $dia = 1; $semana++; }

                $fecha = $plan && $plan->fecha_inicio
                    ? Carbon::parse($plan->fecha_inicio)->addWeeks($semana - 1)->addDays($dia - 1)->toDateString()
                    : now()->toDateString();

                $this->guardarDiaRutina($cliente, $semana, $dia, $diaData['bloques'] ?? [], $fecha, $mapaEjercicios);
                $dia++;
            }
        }

        return redirect()->route('entrenador.rutina.menu', $cliente->id)
            ->with('success', 'Plantilla aplicada correctamente');
    }

    /**
     * Construye un mapa [id_default => id_clonado] para los ejercicios
     * de este entrenador. Sirve para convertir IDs viejos (del catálogo
     * default con entrenador_id NULL) a los IDs propios del entrenador.
     */
    private function construirMapaEjercicios(int $entrenadorId): array
    {
        $defaults = Ejercicio::whereNull('entrenador_id')->get(['id', 'nombre', 'segmento']);
        $propios  = Ejercicio::where('entrenador_id', $entrenadorId)->get(['id', 'nombre', 'segmento']);

        $mapa = [];
        foreach ($defaults as $default) {
            $clon = $propios->first(
                fn ($e) => $e->nombre === $default->nombre && $e->segmento === $default->segmento
            );
            if ($clon) {
                $mapa[$default->id] = $clon->id;
            }
        }

        return $mapa;
    }

    /**
     * Quita cualquier estado de progreso ('completada') de las series
     * de cada ejercicio, en todos los días/bloques de una plantilla.w
     * Una plantilla es un diseño, no debe recordar que "ya se hizo".
     */
    private function limpiarDiasPlantilla(array $dias): array
    {
        foreach ($dias as $diaIdx => $dia) {
            foreach ($dia['bloques'] ?? [] as $grupo => $bloque) {
                foreach ($bloque['ejercicios'] ?? [] as $ejIdx => $ej) {
                    $dias[$diaIdx]['bloques'][$grupo]['ejercicios'][$ejIdx]['series'] =
                        $this->limpiarSeries($ej['series'] ?? []);
                }
            }
        }
        return $dias;
    }

    /**
     * Quita el flag 'completada' (y cualquier otro campo de progreso) de
     * un array de series, dejando intactos los valores prescritos
     * (reps, peso objetivo, método, tempo, rir, etc.).
     */
    private function limpiarSeries(array $series): array
    {
        return array_map(function ($serie) {
            unset($serie['completada']);
            return $serie;
        }, $series);
    }

    /**
     * Normaliza el descanso de un bloque de plantilla al formato que
     * espera Rutina: un array de {valor} en segundos, uno por serie.
     *
     * Soporta dos formatos de entrada:
     * - Nuevo (descanso por serie):  $bloque['descansos_serie'] = [{valor:'90'}, {valor:'60'}, ...]
     * - Legado (un solo descanso para todo el bloque, plantillas
     *   guardadas antes de este cambio): $bloque['descanso_valor'] / $bloque['descanso_unidad']
     *   Ese valor legado se replica en todas las series del bloque.
     */
    private function normalizarDescansosSerie(array $bloque, int $numSeries): array
    {
        if (isset($bloque['descansos_serie']) && is_array($bloque['descansos_serie'])) {
            return $bloque['descansos_serie'];
        }

        $valor  = $bloque['descanso_valor'] ?? '';
        $unidad = $bloque['descanso_unidad'] ?? 'seg';
        $segundos = 0;
        if ($valor !== '' && $valor !== null) {
            $segundos = $unidad === 'min' ? ((int) $valor) * 60 : (int) $valor;
        }

        $descansos = [];
        for ($i = 0; $i < $numSeries; $i++) {
            $descansos[] = ['valor' => $segundos > 0 ? (string) $segundos : ''];
        }
        return $descansos;
    }

    private function guardarDiaRutina(User $cliente, int $semana, int $dia, array $bloques, string $fecha, array $mapaEjercicios = [])
    {
        Rutina::where('user_id', $cliente->id)
            ->where('semana', $semana)
            ->where('dia', $dia)
            ->delete();

        // Este día se está reescribiendo desde una plantilla: si el
        // cliente ya lo tenía marcado como completado, ese estado ya
        // no corresponde a la rutina nueva que se está aplicando.
        DiaCompletado::where('user_id', $cliente->id)
            ->where('semana', $semana)
            ->where('dia', $dia)
            ->delete();

        $orden = 0;
        foreach ($bloques as $grupo => $bloque) {
            $ejercicios = $bloque['ejercicios'] ?? [];
            if (empty($ejercicios)) { $orden++; continue; }

            // El descanso se guarda por bloque (mismo descanso para todos
            // los ejercicios del grupo), calculado a partir del número de
            // series del primer ejercicio del bloque -- igual que hace el
            // editor de rutina.
            $numSeries = count(reset($ejercicios)['series'] ?? []);
            $descansosSerie = $this->normalizarDescansosSerie($bloque, $numSeries);

            foreach ($ejercicios as $ej) {
                if (empty($ej['ejercicio_id'])) continue;

                // Si el ID viene del catálogo default, lo convertimos al clon del entrenador
                $ejercicioId = $mapaEjercicios[$ej['ejercicio_id']] ?? $ej['ejercicio_id'];

                $ejercicio = Ejercicio::find($ejercicioId);
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
                    // Blindaje extra: aunque la plantilla ya venga limpia,
                    // se vuelve a quitar 'completada' justo antes de crear
                    // la rutina real del cliente. Así cubrimos también
                    // plantillas guardadas antes de este fix.
                    'series'          => $this->limpiarSeries($ej['series'] ?? []),
                    // Mismo formato que usa el editor de rutina: descanso
                    // por serie, no un único valor por bloque.
                    'descansos_serie' => $descansosSerie,
                    'nota_sesion'     => '',
                    'nota_ej'         => $ej['nota_ej'] ?? '',
                ]);
            }
            $orden++;
        }
    }
}