@extends('layouts.entrenador')
@section('titulo', 'Clientes')
@section('contenido')

{{-- Header --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div>
        <h1 class="text-lg font-bold text-gray-900">Clientes</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $clientes->count() }} registrados</p>
    </div>
    <button
        onclick="document.getElementById('modalNuevoCliente').style.display='flex'"
        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
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

{{-- Buscador + filtro --}}
<div class="flex gap-2 mb-4">
    <div class="relative flex-1">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
        </svg>
        <input
            id="buscadorClientes"
            type="text"
            placeholder="Buscar cliente..."
            oninput="filtrarClientes()"
            class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 bg-white">
    </div>
    <select id="filtroEstado" onchange="filtrarClientes()"
        class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500 bg-white text-gray-600">
        <option value="">Todos</option>
        <option value="activo">Activo</option>
        <option value="inactivo">Inactivo</option>
    </select>
</div>

{{-- ── DESKTOP: tabla ── --}}
<div class="hidden sm:block bg-white border border-gray-200 rounded-xl overflow-hidden">
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
            <tr class="border-b border-gray-100 hover:bg-gray-50 last:border-b-0 transition-colors">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold flex-shrink-0">
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
                        <div class="w-2 h-2 rounded-full bg-green-500"></div>
                        <span class="text-xs text-green-700 font-medium">Activo</span>
                    </div>
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-1.5">
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
                        <button title="Mensajes"
                            class="w-8 h-8 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
        @empty
           <tr class="border-b border-gray-100 hover:bg-gray-50 last:border-b-0 transition-colors"
    data-cliente
    data-nombre="{{ strtolower($cliente->name) }}"
    data-email="{{ strtolower($cliente->email) }}">
                <td colspan="3" class="px-4 py-10 text-center text-sm text-gray-400">No hay clientes registrados</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- ── MÓVIL: swipe cards ── --}}
<div class="sm:hidden bg-white border border-gray-200 rounded-xl overflow-hidden divide-y divide-gray-100">
@forelse($clientes as $cliente)
<div class="swipe-wrapper relative overflow-hidden"
     style="height:64px;"
     data-nombre="{{ strtolower($cliente->name) }}"
     data-email="{{ strtolower($cliente->email) }}">
        {{-- Acciones detrás (se revelan con swipe) --}}
        <div class="swipe-actions absolute inset-y-0 right-0 flex items-stretch">
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
            <button type="button"
                class="w-16 flex flex-col items-center justify-center bg-amber-500 text-white text-xs font-semibold gap-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                </svg>
                Chat
            </button>
        </div>

        {{-- Card del cliente (la que se desliza) --}}
        <div class="swipe-card absolute inset-0 flex items-center gap-3 px-4 bg-white"
             style="transition: transform 0.2s ease; will-change: transform;">
            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-sm font-bold flex-shrink-0">
                {{ strtoupper(substr($cliente->name, 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-semibold text-gray-900 truncate">{{ $cliente->name }}</div>
                <div class="text-xs text-gray-500 truncate">{{ $cliente->email }}</div>
            </div>
            {{-- Hint de swipe --}}
            <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </div>

    </div>
@empty
    <div class="px-4 py-10 text-center text-sm text-gray-400">No hay clientes registrados</div>
@endforelse
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

    // Cerrar otros al abrir este
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

    // Click fuera cierra
    document.addEventListener('touchstart', e => {
        if (opened && !wrapper.contains(e.target)) snapTo(false);
    }, { passive: true });
});

// ── Modales ────────────────────────────────────────────
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

@if($errors->any())
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('modalNuevoCliente').style.display = 'flex';
});
@endif



function filtrarClientes() {
    const texto  = document.getElementById('buscadorClientes').value.toLowerCase().trim();
    const estado = document.getElementById('filtroEstado').value;

    // Desktop
    document.querySelectorAll('tbody tr[data-cliente]').forEach(row => {
        const nombre = row.dataset.nombre?.toLowerCase() ?? '';
        const email  = row.dataset.email?.toLowerCase()  ?? '';
        const coincide = nombre.includes(texto) || email.includes(texto);
        row.style.display = coincide ? '' : 'none';
    });

    // Móvil
    document.querySelectorAll('.swipe-wrapper').forEach(wrapper => {
        const nombre = wrapper.dataset.nombre?.toLowerCase() ?? '';
        const email  = wrapper.dataset.email?.toLowerCase()  ?? '';
        const coincide = nombre.includes(texto) || email.includes(texto);
        wrapper.style.display = coincide ? '' : 'none';
    });
}
</script>

@endsection