<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rutina;
use App\Models\User;
use App\Models\Ejercicio;
use Illuminate\Support\Facades\Auth;

class EntrenadorRutinaController extends Controller
{
    public function menu(User $cliente)
    {
        if ($cliente->entrenador_id !== Auth::id()) {
            abort(403);
        }

        return view('rutina.menu', compact('cliente'));
    }

   public function editar(User $cliente, $semana, $dia)
{
    if ($cliente->entrenador_id !== Auth::id()) {
        abort(403);
    }

    // 🔥 ejercicios indexados por id
    $ejercicios = Ejercicio::all()->keyBy('id');
    $ejerciciosPorGrupo = Ejercicio::all()->groupBy('segmento');

    // 🔥 lectura correcta
    $bloques = Rutina::where('user_id', $cliente->id)
        ->where('semana', $semana)
        ->where('dia', $dia)
        ->orderBy('orden')     // orden del bloque
        ->orderBy('id')        // orden interno
        ->get()
        ->groupBy('grupo');

    return view('layouts.editar-rutina', compact(
        'cliente',
        'semana',
        'dia',
        'bloques',
        'ejerciciosPorGrupo',
        'ejercicios'
    ));
}


public function guardar(Request $request, User $cliente, $semana, $dia)
{
    if ($cliente->entrenador_id !== Auth::id()) {
        abort(403);
    }

    Rutina::where('user_id', $cliente->id)
        ->where('semana', $semana)
        ->where('dia', $dia)
        ->delete();

    $orden = 0;

    foreach ($request->bloques as $grupo => $bloque) {

        foreach ($bloque['ejercicios'] as $ej) {

            // 🔥 ejercicio REAL
            $ejercicio = Ejercicio::findOrFail($ej['ejercicio_id']);

            Rutina::create([
                'user_id'       => $cliente->id,
                'semana'        => $semana,
                'dia'           => $dia,
                'grupo'         => $grupo,
                'tipo'          => $bloque['tipo'],
                'orden'         => $orden,
                'ejercicio_id'  => $ejercicio->id,
                'segmento'      => $ejercicio->segmento, // ✅ SIEMPRE CORRECTO
                'nombre'        => $ejercicio->nombre,
                'series'        => $ej['series'] ?? 0,
                'reps'          => $ej['reps'] ?? 0,
            ]);
        }

        $orden++;
    }

    return back()->with('success', 'Rutina guardada correctamente');
}


}
