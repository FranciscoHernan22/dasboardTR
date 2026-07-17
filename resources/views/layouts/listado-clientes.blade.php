@extends('layouts.entrenador')
@section('titulo', 'Clientes')
@section('contenido')

@php
    // Paleta de colores para variar los avatares de forma determinística por cliente
    $avatarPalette = [
        ['bg-blue-100', 'text-blue-700'],
        ['bg-violet-100', 'text-violet-700'],
        ['bg-teal-100', 'text-teal-700'],
        ['bg-rose-100', 'text-rose-700'],
        ['bg-amber-100', 'text-amber-700'],
        ['bg-indigo-100', 'text-indigo-700'],
    ];
    $avatarColor = fn($id) => $avatarPalette[$id % count($avatarPalette)];
@endphp

{{-- Header --}}
<div class="flex flex-wrap items-start justify-between gap-3 mb-5">
    <div>
        <h1 class="text-lg font-bold text-gray-900">Clientes</h1>
        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1.5">
            <span class="inline-flex items-center gap-1.5 text-xs text-gray-500">
                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                {{ $clientes->count() }} registrados
            </span>
            <span class="inline-flex items-center gap-1.5 text-xs text-green-700">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                {{ $clientes->where('status', 'activo')->count() }} activos
            </span>
            <span class="inline-flex items-center gap-1.5 text-xs text-gray-500">
                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                {{ $clientes->where('status', 'inactivo')->count() }} inactivos
            </span>
            @php $sinPlanCount = $clientes->whereNull('plan')->count(); @endphp
            @if($sinPlanCount > 0)
            <span class="inline-flex items-center gap-1.5 text-xs text-amber-700">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                {{ $sinPlanCount }} sin plan
            </span>
            @endif
        </div>
    </div>
    <button
        onclick="document.getElementById('modalNuevoCliente').style.display='flex'"
        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm">
        + Nuevo cliente
    </button>
</div>

{{-- Flash --}}
@if(session('success'))
<div class="flex items-center gap-2 bg-green-50 border border-green-200 rounded-lg px-4 py-3 mb-4">
    <div class="w-2 h-2 rounded-full bg-green-500 flex-shrink-0"></div>
    <span class="text-sm text-green-700">{{ session('success') }}</span>
</div>
@endif

{{-- Buscador + filtros --}}
<div class="bg-white border border-gray-200 rounded-xl p-3 mb-4 flex flex-wrap gap-2">
    <div class="relative flex-1 min-w-[180px]">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
        </svg>
        <input
            id="buscadorClientes"
            type="text"
            placeholder="Buscar cliente..."
            oninput="filtrarClientes()"
            class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-blue-500 bg-gray-50 focus:bg-white transition-colors">
    </div>
    <select id="filtroEstado" onchange="filtrarClientes()"
        class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500 bg-gray-50 focus:bg-white text-gray-600 transition-colors">
        <option value="">Todos los estados</option>
        <option value="activo">Activo</option>
        <option value="inactivo">Inactivo</option>
    </select>
    <select id="filtroPlan" onchange="filtrarClientes()"
        class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500 bg-gray-50 focus:bg-white text-gray-600 transition-colors">
        <option value="">Todos los planes</option>
        <option value="con_plan">Con plan</option>
        <option value="sin_plan">Pendiente de plan</option>
    </select>
</div>

{{-- ── DESKTOP: tabla ── --}}
<div class="hidden sm:block bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Cliente</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Estado</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Acciones</th>
            </tr>
        </thead>
        <tbody>
        @forelse($clientes as $cliente)
            <tr class="border-b border-gray-100 hover:bg-gray-50 last:border-b-0 transition-colors"
                data-cliente
                data-id="{{ $cliente->id }}"
                data-nombre="{{ strtolower($cliente->name) }}"
                data-email="{{ strtolower($cliente->email) }}"
                data-estado="{{ $cliente->status }}"
                data-plan="{{ $cliente->plan ? 'con_plan' : 'sin_plan' }}">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full {{ $avatarColor($cliente->id)[0] }} {{ $avatarColor($cliente->id)[1] }} flex items-center justify-center text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($cliente->name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $cliente->name }}</div>
                            <div class="text-xs text-gray-500">{{ $cliente->email }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-1.5">
                        <div class="w-2 h-2 rounded-full {{ $cliente->status === 'activo' ? 'bg-green-500' : 'bg-gray-400' }}"></div>
                        <span class="text-xs font-medium {{ $cliente->status === 'activo' ? 'text-green-700' : 'text-gray-500' }}">
                            {{ $cliente->status === 'activo' ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                    @if(!$cliente->plan)
                    <span class="inline-flex items-center gap-1 mt-1.5 px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[11px] font-semibold">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                        </svg>
                        Sin plan
                    </span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-1.5">
                        <button type="button"
                            onclick="abrirModalEditar({{ $cliente->id }}, @js($cliente->name), @js($cliente->email))"
                            title="Editar datos"
                            class="w-8 h-8 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                            </svg>
                        </button>
                        <a href="{{ route('entrenador.historial.anio', $cliente->id) }}" title="Historial"
                            class="w-8 h-8 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v5h5"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.05 13A9 9 0 1 0 6 5.3L3 8"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l4 2"/>
                            </svg>
                        </a>
                        <a href="{{ route('entrenador.rutina.menu', $cliente->id) }}" title="Rutina"
                            class="w-8 h-8 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </a>
                        <button type="button"
                            onclick="abrirModalSemanas({{ $cliente->id }}, {{ $cliente->plan?->semanas ?? 4 }})"
                            title="{{ $cliente->plan ? 'Cambiar plan' : 'Crear plan' }}"
                            class="w-8 h-8 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                            </svg>
                        </button>
                        <a href="{{ route('entrenador.progreso.index', $cliente->id) }}" title="Progreso"
                            class="w-8 h-8 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/>
                            </svg>
                        </a>
                        <button title="Mensajes"
                            class="w-8 h-8 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                            </svg>
                        </button>
                        <form action="{{ route('entrenador.clientes.toggleEstado', $cliente->id) }}" method="POST"
                              class="form-toggle-estado"
                              data-nombre="{{ $cliente->name }}"
                              data-accion="{{ $cliente->status === 'activo' ? 'desactivar' : 'activar' }}"
                              onsubmit="return interceptarToggle(event, this)">
                            @csrf
                            @method('PATCH')
                            <button type="submit" title="{{ $cliente->status === 'activo' ? 'Desactivar' : 'Activar' }}"
                                class="w-8 h-8 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 flex items-center justify-center transition-colors">
                                @if($cliente->status === 'activo')
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.36 6.64a9 9 0 11-12.73 0M12 2v10"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.36 6.64a9 9 0 11-12.73 0M12 2v10"/>
                                    </svg>
                                @endif
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="px-4 py-14 text-center">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.7M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                    <p class="text-sm text-gray-400">No hay clientes registrados</p>
                </td>
            </tr>
        @endforelse
        <tr id="filaSinResultados" class="hidden">
            <td colspan="3" class="px-4 py-14 text-center">
                <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <p class="text-sm text-gray-400">Ningún cliente coincide con los filtros</p>
            </td>
        </tr>
        </tbody>
    </table>
</div>

{{-- ── MÓVIL: swipe cards ── --}}
<div class="sm:hidden bg-white border border-gray-200 rounded-xl overflow-hidden divide-y divide-gray-100 shadow-sm">
@forelse($clientes as $cliente)
<div class="swipe-wrapper relative overflow-hidden"
     style="height:64px;"
     data-id="{{ $cliente->id }}"
     data-nombre="{{ strtolower($cliente->name) }}"
     data-email="{{ strtolower($cliente->email) }}"
     data-estado="{{ $cliente->status }}"
     data-plan="{{ $cliente->plan ? 'con_plan' : 'sin_plan' }}">
        {{-- Acciones detrás (se revelan con swipe) --}}
        <div class="swipe-actions absolute inset-y-0 right-0 flex items-stretch">
            <button type="button"
                onclick="abrirModalEditar({{ $cliente->id }}, @js($cliente->name), @js($cliente->email))"
                class="w-16 flex flex-col items-center justify-center bg-sky-500 text-white text-xs font-semibold gap-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                </svg>
                Editar
            </button>
            <a href="{{ route('entrenador.historial.anio', $cliente->id) }}"
                class="w-16 flex flex-col items-center justify-center bg-indigo-500 text-white text-xs font-semibold gap-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v5h5"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.05 13A9 9 0 1 0 6 5.3L3 8"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l4 2"/>
                </svg>
                Historial
            </a>
            <a href="{{ route('entrenador.rutina.menu', $cliente->id) }}"
                class="w-16 flex flex-col items-center justify-center bg-blue-500 text-white text-xs font-semibold gap-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Rutina
            </a>
            <button type="button"
                onclick="abrirModalSemanas({{ $cliente->id }}, {{ $cliente->plan?->semanas ?? 4 }})"
                class="w-16 flex flex-col items-center justify-center bg-emerald-500 text-white text-xs font-semibold gap-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                </svg>
                Plan
            </button>
            <a href="{{ route('entrenador.progreso.index', $cliente->id) }}"
                class="w-16 flex flex-col items-center justify-center bg-violet-500 text-white text-xs font-semibold gap-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/>
                </svg>
                Progreso
            </a>
            <form action="{{ route('entrenador.clientes.toggleEstado', $cliente->id) }}" method="POST"
                  class="form-toggle-estado w-16"
                  data-nombre="{{ $cliente->name }}"
                  data-accion="{{ $cliente->status === 'activo' ? 'desactivar' : 'activar' }}"
                  onsubmit="return interceptarToggle(event, this)">
                @csrf
                @method('PATCH')
                <button type="submit"
                    class="w-16 h-full flex flex-col items-center justify-center {{ $cliente->status === 'activo' ? 'bg-red-500' : 'bg-amber-500' }} text-white text-xs font-semibold gap-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.36 6.64a9 9 0 11-12.73 0M12 2v10"/>
                    </svg>
                    {{ $cliente->status === 'activo' ? 'Desactivar' : 'Activar' }}
                </button>
            </form>
        </div>

        {{-- Card del cliente (la que se desliza) --}}
        <div class="swipe-card absolute inset-0 flex items-center gap-3 px-4 bg-white"
             style="transition: transform 0.2s ease; will-change: transform;">
            <div class="w-10 h-10 rounded-full {{ $avatarColor($cliente->id)[0] }} {{ $avatarColor($cliente->id)[1] }} flex items-center justify-center text-sm font-bold flex-shrink-0">
                {{ strtoupper(substr($cliente->name, 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-semibold text-gray-900 truncate">{{ $cliente->name }}</div>
                <div class="text-xs text-gray-500 truncate">{{ $cliente->email }}</div>
                @if(!$cliente->plan)
                <span class="inline-block mt-0.5 px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[10px] font-semibold">
                    Sin plan
                </span>
                @endif
            </div>
            <div class="w-2 h-2 rounded-full flex-shrink-0 {{ $cliente->status === 'activo' ? 'bg-green-500' : 'bg-gray-400' }}"></div>
            {{-- Hint de swipe --}}
            <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </div>

    </div>
@empty
    <div class="px-4 py-14 text-center">
        <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.7M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
        </svg>
        <p class="text-sm text-gray-400">No hay clientes registrados</p>
    </div>
@endforelse
<div id="mensajeSinResultadosMovil" class="hidden px-4 py-14 text-center">
    <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
    </svg>
    <p class="text-sm text-gray-400">Ningún cliente coincide con los filtros</p>
</div>
</div>

{{-- ── Modal nuevo cliente ── --}}
<div id="modalNuevoCliente"
    onclick="if(event.target===this)this.style.display='none'"
    style="display:none;"
    class="fixed inset-0 bg-black/40 z-[10000] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md max-h-[92vh] overflow-y-auto shadow-2xl">
        <div class="flex items-center justify-between px-5 pt-5 pb-4 border-b border-gray-100">
            <div>
                <h3 class="text-base font-bold text-gray-900">Nuevo cliente</h3>
                <p class="text-xs text-gray-500 mt-0.5">Completa los datos para registrarlo</p>
            </div>
            <button onclick="document.getElementById('modalNuevoCliente').style.display='none'"
                class="w-7 h-7 rounded-lg bg-gray-100 hover:bg-red-50 hover:text-red-500 flex items-center justify-center text-gray-400 text-sm transition-colors">✕</button>
        </div>
        <form method="POST" action="{{ route('entrenador.clientes.store') }}" class="p-5 flex flex-col gap-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Nombre</label>
                    <input name="name" type="text" required placeholder="Juan"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                        value="{{ old('name') }}">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Apellido</label>
                    <input name="apellido" type="text" required placeholder="García"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                        value="{{ old('apellido') }}">
                </div>
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Correo electrónico</label>
                <input name="email" type="email" required placeholder="cliente@email.com"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                    value="{{ old('email') }}">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Contraseña temporal</label>
                <input name="password" type="password" required placeholder="Mínimo 6 caracteres"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">
                    Teléfono <span class="normal-case font-normal text-gray-400">(opcional)</span>
                </label>
                <input name="telefono" type="tel" placeholder="+52 664 123 4567"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                    value="{{ old('telefono') }}">
            </div>
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                @foreach($errors->all() as $error)
                    <p class="text-xs text-red-600 mb-0.5">• {{ $error }}</p>
                @endforeach
            </div>
            @endif
            <div class="flex gap-2 pt-1">
                <button type="button"
                    onclick="document.getElementById('modalNuevoCliente').style.display='none'"
                    class="flex-1 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-500 hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                    class="flex-[2] py-2.5 bg-blue-600 hover:bg-blue-700 rounded-lg text-sm font-semibold text-white transition-colors">
                    Registrar cliente
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── Modal editar cliente ── --}}
<div id="modalEditarCliente"
    onclick="if(event.target===this)cerrarModalEditar()"
    style="display:none;"
    class="fixed inset-0 bg-black/40 z-[10000] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md max-h-[92vh] overflow-y-auto shadow-2xl">
        <div class="flex items-center justify-between px-5 pt-5 pb-4 border-b border-gray-100">
            <div>
                <h3 class="text-base font-bold text-gray-900">Editar cliente</h3>
                <p class="text-xs text-gray-500 mt-0.5">Actualiza sus datos</p>
            </div>
            <button onclick="cerrarModalEditar()"
                class="w-7 h-7 rounded-lg bg-gray-100 hover:bg-red-50 hover:text-red-500 flex items-center justify-center text-gray-400 text-sm transition-colors">✕</button>
        </div>
        <form id="formEditarCliente" method="POST" action="#" class="p-5 flex flex-col gap-4">
            @csrf
            @method('PUT')
            <input type="hidden" id="editClienteIdHidden" name="cliente_id_edit" value="{{ old('cliente_id_edit') }}">
            <div class="flex flex-col gap-1">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Nombre completo</label>
                <input name="name" id="editNombre" type="text" required
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                    value="{{ old('name') }}">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Correo electrónico</label>
                <input name="email" id="editEmail" type="email" required
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500"
                    value="{{ old('email') }}">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">
                    Nueva contraseña <span class="normal-case font-normal text-gray-400">(opcional)</span>
                </label>
                <input name="password" type="password" placeholder="Dejar en blanco para no cambiarla"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
            </div>
            @if($errors->editarCliente->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                @foreach($errors->editarCliente->all() as $error)
                    <p class="text-xs text-red-600 mb-0.5">• {{ $error }}</p>
                @endforeach
            </div>
            @endif
            <div class="flex gap-2 pt-1">
                <button type="button"
                    onclick="cerrarModalEditar()"
                    class="flex-1 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-500 hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                    class="flex-[2] py-2.5 bg-blue-600 hover:bg-blue-700 rounded-lg text-sm font-semibold text-white transition-colors">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── Modal semanas ── --}}
<div id="modalSemanas"
    onclick="if(event.target===this)cerrarModalSemanas()"
    style="display:none;"
    class="fixed inset-0 bg-black/40 z-[10000] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-xs max-h-[85vh] overflow-y-auto shadow-2xl p-6">
        <h3 class="text-base font-bold text-gray-900 mb-1">Plan de entrenamiento</h3>
        <p class="text-sm text-gray-500 mb-4">¿Cuántas semanas durará el plan?</p>
        <div class="grid grid-cols-2 gap-2 mb-4">
            @foreach([1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16] as $s)
            <button type="button"
                onclick="confirmarSemanas({{ $s }})"
                id="btn-semana-{{ $s }}"
                class="semana-btn py-2.5 border-2 border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:border-blue-500 hover:text-blue-600 hover:bg-blue-50 transition-all">
                {{ $s }} {{ $s === 1 ? 'semana' : 'semanas' }}
            </button>
            @endforeach
        </div>
        <button type="button" onclick="cerrarModalSemanas()"
            class="w-full py-2.5 border border-gray-200 rounded-xl text-sm font-semibold text-gray-500 hover:bg-gray-50 transition-colors">
            Cancelar
        </button>
    </div>
</div>

<form id="formPlan" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="semanas" id="inputSemanas">
</form>

{{-- ── Modal confirmar activar/desactivar ── --}}
<div id="modalConfirmarEstado"
    onclick="if(event.target===this)cerrarModalConfirmar()"
    style="display:none;"
    class="fixed inset-0 bg-black/40 z-[10000] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-xs p-6 shadow-2xl text-center">

        {{-- Icono: variante desactivar (roja) --}}
        <div id="iconoDesactivar" class="w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-4 bg-red-100 text-red-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18.36 6.64a9 9 0 11-12.73 0M12 2v10"/>
            </svg>
        </div>
        {{-- Icono: variante activar (verde) --}}
        <div id="iconoActivar" class="w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-4 bg-green-100 text-green-600 hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18.36 6.64a9 9 0 11-12.73 0M12 2v10"/>
            </svg>
        </div>

        <h3 id="tituloConfirmarEstado" class="text-base font-bold text-gray-900 mb-2">¿Cambiar estado?</h3>
        <p id="textoConfirmarEstado" class="text-sm text-gray-500 mb-5 leading-relaxed"></p>

        <div class="flex gap-2">
            <button type="button" onclick="cerrarModalConfirmar()"
                class="flex-1 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-500 hover:bg-gray-50 transition-colors">
                Cancelar
            </button>
            {{-- Botón: variante desactivar (roja) --}}
            <button type="button" id="btnDesactivar" onclick="ejecutarAccionConfirmada()"
                class="flex-[2] py-2.5 rounded-lg text-sm font-semibold text-white transition-colors bg-red-600 hover:bg-red-700">
                Desactivar
            </button>
            {{-- Botón: variante activar (verde) --}}
            <button type="button" id="btnActivar" onclick="ejecutarAccionConfirmada()"
                class="flex-[2] py-2.5 rounded-lg text-sm font-semibold text-white transition-colors bg-green-600 hover:bg-green-700 hidden">
                Activar
            </button>
        </div>
    </div>
</div>

{{-- ── Modal sin plan ── --}}
@if(session('sin_plan_cliente'))
<div id="modalSinPlan"
    onclick="if(event.target===this)this.style.display='none'"
    class="fixed inset-0 bg-black/40 z-[10000] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-sm p-7 text-center shadow-2xl">
        <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
            </svg>
        </div>
        <h3 class="text-base font-bold text-gray-900 mb-2">Sin rutina asignada</h3>
        <p class="text-sm text-gray-500 mb-5 leading-relaxed">
            <strong class="text-gray-700">{{ session('sin_plan_cliente') }}</strong>
            no tiene un plan todavía. Asígnale uno con el botón <strong class="text-gray-700">+</strong>.
        </p>
        <button onclick="document.getElementById('modalSinPlan').style.display='none'"
            class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-colors">
            Entendido
        </button>
    </div>
</div>
@endif

<script>
// ── Swipe ──────────────────────────────────────────────
document.querySelectorAll('.swipe-wrapper').forEach(wrapper => {
    const card    = wrapper.querySelector('.swipe-card');
    const actions = wrapper.querySelector('.swipe-actions');
    let startX = 0, currentX = 0, dragging = false, opened = false;
    const THRESHOLD = 40;

    function actionsWidth() { return actions.offsetWidth; }

    function snapTo(open) {
        opened = open;
        card.style.transition = 'transform 0.2s ease';
        card.style.transform  = open ? `translateX(-${actionsWidth()}px)` : 'translateX(0)';
    }

    function closeOthers() {
        document.querySelectorAll('.swipe-card').forEach(c => {
            if (c !== card) {
                c.style.transition = 'transform 0.2s ease';
                c.style.transform  = 'translateX(0)';
                c.closest('.swipe-wrapper')._opened = false;
            }
        });
    }

    card.addEventListener('touchstart', e => {
        startX   = e.touches[0].clientX;
        currentX = startX;
        dragging = true;
        card.style.transition = 'none';
    }, { passive: true });

    card.addEventListener('touchmove', e => {
        if (!dragging) return;
        currentX = e.touches[0].clientX;
        let delta = currentX - startX;
        if (opened) delta -= actionsWidth();
        delta = Math.max(-actionsWidth(), Math.min(0, delta));
        card.style.transform = `translateX(${delta}px)`;
    }, { passive: true });

    card.addEventListener('touchend', () => {
        dragging = false;
        const delta = currentX - startX;
        if (!opened && delta < -THRESHOLD) {
            closeOthers();
            snapTo(true);
        } else if (opened && delta > THRESHOLD) {
            snapTo(false);
        } else {
            snapTo(opened);
        }
    });

    document.addEventListener('touchstart', e => {
        if (opened && !wrapper.contains(e.target)) snapTo(false);
    }, { passive: true });
});

// ── Modal semanas ────────────────────────────────────────
let _clienteIdPendiente = null;

function abrirModalSemanas(clienteId, semanaActual) {
    _clienteIdPendiente = clienteId;
    document.querySelectorAll('.semana-btn').forEach((btn, i) => {
        const s = i + 1;
        if (s === semanaActual) {
            btn.classList.add('border-blue-500', 'text-blue-600', 'bg-blue-50');
            btn.classList.remove('border-gray-200', 'text-gray-600');
        } else {
            btn.classList.remove('border-blue-500', 'text-blue-600', 'bg-blue-50');
            btn.classList.add('border-gray-200', 'text-gray-600');
        }
    });
    document.getElementById('modalSemanas').style.display = 'flex';
}

function cerrarModalSemanas() {
    document.getElementById('modalSemanas').style.display = 'none';
    _clienteIdPendiente = null;
}

function confirmarSemanas(semanas) {
    if (!_clienteIdPendiente) return;
    const form = document.getElementById('formPlan');
    form.action = '/entrenador/plan/' + _clienteIdPendiente;
    document.getElementById('inputSemanas').value = semanas;
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
    document.getElementById('textoConfirmarEstado').innerHTML =
        `¿Seguro que deseas <strong class="text-gray-700">${accion}</strong> a <strong class="text-gray-700">${nombre}</strong>?`;

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