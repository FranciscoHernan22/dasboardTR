<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
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

            // fecha_inicio NO cambia — siempre apunta a la semana 1
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
}