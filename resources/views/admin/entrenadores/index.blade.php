@extends('admin.layout')

@section('title', 'Entrenadores')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold">Entrenadores</h1>
</div>

<div class="flex flex-wrap items-center gap-2 mb-4">
    @php
        $filtros = ['' => 'Todos', 'activos' => 'Activos', 'inactivos' => 'Inactivos', 'vencidos' => 'Vencidos'];
    @endphp
    @foreach ($filtros as $valor => $etiqueta)
        <a href="{{ route('admin.dashboard', array_filter(['estado' => $valor, 'buscar' => $buscar])) }}"
           class="px-3 py-1.5 rounded-full text-sm border
                  {{ $estado === $valor || (!$estado && $valor === '') ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-300 hover:bg-slate-100' }}">
            {{ $etiqueta }}
        </a>
    @endforeach

    <form method="GET" class="ml-auto flex gap-2">
        @if($estado)
            <input type="hidden" name="estado" value="{{ $estado }}">
        @endif
        <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Buscar nombre, email o usuario..."
               class="rounded-md border border-slate-300 px-3 py-1.5 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-slate-800">
        <button type="submit" class="px-3 py-1.5 rounded-md bg-slate-200 text-sm hover:bg-slate-300">Buscar</button>
    </form>
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-slate-500 text-left">
            <tr>
                <th class="px-4 py-3 font-medium">Nombre</th>
                <th class="px-4 py-3 font-medium">Email</th>
                <th class="px-4 py-3 font-medium">Estatus</th>
                <th class="px-4 py-3 font-medium">Último pago</th>
                <th class="px-4 py-3 font-medium">Vence</th>
                <th class="px-4 py-3 font-medium">Notas</th>
                <th class="px-4 py-3 font-medium text-right">Acción</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($entrenadores as $entrenador)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium">{{ $entrenador->nombre }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $entrenador->email }}</td>
                    <td class="px-4 py-3">
                        @if($entrenador->activo)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700">Activo</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-700">Inactivo</span>
                        @endif
                        @if($entrenador->vence_el && $entrenador->vence_el->isPast())
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-amber-100 text-amber-700 ml-1">Vencido</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-slate-500">{{ $entrenador->ultimo_pago?->format('d/m/Y') ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $entrenador->vence_el?->format('d/m/Y') ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-500 max-w-[200px] truncate" title="{{ $entrenador->notas_pago }}">
                        {{ $entrenador->notas_pago ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <button type="button"
                                onclick="document.getElementById('editar-{{ $entrenador->id }}').classList.toggle('hidden')"
                                class="text-slate-600 hover:text-slate-900 text-sm underline">
                            Editar
                        </button>
                    </td>
                </tr>
                <tr id="editar-{{ $entrenador->id }}" class="hidden bg-slate-50">
                    <td colspan="7" class="px-4 py-4">
                        <form method="POST" action="{{ route('admin.entrenadores.estado', $entrenador) }}"
                              class="flex flex-wrap items-end gap-3">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label class="block text-xs text-slate-500 mb-1">Estatus</label>
                                <select name="activo" class="rounded-md border border-slate-300 px-2 py-1.5 text-sm">
                                    <option value="1" @selected($entrenador->activo)>Activo</option>
                                    <option value="0" @selected(!$entrenador->activo)>Inactivo</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs text-slate-500 mb-1">Último pago</label>
                                <input type="date" name="ultimo_pago" value="{{ $entrenador->ultimo_pago?->format('Y-m-d') }}"
                                       class="rounded-md border border-slate-300 px-2 py-1.5 text-sm">
                            </div>

                            <div>
                                <label class="block text-xs text-slate-500 mb-1">Vence el</label>
                                <input type="date" name="vence_el" value="{{ $entrenador->vence_el?->format('Y-m-d') }}"
                                       class="rounded-md border border-slate-300 px-2 py-1.5 text-sm">
                            </div>

                            <div class="flex-1 min-w-[180px]">
                                <label class="block text-xs text-slate-500 mb-1">Notas</label>
                                <input type="text" name="notas_pago" value="{{ $entrenador->notas_pago }}"
                                       class="w-full rounded-md border border-slate-300 px-2 py-1.5 text-sm">
                            </div>

                            <button type="submit"
                                    class="bg-slate-900 text-white rounded-md px-4 py-1.5 text-sm font-medium hover:bg-slate-800">
                                Guardar
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-slate-400">No hay entrenadores que coincidan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $entrenadores->links() }}
</div>
@endsection