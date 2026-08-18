@extends('admin.layout')

@section('title', $ejercicio->exists ? 'Editar ejercicio' : 'Nuevo ejercicio')

@section('content')
<div class="max-w-lg">
    <h1 class="text-xl font-semibold mb-6">
        {{ $ejercicio->exists ? 'Editar ejercicio' : 'Nuevo ejercicio de plantilla' }}
    </h1>

    <form method="POST"
          action="{{ $ejercicio->exists ? route('admin.plantilla.update', $ejercicio) : route('admin.plantilla.store') }}"
          class="bg-white rounded-xl border border-slate-200 p-6 space-y-4">
        @csrf
        @if($ejercicio->exists) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nombre</label>
            <input type="text" name="nombre" value="{{ old('nombre', $ejercicio->nombre) }}" required
                   class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-800">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Segmento</label>
            <input type="text" name="segmento" value="{{ old('segmento', $ejercicio->segmento) }}"
                   placeholder="Ej. Piernas, Espalda, Hombro..."
                   class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-800">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">URL de imagen</label>
            <input type="text" name="imagen" value="{{ old('imagen', $ejercicio->imagen) }}"
                   class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-800">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">URL de video</label>
            <input type="text" name="video" value="{{ old('video', $ejercicio->video) }}"
                   class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-800">
        </div>

        <div class="flex gap-2 pt-2">
            <button type="submit"
                    class="bg-slate-900 text-white rounded-md px-4 py-2 text-sm font-medium hover:bg-slate-800">
                Guardar
            </button>
            <a href="{{ route('admin.plantilla.index') }}"
               class="text-sm text-slate-500 px-4 py-2 hover:text-slate-800">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection