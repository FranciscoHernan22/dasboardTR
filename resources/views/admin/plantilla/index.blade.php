@extends('admin.layout')

@section('title', 'Plantilla de ejercicios')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold">Plantilla de ejercicios</h1>
    <a href="{{ route('admin.plantilla.create') }}"
       class="bg-slate-900 text-white rounded-md px-4 py-2 text-sm font-medium hover:bg-slate-800">
        + Agregar ejercicio
    </a>
</div>

<p class="text-sm text-slate-500 mb-4">
    Estos son los ejercicios que reciben los entrenadores nuevos al registrarse.
    Editar aquí <strong>no afecta</strong> a entrenadores que ya clonaron y personalizaron su lista.
</p>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse ($ejercicios as $ejercicio)
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden flex flex-col">
            <div class="aspect-video bg-slate-100 flex items-center justify-center">
                @if($ejercicio->imagen)
                    <img src="{{ $ejercicio->imagen }}" alt="{{ $ejercicio->nombre }}" class="w-full h-full object-cover">
                @else
                    <span class="text-slate-400 text-xs">Sin imagen</span>
                @endif
            </div>
            <div class="p-4 flex-1 flex flex-col">
                <h2 class="font-medium">{{ $ejercicio->nombre }}</h2>
                <p class="text-xs text-slate-500 mb-3">{{ $ejercicio->segmento ?? 'Sin segmento' }}</p>

                @if($ejercicio->video)
                    <a href="{{ $ejercicio->video }}" target="_blank" class="text-xs text-blue-600 hover:underline mb-3">
                        Ver video ↗
                    </a>
                @endif

                <div class="mt-auto flex gap-2 pt-2">
                    <a href="{{ route('admin.plantilla.edit', $ejercicio) }}"
                       class="flex-1 text-center text-sm border border-slate-300 rounded-md py-1.5 hover:bg-slate-50">
                        Editar
                    </a>
                    <form method="POST" action="{{ route('admin.plantilla.destroy', $ejercicio) }}"
                          onsubmit="return confirm('¿Eliminar este ejercicio de la plantilla?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="text-sm border border-red-200 text-red-600 rounded-md py-1.5 px-3 hover:bg-red-50">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <p class="text-slate-400 col-span-full text-center py-12">Aún no hay ejercicios en la plantilla.</p>
    @endforelse
</div>
@endsection