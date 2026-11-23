<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entrenador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Entrenador::query()
            ->withCount([
                // Cuenta solo los clientes activos de cada entrenador
                'users as clientes_activos_count' => fn ($q) => $q->where('activo', true),
            ])
            ->orderBy('nombre');

        // Filtro por estatus: ?estado=activos | inactivos | vencidos
        if ($request->estado === 'activos') {
            $query->where('activo', true);
        } elseif ($request->estado === 'inactivos') {
            $query->where('activo', false);
        } elseif ($request->estado === 'vencidos') {
            $query->whereNotNull('vence_el')->where('vence_el', '<', now());
        }

        if ($request->filled('buscar')) {
            $q = $request->buscar;
            $query->where(function ($sub) use ($q) {
                $sub->where('nombre', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('username', 'like', "%{$q}%");
            });
        }

        $entrenadores = $query->paginate(20)->withQueryString();

        return view('admin.entrenadores.index', [
            'entrenadores' => $entrenadores,
            'estado'       => $request->estado,
            'buscar'       => $request->buscar,
        ]);
    }

    // Crear entrenador desde el panel
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'   => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:entrenadores,username'],
            'email'    => ['required', 'email', 'max:255', 'unique:entrenadores,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['activo']   = true;

        $entrenador = Entrenador::create($data);

        return back()->with('status', "Entrenador {$entrenador->nombre} creado.");
    }

    // Eliminar entrenador (solo si no tiene clientes asignados)
    public function destroy(Entrenador $entrenador)
    {
        $clientes = $entrenador->users()->count();

        if ($clientes > 0) {
            return back()->with('error',
                "No se puede eliminar a {$entrenador->nombre}: tiene {$clientes} cliente(s) asignado(s). " .
                "Márcalo como inactivo o reasigna sus clientes primero."
            );
        }

        $nombre = $entrenador->nombre;
        $entrenador->delete();

        return back()->with('status', "Entrenador {$nombre} eliminado.");
    }

    // Editar datos del entrenador + estatus/pago desde el panel "Editar"
    public function actualizar(Request $request, Entrenador $entrenador)
    {
        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:255'],
            'username'    => ['required', 'string', 'max:255', Rule::unique('entrenadores')->ignore($entrenador->id)],
            'email'       => ['required', 'email', 'max:255', Rule::unique('entrenadores')->ignore($entrenador->id)],
            'activo'      => ['required', 'boolean'],
            'ultimo_pago' => ['nullable', 'date'],
            'vence_el'    => ['nullable', 'date'],
            'notas_pago'  => ['nullable', 'string', 'max:500'],
        ]);

        $entrenador->update($data);

        return back()->with('status', "Se actualizaron los datos de {$entrenador->nombre}.");
    }

    // Marcar activo/inactivo, o actualizar fecha de pago desde el listado
    public function actualizarEstado(Request $request, Entrenador $entrenador)
    {
        $data = $request->validate([
            'activo'      => ['sometimes', 'boolean'],
            'ultimo_pago' => ['sometimes', 'nullable', 'date'],
            'vence_el'    => ['sometimes', 'nullable', 'date'],
            'notas_pago'  => ['sometimes', 'nullable', 'string'],
        ]);

        $entrenador->update($data);

        return back()->with('status', 'Entrenador actualizado.');
    }
}