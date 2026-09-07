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
    $cliente = User::findOrFail($clienteId);

    if ($cliente->entrenador_id !== Auth::id()) {
        abort(403);
    }

    $request->validate([
        'semanas'      => 'required|integer|min:1|max:52',
        'fecha_inicio' => 'required|date|after_or_equal:' . Carbon::today()->toDateString(),
    ]);

    $semanas = (int) $request->semanas;
    $nuevaFechaInicio = Carbon::parse($request->fecha_inicio); // ✅ definida aquí

    Plan::updateOrCreate(
        ['user_id' => $clienteId],
        [
            'semanas'       => $semanas,
            'semana_inicio' => 1,
            'fecha_inicio'  => $request->fecha_inicio,
        ]
    );

    // Recalcular fechas de TODAS las rutinas existentes para que
    // queden alineadas con el nuevo fecha_inicio del plan.
    $rutinas = Rutina::where('user_id', $clienteId)->get();

    foreach ($rutinas as $r) {                          // ✅ $rutinas, no $rutina
        $r->fecha = $nuevaFechaInicio->copy()           // ✅ -> en vez de -
            ->addDays(($r->semana - 1) * 7 + ($r->dia - 1))
            ->toDateString();                            // ✅ corregido el typo
        $r->save();
    }

    return redirect()->route('entrenador.rutina.menu', $cliente->id)
        ->with('success', 'Plan de entrenamiento guardado correctamente.');
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

    public function update(Request $request, $clienteId)
    {
        $cliente = User::findOrFail($clienteId);

        // Seguridad: que el cliente pertenezca a este entrenador
        if ($cliente->entrenador_id !== Auth::id()) {
            abort(403);
        }

        $request->validateWithBag('editarCliente', [
            'name'     => 'required|string|max:150',
            'email'    => 'required|email|unique:users,email,' . $cliente->id,
            'password' => 'nullable|string|min:6',
        ]);

        $cliente->name  = $request->name;
        $cliente->email = $request->email;

        if ($request->filled('password')) {
            $cliente->password = Hash::make($request->password);
        }

        $cliente->save();

        return back()->with('success', "Datos de {$cliente->name} actualizados correctamente.");
    }
}