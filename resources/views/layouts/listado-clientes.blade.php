@extends('layouts.entrenador')
@section('titulo', 'Plan de Entrenamiento')
@section('contenido')

@php
    $totalSemanas = $semanaFin - $semanaInicio + 1;

    // Datos opcionales: si $cliente->progreso no existe en esta vista, todo cae a null
    // y la página se muestra sin fechas ni semana actual.
    $progreso     = $cliente->progreso ?? null;
    $fechaInicio  = !empty($progreso['fecha_inicio']) ? \Carbon\Carbon::parse($progreso['fecha_inicio'])->startOfDay() : null;
    $semanaActual = $progreso['semana_actual'] ?? null;   // numeración visual (1..N)
    $porcentaje   = $progreso['porcentaje'] ?? null;
    $hoy          = now()->startOfDay();
    $fechaFin     = $fechaInicio?->copy()->addDays($totalSemanas * 7 - 1);

    $urlVolver = Route::has('entrenador.clientes.index')
        ? route('entrenador.clientes.index')
        : url()->previous();
@endphp

<div class="max-w-6xl mx-auto">

    {{-- Volver --}}
    <a href="{{ $urlVolver }}"
       class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-blue-600 transition-colors mb-4">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Clientes
    </a>

    {{-- Encabezado del cliente --}}
    <div class="bg-white border border-gray-200 rounded-xl p-4 mb-5 shadow-sm flex flex-col gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-sm font-bold flex-shrink-0">
                {{ strtoupper(substr($cliente->name, 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs text-gray-500">Plan de entrenamiento</p>
                <h1 class="text-lg font-bold text-gray-900 truncate">{{ $cliente->name }}</h1>
                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $totalSemanas }} {{ $totalSemanas === 1 ? 'semana' : 'semanas' }}
                    @if($fechaInicio)
                        · {{ $fechaInicio->translatedFormat('d M') }} – {{ $fechaFin->translatedFormat('d M Y') }}
                    @endif
                </p>
            </div>
        </div>

        @if(!is_null($porcentaje))
        <div class="flex flex-col gap-1">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold text-gray-600">
                    @if($semanaActual)
                        Semana {{ min($semanaActual, $totalSemanas) }} de {{ $totalSemanas }}
                    @else
                        Progreso
                    @endif
                </span>
                <span class="text-[11px] font-bold text-blue-600">{{ $porcentaje }}%</span>
            </div>
            <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full bg-blue-500 transition-all" style="width: {{ $porcentaje }}%"></div>
            </div>
        </div>
        @endif
    </div>

    {{-- Semanas --}}
    <div id="semanas" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(230px, 1fr)); gap:1rem;">
    @for($s = $semanaInicio; $s <= $semanaFin; $s++)
        @php
            $sVisual = $s - $semanaInicio + 1;

            $inicioSemana = $fechaInicio?->copy()->addDays(($sVisual - 1) * 7);
            $finSemana    = $inicioSemana?->copy()->addDays(6);

            if ($inicioSemana) {
                $estado = $hoy->lt($inicioSemana) ? 'proxima' : ($hoy->gt($finSemana) ? 'pasada' : 'actual');
            } elseif ($semanaActual) {
                $estado = $sVisual < $semanaActual ? 'pasada' : ($sVisual == $semanaActual ? 'actual' : 'proxima');
            } else {
                $estado = null;
            }
            $esActual = $estado === 'actual';
        @endphp

        <section @if($esActual) id="semana-actual" @endif
            class="bg-white border {{ $esActual ? 'border-blue-500' : 'border-gray-200' }} rounded-xl shadow-sm overflow-hidden">

            {{-- Cabecera de la semana --}}
            <div class="flex items-center justify-between gap-2 px-4 py-3 border-b border-gray-100 {{ $esActual ? 'bg-blue-50' : 'bg-gray-50' }}">
                <div class="min-w-0">
                    <h2 class="text-sm font-bold text-gray-900">Semana {{ $sVisual }}</h2>
                    @if($inicioSemana)
                        <p class="text-[11px] text-gray-500">
                            {{ $inicioSemana->translatedFormat('d M') }} – {{ $finSemana->translatedFormat('d M') }}
                        </p>
                    @endif
                </div>
                @if($estado === 'actual')
                    <span class="px-2 py-0.5 rounded-full bg-blue-600 text-white text-[10px] font-semibold flex-shrink-0">En curso</span>
                @elseif($estado === 'pasada')
                    <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 text-[10px] font-semibold flex-shrink-0">Pasada</span>
                @endif
            </div>

            {{-- Días --}}
            <div class="flex flex-col divide-y divide-gray-100">
            @for($d = 1; $d <= 7; $d++)
                @php
                    $fechaDia = $inicioSemana?->copy()->addDays($d - 1);
                    $esHoy    = $fechaDia && $fechaDia->isSameDay($hoy);
                @endphp
                <a href="{{ route('entrenador.rutina.editar', ['cliente' => $cliente->id, 'semana' => $s, 'dia' => $d]) }}"
                   class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition-colors {{ $esHoy ? 'bg-blue-50' : '' }}">
                    <span class="w-8 h-8 rounded-lg {{ $esHoy ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600' }} flex items-center justify-center text-xs font-bold flex-shrink-0">
                        {{ $d }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-gray-900">Día {{ $d }}</div>
                        @if($fechaDia)
                            <div class="text-[11px] text-gray-500 truncate">{{ ucfirst($fechaDia->translatedFormat('l d M')) }}</div>
                        @endif
                    </div>
                    @if($esHoy)
                        <span class="text-[10px] font-bold text-blue-600 flex-shrink-0">HOY</span>
                    @endif
                    <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @endfor
            </div>
        </section>
    @endfor
    </div>
</div>

<script>
// En móvil, lleva directo a la semana en curso (si no es la primera)
document.addEventListener('DOMContentLoaded', () => {
    const actual = document.getElementById('semana-actual');
    const primera = document.querySelector('#semanas section');
    if (actual && actual !== primera && window.innerWidth < 640) {
        actual.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
});
</script>

@endsection