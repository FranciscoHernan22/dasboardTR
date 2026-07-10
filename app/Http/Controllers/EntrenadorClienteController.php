<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Plan;
use App\Models\Rutina;
use Carbon\Carbon;

class EntrenadorClienteController extends Controller
{
    public function index()
    {
        $entrenador = Auth::user();
        $clientes   = $entrenador->users()->with('plan')->get();
        return view('layouts.listado-clientes', compact('clientes'));
    }

    public function guardarPlan(Request $request, $clienteId)
    {
        $cliente       = User::findOrFail($clienteId);
        $semanas       = (int) $request->semanas;
        $planExistente = Plan::where('user_id', $clienteId)->first();

        if ($planExistente) {
            $nuevaInicio = $planExistente->semana_inicio + $planExistente->semanas;
            $planExistente->update([
                'semanas'       => $semanas,
                'semana_inicio' => $nuevaInicio,
            ]);
        } else {
            Plan::create([
                'user_id'       => $clienteId,
                'semanas'       => $semanas,
                'semana_inicio' => 1,
                'fecha_inicio'  => Carbon::now()->startOfWeek()->toDateString(),
            ]);
        }

        return redirect()->route('entrenador.rutina.menu', $cliente->id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name'          => trim($request->name . ' ' . $request->apellido),
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'entrenador_id' => Auth::id(),
            'status'        => 'activo',
        ]);

        return redirect()->route('entrenador.clientes')
                 ->with('success', 'Cliente registrado correctamente.');
    }


      public function toggleEstado($clienteId)
    {
        $cliente = User::findOrFail($clienteId);

        // Seguridad: que el cliente pertenezca a este entrenador
        if ($cliente->entrenador_id !== Auth::id()) {
            abort(403);
        }

        $cliente->status = $cliente->status === 'activo' ? 'inactivo' : 'activo';
        $cliente->save();

        return back()->with('success', $cliente->status === 'activo'
            ? "{$cliente->name} fue activado."
            : "{$cliente->name} fue desactivado.");
    }


    
}