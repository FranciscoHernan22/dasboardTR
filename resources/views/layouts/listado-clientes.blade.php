@extends('layouts.entrenador')
@section('titulo', 'Clientes')
@section('contenido')

@php
    // Paleta de colores para variar los avatares de forma determinística por cliente
    $avatarPalette = [
        ['bg-blue-50',   'text-blue-700'],
        ['bg-violet-50', 'text-violet-700'],
        ['bg-teal-50',   'text-teal-700'],
        ['bg-rose-50',   'text-rose-700'],
        ['bg-amber-50',  'text-amber-700'],
        ['bg-indigo-50', 'text-indigo-700'],
    ];
    $avatarColor = fn($id) => $avatarPalette[$id % count($avatarPalette)];

    // Clases compartidas
    $labelCls   = 'text-xs font-medium text-gray-600';
    $inputCls   = 'w-full border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-100 transition';
    $btnPrimary = 'py-2.5 bg-gray-900 hover:bg-gray-800 rounded-lg text-sm font-medium text-white transition-colors';
    $btnGhost   = 'py-2.5 rounded-lg text-sm font-medium text-gray-500 hover:bg-gray-100 transition-colors';
    $iconBtn    = 'w-8 h-8 rounded-lg text-gray-400 hover:text-gray-900 hover:bg-gray-100 flex items-center justify-center transition-colors';
    $modalWrap  = 'fixed inset-0 bg-gray-900/30 backdrop-blur-[2px] z-[10000] flex items-center justify-center p-4';
    $modalCard  = 'bg-white rounded-2xl w-full max-h-[92vh] overflow-y-auto shadow-xl';

    $totalActivos   = $clientes->where('status', 'activo')->count();
    $totalInactivos = $clientes->where('status', 'inactivo')->count();
    $sinPlanCount   = $clientes->whereNull('plan')->count();
@endphp

{{-- Header --}}
<div class="flex flex-wrap items-end justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl font-semibold text-gray-900 tracking-tight">Clientes</h1>
        <p class="text-sm text-gray-500 mt-1">
            {{ $clientes->count() }} registrados
            <span class="text-gray-300 mx-1">·</span>
            <span class="text-emerald-600">{{ $totalActivos }} activos</span>
            <span class="text-gray-300 mx-1">·</span>
            {{ $totalInactivos }} inactivos
            @if($sinPlanCount > 0)
                <span class="text-gray-300 mx-1">·</span>
                <span class="text-amber-600">{{ $sinPlanCount }} sin plan</span>
            @endif
        </p>
    </div>
    <button
        onclick="document.getElementById('modalNuevoCliente').style.display='flex'"
        class="inline-flex items-center gap-1.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium px-3.5 py-2 rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
        </svg>
        Nuevo cliente
    </button>
</div>

{{-- Flash --}}
@if(session('success'))
<div class="flex items-center gap-2 border-l-2 border-emerald-500 bg-emerald-50/60 rounded-r-lg px-4 py-2.5 mb-4">
    <span class="text-sm text-emerald-800">{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div class="flex items-center gap-2 border-l-2 border-red-500 bg-red-50/60 rounded-r-lg px-4 py-2.5 mb-4">
    <span class="text-sm text-red-800">{{ session('error') }}</span>
</div>
@endif

@if($errors->has('fecha_inicio') || $errors->has('semanas'))
<div class="border-l-2 border-red-500 bg-red-50/60 rounded-r-lg px-4 py-2.5 mb-4">
    @foreach($errors->get('fecha_inicio') as $error)
        <p class="text-sm text-red-800">{{ $error }}</p>
    @endforeach
    @foreach($errors->get('semanas') as $error)
        <p class="text-sm text-red-800">{{ $error }}</p>
    @endforeach
</div>
@endif

{{-- Buscador + filtros --}}
<div class="flex flex-wrap gap-2 mb-4">
    <div class="relative flex-1 min-w-[180px]">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
        </svg>
        <input
            id="buscadorClientes"
            type="text"
            placeholder="Buscar por nombre o correo"
            oninput="filtrarClientes()"
            class="{{ $inputCls }} pl-9 bg-white">
    </div>
    <select id="filtroEstado" onchange="filtrarClientes()"
        class="text-sm border border-gray-200 rounded-lg pl-3 pr-8 py-2 bg-white text-gray-600 focus:outline-none focus:border-gray-400 transition">
        <option value="">Todos los estados</option>
        <option value="activo">Activos</option>
        <option value="inactivo">Inactivos</option>
    </select>
    <select id="filtroPlan" onchange="filtrarClientes()"
        class="text-sm border border-gray-200 rounded-lg pl-3 pr-8 py-2 bg-white text-gray-600 focus:outline-none focus:border-gray-400 transition">
        <option value="">Todos los planes</option>
        <option value="con_plan">Con plan</option>
        <option value="sin_plan">Sin plan</option>
    </select>
</div>

{{-- ── DESKTOP: tabla ── --}}
<div class="hidden sm:block bg-white border border-gray-200 rounded-xl overflow-hidden">
    <table class="w-full border-collapse">
        <thead>
            <tr class="border-b border-gray-100">
                <th class="px-4 py-2.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wider">Cliente</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-medium text-gray-400 uppercase tracking-wider">Plan</th>
                <th class="px-4 py-2.5 text-right text-[11px] font-medium text-gray-400 uppercase tracking-wider"><span class="sr-only">Acciones</span></th>
            </tr>
        </thead>
        <tbody>
        @forelse($clientes as $cliente)
            @php
                [$avatarBg, $avatarText] = $avatarColor($cliente->id);
                $activo = $cliente->status === 'activo';
                $p = $cliente->progreso ?? null;
                $colorBarra = null; $completo = false; $semanasEnCero = 0;
                if ($cliente->plan && $p && $p['estado'] !== 'no_iniciado') {
                    $completo   = $p['estado'] === 'finalizado' && $p['porcentaje'] === 100;
                    $colorBarra = $p['estado'] === 'finalizado'
                        ? ($completo ? 'bg-emerald-500' : 'bg-amber-400')
                        : 'bg-gray-900';
                    $semanasEnCero = count($p['semanas_sin_entrenar'] ?? []);
                }
            @endphp
            <tr class="group border-b border-gray-100 last:border-b-0 hover:bg-gray-50/70 transition-colors"
                data-cliente
                data-id="{{ $cliente->id }}"
                data-nombre="{{ strtolower($cliente->name) }}"
                data-email="{{ strtolower($cliente->email) }}"
                data-estado="{{ $cliente->status }}"
                data-plan="{{ $cliente->plan ? 'con_plan' : 'sin_plan' }}">

                {{-- Cliente --}}
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="relative flex-shrink-0">
                            <div class="w-9 h-9 rounded-full {{ $avatarBg }} {{ $avatarText }} flex items-center justify-center text-xs font-semibold">
                                {{ strtoupper(substr($cliente->name, 0, 2)) }}
                            </div>
                            <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full ring-2 ring-white {{ $activo ? 'bg-emerald-500' : 'bg-gray-300' }}"
                                  title="{{ $activo ? 'Activo' : 'Inactivo' }}"></span>
                        </div>
                        <div class="min-w-0">
                            <div class="text-sm font-medium {{ $activo ? 'text-gray-900' : 'text-gray-400' }} truncate">{{ $cliente->name }}</div>
                            <div class="text-xs text-gray-400 truncate">{{ $cliente->email }}</div>
                        </div>
                    </div>
                </td>

                {{-- Plan / progreso --}}
                <td class="px-4 py-3">
                    @if(!$cliente->plan)
                        <span class="text-xs text-amber-600">Sin plan</span>

                    @elseif($p['estado'] === 'no_iniciado')
                        <span class="text-xs text-gray-400">
                            Inicia {{ $p['fecha_inicio']->translatedFormat('d M') }}
                        </span>

                    @else
                        <div class="w-48">
                            <div class="flex items-baseline justify-between mb-1.5">
                                <span class="text-xs {{ $completo ? 'text-emerald-600' : 'text-gray-600' }}">
                                    @if($p['estado'] === 'finalizado')
                                        {{ $completo ? '✓ Completado' : 'Finalizado' }}
                                    @else
                                        Semana {{ $p['semana_actual'] }} de {{ $p['semanas_total'] }}
                                    @endif
                                </span>
                                <span class="text-xs text-gray-400 tabular-nums">{{ $p['porcentaje'] ?? 0 }}%</span>
                            </div>
                            <div class="h-1 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full {{ $colorBarra }}" style="width: {{ $p['porcentaje'] ?? 0 }}%"></div>
                            </div>
                            @if($semanasEnCero > 0)
                                <p class="text-[11px] text-red-500 mt-1"
                                   title="Semana(s) {{ implode(', ', $p['semanas_sin_entrenar']) }} sin ningún día entrenado">
                                    {{ $semanasEnCero === 1 ? '1 semana' : $semanasEnCero.' semanas' }} sin entrenar
                                </p>
                            @endif
                        </div>
                    @endif
                </td>

                {{-- Acciones --}}
                <td class="px-3 py-3">
                    <div class="flex items-center justify-end gap-0.5 opacity-60 group-hover:opacity-100 transition-opacity">
                        <a href="{{ route('entrenador.rutina.menu', $cliente->id) }}" title="Rutina" class="{{ $iconBtn }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </a>
                        <a href="{{ route('entrenador.progreso.index', $cliente->id) }}" title="Progreso" class="{{ $iconBtn }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/>
                            </svg>
                        </a>
                        <a href="{{ route('entrenador.historial.anio', $cliente->id) }}" title="Historial" class="{{ $iconBtn }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v5h5"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.05 13A9 9 0 1 0 6 5.3L3 8"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l4 2"/>
                            </svg>
                        </a>
                        <button type="button"
                            onclick="abrirModalSemanas({{ $cliente->id }}, {{ $cliente->plan?->semanas ?? 4 }})"
                            title="{{ $cliente->plan ? 'Cambiar plan' : 'Crear plan' }}" class="{{ $iconBtn }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
                            </svg>
                        </button>
                        <button title="Mensajes" class="{{ $iconBtn }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                            </svg>
                        </button>
                        <button type="button"
                            onclick="abrirModalEditar({{ $cliente->id }}, @js($cliente->name), @js($cliente->email))"
                            title="Editar datos" class="{{ $iconBtn }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                            </svg>
                        </button>
                        <span class="w-px h-4 bg-gray-200 mx-1"></span>
                        <form action="{{ route('entrenador.clientes.toggleEstado', $cliente->id) }}" method="POST"
                              class="form-toggle-estado"
                              data-nombre="{{ $cliente->name }}"
                              data-accion="{{ $activo ? 'desactivar' : 'activar' }}"
                              onsubmit="return interceptarToggle(event, this)">
                            @csrf
                            @method('PATCH')
                            <button type="submit" title="{{ $activo ? 'Desactivar' : 'Activar' }}"
                                class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ $activo ? 'text-gray-400 hover:text-red-600 hover:bg-red-50' : 'text-emerald-500 hover:text-emerald-700 hover:bg-emerald-50' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.36 6.64a9 9 0 11-12.73 0M12 2v10"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="px-4 py-16 text-center">
                    <p class="text-sm text-gray-500">Todavía no tienes clientes</p>
                    <p class="text-xs text-gray-400 mt-1">Registra el primero con “Nuevo cliente”.</p>
                </td>
            </tr>
        @endforelse
        <tr id="filaSinResultados" class="hidden">
            <td colspan="3" class="px-4 py-16 text-center">
                <p class="text-sm text-gray-500">Ningún cliente coincide con los filtros</p>
            </td>
        </tr>
        </tbody>
    </table>
</div>

{{-- ── MÓVIL: swipe cards ── --}}
<div class="sm:hidden bg-white border border-gray-200 rounded-xl overflow-hidden divide-y divide-gray-100">
@forelse($clientes as $cliente)
@php
    [$avatarBg, $avatarText] = $avatarColor($cliente->id);
    $activo = $cliente->status === 'activo';
    $p = $cliente->progreso ?? null;
    // Se reinician en cada vuelta del bucle para no arrastrar valores del cliente anterior
    $colorBarra = null; $completo = false; $semanasEnCero = 0;

    if ($cliente->plan && $p && $p['estado'] !== 'no_iniciado') {
        $completo   = $p['estado'] === 'finalizado' && $p['porcentaje'] === 100;
        $colorBarra = $p['estado'] === 'finalizado'
            ? ($completo ? 'bg-emerald-500' : 'bg-amber-400')
            : 'bg-gray-900';
        $semanasEnCero = count($p['semanas_sin_entrenar'] ?? []);
    }
    $swipeBtn = 'w-[72px] flex flex-col items-center justify-center gap-1 text-[11px] font-medium';
@endphp
<div class="swipe-wrapper relative overflow-hidden"
     style="height:76px;"
     data-id="{{ $cliente->id }}"
     data-nombre="{{ strtolower($cliente->name) }}"
     data-email="{{ strtolower($cliente->email) }}"
     data-estado="{{ $cliente->status }}"
     data-plan="{{ $cliente->plan ? 'con_plan' : 'sin_plan' }}">

    {{-- Acciones IZQUIERDA (se revelan deslizando hacia la derecha →) --}}
    <div class="swipe-actions-left absolute inset-y-0 left-0 flex items-stretch bg-gray-900 divide-x divide-white/10">
        <a href="{{ route('entrenador.rutina.menu', $cliente->id) }}" class="{{ $swipeBtn }} text-white active:bg-gray-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Rutina
        </a>
        <a href="{{ route('entrenador.progreso.index', $cliente->id) }}" class="{{ $swipeBtn }} text-white active:bg-gray-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/>
            </svg>
            Progreso
        </a>
        <a href="{{ route('entrenador.historial.anio', $cliente->id) }}" class="{{ $swipeBtn }} text-white active:bg-gray-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v5h5"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.05 13A9 9 0 1 0 6 5.3L3 8"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l4 2"/>
            </svg>
            Historial
        </a>
    </div>

    {{-- Acciones DERECHA (se revelan deslizando hacia la izquierda ←) --}}
    <div class="swipe-actions-right absolute inset-y-0 right-0 flex items-stretch bg-gray-50 divide-x divide-gray-200/70">
        <button type="button"
            onclick="abrirModalSemanas({{ $cliente->id }}, {{ $cliente->plan?->semanas ?? 4 }})"
            class="{{ $swipeBtn }} text-gray-700 active:bg-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
            </svg>
            Plan
        </button>
        <button type="button"
            onclick="abrirModalEditar({{ $cliente->id }}, @js($cliente->name), @js($cliente->email))"
            class="{{ $swipeBtn }} text-gray-700 active:bg-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
            </svg>
            Editar
        </button>
        <form action="{{ route('entrenador.clientes.toggleEstado', $cliente->id) }}" method="POST"
              class="form-toggle-estado flex"
              data-nombre="{{ $cliente->name }}"
              data-accion="{{ $activo ? 'desactivar' : 'activar' }}"
              onsubmit="return interceptarToggle(event, this)">
            @csrf
            @method('PATCH')
            <button type="submit"
                class="{{ $swipeBtn }} h-full {{ $activo ? 'text-red-600 bg-red-50 active:bg-red-100' : 'text-emerald-700 bg-emerald-50 active:bg-emerald-100' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.36 6.64a9 9 0 11-12.73 0M12 2v10"/>
                </svg>
                {{ $activo ? 'Desactivar' : 'Activar' }}
            </button>
        </form>
    </div>

    {{-- Card del cliente (la que se desliza) --}}
    <div class="swipe-card absolute inset-0 flex items-center gap-3 px-4 bg-white active:bg-gray-50"
         style="transition: transform 0.22s ease; will-change: transform; touch-action: pan-y;">

        <div class="relative flex-shrink-0">
            <div class="w-10 h-10 rounded-full {{ $avatarBg }} {{ $avatarText }} flex items-center justify-center text-[13px] font-semibold tracking-wide">
                {{ strtoupper(substr($cliente->name, 0, 2)) }}
            </div>
            <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full ring-2 ring-white {{ $activo ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>
        </div>

        <div class="flex-1 min-w-0">
            <div class="flex items-baseline justify-between gap-2">
                <span class="text-sm font-medium {{ $activo ? 'text-gray-900' : 'text-gray-400' }} truncate">{{ $cliente->name }}</span>
                @if(!$cliente->plan)
                    <span class="text-[11px] text-amber-600 whitespace-nowrap">Sin plan</span>
                @elseif($p['estado'] === 'no_iniciado')
                    <span class="text-[11px] text-gray-400 whitespace-nowrap">Inicia {{ $p['fecha_inicio']->translatedFormat('d M') }}</span>
                @elseif($p['estado'] === 'finalizado')
                    <span class="text-[11px] whitespace-nowrap {{ $completo ? 'text-emerald-600' : 'text-gray-500' }}">
                        {{ $completo ? '✓ Completado' : 'Finalizó' }}
                    </span>
                @else
                    <span class="text-[11px] text-gray-500 whitespace-nowrap tabular-nums">Sem {{ $p['semana_actual'] }}/{{ $p['semanas_total'] }}</span>
                @endif
            </div>

            <div class="text-xs text-gray-400 truncate">{{ $cliente->email }}</div>

            @if($colorBarra)
                <div class="flex items-center gap-2 mt-1.5">
                    <div class="flex-1 h-1 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full {{ $colorBarra }}" style="width: {{ $p['porcentaje'] ?? 0 }}%"></div>
                    </div>
                    <span class="text-[10px] text-gray-400 tabular-nums w-7 text-right">{{ $p['porcentaje'] ?? 0 }}%</span>
                    @if($semanasEnCero > 0)
                        <span class="text-[10px] text-red-500 whitespace-nowrap">{{ $semanasEnCero }} sem. en 0</span>
                    @endif
                </div>
            @endif
        </div>

        <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7l-5 5 5 5M16 7l5 5-5 5"/>
        </svg>
    </div>
</div>
@empty
    <div class="px-4 py-16 text-center">
        <p class="text-sm text-gray-500">Todavía no tienes clientes</p>
        <p class="text-xs text-gray-400 mt-1">Registra el primero con “Nuevo cliente”.</p>
    </div>
@endforelse
<div id="mensajeSinResultadosMovil" class="hidden px-4 py-16 text-center">
    <p class="text-sm text-gray-500">Ningún cliente coincide con los filtros</p>
</div>
</div>

{{-- Ayuda del swipe (solo móvil) --}}
<p class="sm:hidden flex items-center justify-center gap-3 text-[11px] text-gray-400 mt-3">
    <span>→ Rutina · Progreso · Historial</span>
    <span class="text-gray-300">|</span>
    <span>← Plan · Editar · Estado</span>
</p>

{{-- ── Modal nuevo cliente ── --}}
<div id="modalNuevoCliente"
    onclick="if(event.target===this)this.style.display='none'"
    style="display:none;"
    class="{{ $modalWrap }}">
    <div class="{{ $modalCard }} max-w-md">
        <div class="flex items-start justify-between px-6 pt-6 pb-2">
            <div>
                <h3 class="text-base font-semibold text-gray-900">Nuevo cliente</h3>
                <p class="text-sm text-gray-500 mt-0.5">Completa los datos para registrarlo.</p>
            </div>
            <button onclick="document.getElementById('modalNuevoCliente').style.display='none'"
                class="w-8 h-8 -mr-2 -mt-1 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 flex items-center justify-center transition-colors" aria-label="Cerrar">✕</button>
        </div>
        <form method="POST" action="{{ route('entrenador.clientes.store') }}" class="px-6 pb-6 pt-4 flex flex-col gap-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div class="flex flex-col gap-1.5">
                    <label class="{{ $labelCls }}">Nombre</label>
                    <input name="name" type="text" required placeholder="Juan" class="{{ $inputCls }}" value="{{ old('name') }}">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label class="{{ $labelCls }}">Apellido</label>
                    <input name="apellido" type="text" required placeholder="García" class="{{ $inputCls }}" value="{{ old('apellido') }}">
                </div>
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="{{ $labelCls }}">Correo electrónico</label>
                <input name="email" type="email" required placeholder="cliente@email.com" class="{{ $inputCls }}" value="{{ old('email') }}">
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="{{ $labelCls }}">Contraseña temporal</label>
                <input name="password" type="password" required placeholder="Mínimo 6 caracteres" class="{{ $inputCls }}">
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="{{ $labelCls }}">Teléfono <span class="text-gray-400 font-normal">· opcional</span></label>
                <input name="telefono" type="tel" placeholder="+52 664 123 4567" class="{{ $inputCls }}" value="{{ old('telefono') }}">
            </div>
            @if($errors->any())
            <div class="border-l-2 border-red-500 bg-red-50/60 rounded-r-lg px-3 py-2">
                @foreach($errors->all() as $error)
                    <p class="text-xs text-red-700">{{ $error }}</p>
                @endforeach
            </div>
            @endif
            <div class="flex gap-2 pt-2">
                <button type="button"
                    onclick="document.getElementById('modalNuevoCliente').style.display='none'"
                    class="flex-1 {{ $btnGhost }}">Cancelar</button>
                <button type="submit" class="flex-[2] {{ $btnPrimary }}">Registrar cliente</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Modal editar cliente ── --}}
<div id="modalEditarCliente"
    onclick="if(event.target===this)cerrarModalEditar()"
    style="display:none;"
    class="{{ $modalWrap }}">
    <div class="{{ $modalCard }} max-w-md">
        <div class="flex items-start justify-between px-6 pt-6 pb-2">
            <div>
                <h3 class="text-base font-semibold text-gray-900">Editar cliente</h3>
                <p class="text-sm text-gray-500 mt-0.5">Actualiza sus datos.</p>
            </div>
            <button onclick="cerrarModalEditar()"
                class="w-8 h-8 -mr-2 -mt-1 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 flex items-center justify-center transition-colors" aria-label="Cerrar">✕</button>
        </div>
        <form id="formEditarCliente" method="POST" action="#" class="px-6 pb-6 pt-4 flex flex-col gap-4">
            @csrf
            @method('PUT')
            <input type="hidden" id="editClienteIdHidden" name="cliente_id_edit" value="{{ old('cliente_id_edit') }}">
            <div class="flex flex-col gap-1.5">
                <label class="{{ $labelCls }}">Nombre completo</label>
                <input name="name" id="editNombre" type="text" required class="{{ $inputCls }}" value="{{ old('name') }}">
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="{{ $labelCls }}">Correo electrónico</label>
                <input name="email" id="editEmail" type="email" required class="{{ $inputCls }}" value="{{ old('email') }}">
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="{{ $labelCls }}">Nueva contraseña <span class="text-gray-400 font-normal">· opcional</span></label>
                <input name="password" type="password" placeholder="Déjala en blanco para no cambiarla" class="{{ $inputCls }}">
            </div>
            @if($errors->editarCliente->any())
            <div class="border-l-2 border-red-500 bg-red-50/60 rounded-r-lg px-3 py-2">
                @foreach($errors->editarCliente->all() as $error)
                    <p class="text-xs text-red-700">{{ $error }}</p>
                @endforeach
            </div>
            @endif
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="cerrarModalEditar()" class="flex-1 {{ $btnGhost }}">Cancelar</button>
                <button type="submit" class="flex-[2] {{ $btnPrimary }}">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Modal semanas / plan ── --}}
<div id="modalSemanas"
    onclick="if(event.target===this)cerrarModalSemanas()"
    style="display:none;"
    class="{{ $modalWrap }}">
    <div class="{{ $modalCard }} max-w-sm p-6">
        <h3 class="text-base font-semibold text-gray-900">Plan de entrenamiento</h3>
        <p class="text-sm text-gray-500 mt-0.5 mb-5">Elige la duración y la fecha de inicio.</p>

        <label class="{{ $labelCls }} block mb-2">Semanas</label>
        <div class="grid grid-cols-8 gap-1.5 mb-5">
            @foreach(range(1, 16) as $s)
            <button type="button"
                onclick="seleccionarSemanas({{ $s }})"
                id="btn-semana-{{ $s }}"
                class="semana-btn aspect-square border border-gray-200 rounded-lg text-sm font-medium text-gray-600 hover:border-gray-400 transition-colors tabular-nums">
                {{ $s }}
            </button>
            @endforeach
        </div>

        <div class="flex flex-col gap-1.5 mb-6">
            <label for="inputFechaInicioPlan" class="{{ $labelCls }}">Inicio (semana 1 · día 1)</label>
            <input type="date" id="inputFechaInicioPlan" class="{{ $inputCls }}">
            <p class="text-xs text-gray-400">No puede ser anterior a hoy. Si el cliente ya tenía un plan, este lo reemplaza por completo.</p>
        </div>

        <div class="flex gap-2">
            <button type="button" onclick="cerrarModalSemanas()" class="flex-1 {{ $btnGhost }}">Cancelar</button>
            <button type="button" id="btnConfirmarPlan" onclick="confirmarPlan()"
                class="flex-[2] {{ $btnPrimary }} disabled:opacity-40 disabled:cursor-not-allowed">
                Guardar plan
            </button>
        </div>
    </div>
</div>

<form id="formPlan" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="semanas" id="inputSemanas">
    <input type="hidden" name="fecha_inicio" id="inputFechaInicioHidden">
</form>

{{-- ── Modal confirmar activar/desactivar ── --}}
<div id="modalConfirmarEstado"
    onclick="if(event.target===this)cerrarModalConfirmar()"
    style="display:none;"
    class="{{ $modalWrap }}">
    <div class="{{ $modalCard }} max-w-xs p-6 text-center">

        <div id="iconoDesactivar" class="w-11 h-11 rounded-full flex items-center justify-center mx-auto mb-4 bg-red-50 text-red-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18.36 6.64a9 9 0 11-12.73 0M12 2v10"/>
            </svg>
        </div>
        <div id="iconoActivar" class="w-11 h-11 rounded-full flex items-center justify-center mx-auto mb-4 bg-emerald-50 text-emerald-600 hidden">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18.36 6.64a9 9 0 11-12.73 0M12 2v10"/>
            </svg>
        </div>

        <h3 id="tituloConfirmarEstado" class="text-base font-semibold text-gray-900 mb-1.5">¿Cambiar estado?</h3>
        <p id="textoConfirmarEstado" class="text-sm text-gray-500 mb-6 leading-relaxed"></p>

        <div class="flex gap-2">
            <button type="button" onclick="cerrarModalConfirmar()" class="flex-1 {{ $btnGhost }}">Cancelar</button>
            <button type="button" id="btnDesactivar" onclick="ejecutarAccionConfirmada()"
                class="flex-[2] py-2.5 rounded-lg text-sm font-medium text-white transition-colors bg-red-600 hover:bg-red-700">
                Desactivar
            </button>
            <button type="button" id="btnActivar" onclick="ejecutarAccionConfirmada()"
                class="flex-[2] py-2.5 rounded-lg text-sm font-medium text-white transition-colors bg-emerald-600 hover:bg-emerald-700 hidden">
                Activar
            </button>
        </div>
    </div>
</div>

{{-- ── Modal sin plan ── --}}
@if(session('sin_plan_cliente'))
<div id="modalSinPlan"
    onclick="if(event.target===this)this.style.display='none'"
    class="{{ $modalWrap }}">
    <div class="{{ $modalCard }} max-w-sm p-6 text-center">
        <div class="w-11 h-11 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
            </svg>
        </div>
        <h3 class="text-base font-semibold text-gray-900 mb-1.5">Sin rutina asignada</h3>
        <p class="text-sm text-gray-500 mb-6 leading-relaxed">
            <span class="text-gray-900 font-medium">{{ session('sin_plan_cliente') }}</span>
            todavía no tiene un plan. Asígnale uno desde el botón de <span class="text-gray-900 font-medium">Plan</span>.
        </p>
        <button onclick="document.getElementById('modalSinPlan').style.display='none'" class="w-full {{ $btnPrimary }}">
            Entendido
        </button>
    </div>
</div>
@endif

<script>
// ── Swipe en ambos lados ──────────────────────────────
//  → derecha: revela Rutina / Progreso / Historial
//  ← izquierda: revela Plan / Editar / Desactivar
const SWIPE_UMBRAL = 40; // px que hay que arrastrar para abrir/cerrar

function cerrarTodosLosSwipes(excepto) {
    document.querySelectorAll('.swipe-wrapper').forEach(w => {
        if (w !== excepto && w._pos !== 0 && w._snap) w._snap(0);
    });
}

document.querySelectorAll('.swipe-wrapper').forEach(wrapper => {
    const card  = wrapper.querySelector('.swipe-card');
    const left  = wrapper.querySelector('.swipe-actions-left');
    const right = wrapper.querySelector('.swipe-actions-right');
    const anchoIzq = () => left?.offsetWidth  ?? 0;
    const anchoDer = () => right?.offsetWidth ?? 0;

    // _pos:  1 = acciones izquierdas abiertas · -1 = derechas abiertas · 0 = cerrada
    wrapper._pos = 0;
    let startX = 0, startY = 0, dx = 0, base = 0, eje = null;

    function moverA(x, animado) {
        card.style.transition = animado ? 'transform 0.22s ease' : 'none';
        card.style.transform  = `translateX(${x}px)`;
    }
    function snap(pos) {
        wrapper._pos = pos;
        moverA(pos === 1 ? anchoIzq() : pos === -1 ? -anchoDer() : 0, true);
    }
    wrapper._snap  = snap;
    wrapper._mover = moverA;

    card.addEventListener('touchstart', e => {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
        dx = 0; eje = null;
        base = wrapper._pos === 1 ? anchoIzq() : wrapper._pos === -1 ? -anchoDer() : 0;
    }, { passive: true });

    card.addEventListener('touchmove', e => {
        const mx = e.touches[0].clientX - startX;
        const my = e.touches[0].clientY - startY;
        // Decide si el gesto es horizontal (swipe) o vertical (scroll de la página)
        if (!eje && (Math.abs(mx) > 6 || Math.abs(my) > 6)) {
            eje = Math.abs(mx) > Math.abs(my) ? 'x' : 'y';
        }
        if (eje !== 'x') return;
        dx = mx;
        const x = Math.max(-anchoDer(), Math.min(anchoIzq(), base + dx));
        moverA(x, false);
    }, { passive: true });

    card.addEventListener('touchend', () => {
        if (eje !== 'x') return;
        let destino = wrapper._pos;
        if (wrapper._pos === 0) {
            if (dx >  SWIPE_UMBRAL) destino = 1;
            if (dx < -SWIPE_UMBRAL) destino = -1;
        } else if (wrapper._pos === 1  && dx < -SWIPE_UMBRAL) {
            destino = 0;
        } else if (wrapper._pos === -1 && dx >  SWIPE_UMBRAL) {
            destino = 0;
        }
        if (destino !== 0) cerrarTodosLosSwipes(wrapper);
        snap(destino);
    });

    // Tocar la tarjeta abierta la cierra
    card.addEventListener('click', () => { if (wrapper._pos !== 0) snap(0); });
});

// Tocar fuera de una tarjeta abierta la cierra
document.addEventListener('touchstart', e => {
    if (!e.target.closest('.swipe-wrapper')) cerrarTodosLosSwipes(null);
}, { passive: true });

// Pista la primera vez: la primera tarjeta se asoma a ambos lados
document.addEventListener('DOMContentLoaded', () => {
    const PISTA_KEY = 'clientes_swipe_pista_v2';
    try { if (localStorage.getItem(PISTA_KEY)) return; } catch (e) { return; }
    const primero = [...document.querySelectorAll('.swipe-wrapper')].find(w => w.offsetParent !== null);
    if (!primero) return;
    setTimeout(() => primero._mover(56, true), 600);
    setTimeout(() => primero._mover(-56, true), 1300);
    setTimeout(() => primero._mover(0, true), 2000);
    try { localStorage.setItem(PISTA_KEY, '1'); } catch (e) {}
});

// ── Modal semanas / plan ─────────────────────────────────
let _clienteIdPendiente   = null;
let _semanasSeleccionadas = null;

function hoyISO() {
    const d = new Date();
    const tz = d.getTimezoneOffset() * 60000;
    return new Date(d - tz).toISOString().slice(0, 10);
}

// Resalta el botón de semanas elegido
const SEMANA_ON  = ['bg-gray-900', 'border-gray-900', 'text-white'];
const SEMANA_OFF = ['border-gray-200', 'text-gray-600'];
function marcarSemana(semanas) {
    document.querySelectorAll('.semana-btn').forEach((btn, i) => {
        const activo = (i + 1) === semanas;
        btn.classList.remove(...(activo ? SEMANA_OFF : SEMANA_ON));
        btn.classList.add(...(activo ? SEMANA_ON : SEMANA_OFF));
    });
}

function abrirModalSemanas(clienteId, semanaActual) {
    _clienteIdPendiente = clienteId;
    _semanasSeleccionadas = semanaActual || null;

    marcarSemana(semanaActual);

    const inputFecha = document.getElementById('inputFechaInicioPlan');
    const hoy = hoyISO();
    inputFecha.min = hoy;
    inputFecha.value = hoy;

    document.getElementById('modalSemanas').style.display = 'flex';
}

function cerrarModalSemanas() {
    document.getElementById('modalSemanas').style.display = 'none';
    _clienteIdPendiente   = null;
    _semanasSeleccionadas = null;
}

function seleccionarSemanas(semanas) {
    _semanasSeleccionadas = semanas;
    marcarSemana(semanas);
}

function confirmarPlan() {
    if (!_clienteIdPendiente) return;

    if (!_semanasSeleccionadas) {
        alert('Selecciona cuántas semanas durará el plan.');
        return;
    }

    const fecha = document.getElementById('inputFechaInicioPlan').value;
    if (!fecha) {
        alert('Selecciona la fecha de inicio del plan.');
        return;
    }
    if (fecha < hoyISO()) {
        alert('La fecha de inicio no puede ser anterior a hoy.');
        return;
    }

    const form = document.getElementById('formPlan');
    form.action = '/entrenador/plan/' + _clienteIdPendiente;
    document.getElementById('inputSemanas').value = _semanasSeleccionadas;
    document.getElementById('inputFechaInicioHidden').value = fecha;
    form.submit();
}

// ── Modal editar cliente ─────────────────────────────────
function abrirModalEditar(id, nombre, email) {
    const form = document.getElementById('formEditarCliente');
    form.action = '/entrenador/clientes/' + id;
    document.getElementById('editClienteIdHidden').value = id;
    document.getElementById('editNombre').value = nombre;
    document.getElementById('editEmail').value = email;
    form.querySelector('input[name="password"]').value = '';
    document.getElementById('modalEditarCliente').style.display = 'flex';
}

function cerrarModalEditar() {
    document.getElementById('modalEditarCliente').style.display = 'none';
}

// ── Confirmación activar/desactivar ─────────────────────
let _accionConfirmada = null;

function interceptarToggle(event, form) {
    event.preventDefault();

    const nombre = form.dataset.nombre;
    const accion = form.dataset.accion; // 'activar' | 'desactivar'
    const esActivar = accion === 'activar';

    document.getElementById('tituloConfirmarEstado').textContent =
        esActivar ? 'Activar cliente' : 'Desactivar cliente';
    // Se arma con nodos de texto para que un nombre con "<" o "&" no rompa el HTML
    const p = document.getElementById('textoConfirmarEstado');
    const fuerte = (t) => { const b = document.createElement('span'); b.className = 'text-gray-900 font-medium'; b.textContent = t; return b; };
    p.replaceChildren('¿Seguro que deseas ', fuerte(accion), ' a ', fuerte(nombre), '?');

    document.getElementById('iconoActivar').classList.toggle('hidden', !esActivar);
    document.getElementById('iconoDesactivar').classList.toggle('hidden', esActivar);
    document.getElementById('btnActivar').classList.toggle('hidden', !esActivar);
    document.getElementById('btnDesactivar').classList.toggle('hidden', esActivar);

    _accionConfirmada = () => form.submit();

    document.getElementById('modalConfirmarEstado').style.display = 'flex';
    return false;
}

function cerrarModalConfirmar() {
    document.getElementById('modalConfirmarEstado').style.display = 'none';
    _accionConfirmada = null;
}

function ejecutarAccionConfirmada() {
    if (_accionConfirmada) {
        _accionConfirmada();
    }
    cerrarModalConfirmar();
}

@if($errors->any())
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('modalNuevoCliente').style.display = 'flex';
});
@endif

@if($errors->editarCliente->any())
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formEditarCliente');
    form.action = '/entrenador/clientes/' + document.getElementById('editClienteIdHidden').value;
    document.getElementById('modalEditarCliente').style.display = 'flex';
});
@endif

@if($errors->has('fecha_inicio') || $errors->has('semanas'))
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('modalSemanas').style.display = 'flex';
});
@endif

// ── Filtros + persistencia ──────────────────────────────
const FILTROS_STORAGE_KEY = 'clientes_filtros';

function guardarFiltros(texto, estado, plan) {
    try {
        localStorage.setItem(FILTROS_STORAGE_KEY, JSON.stringify({ texto, estado, plan }));
    } catch (e) {
        // localStorage no disponible (modo privado, etc.) — se ignora silenciosamente
    }
}

function cargarFiltros() {
    try {
        const guardado = localStorage.getItem(FILTROS_STORAGE_KEY);
        return guardado ? JSON.parse(guardado) : null;
    } catch (e) {
        return null;
    }
}

function filtrarClientes() {
    const texto  = document.getElementById('buscadorClientes').value.toLowerCase().trim();
    const estado = document.getElementById('filtroEstado').value;
    const plan   = document.getElementById('filtroPlan').value;

    guardarFiltros(texto, estado, plan);

    // Desktop
    let visiblesDesktop = 0;
    document.querySelectorAll('tbody tr[data-cliente]').forEach(row => {
        const nombre = row.dataset.nombre?.toLowerCase() ?? '';
        const email  = row.dataset.email?.toLowerCase()  ?? '';
        const est    = row.dataset.estado ?? '';
        const pln    = row.dataset.plan ?? '';
        const coincideTexto  = nombre.includes(texto) || email.includes(texto);
        const coincideEstado = !estado || est === estado;
        const coincidePlan   = !plan || pln === plan;
        const visible = coincideTexto && coincideEstado && coincidePlan;
        row.style.display = visible ? '' : 'none';
        if (visible) visiblesDesktop++;
    });
    const filaSinResultados = document.getElementById('filaSinResultados');
    if (filaSinResultados) {
        filaSinResultados.classList.toggle('hidden', visiblesDesktop !== 0 || document.querySelectorAll('tbody tr[data-cliente]').length === 0);
    }

    // Móvil
    let visiblesMovil = 0;
    document.querySelectorAll('.swipe-wrapper').forEach(wrapper => {
        const nombre = wrapper.dataset.nombre?.toLowerCase() ?? '';
        const email  = wrapper.dataset.email?.toLowerCase()  ?? '';
        const est    = wrapper.dataset.estado ?? '';
        const pln    = wrapper.dataset.plan ?? '';
        const coincideTexto  = nombre.includes(texto) || email.includes(texto);
        const coincideEstado = !estado || est === estado;
        const coincidePlan   = !plan || pln === plan;
        const visible = coincideTexto && coincideEstado && coincidePlan;
        wrapper.style.display = visible ? '' : 'none';
        if (visible) visiblesMovil++;
    });
    const mensajeSinResultadosMovil = document.getElementById('mensajeSinResultadosMovil');
    if (mensajeSinResultadosMovil) {
        mensajeSinResultadosMovil.classList.toggle('hidden', visiblesMovil !== 0 || document.querySelectorAll('.swipe-wrapper').length === 0);
    }
}

// Restaurar filtros guardados al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    const filtros = cargarFiltros();
    if (filtros) {
        document.getElementById('buscadorClientes').value = filtros.texto || '';
        document.getElementById('filtroEstado').value = filtros.estado || '';
        document.getElementById('filtroPlan').value = filtros.plan || '';
    }
    filtrarClientes();
});
</script>

@endsection