@extends('layouts.entrenador')
@section('titulo', 'Clientes')
@section('contenido')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-lg font-semibold text-gray-900">Clientes</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $clientes->count() }} registrados</p>
    </div>
  <button 
    onclick="document.getElementById('modalNuevoCliente').style.display='flex'"
    class="bg-blue-700 hover:bg-blue-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors duration-100">
    + Nuevo cliente
</button>
</div>

@if (session('success'))
    <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; padding:10px 14px; margin-bottom:16px; display:flex; align-items:center; gap:8px;">
        <div style="width:8px; height:8px; border-radius:50%; background:#22c55e; flex-shrink:0;"></div>
        <span style="font-size:0.85rem; color:#15803d;">{{ session('success') }}</span>
    </div>
@endif

<div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Cliente</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Estado</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Acciones</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($clientes as $cliente)
            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-75 last:border-b-0">

                {{-- Cliente --}}
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-semibold flex-shrink-0">
                            {{ strtoupper(substr($cliente->name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $cliente->name }}</div>
                            <div class="text-xs text-gray-500">{{ $cliente->email }}</div>
                        </div>
                    </div>
                </td>

                {{-- Estado --}}
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-green-500"></div>
                        <span class="text-xs text-green-700">Activo</span>
                    </div>
                </td>

                {{-- Acciones --}}
                <td class="px-4 py-3">
                    <div class="flex items-center gap-1.5">

                        {{-- Historial --}}
                        <a href="{{ route('entrenador.historial.anio', $cliente->id) }}"
                            title="Historial de entrenamientos"
                            class="w-7 h-7 rounded-md border border-gray-200 bg-white hover:bg-gray-50 flex items-center justify-center transition-colors duration-75"
                        >
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M3 3v5h5"/><path d="M3.05 13A9 9 0 1 0 6 5.3L3 8"/>
                                <path d="M12 7v5l4 2"/>
                            </svg>
                        </a>

                        {{-- Ver rutina --}}
                        <a href="{{ route('entrenador.rutina.menu', $cliente->id) }}"
                            title="Ver rutina actual"
                            class="w-7 h-7 rounded-md border border-gray-200 bg-white hover:bg-gray-50 flex items-center justify-center transition-colors duration-75"
                        >
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </a>

                        {{-- Crear / cambiar plan --}}
                        <button
                            type="button"
                            onclick="abrirModalSemanas({{ $cliente->id }}, {{ $cliente->plan?->semanas ?? 4 }})"
                            title="{{ $cliente->plan ? 'Cambiar plan' : 'Crear plan' }}"
                            class="w-7 h-7 rounded-md border border-gray-200 bg-white hover:bg-gray-50 flex items-center justify-center transition-colors duration-75"
                        >
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14"/>
                            </svg>
                        </button>

                        {{-- Chat --}}
                        <button
                            title="Mensajes"
                            class="w-7 h-7 rounded-md border border-gray-200 bg-white hover:bg-gray-50 flex items-center justify-center transition-colors duration-75"
                        >
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                            </svg>
                        </button>

                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="px-4 py-10 text-center text-sm text-gray-400">
                    No hay clientes registrados
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- ── Modal seleccionar semanas ── --}}
<div
    id="modalSemanas"
    onclick="if(event.target===this)cerrarModalSemanas()"
    style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:10000; align-items:center; justify-content:center;"
>
    <div style="background:white; border-radius:14px; width:100%; max-width:320px; padding:24px; box-shadow:0 20px 60px rgba(0,0,0,.2);">

        <h3 style="font-size:1rem; font-weight:700; margin:0 0 4px; color:#111827;">
            Plan de entrenamiento
        </h3>
        <p style="font-size:0.85rem; color:#6b7280; margin:0 0 16px;">
            ¿Cuántas semanas durará el plan?
        </p>

        <div style="display:flex; flex-direction:column; gap:8px; margin-bottom:16px;">
@foreach([1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16] as $s)
                <button
                    type="button"
                    onclick="confirmarSemanas({{ $s }})"
                    id="btn-semana-{{ $s }}"
                    style="padding:10px; border:1.5px solid #d0d5dd; border-radius:8px; background:white; color:#374151; font-size:0.9rem; font-weight:500; cursor:pointer;"
                    onmouseover="this.style.borderColor='#2563eb'; this.style.color='#2563eb';"
                    onmouseout="if(!this.classList.contains('activo')){this.style.borderColor='#d0d5dd'; this.style.color='#374151';}"
                >
                    {{ $s }} {{ $s === 1 ? 'semana' : 'semanas' }}
                </button>
            @endforeach
        </div>

        <button
            type="button"
            onclick="cerrarModalSemanas()"
            style="width:100%; padding:9px; border:1px solid #d0d5dd; border-radius:8px; background:white; color:#6b7280; font-size:0.85rem; font-weight:600; cursor:pointer;"
        >
            Cancelar
        </button>

    </div>
</div>

{{-- Form oculto para enviar por POST --}}
<form id="formPlan" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="semanas" id="inputSemanas">
</form>

<script>
let _clienteIdPendiente = null;

function abrirModalSemanas(clienteId, semanaActual) {
    _clienteIdPendiente = clienteId;

    // Marcar la semana actual si ya tiene plan
[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16].forEach(s => {
        const btn = document.getElementById('btn-semana-' + s);
        if (s === semanaActual) {
            btn.style.borderColor = '#2563eb';
            btn.style.color       = '#2563eb';
            btn.style.background  = '#eff6ff';
            btn.classList.add('activo');
        } else {
            btn.style.borderColor = '#d0d5dd';
            btn.style.color       = '#374151';
            btn.style.background  = 'white';
            btn.classList.remove('activo');
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


// Reabrir el modal si hubo errores de validación
@if ($errors->any())
    document.getElementById('modalNuevoCliente').style.display = 'flex';
@endif
</script>


{{-- ── Modal nuevo cliente ── --}}
<div
    id="modalNuevoCliente"
    onclick="if(event.target===this)this.style.display='none'"
    style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:10000; align-items:center; justify-content:center;"
>
    <div style="background:white; border-radius:14px; width:100%; max-width:400px; padding:24px; margin:1rem; box-shadow:0 20px 60px rgba(0,0,0,.2);">

        <h3 style="font-size:1rem; font-weight:700; margin:0 0 4px; color:#111827;">Nuevo cliente</h3>
        <p style="font-size:0.85rem; color:#6b7280; margin:0 0 20px;">Completa los datos para registrarlo</p>

        <form method="POST" action="{{ route('entrenador.clientes.store') }}">
            @csrf

            {{-- Nombre y apellido --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:12px;">
                <div>
                    <label style="display:block; font-size:0.75rem; font-weight:600; color:#374151; margin-bottom:4px;">Nombre</label>
                    <input name="name" type="text" required placeholder="Juan"
                        style="width:100%; padding:8px 10px; border:1.5px solid #d0d5dd; border-radius:8px; font-size:0.85rem; color:#111827;"
                        value="{{ old('name') }}">
                </div>
                <div>
                    <label style="display:block; font-size:0.75rem; font-weight:600; color:#374151; margin-bottom:4px;">Apellido</label>
                    <input name="apellido" type="text" required placeholder="García"
                        style="width:100%; padding:8px 10px; border:1.5px solid #d0d5dd; border-radius:8px; font-size:0.85rem; color:#111827;"
                        value="{{ old('apellido') }}">
                </div>
            </div>

            {{-- Email --}}
            <div style="margin-bottom:12px;">
                <label style="display:block; font-size:0.75rem; font-weight:600; color:#374151; margin-bottom:4px;">Correo electrónico</label>
                <input name="email" type="email" required placeholder="cliente@email.com"
                    style="width:100%; padding:8px 10px; border:1.5px solid #d0d5dd; border-radius:8px; font-size:0.85rem; color:#111827;"
                    value="{{ old('email') }}">
            </div>

            {{-- Contraseña --}}
            <div style="margin-bottom:12px;">
                <label style="display:block; font-size:0.75rem; font-weight:600; color:#374151; margin-bottom:4px;">Contraseña temporal</label>
                <input name="password" type="password" required placeholder="Mínimo 6 caracteres"
                    style="width:100%; padding:8px 10px; border:1.5px solid #d0d5dd; border-radius:8px; font-size:0.85rem; color:#111827;">
            </div>

            {{-- Teléfono --}}
            <div style="margin-bottom:20px;">
                <label style="display:block; font-size:0.75rem; font-weight:600; color:#374151; margin-bottom:4px;">
                    Teléfono <span style="font-weight:400; color:#9ca3af;">(opcional)</span>
                </label>
                <input name="telefono" type="tel" placeholder="+52 664 123 4567"
                    style="width:100%; padding:8px 10px; border:1.5px solid #d0d5dd; border-radius:8px; font-size:0.85rem; color:#111827;"
                    value="{{ old('telefono') }}">
            </div>

            {{-- Errores --}}
            @if ($errors->any())
                <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:10px 12px; margin-bottom:14px;">
                    @foreach ($errors->all() as $error)
                        <p style="font-size:0.8rem; color:#dc2626; margin:0 0 2px;">• {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div style="display:flex; gap:8px;">
                <button type="button"
                    onclick="document.getElementById('modalNuevoCliente').style.display='none'"
                    style="flex:1; padding:9px; border:1.5px solid #d0d5dd; border-radius:8px; background:white; color:#6b7280; font-size:0.85rem; font-weight:600; cursor:pointer;">
                    Cancelar
                </button>
                <button type="submit"
                    style="flex:2; padding:9px; border:none; border-radius:8px; background:#1d4ed8; color:white; font-size:0.85rem; font-weight:600; cursor:pointer;">
                    Registrar cliente
                </button>
            </div>
        </form>
    </div>
</div>


@endsection