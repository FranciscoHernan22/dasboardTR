<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ejercicio;
use Illuminate\Http\Request;

class CrearRutinaController extends Controller
{
    public function crearRutina()
    {
        // Obtener todos los clientes
        $clientes = User::all();

        // Obtener todos los ejercicios
        $ejercicios = Ejercicio::all();

        return view('crear-rutina', compact('clientes', 'ejercicios'));
    }
}