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
$ejerciciosPorGrupo = Ejercicio::select('id', 'nombre', 'segmento', 'imagen')
    ->get()
    ->groupBy('segmento');
    
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

            $ejercicio = Ejercicio::findOrFail($ej['ejercicio_id']);

            // 🔥 NORMALIZAR SERIES SIEMPRE
             $series = [];

            if (!empty($ej['series']) && is_array($ej['series'])) {
                    foreach ($ej['series'] as $s) {
                        $series[] = [
                            'reps' => (int) ($s['reps'] ?? 0),
                            'peso' => (int) ($s['peso'] ?? 0),
                        ];
                    }
                }

            Rutina::create([
                'user_id'      => $cliente->id,
                'semana'       => $semana,
                'dia'          => $dia,
                'grupo'        => $grupo,
                'tipo'         => $bloque['tipo'],
                'orden'        => $orden,
                'ejercicio_id' => $ejercicio->id,
                'segmento'     => $ejercicio->segmento,
                'nombre'       => $ejercicio->nombre,
                'series'       => $series,

                // 🔥 AQUÍ ESTÁ EL FIX
             
                //    'series'       => $ej['series'] ?? [],
            ]);
        }

        $orden++;
    }

    return back()->with('success', 'Rutina guardada correctamente');
}


}
