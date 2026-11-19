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

    {{-- Semanas: vista compacta (fila = semana, cuadro = día) --}}
    <div class="bg-white border border-gray-200 rounded-xl p-3 shadow-sm" style="max-width:640px;">

        {{-- Cabecera de días --}}
        <div class="flex items-center gap-1.5 px-2 mb-1">
            <span class="w-10 flex-shrink-0"></span>
            @for($d = 1; $d <= 7; $d++)
                <span class="flex-1 text-center text-[10px] font-bold text-gray-400 uppercase">
                    @if($fechaInicio)
                        {{ mb_substr($fechaInicio->copy()->addDays($d - 1)->translatedFormat('D'), 0, 2) }}
                    @else
                        D{{ $d }}
                    @endif
                </span>
            @endfor
        </div>

        <div class="flex flex-col gap-1">
        @for($s = $semanaInicio; $s <= $semanaFin; $s++)
            @php
                $sVisual      = $s - $semanaInicio + 1;
                $inicioSemana = $fechaInicio?->copy()->addDays(($sVisual - 1) * 7);
                $finSemana    = $inicioSemana?->copy()->addDays(6);

                if ($inicioSemana) {
                    $esActual = $hoy->between($inicioSemana, $finSemana);
                } else {
                    $esActual = $semanaActual && $sVisual == $semanaActual;
                }
            @endphp

            <div class="flex items-center gap-1.5 px-2 rounded-xl {{ $esActual ? 'bg-blue-50' : '' }}" style="padding-top:.375rem; padding-bottom:.375rem;"
                 @if($inicioSemana) title="{{ $inicioSemana->translatedFormat('d M') }} – {{ $finSemana->translatedFormat('d M') }}" @endif>
                <span class="w-10 flex-shrink-0 text-xs font-bold {{ $esActual ? 'text-blue-600' : 'text-gray-700' }}">
                    S{{ $sVisual }}
                </span>

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
                       title="Semana {{ $sVisual }} · Día {{ $d }}{{ $fechaDia ? ' · ' . $fechaDia->translatedFormat('l d M') : '' }}"
                       class="flex-1 h-10 rounded-lg border flex flex-col items-center justify-center transition-colors {{ $clasesDia }}">
                        <span class="text-xs font-bold">{{ $fechaDia ? $fechaDia->format('j') : $d }}</span>
                        @if($fechaDia && ($fechaDia->day === 1 || ($sVisual === 1 && $d === 1)))
                            <span class="text-[10px] font-medium" style="line-height:1;">{{ $fechaDia->translatedFormat('M') }}</span>
                        @endif
                    </a>
                @endfor
            </div>
        @endfor
        </div>

        {{-- Leyenda --}}
        @if($fechaInicio)
        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 px-2 mt-1" style="padding-top:.75rem; border-top:1px solid #f3f4f6;">
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
</div>

@endsection