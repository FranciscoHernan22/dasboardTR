<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ejercicio;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrearRutinaController extends Controller
{
  public function crearRutina()
{
    $clientes   = User::all();
    $ejercicios = Ejercicio::all();
    return view('crear-rutina', compact('clientes', 'ejercicios'));
}
}