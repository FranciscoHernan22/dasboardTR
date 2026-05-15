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

  <div class="grid grid-cols-4 gap-4">
    @foreach($mesesData as $mi => $mes)
    <a href="{{ route('entrenador.historial.mes', [$cliente->id, $anio, $mi + 1]) }}"
       class="bg-gray-50 p-3 rounded shadow hover:bg-gray-100 transition-colors block">
      <div class="flex items-center justify-between mb-2">
        <h3 class="font-semibold">{{ $mes['nombre'] }}</h3>
        <span class="text-xs {{ $mes['total'] > 0 ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-400' }}
                    px-2 py-0.5 rounded-full">
          {{ $mes['total'] ?: '–' }}
        </span>
      </div>
      {{-- Mini heatmap --}}
      <div class="grid gap-0.5" style="grid-template-columns: repeat(7, 1fr)">
        @for($s = 1; $s <= 4; $s++)
          @for($d = 1; $d <= 7; $d++)
            <div class="aspect-square rounded-sm
                        {{ isset($mes['dias'][$s][$d]) ? 'bg-blue-500' : 'bg-gray-200' }}">
            </div>
          @endfor
        @endfor
      </div>
    </a>
    @endforeach
  </div>

</div>

@endsection