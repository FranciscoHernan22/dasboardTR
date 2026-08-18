<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePlantillaEjercicioRequest;
use App\Http\Requests\UpdatePlantillaEjercicioRequest;
use App\Models\Ejercicio;

class PlantillaEjercicioController extends Controller
{
    /**
     * Listar todos los ejercicios de la plantilla (entrenador_id IS NULL).
     */
    public function index()
    {
        $ejercicios = Ejercicio::whereNull('entrenador_id')
            ->orderBy('nombre')
            ->get();

        return response()->json($ejercicios);
    }

    /**
     * Crear un ejercicio nuevo en la plantilla.
     * Los entrenadores que se registren después lo recibirán al clonar.
     */
    public function store(StorePlantillaEjercicioRequest $request)
    {
        $ejercicio = Ejercicio::create([
            'entrenador_id' => null, // siempre NULL: pertenece a la plantilla
            'nombre'        => $request->nombre,
            'segmento'      => $request->segmento,
            'imagen'        => $request->imagen,
            'video'         => $request->video,
        ]);

        return response()->json($ejercicio, 201);
    }

    /**
     * Editar un ejercicio existente de la plantilla.
     */
    public function update(UpdatePlantillaEjercicioRequest $request, Ejercicio $ejercicio)
    {
        // Seguro extra: nunca editar si el registro no es de la plantilla,
        // aunque llegue un id que pertenezca a un entrenador.
        if (! is_null($ejercicio->entrenador_id)) {
            return response()->json([
                'message' => 'Este ejercicio no pertenece a la plantilla.',
            ], 422);
        }

        $ejercicio->update($request->only(['nombre', 'segmento', 'imagen', 'video']));

        return response()->json($ejercicio);
    }

    /**
     * Eliminar un ejercicio de la plantilla.
     */
    public function destroy(Ejercicio $ejercicio)
    {
        if (! is_null($ejercicio->entrenador_id)) {
            return response()->json([
                'message' => 'Este ejercicio no pertenece a la plantilla.',
            ], 422);
        }

        $ejercicio->delete();

        return response()->json(['message' => 'Ejercicio eliminado de la plantilla.']);
    }
}