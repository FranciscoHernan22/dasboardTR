<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ejercicio;
use Illuminate\Http\Request;

class CrearRutinaController extends Controller
{
    public function crearRutina(Request $request, $clienteId = null)
    {
        $semanas    = (int) $request->query('semanas', 4);
        $semanas    = max(1, min(12, $semanas));
        $cliente    = $clienteId ? User::findOrFail($clienteId) : null;
        $clientes   = User::all();
        $ejercicios = Ejercicio::all();

        return view('crear-rutina', compact('clientes', 'ejercicios', 'semanas', 'cliente'));
    }
}