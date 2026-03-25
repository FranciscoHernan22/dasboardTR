 @extends('layouts.entrenador')

@section('titulo', 'Plan de Entrenamiento')

@section('contenido')

<div class="max-w-6xl mx-auto mt-6">

    <h2 class="text-2xl font-bold mb-6">
        Plan de entrenamiento del cliente
    </h2>

    <div class="grid grid-cols-4 gap-4">

        @for($s = 1; $s <= 4; $s++)
            <div class="bg-gray-50 p-3 rounded shadow">

                <h3 class="font-semibold text-center mb-2">
                    Semana {{ $s }}
                </h3>

                @for($d = 1; $d <= 7; $d++)
                    <a
                        href="{{ route('entrenador.rutina.editar', [
                            'cliente' => $cliente->id,
                            'semana' => $s,
                            'dia' => $d
                        ]) }}"
                        class="block bg-white p-2 mb-1 rounded text-sm text-center hover:bg-gray-100"
                    >
                        Día {{ $d }}
                    </a>
                @endfor

            </div>
        @endfor

    </div>

</div>

@endsection
