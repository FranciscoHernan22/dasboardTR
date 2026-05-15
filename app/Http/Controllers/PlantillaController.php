<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plantilla;
use App\Models\Ejercicio;
use Illuminate\Support\Facades\Auth;

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
            'nombre'    => 'required|string|max:100',
            'datos_json'=> 'required|string',
        ]);

        $datos = json_decode($request->datos_json, true);

        if (json_last_error() !== JSON_ERROR_NONE || empty($datos['bloques'])) {
            return back()->withErrors(['error' => 'Error al procesar los datos.']);
        }

        Plantilla::create([
            'entrenador_id' => Auth::id(),
            'nombre'        => $request->nombre,
            'descripcion'   => $request->descripcion,
            'bloques'       => $datos['bloques'],
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
            'nombre'    => 'required|string|max:100',
            'datos_json'=> 'required|string',
        ]);

        $datos = json_decode($request->datos_json, true);

        if (json_last_error() !== JSON_ERROR_NONE || empty($datos['bloques'])) {
            return back()->withErrors(['error' => 'Error al procesar los datos.']);
        }

        $plantilla->update([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'bloques'     => $datos['bloques'],
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
}