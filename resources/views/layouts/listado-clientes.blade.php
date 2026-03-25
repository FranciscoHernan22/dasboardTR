@extends('layouts.entrenador')

@section('titulo', 'Mis Clientes')

@section('contenido')

<h1 class="text-2xl font-bold mb-6">Mis Clientes</h1>

<table class="w-full bg-white shadow rounded-lg overflow-hidden">
    <thead class="bg-gray-200">
        <tr>
            <th class="p-3 text-left">Id</th>
            <th class="p-3 text-left">Nombre</th>
            <th class="p-3 text-left">Email</th>
                        <th class="p-3 text-left">Historial de planes de entrenamiento</th>

            <th class="p-3 text-left">Entrenamiento</th>
            <th class="p-3 text-left">Chat</th>
            <th class="p-3 text-left">Status</th>

        </tr>
    </thead>

    <tbody>
        @forelse ($clientes as $cliente)
            <tr class="border-t hover:bg-gray-50">
                <td class="p-3">{{ $cliente->id }}</td>
                <td class="p-3">{{ $cliente->name }}</td>
                <td class="p-3">{{ $cliente->email }}</td>
             <td class="p-3"> <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors"> Planes de entrenamiento</button></td>

                <td class="p-3"> <a href="{{ route('entrenador.rutina.menu', $cliente->id) }}"class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                Plan de entrenamiento </a></td>
                
                <td class="p-3">  <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors"> Mensajes</button></td>
                <td class="p-3"> <div class="w-12 h-12 bg-green-500 rounded-full"></div>
  </td>

            </tr>
        @empty
            <tr>
                <td colspan="3" class="p-4 text-center text-gray-500">
                    No hay clientes registrados
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

@endsection
