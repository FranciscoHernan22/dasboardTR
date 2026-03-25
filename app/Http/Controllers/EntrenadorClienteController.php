<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;


use App\Models\User;

class EntrenadorClienteController extends Controller
{
    public function index()
    {
        $entrenador = Auth::user(); // entrenador logueado

        // Obtener todos los clientes
        //  $clientes = User::all();
        $clientes = $entrenador->users; // 🔥 SOLO SUS CLIENTES


        
        return view('layouts.listado-clientes', compact('clientes'));
    }
}
