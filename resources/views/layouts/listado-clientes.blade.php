@extends('layouts.entrenador')
@section('titulo', 'Clientes')
@section('contenido')

<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-lg font-semibold text-gray-900">Clientes</h1>
    <p class="text-sm text-gray-500 mt-0.5">{{ $clientes->count() }} registrados</p>
  </div>
  <button class="bg-blue-700 hover:bg-blue-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors duration-100">
    + Nuevo cliente
  </button>
</div>

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
<a
  href="{{ route('entrenador.historial.anio', $cliente->id) }}"
  title="Historial de entrenamientos"
  class="w-7 h-7 rounded-md border border-gray-200 bg-white hover:bg-gray-50 flex items-center justify-center transition-colors duration-75"
>
  <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
    <path d="M3 3v5h5"/><path d="M3.05 13A9 9 0 1 0 6 5.3L3 8"/>
    <path d="M12 7v5l4 2"/>
  </svg>
</a>

            {{-- Entrenamiento --}}
            <a
              href="{{ route('entrenador.rutina.menu', $cliente->id) }}"
              title="Plan de entrenamiento"
              class="w-7 h-7 rounded-md border border-gray-200 bg-white hover:bg-gray-50 flex items-center justify-center transition-colors duration-75"
            >
              <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
              </svg>
            </a>

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

@endsection



 