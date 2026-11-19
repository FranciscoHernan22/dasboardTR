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
    $inputCls   = 'w-full border border-gray-200 rounded-lg px-3 py-2.5 sm:py-2 text-base sm:text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-100 transition';
    $btnPrimary = 'py-2.5 bg-gray-900 hover:bg-gray-800 rounded-lg text-sm font-medium text-white transition-colors';
    $btnGhost   = 'py-2.5 rounded-lg text-sm font-medium text-gray-500 hover:bg-gray-100 transition-colors';
    $iconBtn    = 'w-8 h-8 rounded-lg text-gray-400 hover:text-gray-900 hover:bg-gray-100 flex items-center justify-center transition-colors';
    $modalWrap  = 'fixed inset-0 bg-gray-900/30 backdrop-blur-[2px] z-[10000] flex items-end sm:items-center justify-center sm:p-4';
    $modalCard  = 'bg-white rounded-t-3xl sm:rounded-2xl w-full max-h-[92vh] overflow-y-auto shadow-xl pb-[env(safe-area-inset-bottom)]';

    $totalActivos   = $clientes->where('status', 'activo')->count();
    $totalInactivos = $clientes->where('status', 'inactivo')->count();
    $sinPlanCount   = $clientes->whereNull('plan')->count();
@endphp

{{-- Header --}}
<div class="flex flex-wrap items-end justify-between gap-3 mb-4 sm:mb-6">
    <div>
        <h1 class="text-2xl sm:text-xl font-semibold text-gray-900 tracking-tight">Clientes</h1>
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
        class="hidden sm:inline-flex items-center gap-1.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium px-3.5 py-2 rounded-lg transition-colors">
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
<div class="flex flex-wrap gap-2 mb-3 sm:mb-4">
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
        class="hidden sm:block text-sm border border-gray-200 rounded-lg pl-3 pr-8 py-2 bg-white text-gray-600 focus:outline-none focus:border-gray-400 transition">
        <option value="">Todos los estados</option>
        <option value="activo">Activos</option>
        <option value="inactivo">Inactivos</option>
    </select>
    <select id="filtroPlan" onchange="filtrarClientes()"
        class="hidden sm:block text-sm border border-gray-200 rounded-lg pl-3 pr-8 py-2 bg-white text-gray-600 focus:outline-none focus:border-gray-400 transition">
        <option value="">Todos los planes</option>
        <option value="con_plan">Con plan</option>
        <option value="sin_plan">Sin plan</option>
    </select>
</div>

{{-- Filtros rápidos (solo móvil) --}}
<div class="sm:hidden flex gap-2 overflow-x-auto -mx-1 px-1 pb-1 mb-3" style="scrollbar-width:none;">
    @foreach([
        'todos'    => ['Todos',     $clientes->count()],
        'activo'   => ['Activos',   $totalActivos],
        'inactivo' => ['Inactivos', $totalInactivos],
        'sin_plan' => ['Sin plan',  $sinPlanCount],
    ] as $clave => [$texto, $num])
    <button type="button" data-chip="{{ $clave }}" onclick="filtroChip('{{ $clave }}')"
        class="chip-filtro flex-shrink-0 inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full border text-sm transition-colors">
        {{ $texto }} <span class="chip-num tabular-nums">{{ $num }}</span>
    </button>
    @endforeach
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

{{-- ── MÓVIL: lista de tarjetas + menú inferior ── --}}
<div class="sm:hidden flex flex-col gap-2 pb-24">
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
@endphp
<div class="cliente-movil"
     data-id="{{ $cliente->id }}"
     data-nombre="{{ strtolower($cliente->name) }}"
     data-email="{{ strtolower($cliente->email) }}"
     data-estado="{{ $cliente->status }}"
     data-plan="{{ $cliente->plan ? 'con_plan' : 'sin_plan' }}"
     data-nombre-real="{{ $cliente->name }}"
     data-email-real="{{ $cliente->email }}"
     data-iniciales="{{ strtoupper(substr($cliente->name, 0, 2)) }}"
     data-avatar="{{ $avatarBg }} {{ $avatarText }}"
     data-semanas="{{ $cliente->plan?->semanas ?? 4 }}"
     data-tiene-plan="{{ $cliente->plan ? '1' : '0' }}"
     data-url-rutina="{{ route('entrenador.rutina.menu', $cliente->id) }}"
     data-url-progreso="{{ route('entrenador.progreso.index', $cliente->id) }}"
     data-url-historial="{{ route('entrenador.historial.anio', $cliente->id) }}"
     data-url-toggle="{{ route('entrenador.clientes.toggleEstado', $cliente->id) }}">

    <button type="button" onclick="abrirSheet(this.parentElement)"
        class="w-full flex items-center gap-3 px-4 py-3.5 bg-white border border-gray-200/80 rounded-2xl text-left active:scale-[0.99] active:bg-gray-50 transition">

        <div class="relative flex-shrink-0">
            <div class="w-11 h-11 rounded-full {{ $avatarBg }} {{ $avatarText }} flex items-center justify-center text-sm font-semibold">
                {{ strtoupper(substr($cliente->name, 0, 2)) }}
            </div>
            <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full ring-2 ring-white {{ $activo ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>
        </div>

        <div class="flex-1 min-w-0">
            <p class="text-[15px] font-medium leading-tight truncate {{ $activo ? 'text-gray-900' : 'text-gray-400' }}">{{ $cliente->name }}</p>

            @if(!$cliente->plan)
                <p class="text-[13px] text-amber-600 mt-0.5">Sin plan asignado</p>
            @elseif($p['estado'] === 'no_iniciado')
                <p class="text-[13px] text-gray-500 mt-0.5">Inicia el {{ $p['fecha_inicio']->translatedFormat('d \d\e F') }}</p>
            @else
                <div class="flex items-center gap-2 mt-1.5">
                    <div class="flex-1 h-1 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full {{ $colorBarra }}" style="width: {{ $p['porcentaje'] ?? 0 }}%"></div>
                    </div>
                    <span class="text-xs tabular-nums whitespace-nowrap {{ $completo ? 'text-emerald-600' : 'text-gray-500' }}">
                        @if($p['estado'] === 'finalizado')
                            {{ $completo ? '✓ Completado' : 'Terminó · '.$p['porcentaje'].'%' }}
                        @else
                            Sem {{ $p['semana_actual'] }}/{{ $p['semanas_total'] }}
                        @endif
                    </span>
                </div>
                @if($semanasEnCero > 0)
                    <p class="text-xs text-red-500 mt-1">
                        {{ $semanasEnCero === 1 ? '1 semana' : $semanasEnCero.' semanas' }} sin entrenar
                    </p>
                @endif
            @endif
        </div>

        <svg class="w-5 h-5 text-gray-300 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
            <circle cx="5" cy="12" r="1.6"/><circle cx="12" cy="12" r="1.6"/><circle cx="19" cy="12" r="1.6"/>
        </svg>
    </button>
</div>
@empty
    <div class="px-4 py-16 text-center bg-white border border-gray-200/80 rounded-2xl">
        <p class="text-sm text-gray-500">Todavía no tienes clientes</p>
        <p class="text-xs text-gray-400 mt-1">Toca el botón + para registrar el primero.</p>
    </div>
@endforelse
<div id="mensajeSinResultadosMovil" class="hidden px-4 py-16 text-center">
    <p class="text-sm text-gray-500">Ningún cliente coincide con la búsqueda</p>
</div>
</div>

{{-- Botón flotante: nuevo cliente (solo móvil) --}}
<button type="button"
    onclick="document.getElementById('modalNuevoCliente').style.display='flex'"
    class="sm:hidden fixed right-5 z-40 w-14 h-14 rounded-full bg-gray-900 text-white shadow-lg shadow-gray-900/20 flex items-center justify-center active:scale-95 transition"
    style="bottom: max(1.25rem, env(safe-area-inset-bottom));"
    aria-label="Nuevo cliente">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
    </svg>
</button>

{{-- Menú inferior con las acciones del cliente (solo móvil) --}}
<div id="sheetCliente" class="sm:hidden fixed inset-0 z-[9999] hidden">
    <div id="sheetFondo" class="absolute inset-0 bg-gray-900/30 opacity-0 transition-opacity duration-200" onclick="cerrarSheet()"></div>
    <div id="sheetPanel"
         class="absolute inset-x-0 bottom-0 bg-white rounded-t-3xl translate-y-full transition-transform duration-200 ease-out"
         style="padding-bottom: max(1rem, env(safe-area-inset-bottom));">
        <div class="w-10 h-1 bg-gray-200 rounded-full mx-auto mt-2.5"></div>

        <div class="flex items-center gap-3 px-5 pt-4 pb-5">
            <div id="sheetAvatar" class="w-12 h-12 rounded-full flex items-center justify-center text-sm font-semibold flex-shrink-0"></div>
            <div class="min-w-0">
                <p id="sheetNombre" class="text-base font-semibold text-gray-900 truncate"></p>
                <p id="sheetEmail" class="text-sm text-gray-500 truncate"></p>
            </div>
        </div>

        @php $tile = 'flex flex-col items-center justify-center gap-1.5 py-4 rounded-2xl bg-gray-50 active:bg-gray-100 text-gray-800 text-[13px] font-medium transition'; @endphp
        <div class="grid grid-cols-3 gap-2 px-4">
            <a id="sheetRutina" href="#" class="{{ $tile }}">
                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Rutina
            </a>
            <a id="sheetProgreso" href="#" class="{{ $tile }}">
                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>
                Progreso
            </a>
            <a id="sheetHistorial" href="#" class="{{ $tile }}">
                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v5h5"/><path stroke-linecap="round" stroke-linejoin="round" d="M3.05 13A9 9 0 1 0 6 5.3L3 8"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l4 2"/></svg>
                Historial
            </a>
            <button id="sheetPlan" type="button" class="{{ $tile }}">
                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/></svg>
                <span id="sheetPlanTxt">Plan</span>
            </button>
            <button id="sheetEditar" type="button" class="{{ $tile }}">
                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                Editar
            </button>
            <button type="button" class="{{ $tile }}">
                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                Mensajes
            </button>
        </div>

        <form id="sheetToggleForm" method="POST" action="#" class="form-toggle-estado px-4 pt-3"
              onsubmit="cerrarSheet(); return interceptarToggle(event, this)">
            @csrf
            @method('PATCH')
            <button id="sheetToggleBtn" type="submit" class="w-full py-3.5 rounded-2xl text-[15px] font-medium transition"></button>
        </form>
    </div>
</div>

{{-- ── Modal nuevo cliente ── --}}
<div id="modalNuevoCliente"
    onclick="if(event.target===this)this.style.display='none'"
    style="display:none;"
    class="{{ $modalWrap }}">
    <div class="{{ $modalCard }} sm:max-w-md">
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
    <div class="{{ $modalCard }} sm:max-w-md">
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
    <div class="{{ $modalCard }} sm:max-w-sm p-6">
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
    <div class="{{ $modalCard }} sm:max-w-xs p-6 text-center">

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
    <div class="{{ $modalCard }} sm:max-w-sm p-6 text-center">
        <div class="w-11 h-11 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
            </svg>
        </div>
        <h3 class="text-base font-semibold text-gray-900 mb-1.5">Sin rutina asignada</h3>
        <p class="text-sm text-gray-500 mb-6 leading-relaxed">
            <span class="text-gray-900 font-medium">{{ session('sin_plan_cliente') }}</span>
            todavía no tiene un plan. Asígnale uno desde la opción <span class="text-gray-900 font-medium">Plan</span>.
        </p>
        <button onclick="document.getElementById('modalSinPlan').style.display='none'" class="w-full {{ $btnPrimary }}">
            Entendido
        </button>
    </div>
</div>
@endif

<script>
// ── Menú inferior del cliente (móvil) ───────────────────
function abrirSheet(card) {
    const d = card.dataset;
    const activo = d.estado === 'activo';

    const avatar = document.getElementById('sheetAvatar');
    avatar.className = 'w-12 h-12 rounded-full flex items-center justify-center text-sm font-semibold flex-shrink-0 ' + d.avatar;
    avatar.textContent = d.iniciales;
    document.getElementById('sheetNombre').textContent = d.nombreReal;
    document.getElementById('sheetEmail').textContent  = d.emailReal;

    document.getElementById('sheetRutina').href    = d.urlRutina;
    document.getElementById('sheetProgreso').href  = d.urlProgreso;
    document.getElementById('sheetHistorial').href = d.urlHistorial;
    document.getElementById('sheetPlanTxt').textContent = d.tienePlan === '1' ? 'Cambiar plan' : 'Crear plan';

    document.getElementById('sheetPlan').onclick = () => {
        cerrarSheet();
        abrirModalSemanas(Number(d.id), Number(d.semanas));
    };
    document.getElementById('sheetEditar').onclick = () => {
        cerrarSheet();
        abrirModalEditar(Number(d.id), d.nombreReal, d.emailReal);
    };

    const form = document.getElementById('sheetToggleForm');
    form.action         = d.urlToggle;
    form.dataset.nombre = d.nombreReal;
    form.dataset.accion = activo ? 'desactivar' : 'activar';
    const btn = document.getElementById('sheetToggleBtn');
    btn.textContent = activo ? 'Desactivar cliente' : 'Activar cliente';
    btn.className = 'w-full py-3.5 rounded-2xl text-[15px] font-medium transition ' +
        (activo ? 'text-red-600 active:bg-red-50' : 'text-emerald-700 bg-emerald-50 active:bg-emerald-100');

    const sheet = document.getElementById('sheetCliente');
    sheet.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    requestAnimationFrame(() => {
        document.getElementById('sheetFondo').classList.remove('opacity-0');
        document.getElementById('sheetPanel').classList.remove('translate-y-full');
    });
}

function cerrarSheet() {
    const sheet = document.getElementById('sheetCliente');
    if (!sheet || sheet.classList.contains('hidden')) return;
    document.getElementById('sheetFondo').classList.add('opacity-0');
    document.getElementById('sheetPanel').classList.add('translate-y-full');
    document.body.style.overflow = '';
    setTimeout(() => sheet.classList.add('hidden'), 200);
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarSheet(); });

// ── Filtros rápidos (chips, móvil) ───────────────────────
// Cada chip solo fija los dos <select> ocultos y reutiliza filtrarClientes()
const CHIPS = {
    todos:    { estado: '',         plan: '' },
    activo:   { estado: 'activo',   plan: '' },
    inactivo: { estado: 'inactivo', plan: '' },
    sin_plan: { estado: '',         plan: 'sin_plan' },
};
const CHIP_ON  = ['bg-gray-900', 'border-gray-900', 'text-white'];
const CHIP_OFF = ['bg-white', 'border-gray-200', 'text-gray-600'];

function filtroChip(clave) {
    document.getElementById('filtroEstado').value = CHIPS[clave].estado;
    document.getElementById('filtroPlan').value   = CHIPS[clave].plan;
    filtrarClientes();
}

function syncChips() {
    const estado = document.getElementById('filtroEstado').value;
    const plan   = document.getElementById('filtroPlan').value;
    document.querySelectorAll('.chip-filtro').forEach(chip => {
        const f = CHIPS[chip.dataset.chip];
        const on = f.estado === estado && f.plan === plan;
        chip.classList.remove(...(on ? CHIP_OFF : CHIP_ON));
        chip.classList.add(...(on ? CHIP_ON : CHIP_OFF));
        chip.querySelector('.chip-num').classList.toggle('text-gray-400', !on);
        chip.querySelector('.chip-num').classList.toggle('text-white/60', on);
    });
}

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
    document.querySelectorAll('.cliente-movil').forEach(wrapper => {
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
        mensajeSinResultadosMovil.classList.toggle('hidden', visiblesMovil !== 0 || document.querySelectorAll('.cliente-movil').length === 0);
    }

    syncChips();
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