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
    $hoy          = now()->startOfDay();
    $fechaFin     = $fechaInicio?->copy()->addDays($totalSemanas * 7 - 1);

    $urlVolver = Route::has('entrenador.clientes.index')
        ? route('entrenador.clientes.index')
        : url()->previous();
@endphp

<div class="max-w-6xl mx-auto">

    {{-- Encabezado compacto --}}
    <div class="flex items-center gap-3 mb-4">
        <a href="{{ $urlVolver }}" aria-label="Volver a clientes"
           class="w-8 h-8 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 flex items-center justify-center text-gray-500 transition-colors flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1 min-w-0">
            <h1 class="text-base font-bold text-gray-900 truncate">Plan de {{ $cliente->name }}</h1>
            <p class="text-xs text-gray-500 truncate">
                {{ $totalSemanas }} {{ $totalSemanas === 1 ? 'semana' : 'semanas' }}
                @if($fechaInicio)
                    · {{ $fechaInicio->translatedFormat('d M') }} – {{ $fechaFin->translatedFormat('d M') }}
                @endif
                @if($semanaActual && $semanaActual <= $totalSemanas)
                    · <span class="font-semibold text-blue-600">va en la semana {{ $semanaActual }}</span>
                @endif
            </p>
        </div>
    </div>

    {{-- Semanas: 1 columna en móvil, 2–3 en computadora --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:.75rem;">
    @for($s = $semanaInicio; $s <= $semanaFin; $s++)
        @php
            $sVisual      = $s - $semanaInicio + 1;
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

        <section class="rounded-xl border p-3 shadow-sm {{ $esActual ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-white' }}">

            {{-- Título de la semana --}}
            <div class="flex items-center justify-between gap-2 mb-2">
                <div class="flex items-center gap-2 min-w-0">
                    <h2 class="text-sm font-bold {{ $esActual ? 'text-blue-600' : 'text-gray-900' }}">Semana {{ $sVisual }}</h2>
                    @if($inicioSemana)
                        <span class="text-[11px] text-gray-500 truncate">
                            {{ $inicioSemana->translatedFormat('d M') }} – {{ $finSemana->translatedFormat('d M') }}
                        </span>
                    @endif
                </div>
                @if($estado === 'actual')
                    <span class="px-2 py-0.5 rounded-full bg-blue-600 text-white text-[10px] font-semibold flex-shrink-0">En curso</span>
                @elseif($estado === 'pasada')
                    <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 text-[10px] font-semibold flex-shrink-0">Terminada</span>
                @endif
            </div>

            {{-- 7 días --}}
            <div class="flex gap-1.5">
            @for($d = 1; $d <= 7; $d++)
                @php
                    $fechaDia = $inicioSemana?->copy()->addDays($d - 1);
                    $esHoy    = $fechaDia && $fechaDia->isSameDay($hoy);
                    $esPasado = $fechaDia && $fechaDia->lt($hoy);

                    $clasesDia = $esHoy
                        ? 'bg-blue-600 text-white border-blue-500'
                        : ($esPasado
                            ? 'bg-gray-50 text-gray-400 border-gray-100'
                            : 'bg-white text-gray-700 border-gray-200 hover:border-blue-500 hover:text-blue-600');
                @endphp
                <a href="{{ route('entrenador.rutina.editar', ['cliente' => $cliente->id, 'semana' => $s, 'dia' => $d]) }}"
                   title="Semana {{ $sVisual }} · Día {{ $d }}{{ $fechaDia ? ' · ' . ucfirst($fechaDia->translatedFormat('l d M')) : '' }}"
                   class="flex-1 h-12 rounded-lg border flex flex-col items-center justify-center gap-1 transition-colors {{ $clasesDia }}">
                    <span class="text-[10px] font-semibold uppercase" style="line-height:1;">
                        {{ $fechaDia ? rtrim($fechaDia->translatedFormat('D'), '.') : 'Día' }}
                    </span>
                    <span class="text-sm font-bold" style="line-height:1;">
                        {{ $fechaDia ? $fechaDia->format('j') : $d }}
                    </span>
                </a>
            @endfor
            </div>
        </section>
    @endfor
    </div>

    {{-- Leyenda --}}
    @if($fechaInicio)
    <div class="flex flex-wrap items-center gap-x-3 gap-y-1" style="margin-top:1rem;">
        <span class="inline-flex items-center gap-1.5 text-[11px] text-gray-500">
            <span class="w-3 h-3 rounded bg-blue-600"></span> Hoy
        </span>
        <span class="inline-flex items-center gap-1.5 text-[11px] text-gray-500">
            <span class="w-3 h-3 rounded border border-gray-200 bg-white"></span> Próximo
        </span>
        <span class="inline-flex items-center gap-1.5 text-[11px] text-gray-500">
            <span class="w-3 h-3 rounded border border-gray-100 bg-gray-50"></span> Pasado
        </span>
        <span class="text-[11px] text-gray-400">· Toca un día para editar su rutina</span>
    </div>
    @endif
</div>

@endsection