<?php

namespace App\Http\Controllers;

use App\Models\Entrenador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EntrenadorController extends Controller
{
    // Guarda el registro del entrenador
    public function store(Request $request)
    {
        // 1. Validación
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:entrenadores',
            'email' => 'required|email|unique:entrenadores',
            'password' => 'required|confirmed|min:8',
        ]);

        // 2. Crear entrenador
        Entrenador::create([
            'nombre' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 3. Redireccionar
        return redirect()
            ->route('login')
            ->with('mensaje', 'Entrenador registrado correctamente');
    }
}
