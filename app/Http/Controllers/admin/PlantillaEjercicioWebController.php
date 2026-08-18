<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ejercicio;
use Illuminate\Http\Request;

class PlantillaEjercicioWebController extends Controller
{
    public function index()
    {
        $ejercicios = Ejercicio::whereNull('entrenador_id')
            ->orderBy('nombre')
            ->get();

        return view('admin.plantilla.index', compact('ejercicios'));
    }

    public function create()
    {
        return view('admin.plantilla.form', [
            'ejercicio' => new Ejercicio(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'   => ['required', 'string', 'max:255'],
            'segmento' => ['nullable', 'string', 'max:255'],
            'imagen'   => ['nullable', 'string', 'max:255'],
            'video'    => ['nullable', 'string', 'max:255'],
        ]);

        $data['entrenador_id'] = null; // siempre plantilla

        Ejercicio::create($data);

        return redirect()
            ->route('admin.plantilla.index')
            ->with('status', 'Ejercicio agregado a la plantilla.');
    }

    public function edit(Ejercicio $ejercicio)
    {
        $this->verificarEsPlantilla($ejercicio);

        return view('admin.plantilla.form', compact('ejercicio'));
    }

    public function update(Request $request, Ejercicio $ejercicio)
    {
        $this->verificarEsPlantilla($ejercicio);

        $data = $request->validate([
            'nombre'   => ['required', 'string', 'max:255'],
            'segmento' => ['nullable', 'string', 'max:255'],
            'imagen'   => ['nullable', 'string', 'max:255'],
            'video'    => ['nullable', 'string', 'max:255'],
        ]);

        $ejercicio->update($data);

        return redirect()
            ->route('admin.plantilla.index')
            ->with('status', 'Ejercicio actualizado.');
    }

    public function destroy(Ejercicio $ejercicio)
    {
        $this->verificarEsPlantilla($ejercicio);

        $ejercicio->delete();

        return redirect()
            ->route('admin.plantilla.index')
            ->with('status', 'Ejercicio eliminado de la plantilla.');
    }

    // Seguro: nunca operar sobre un ejercicio que ya pertenece a un entrenador
    private function verificarEsPlantilla(Ejercicio $ejercicio): void
    {
        abort_if(
            ! is_null($ejercicio->entrenador_id),
            422,
            'Este ejercicio no pertenece a la plantilla.'
        );
    }
}