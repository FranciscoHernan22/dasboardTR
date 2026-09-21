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
 
    // Se calcula acá (no en la vista) para no repetir la query dentro
    // del @foreach del Blade — con esto es una query por cliente CON
    // plan, no una por cada fila de la tabla.
    $clientes->each(function ($cliente) {
        $cliente->progreso = $this->calcularProgresoPlan($cliente);
    });
 
    return view('layouts.listado-clientes', compact('clientes'));
}


    public function guardarPlan(Request $request, $clienteId)
    {
        $cliente = User::findOrFail($clienteId);

        // Seguridad: que el cliente pertenezca a este entrenador
        if ($cliente->entrenador_id !== Auth::id()) {
            abort(403);
        }

        // "Hoy" debe calcularse en la zona horaria del entrenador, no en
        // la zona horaria del servidor (que por defecto en Laravel es UTC).
        // Si no se hace esto, un entrenador en México puede seleccionar
        // "hoy" en el date picker del navegador y el servidor lo rechaza
        // porque para él (en UTC) ya es el día siguiente.
        $hoyLocal = Carbon::now('America/Mazatlan')->startOfDay()->toDateString();

        $request->validate([
            'semanas'      => 'required|integer|min:1|max:52',
            // La fecha de inicio la elige el entrenador y nunca puede ser anterior a hoy.
            'fecha_inicio' => 'required|date|after_or_equal:' . $hoyLocal,
        ]);

        $semanasNuevas = (int) $request->semanas;
        $planExistente = $cliente->plan;

        // Si ya existía un plan, el nuevo ciclo arranca justo después
        // de la última semana del ciclo anterior. Esas semanas viejas
        // NO se tocan: quedan en Rutina como historial.
        $nuevaSemanaInicio = $planExistente
            ? $planExistente->semana_inicio + $planExistente->semanas
            : 1;

        // Reemplazo total: la Semana 1 / Día 1 del plan siempre coincide
        // con la fecha que eligió el entrenador. Esto evita que un plan
        // nuevo arrastre fechas de un plan anterior (por ejemplo después
        // de borrar el historial).
        Plan::updateOrCreate(
            ['user_id' => $clienteId],
            [
                'semanas'       => $semanasNuevas,
                'semana_inicio' => $nuevaSemanaInicio,
                'fecha_inicio'  => $request->fecha_inicio,
            ]
        );

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


    private function calcularProgresoPlan(User $cliente): ?array
{
    $plan = $cliente->plan;
    if (!$plan) {
        return null;
    }
 
    $hoy         = Carbon::now('America/Mazatlan')->startOfDay();
    $fechaInicio = Carbon::parse($plan->fecha_inicio)->startOfDay();
    // Fin del plan: N semanas de 7 días calendario desde el inicio.
    $fechaFin    = $fechaInicio->copy()->addWeeks($plan->semanas)->subDay();
 
    $semanaFinPlan = $plan->semana_inicio + $plan->semanas - 1;
 
    $filas = Rutina::where('user_id', $cliente->id)
        ->whereBetween('semana', [$plan->semana_inicio, $semanaFinPlan])
        ->select('semana', 'dia', 'series')
        ->get()
        ->unique(fn ($r) => $r->semana . '-' . $r->dia); // 1 fila por día, sin importar cuántos bloques tenga
 
    $diaEstaCompletado = function ($r) {
        $series = $r->series;
        if (is_string($series)) {
            $series = json_decode($series, true) ?? [];
        }
        foreach ($series as $s) {
            if (!empty($s['completada'])) {
                return true;
            }
        }
        return false;
    };
 
    $diasAsignados   = $filas->count();
    $diasCompletados = $filas->filter($diaEstaCompletado)->count();
 
    if ($hoy->lt($fechaInicio)) {
        $estado = 'no_iniciado';
    } elseif ($hoy->gt($fechaFin)) {
        $estado = 'finalizado';
    } else {
        $estado = 'en_curso';
    }
 
    // Semana actual del plan (1..semanas), por fecha calendario —
    // independiente de si el cliente entrenó ese día o no.
    $semanaActual = null;
    if ($estado !== 'no_iniciado') {
        $diasTranscurridos = $fechaInicio->diffInDays($hoy) + 1;
        $semanaActual = (int) floor(($diasTranscurridos - 1) / 7) + 1;
        $semanaActual = max(1, min($plan->semanas, $semanaActual));
    }
 
    // Semanas COMPLETAS sin ningún día entrenado, ya transcurridas
    // (o todo el plan si ya finalizó). Es distinto de "le faltó un
    // día suelto": acá es la semana entera en cero, que suele ser la
    // señal más útil para que el entrenador reaccione.
    //
    // Nota: una semana sin NINGUNA fila en Rutina (porque el
    // entrenador nunca le asignó rutina esa semana) no aparece acá —
    // solo se marcan semanas que SÍ tenían algo asignado pero el
    // cliente no tocó nada. Si también querés detectar "semanas sin
    // rutina asignada" como un caso aparte, avisame.
    $semanasSinEntrenar = $filas
        ->groupBy('semana')
        ->filter(function ($filasSemana, $semanaAbs) use ($estado, $semanaActual, $plan, $diaEstaCompletado) {
            $semanaRel = $semanaAbs - $plan->semana_inicio + 1;
 
            $yaPaso = $estado === 'finalizado'
                || ($semanaActual !== null && $semanaRel < $semanaActual);
 
            if (!$yaPaso) {
                return false; // semana futura o la que está en curso: no se puede "juzgar" todavía
            }
 
            return $filasSemana->contains($diaEstaCompletado) === false; // ni un solo día completado
        })
        ->keys()
        ->map(fn ($semanaAbs) => $semanaAbs - $plan->semana_inicio + 1)
        ->sort()
        ->values();
 
    return [
        'estado'              => $estado,
        'semana_actual'       => $semanaActual,
        'semanas_total'       => $plan->semanas,
        'dias_asignados'      => $diasAsignados,
        'dias_completados'    => $diasCompletados,
        'porcentaje'          => $diasAsignados > 0 ? (int) round($diasCompletados / $diasAsignados * 100) : null,
        'semanas_sin_entrenar'=> $semanasSinEntrenar->all(), // ej. [2, 4] -> semanas relativas 2 y 4 en cero
        'fecha_inicio'        => $fechaInicio,
        'fecha_fin'           => $fechaFin,
    ];
}
}