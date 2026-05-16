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
            // La semana inicio del nuevo bloque es la última semana del plan anterior + 1
            $ultimaSemana  = $planExistente->semana_inicio + $planExistente->semanas - 1;
            $nuevaInicio   = $ultimaSemana + 1;
            $fechaInicio   = Carbon::parse($planExistente->fecha_inicio)
                ->addWeeks($ultimaSemana);

            $planExistente->update([
                'semanas'       => $semanas,
                'semana_inicio' => $nuevaInicio,
                'fecha_inicio'  => $fechaInicio,
            ]);
        } else {
            Plan::create([
                'user_id'       => $clienteId,
                'semanas'       => $semanas,
                'semana_inicio' => 1,
                'fecha_inicio'  => Carbon::now()->startOfWeek(),
            ]);
        }

        return redirect()->route('entrenador.rutina.menu', $cliente->id);
    }
}