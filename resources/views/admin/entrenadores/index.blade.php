@extends('admin.layout')

@section('title', 'Entrenadores')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold">Entrenadores</h1>
    <button type="button"
            onclick="document.getElementById('nuevo-entrenador').classList.toggle('hidden')"
            class="bg-slate-900 text-white rounded-md px-4 py-2 text-sm font-medium hover:bg-slate-800">
        + Nuevo entrenador
    </button>
</div>

@if (session('status'))
    <div class="mb-4 rounded-md bg-green-50 border border-green-200 px-4 py-2 text-sm text-green-700">
        {{ session('status') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-2 text-sm text-red-700">
        {{ session('error') }}
    </div>
@endif

{{-- Formulario para crear entrenador --}}
@php $creandoConErrores = old('_form') === 'crear'; @endphp
<div id="nuevo-entrenador" class="{{ $creandoConErrores ? '' : 'hidden' }} mb-6 bg-white rounded-xl border border-slate-200 p-4">
    <h2 class="text-sm font-semibold mb-3">Nuevo entrenador</h2>

    @if($creandoConErrores && $errors->any())
        <ul class="mb-3 rounded-md bg-red-50 border border-red-200 px-4 py-2 text-xs text-red-700 list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('admin.entrenadores.store') }}" class="flex flex-wrap items-end gap-3">
        @csrf
        <input type="hidden" name="_form" value="crear">

        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs text-slate-500 mb-1">Nombre</label>
            <input type="text" name="nombre" required value="{{ $creandoConErrores ? old('nombre') : '' }}"
                   class="w-full rounded-md border border-slate-300 px-2 py-1.5 text-sm">
        </div>
        <div class="min-w-[150px]">
            <label class="block text-xs text-slate-500 mb-1">Usuario</label>
            <input type="text" name="username" required value="{{ $creandoConErrores ? old('username') : '' }}"
                   class="w-full rounded-md border border-slate-300 px-2 py-1.5 text-sm">
        </div>
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-slate-500 mb-1">Email</label>
            <input type="email" name="email" required value="{{ $creandoConErrores ? old('email') : '' }}"
                   class="w-full rounded-md border border-slate-300 px-2 py-1.5 text-sm">
        </div>
        <div class="min-w-[150px]">
            <label class="block text-xs text-slate-500 mb-1">Contraseña</label>
            <input type="password" name="password" required minlength="8"
                   class="w-full rounded-md border border-slate-300 px-2 py-1.5 text-sm">
        </div>
        <div class="min-w-[150px]">
            <label class="block text-xs text-slate-500 mb-1">Confirmar contraseña</label>
            <input type="password" name="password_confirmation" required minlength="8"
                   class="w-full rounded-md border border-slate-300 px-2 py-1.5 text-sm">
        </div>

        <button type="submit"
                class="bg-slate-900 text-white rounded-md px-4 py-1.5 text-sm font-medium hover:bg-slate-800">
            Crear
        </button>
    </form>
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
                <th class="px-4 py-3 font-medium text-center">Clientes activos</th>
                <th class="px-4 py-3 font-medium">Último pago</th>
                <th class="px-4 py-3 font-medium">Vence</th>
                <th class="px-4 py-3 font-medium">Notas</th>
                <th class="px-4 py-3 font-medium text-right">Acción</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($entrenadores as $entrenador)
                @php
                    // Si el guardado falló por validación, reabrimos el panel de este entrenador con lo que se escribió
                    $conErrores = old('entrenador_id') == $entrenador->id;
                @endphp
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium">
                        {{ $entrenador->nombre }}
                        @if($entrenador->username)
                            <div class="text-xs text-slate-400 font-normal">{{ '@' . $entrenador->username }}</div>
                        @endif
                    </td>
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
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex min-w-[2rem] justify-center px-2 py-0.5 rounded-full text-xs font-medium
                                     {{ $entrenador->clientes_activos_count > 0 ? 'bg-sky-100 text-sky-700' : 'bg-slate-100 text-slate-400' }}">
                            {{ $entrenador->clientes_activos_count }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-slate-500">{{ $entrenador->ultimo_pago?->format('d/m/Y') ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $entrenador->vence_el?->format('d/m/Y') ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-500 max-w-[200px] truncate" title="{{ $entrenador->notas_pago }}">
                        {{ $entrenador->notas_pago ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <button type="button"
                                onclick="document.getElementById('editar-{{ $entrenador->id }}').classList.toggle('hidden')"
                                class="text-slate-600 hover:text-slate-900 text-sm underline">
                            Editar
                        </button>
                        <form method="POST" action="{{ route('admin.entrenadores.destroy', $entrenador) }}" class="inline ml-3"
                              onsubmit="return confirm('¿Eliminar a {{ addslashes($entrenador->nombre) }}? Esta acción no se puede deshacer.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm underline">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
                <tr id="editar-{{ $entrenador->id }}" class="{{ $conErrores ? '' : 'hidden' }} bg-slate-50">
                    <td colspan="8" class="px-4 py-4">
                        <form method="POST" action="{{ route('admin.entrenadores.update', $entrenador) }}" class="space-y-4">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="entrenador_id" value="{{ $entrenador->id }}">

                            @if($conErrores && $errors->any())
                                <ul class="rounded-md bg-red-50 border border-red-200 px-4 py-2 text-xs text-red-700 list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif

                            {{-- Datos del entrenador --}}
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-2">Datos</p>
                                <div class="flex flex-wrap items-end gap-3">
                                    <div class="flex-1 min-w-[180px]">
                                        <label class="block text-xs text-slate-500 mb-1">Nombre</label>
                                        <input type="text" name="nombre" required
                                               value="{{ $conErrores ? old('nombre') : $entrenador->nombre }}"
                                               class="w-full rounded-md border border-slate-300 px-2 py-1.5 text-sm">
                                    </div>
                                    <div class="flex-1 min-w-[200px]">
                                        <label class="block text-xs text-slate-500 mb-1">Email</label>
                                        <input type="email" name="email" required
                                               value="{{ $conErrores ? old('email') : $entrenador->email }}"
                                               class="w-full rounded-md border border-slate-300 px-2 py-1.5 text-sm">
                                    </div>
                                    <div class="min-w-[150px]">
                                        <label class="block text-xs text-slate-500 mb-1">Usuario</label>
                                        <input type="text" name="username"
                                               value="{{ $conErrores ? old('username') : $entrenador->username }}"
                                               class="w-full rounded-md border border-slate-300 px-2 py-1.5 text-sm">
                                    </div>
                                </div>
                            </div>

                            {{-- Estatus y pago --}}
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-2">Estatus y pago</p>
                                <div class="flex flex-wrap items-end gap-3">
                                    <div>
                                        <label class="block text-xs text-slate-500 mb-1">Estatus</label>
                                        @php $activo = $conErrores ? (bool) old('activo') : $entrenador->activo; @endphp
                                        <select name="activo" class="rounded-md border border-slate-300 px-2 py-1.5 text-sm">
                                            <option value="1" @selected($activo)>Activo</option>
                                            <option value="0" @selected(!$activo)>Inactivo</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs text-slate-500 mb-1">Último pago</label>
                                        <input type="date" name="ultimo_pago"
                                               value="{{ $conErrores ? old('ultimo_pago') : $entrenador->ultimo_pago?->format('Y-m-d') }}"
                                               class="rounded-md border border-slate-300 px-2 py-1.5 text-sm">
                                    </div>

                                    <div>
                                        <label class="block text-xs text-slate-500 mb-1">Vence el</label>
                                        <input type="date" name="vence_el"
                                               value="{{ $conErrores ? old('vence_el') : $entrenador->vence_el?->format('Y-m-d') }}"
                                               class="rounded-md border border-slate-300 px-2 py-1.5 text-sm">
                                    </div>

                                    <div class="flex-1 min-w-[180px]">
                                        <label class="block text-xs text-slate-500 mb-1">Notas</label>
                                        <input type="text" name="notas_pago"
                                               value="{{ $conErrores ? old('notas_pago') : $entrenador->notas_pago }}"
                                               class="w-full rounded-md border border-slate-300 px-2 py-1.5 text-sm">
                                    </div>

                                    <button type="submit"
                                            class="bg-slate-900 text-white rounded-md px-4 py-1.5 text-sm font-medium hover:bg-slate-800">
                                        Guardar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-slate-400">No hay entrenadores que coincidan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $entrenadores->links() }}
</div>
@endsection