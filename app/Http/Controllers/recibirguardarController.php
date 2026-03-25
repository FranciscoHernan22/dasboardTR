<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Ejercicio;

class recibirguardarController extends Controller
{
    public function crearRutina()
    {
        // Obtenemos todos los usuarios (clientes)
        $clientes = User::all();

        // Enviar a la vista
        return view('crear-rutina', compact('clientes'));
    }

   public function guardarRutina(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'entrenamiento' => 'required',
        'semana' => 'required',
        'dia' => 'required',
    ]);

    $user_id = $request->user_id;
    $entrenamiento = $request->entrenamiento;
    $semana = $request->semana;
    $dia = $request->dia;

    $rutina = $request->rutina;

    foreach ($rutina as $idUnico => $bloque) {

        $tipo = $bloque['tipo'];
         $grupo = $bloque['grupo'];

        foreach ($bloque as $key => $ejercicioForm) {

           // ⛔ Ignorar tipo, grupo y cualquier otro metadato
            if (!is_numeric($key)) {
                continue;
            }

            // ------------------------------------------------------------------
            // 🔥 1. Obtienes el ejercicio ID que viene del select
            // ------------------------------------------------------------------
            $ejercicio_id = $ejercicioForm['ejercicio_id'];

            // ------------------------------------------------------------------
            // 🔥 2. Buscamos el nombre real del ejercicio en la BD
            // ------------------------------------------------------------------
            $ejercicioDB = Ejercicio::find($ejercicio_id);

            if (!$ejercicioDB) continue;

            $nombreReal = $ejercicioDB->nombre;   // ⭐ ESTE es el que guardamos

            // ------------------------------------------------------------------
            //  Guarda en DB
            // ------------------------------------------------------------------
            DB::table('rutinas')->insert([
                'user_id'       => $user_id,
                'tipo'          => $tipo,
                   'grupo'          => $grupo,
                'segmento' => $ejercicioDB->segmento,
                'nombre'        => $nombreReal,                 // ⭐ YA NO VIENE DEL FORM
                'series'        => $ejercicioForm['series'],
                'reps'          => $ejercicioForm['reps'],
                'entrenamiento' => $entrenamiento,
                'semana'        => $semana,
                'dia'           => $dia,
                'created_at'    => now(),
                'updated_at'    => now()
            ]);
        }
    }

    return redirect()->back()->with('success', 'Rutina guardada correctamente');
}
}
