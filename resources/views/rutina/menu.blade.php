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

    {{-- Semanas: vista compacta (fila = semana, cuadro = día) --}}
    <div class="bg-white border border-gray-200 rounded-xl p-3 shadow-sm" style="max-width:640px;">

        {{-- Cabecera con el día de la semana (solo si conocemos la fecha de inicio) --}}
        @if($fechaInicio)
        <div class="flex items-center gap-1.5 px-2 mb-1">
            <span class="w-12 flex-shrink-0"></span>
            @for($d = 1; $d <= 7; $d++)
                <span class="flex-1 text-center text-[10px] font-bold text-gray-400 uppercase">
                    {{ rtrim($fechaInicio->copy()->addDays($d - 1)->translatedFormat('D'), '.') }}
                </span>
            @endfor
        </div>
        @endif

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
                <div class="w-12 flex-shrink-0 flex flex-col">
                    <span class="text-[10px] font-medium {{ $esActual ? 'text-blue-600' : 'text-gray-400' }}" style="line-height:1.2;">Semana</span>
                    <span class="text-sm font-bold {{ $esActual ? 'text-blue-600' : 'text-gray-900' }}" style="line-height:1.2;">{{ $sVisual }}</span>
                </div>

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
                        @unless($fechaDia)
                            <span class="text-[10px] font-medium" style="line-height:1;">Día</span>
                        @endunless
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