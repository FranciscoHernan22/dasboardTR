@php
  $nombresMes = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio',
                 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
  $diasNombre = ['','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
@endphp

@extends('layouts.entrenador')
@section('titulo', $nombresMes[$mes] . ' ' . $anio)
@section('contenido')

<div class="max-w-6xl mx-auto mt-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('entrenador.historial.anio', $cliente->id) }}"
           class="text-gray-400 hover:text-gray-600 text-lg">‹</a>
        <h2 class="text-2xl font-bold">{{ $nombresMes[$mes] }} {{ $anio }}</h2>
        <span class="text-gray-400 text-sm font-normal mt-1">· {{ $cliente->name }}</span>
        <div class="flex items-center gap-1 ml-auto">
            @if($mes > 1)
                <a href="{{ route('entrenador.historial.mes', [$cliente->id, $anio, $mes - 1]) }}"
                   class="text-sm text-gray-400 hover:text-gray-600 px-2">‹ {{ $nombresMes[$mes - 1] }}</a>
            @endif
            @if($mes < 12)
                <a href="{{ route('entrenador.historial.mes', [$cliente->id, $anio, $mes + 1]) }}"
                   class="text-sm text-gray-400 hover:text-gray-600 px-2">{{ $nombresMes[$mes + 1] }} ›</a>
            @endif
        </div>
    </div>

    @if($rutinas->isEmpty())
        <div class="bg-gray-50 rounded-xl p-10 text-center text-gray-400 text-sm">
            Sin entrenamientos registrados en {{ $nombresMes[$mes] }} {{ $anio }}
        </div>
    @else
        <div class="grid grid-cols-4 gap-4">
            @foreach($rutinas->keys()->sort() as $semana)
                <div class="bg-gray-50 p-3 rounded shadow">
                    <h3 class="font-semibold text-center mb-2">Semana {{ $semana }}</h3>
                    @for($d = 1; $d <= 7; $d++)
                        @php $tiene = isset($rutinas[$semana][$d]); @endphp
                        <a href="{{ route('entrenador.historial.dia', [$cliente->id, $anio, $mes, $semana, $d]) }}"
                           class="flex items-center justify-between bg-white p-2 mb-1 rounded text-sm hover:bg-gray-100 transition-colors">
                            <span class="{{ $tiene ? 'text-gray-800 font-medium' : 'text-gray-400' }}">
                                {{ $diasNombre[$d] }}
                            </span>
                            @if($tiene)
                                <span class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0"></span>
                            @endif
                        </a>
                    @endfor
                </div>
            @endforeach
        </div>
    @endif
</div>

@endsection