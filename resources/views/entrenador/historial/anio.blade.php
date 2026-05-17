@php
  $nombresMes = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio',
                 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
@endphp

@extends('layouts.entrenador')
@section('titulo', 'Historial ' . $anio)
@section('contenido')

<div class="max-w-6xl mx-auto mt-6">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('entrenador.clientes') }}"
           class="text-gray-400 hover:text-gray-600 text-lg">‹</a>
        <h2 class="text-2xl font-bold">Historial {{ $anio }}</h2>
        <span class="text-gray-400 text-sm font-normal mt-1">· {{ $cliente->name }}</span>
        <div class="flex items-center gap-2 ml-auto">
            <a href="?anio={{ $anio - 1 }}"
               class="text-sm text-gray-400 hover:text-gray-600 px-2">‹ {{ $anio - 1 }}</a>
            <a href="?anio={{ $anio + 1 }}"
               class="text-sm text-gray-400 hover:text-gray-600 px-2">{{ $anio + 1 }} ›</a>
        </div>
    </div>

    <div class="grid grid-cols-4 gap-3">
        @foreach($mesesData as $mi => $mesData)
        <a href="{{ route('entrenador.historial.mes', [$cliente->id, $anio, $mesData['mes']]) }}"
           class="bg-white border border-gray-200 p-2.5 rounded-lg shadow-sm hover:shadow-md transition-shadow block">

            <div class="flex items-center justify-between mb-2">
                <h3 style="font-size:0.78rem;font-weight:700;color:#111827;">{{ $mesData['nombre'] }}</h3>
                <span style="font-size:0.6rem;font-weight:700;padding:1px 6px;border-radius:99px;"
                    class="{{ $mesData['total'] > 0 ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-400' }}">
                    {{ $mesData['total'] > 0 ? $mesData['total'] : '–' }}
                </span>
            </div>

            {{-- Cabecera días --}}
            <div class="grid gap-px mb-0.5" style="grid-template-columns: repeat(7, 1fr)">
                @foreach(['L','M','X','J','V','S','D'] as $letra)
                    <div style="text-align:center;font-size:0.48rem;font-weight:700;color:#9ca3af;">{{ $letra }}</div>
                @endforeach
            </div>

            {{-- Celdas --}}
            @php
                $primerDia = date('N', mktime(0,0,0, $mesData['mes'], 1, $anio));
                $diasEnMes = $mesData['diasMes'];
            @endphp
            <div class="grid gap-px" style="grid-template-columns: repeat(7, 1fr)">
                @for($v = 0; $v < $primerDia - 1; $v++)
                    <div></div>
                @endfor
                @for($d = 1; $d <= $diasEnMes; $d++)
                    @php
                        $fechaStr = sprintf('%04d-%02d-%02d', $anio, $mesData['mes'], $d);
                        $tiene    = isset($mesData['fechas'][$fechaStr]);
                    @endphp
                    <div style="aspect-ratio:1;border-radius:2px;display:flex;align-items:center;justify-content:center;
                                font-size:0.42rem;font-weight:600;
                                background:{{ $tiene ? '#3b82f6' : '#f3f4f6' }};
                                color:{{ $tiene ? 'white' : '#9ca3af' }};">
                        {{ $d }}
                    </div>
                @endfor
            </div>

        </a>
        @endforeach
    </div>

</div>

@endsection