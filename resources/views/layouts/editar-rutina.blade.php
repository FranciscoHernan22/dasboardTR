@extends('layouts.entrenador')

@section('titulo','Editar Rutina')

@section('contenido')

<h2 class="text-xl font-bold mb-4">
    {{ $cliente->name }} — Semana {{ $semana }} / Día {{ $dia }}
</h2>

<form method="POST" action="{{ route('entrenador.rutina.guardar', [$cliente->id,$semana,$dia]) }}">
@csrf

<div id="contenedor-bloques">

@foreach($bloques as $grupo => $rutinasGrupo)
@php
    $tipo = $rutinasGrupo->first()->tipo;
    $orden = $rutinasGrupo->first()->orden;
@endphp

<div class="bloque border rounded p-4 mb-4">

    <div class="flex justify-between mb-3">
        <h3 class="font-bold">{{ strtoupper($tipo) }}</h3>
        <button type="button"
            onclick="this.closest('.bloque').remove(); actualizarOrden();"
            class="bg-red-600 text-white px-2 py-1 text-xs rounded">❌</button>
    </div>

    <input type="hidden" name="bloques[{{ $grupo }}][tipo]" value="{{ $tipo }}">
    <input type="hidden" name="bloques[{{ $grupo }}][orden]" value="{{ $orden }}">

    @foreach($rutinasGrupo as $i => $rutina)
    <div class="grid grid-cols-12 gap-2 mb-2">

        {{-- SEGMENTO --}}
        <div class="col-span-3">
            <select
                class="segmento-select w-full border px-2 py-1"
                data-ej="ej-{{ $grupo }}-{{ $i }}"
                name="bloques[{{ $grupo }}][ejercicios][{{ $i }}][segmento]">

                @foreach($ejerciciosPorGrupo as $seg => $list)
                    <option value="{{ $seg }}" {{ $seg==$rutina->segmento?'selected':'' }}>
                        {{ $seg }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- EJERCICIO --}}
        <div class="col-span-5">
            <select
                id="ej-{{ $grupo }}-{{ $i }}"
                name="bloques[{{ $grupo }}][ejercicios][{{ $i }}][ejercicio_id]"
                class="ejercicio-select w-full border px-2 py-1">

                @foreach($ejerciciosPorGrupo[$rutina->segmento] ?? [] as $ej)
                    <option value="{{ $ej->id }}"
                        {{ $ej->id==$rutina->ejercicio_id?'selected':'' }}>
                        {{ $ej->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-span-2">
            <input type="number"
                name="bloques[{{ $grupo }}][ejercicios][{{ $i }}][series]"
                value="{{ $rutina->series }}"
                class="w-full border px-2 py-1">
        </div>

        <div class="col-span-2">
            <input type="number"
                name="bloques[{{ $grupo }}][ejercicios][{{ $i }}][reps]"
                value="{{ $rutina->reps }}"
                class="w-full border px-2 py-1">
        </div>

    </div>
    @endforeach
</div>
@endforeach

</div>

{{-- BOTONES --}}
<div class="space-x-2 mt-4">
    <button type="button" onclick="agregarBloque('monoserie',1)" class="btn">+ Monoserie</button>
    <button type="button" onclick="agregarBloque('biserie',2)" class="btn">+ Biserie</button>
    <button type="button" onclick="agregarBloque('triserie',3)" class="btn">+ Triserie</button>
    <button type="button" onclick="agregarBloque('circuito',4)" class="btn">+ Circuito</button>
</div>

<button class="mt-6 bg-blue-600 text-white px-6 py-2 rounded">
    Guardar rutina
</button>

</form>

{{-- ===================== JS ===================== --}}
<script>
const ejerciciosPorGrupo = @json($ejerciciosPorGrupo);
let contador = Date.now();
const contenedor = document.getElementById('contenedor-bloques');

/* ---------- ORDEN ---------- */
function actualizarOrden() {
    document.querySelectorAll('.bloque').forEach((b, i) => {
        b.querySelector('input[name$="[orden]"]').value = i;
    });
}

/* ---------- CARGAR EJERCICIOS ---------- */
function cargarEjercicios(segmento, selectEjercicio) {
    selectEjercicio.innerHTML = '';
    ejerciciosPorGrupo[segmento].forEach(e => {
        const opt = document.createElement('option');
        opt.value = e.id;
        opt.textContent = e.nombre;
        selectEjercicio.appendChild(opt);
    });
}

/* ---------- CAMBIO DE SEGMENTO ---------- */
document.addEventListener('change', e => {
    if (!e.target.classList.contains('segmento-select')) return;

    const selectEjercicio = document.getElementById(e.target.dataset.ej);
    cargarEjercicios(e.target.value, selectEjercicio);
});

/* ---------- AGREGAR BLOQUE ---------- */
function agregarBloque(tipo, cantidad) {
    const grupo = 'G' + contador++;
    const orden = document.querySelectorAll('.bloque').length;

    let html = `
    <div class="bloque border rounded p-4 mb-4">
        <div class="flex justify-between mb-3">
            <h3 class="font-bold">${tipo.toUpperCase()}</h3>
            <button type="button"
                onclick="this.closest('.bloque').remove(); actualizarOrden();"
                class="bg-red-600 text-white px-2 py-1 text-xs rounded">❌</button>
        </div>

        <input type="hidden" name="bloques[${grupo}][tipo]" value="${tipo}">
        <input type="hidden" name="bloques[${grupo}][orden]" value="${orden}">
    `;

    for (let i = 0; i < cantidad; i++) {
        const ejId = `ej-${grupo}-${i}`;

        html += `
        <div class="grid grid-cols-12 gap-2 mb-2">
            <div class="col-span-3">
                <select class="segmento-select w-full border px-2 py-1"
                    data-ej="${ejId}"
                    name="bloques[${grupo}][ejercicios][${i}][segmento]">
                    ${Object.keys(ejerciciosPorGrupo)
                        .map(s => `<option value="${s}">${s}</option>`).join('')}
                </select>
            </div>

            <div class="col-span-5">
                <select id="${ejId}"
                    name="bloques[${grupo}][ejercicios][${i}][ejercicio_id]"
                    class="w-full border px-2 py-1"></select>
            </div>

            <div class="col-span-2">
                <input type="number"
                    name="bloques[${grupo}][ejercicios][${i}][series]"
                    class="w-full border px-2 py-1">
            </div>

            <div class="col-span-2">
                <input type="number"
                    name="bloques[${grupo}][ejercicios][${i}][reps]"
                    class="w-full border px-2 py-1">
            </div>
        </div>`;
    }

    html += '</div>';
    contenedor.insertAdjacentHTML('beforeend', html);
    actualizarOrden();

    /* 🔥 CARGAR EJERCICIOS PARA CADA FILA */
    for (let i = 0; i < cantidad; i++) {
        const segSel = document.querySelector(
            `[data-ej="ej-${grupo}-${i}"]`
        );
        const ejSel = document.getElementById(`ej-${grupo}-${i}`);
        cargarEjercicios(segSel.value, ejSel);
    }
}
</script>


@endsection
