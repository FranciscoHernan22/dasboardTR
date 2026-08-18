<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entrenador;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Entrenador::query()->orderBy('nombre');

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