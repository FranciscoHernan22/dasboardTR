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
    $cantidad = count($rutinasGrupo);
    $primeraRutina = $rutinasGrupo->first();
    $seriesRaw = $primeraRutina->series ?? [];
    if (is_string($seriesRaw)) {
        $seriesRaw = json_decode($seriesRaw, true) ?? [];
    }
    $numSeries = count($seriesRaw);
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

    <div class="mb-3">
        <input type="number" min="1"
            value="{{ $numSeries }}"
            placeholder="Cantidad de series"
            class="border px-2 py-1 w-40"
            onchange="generarSeriesBloque(this, '{{ $grupo }}', {{ $cantidad }})">
    </div>

    @foreach($rutinasGrupo as $i => $rutina)
    @php
        $seriesRaw = $rutina->series ?? [];
        if (is_string($seriesRaw)) {
            $seriesRaw = json_decode($seriesRaw, true) ?? [];
        }
        $series = is_array($seriesRaw) ? $seriesRaw : [];
    @endphp

    <div class="grid grid-cols-8 gap-2 mb-2">
        <div class="col-span-3">
            <select class="segmento-select w-full border px-2 py-1"
                data-ej="ej-{{ $grupo }}-{{ $i }}"
                name="bloques[{{ $grupo }}][ejercicios][{{ $i }}][segmento]">
                @foreach($ejerciciosPorGrupo as $seg => $list)
                    <option value="{{ $seg }}" {{ $seg==$rutina->segmento?'selected':'' }}>
                        {{ $seg }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-span-3">
            <select id="ej-{{ $grupo }}-{{ $i }}"
                name="bloques[{{ $grupo }}][ejercicios][{{ $i }}][ejercicio_id]"
                class="ejercicio-select w-full border px-2 py-1">
                @foreach($ejerciciosPorGrupo[$rutina->segmento] ?? [] as $ej)
                    <option value="{{ $ej->id }}" {{ $ej->id==$rutina->ejercicio_id?'selected':'' }}>
                        {{ $ej->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-span-8">
            <div class="series-container" data-grupo="{{ $grupo }}" data-ej="{{ $i }}">
                @foreach($series as $s => $serie)
                @php $metodo = $serie['metodo'] ?? 'normal'; @endphp
                <div class="flex gap-2 mb-1 items-start" data-serie>
                    <select
                        name="bloques[{{ $grupo }}][ejercicios][{{ $i }}][series][{{ $s }}][metodo]"
                        class="border rounded px-2 py-1 text-xs text-gray-500 bg-white"
                        onchange="cambiarMetodo(this)">
                        <option value="normal"    {{ $metodo==='normal'    ?'selected':'' }}>Normal</option>
                        <option value="888"       {{ $metodo==='888'       ?'selected':'' }}>8-8-8</option>
                        <option value="restpause" {{ $metodo==='restpause' ?'selected':'' }}>Rest-pause</option>
                        <option value="21s"       {{ $metodo==='21s'       ?'selected':'' }}>21s</option>
                        <option value="10_21"     {{ $metodo==='10_21'     ?'selected':'' }}>10 + 21s</option>
                    </select>
                    <div class="flex-1">
                        <div class="metodo-fields gap-2" data-metodo="normal" style="{{ $metodo==='normal' ? 'display:flex;flex-wrap:wrap;' : 'display:none;' }}">
                            <input type="number" name="bloques[{{ $grupo }}][ejercicios][{{ $i }}][series][{{ $s }}][reps]" value="{{ $serie['reps'] ?? '' }}" placeholder="Reps" class="w-1/2 border rounded px-2 py-1 text-sm">
                            <input type="number" name="bloques[{{ $grupo }}][ejercicios][{{ $i }}][series][{{ $s }}][peso]" value="{{ $serie['peso'] ?? '' }}" placeholder="Peso" class="w-1/2 border rounded px-2 py-1 text-sm">
                        </div>
                        <div class="metodo-fields gap-1" data-metodo="888" style="{{ $metodo==='888' ? 'display:flex;flex-wrap:wrap;' : 'display:none;' }}">
                            <input type="number" name="bloques[{{ $grupo }}][ejercicios][{{ $i }}][series][{{ $s }}][peso1]" value="{{ $serie['peso1'] ?? '' }}" placeholder="Peso 1" class="w-[31%] border rounded px-2 py-1 text-sm">
                            <input type="number" name="bloques[{{ $grupo }}][ejercicios][{{ $i }}][series][{{ $s }}][peso2]" value="{{ $serie['peso2'] ?? '' }}" placeholder="Peso 2" class="w-[31%] border rounded px-2 py-1 text-sm">
                            <input type="number" name="bloques[{{ $grupo }}][ejercicios][{{ $i }}][series][{{ $s }}][peso3]" value="{{ $serie['peso3'] ?? '' }}" placeholder="Peso 3" class="w-[31%] border rounded px-2 py-1 text-sm">
                            <p class="w-full text-xs text-gray-400 mt-0.5">8 reps cada peso, bajando carga</p>
                        </div>
                        <div class="metodo-fields gap-2" data-metodo="restpause" style="{{ $metodo==='restpause' ? 'display:flex;flex-wrap:wrap;' : 'display:none;' }}">
                            <input type="number" name="bloques[{{ $grupo }}][ejercicios][{{ $i }}][series][{{ $s }}][peso_ini]" value="{{ $serie['peso_ini'] ?? '' }}" placeholder="Peso inicial" class="w-1/2 border rounded px-2 py-1 text-sm">
                            <input type="number" name="bloques[{{ $grupo }}][ejercicios][{{ $i }}][series][{{ $s }}][peso_fin]" value="{{ $serie['peso_fin'] ?? '' }}" placeholder="Peso final" class="w-1/2 border rounded px-2 py-1 text-sm">
                            <p class="w-full text-xs text-gray-400 mt-0.5">10 reps → pausa → bajar peso → continuar</p>
                        </div>
                        <div class="metodo-fields gap-2" data-metodo="21s" style="{{ $metodo==='21s' ? 'display:flex;flex-wrap:wrap;' : 'display:none;' }}">
                            <input type="number" name="bloques[{{ $grupo }}][ejercicios][{{ $i }}][series][{{ $s }}][peso]" value="{{ $serie['peso'] ?? '' }}" placeholder="Peso" class="w-full border rounded px-2 py-1 text-sm">
                            <p class="w-full text-xs text-gray-400 mt-0.5">7 parcial bajo + 7 parcial alto + 7 completo</p>
                        </div>
                        <div class="metodo-fields gap-2" data-metodo="10_21" style="{{ $metodo==='10_21' ? 'display:flex;flex-wrap:wrap;' : 'display:none;' }}">
                            <input type="number" name="bloques[{{ $grupo }}][ejercicios][{{ $i }}][series][{{ $s }}][peso_10]" value="{{ $serie['peso_10'] ?? '' }}" placeholder="Peso ×10" class="w-1/2 border rounded px-2 py-1 text-sm">
                            <input type="number" name="bloques[{{ $grupo }}][ejercicios][{{ $i }}][series][{{ $s }}][peso_21]" value="{{ $serie['peso_21'] ?? '' }}" placeholder="Peso ×21s" class="w-1/2 border rounded px-2 py-1 text-sm">
                            <p class="w-full text-xs text-gray-400 mt-0.5">10 reps → bajar peso → 21s</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach
</div>
@endforeach

</div>

<div class="grid grid-cols-4 gap-2 py-4">
    <button type="button" onclick="agregarBloque('monoserie',1)"
        class="relative overflow-hidden px-3 py-3 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-400 tracking-wide transition-all duration-150 hover:bg-gray-50 hover:border-gray-300 hover:text-blue-600 active:scale-95 group">
        Lineal
        <span class="absolute bottom-0 left-4 right-4 h-0.5 rounded-t bg-blue-500 scale-x-0 group-hover:scale-x-100 transition-transform duration-200"></span>
    </button>
    <button type="button" onclick="agregarBloque('biserie',2)"
        class="relative overflow-hidden px-3 py-3 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-400 tracking-wide transition-all duration-150 hover:bg-gray-50 hover:border-gray-300 hover:text-emerald-700 active:scale-95 group">
        Biserie
        <span class="absolute bottom-0 left-4 right-4 h-0.5 rounded-t bg-emerald-600 scale-x-0 group-hover:scale-x-100 transition-transform duration-200"></span>
    </button>
    <button type="button" onclick="agregarBloque('triserie',3)"
        class="relative overflow-hidden px-3 py-3 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-400 tracking-wide transition-all duration-150 hover:bg-gray-50 hover:border-gray-300 hover:text-amber-700 active:scale-95 group">
        Triserie
        <span class="absolute bottom-0 left-4 right-4 h-0.5 rounded-t bg-amber-500 scale-x-0 group-hover:scale-x-100 transition-transform duration-200"></span>
    </button>
    <button type="button" onclick="agregarBloque('circuito',4)"
        class="relative overflow-hidden px-3 py-3 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-400 tracking-wide transition-all duration-150 hover:bg-gray-50 hover:border-gray-300 hover:text-pink-700 active:scale-95 group">
        Circuito
        <span class="absolute bottom-0 left-4 right-4 h-0.5 rounded-t bg-pink-500 scale-x-0 group-hover:scale-x-100 transition-transform duration-200"></span>
    </button>
</div>

<button class="mt-6 bg-blue-600 text-white px-6 py-2 rounded">
    Guardar rutina
</button>

</form>

<script>
const ejerciciosPorGrupo = @json($ejerciciosPorGrupo);


let contador = Date.now();
const contenedor = document.getElementById('contenedor-bloques');

/* ---- ORDEN ---- */
function actualizarOrden() {
    document.querySelectorAll('.bloque').forEach((b, i) => {
        b.querySelector('input[name$="[orden]"]').value = i;
    });
}

/* ---- EJERCICIOS ---- */
function cargarEjercicios(segmento, selectEjercicio) {
    selectEjercicio.innerHTML = '<option value="">-- Ejercicio --</option>';
    if (!segmento || !ejerciciosPorGrupo[segmento]) return;
    ejerciciosPorGrupo[segmento].forEach(e => {
        const opt = document.createElement('option');
        opt.value = e.id;
        opt.textContent = e.nombre;
        selectEjercicio.appendChild(opt);
    });
}

document.addEventListener('change', e => {
    if (!e.target.classList.contains('segmento-select')) return;
    const selectEjercicio = document.getElementById(e.target.dataset.ej);
    cargarEjercicios(e.target.value, selectEjercicio);
});

/* ---- CAMBIAR MÉTODO ---- */
function cambiarMetodo(select) {
    const serie = select.closest('[data-serie]');
    const metodoActivo = select.value;
    serie.querySelectorAll('.metodo-fields').forEach(div => {
        if (div.dataset.metodo === metodoActivo) {
            div.style.display = 'flex';
            div.style.flexWrap = 'wrap';
        } else {
            div.style.display = 'none';
        }
    });
}

function htmlSerie(nameBase, existente = {}) {
    const metodo = existente.metodo ?? 'normal';
    const show   = (m) => metodo === m ? 'display:flex;flex-wrap:wrap;' : 'display:none;';

    return `
        <div class="flex gap-2 mb-1 items-start" data-serie>
            <select name="${nameBase}[metodo]"
                class="border rounded px-2 py-1 text-xs text-gray-500 bg-white"
                onchange="cambiarMetodo(this)">
                <option value="normal"      ${metodo==='normal'      ?'selected':''}>Normal</option>
                <option value="10_21"       ${metodo==='10_21'       ?'selected':''}>10 + 21s</option>
                <option value="888"         ${metodo==='888'         ?'selected':''}>8-8-8</option>
                <option value="isometria"   ${metodo==='isometria'   ?'selected':''}>Isometría + ROM</option>
                <option value="21s"         ${metodo==='21s'         ?'selected':''}>21s</option>
                <option value="restpause"   ${metodo==='restpause'   ?'selected':''}>Rest-pause</option>
                <option value="forzadas"    ${metodo==='forzadas'    ?'selected':''}>Repeticiones forzadas</option>
                <option value="parciales"   ${metodo==='parciales'   ?'selected':''}>Repeticiones parciales</option>
                <option value="negativas"   ${metodo==='negativas'   ?'selected':''}>Negativas (excéntricas)</option>
            </select>

            <div class="flex-1">

                <div class="metodo-fields gap-2" data-metodo="normal" style="${show('normal')}">
                    <input type="number" name="${nameBase}[reps]" value="${existente.reps??''}" placeholder="Reps" class="w-1/2 border rounded px-2 py-1 text-sm">
                    <input type="number" name="${nameBase}[peso]" value="${existente.peso??''}" placeholder="Peso" class="w-1/2 border rounded px-2 py-1 text-sm">
                </div>

                <div class="metodo-fields gap-2" data-metodo="10_21" style="${show('10_21')}">
                    <div class="w-full flex gap-2">
                        <div class="w-1/2">
                            <p class="text-xs text-gray-400 mb-1">Peso ×10 reps</p>
                            <input type="number" name="${nameBase}[peso_10]" value="${existente.peso_10??''}" placeholder="Ej: 100" class="w-full border rounded px-2 py-1 text-sm" oninput="calcular40(this)">
                        </div>
                        <div class="w-1/2">
                            <p class="text-xs text-gray-400 mb-1">Peso ×21s <span class="text-blue-400">(-40%)</span></p>
                            <input type="number" name="${nameBase}[peso_21]" value="${existente.peso_21??''}" placeholder="Auto" class="w-full border rounded px-2 py-1 text-sm peso-21-result">
                        </div>
                    </div>
                    <p class="w-full text-xs text-gray-400 mt-1">10 reps → quitar 40% → inmediato 21s (7+7+7)</p>
                </div>

                <div class="metodo-fields gap-1" data-metodo="888" style="${show('888')}">
                    <input type="number" name="${nameBase}[peso1]" value="${existente.peso1??''}" placeholder="Peso 1" class="w-[31%] border rounded px-2 py-1 text-sm">
                    <input type="number" name="${nameBase}[peso2]" value="${existente.peso2??''}" placeholder="Peso 2" class="w-[31%] border rounded px-2 py-1 text-sm">
                    <input type="number" name="${nameBase}[peso3]" value="${existente.peso3??''}" placeholder="Peso 3" class="w-[31%] border rounded px-2 py-1 text-sm">
                    <p class="w-full text-xs text-gray-400 mt-0.5">8 reps cada peso, carga descendente</p>
                </div>

                <div class="metodo-fields gap-2" data-metodo="isometria" style="${show('isometria')}">
                    <input type="number" name="${nameBase}[peso]" value="${existente.peso??''}" placeholder="Peso" class="w-full border rounded px-2 py-1 text-sm">
                    <div class="w-full flex gap-2">
                        <div class="w-1/2">
                            <p class="text-xs text-gray-400 mb-1">Reps por brazo</p>
                            <input type="number" name="${nameBase}[reps_brazo]" value="${existente.reps_brazo??4}" placeholder="Ej: 4" class="w-full border rounded px-2 py-1 text-sm">
                        </div>
                        <div class="w-1/2">
                            <p class="text-xs text-gray-400 mb-1">Reps ambos brazos</p>
                            <input type="number" name="${nameBase}[reps_ambos]" value="${existente.reps_ambos??8}" placeholder="Ej: 8" class="w-full border rounded px-2 py-1 text-sm">
                        </div>
                    </div>
                    <p class="w-full text-xs text-gray-400 mt-0.5">Isometría en su máximo punto de tensión un brazo → reps ROM completo el otro brazo → alternar → reps ambos</p>
                </div>

                <div class="metodo-fields gap-2" data-metodo="21s" style="${show('21s')}">
                    <input type="number" name="${nameBase}[peso]" value="${existente.peso??''}" placeholder="Peso" class="w-full border rounded px-2 py-1 text-sm">
                    <p class="w-full text-xs text-gray-400 mt-0.5">7 parcial bajo + 7 parcial alto + 7 ROM completo</p>
                </div>

                <div class="metodo-fields gap-2" data-metodo="restpause" style="${show('restpause')}">
                    <div class="w-full flex gap-2">
                        <div class="w-1/2">
                            <p class="text-xs text-gray-400 mb-1">Reps</p>
                            <input type="number" name="${nameBase}[reps]" value="${existente.reps??''}" placeholder="Ej: 10" class="w-full border rounded px-2 py-1 text-sm">
                        </div>
                        <div class="w-1/2">
                            <p class="text-xs text-gray-400 mb-1">Peso</p>
                            <input type="number" name="${nameBase}[peso]" value="${existente.peso??''}" placeholder="Peso" class="w-full border rounded px-2 py-1 text-sm">
                        </div>
                    </div>
                    <div class="w-full flex gap-2">
                        <div class="w-1/2">
                            <p class="text-xs text-gray-400 mb-1">Descanso (seg)</p>
                            <input type="number" name="${nameBase}[descanso]" value="${existente.descanso??15}" placeholder="10–20" class="w-full border rounded px-2 py-1 text-sm">
                        </div>
                        <div class="w-1/2">
                            <p class="text-xs text-gray-400 mb-1">Reps extra</p>
                            <input type="number" name="${nameBase}[reps_extra]" value="${existente.reps_extra??''}" placeholder="Ej: 3" class="w-full border rounded px-2 py-1 text-sm">
                        </div>
                    </div>
                    <p class="w-full text-xs text-gray-400 mt-0.5">Llevar al fallo → descansar 10–20 seg → continuar la serie</p>
                </div>

                <div class="metodo-fields gap-2" data-metodo="forzadas" style="${show('forzadas')}">
                    <div class="w-full flex gap-2">
                        <div class="w-1/2">
                            <p class="text-xs text-gray-400 mb-1">Reps solo</p>
                            <input type="number" name="${nameBase}[reps]" value="${existente.reps??''}" placeholder="Hasta fallo" class="w-full border rounded px-2 py-1 text-sm">
                        </div>
                        <div class="w-1/2">
                            <p class="text-xs text-gray-400 mb-1">Reps asistidas</p>
                            <input type="number" name="${nameBase}[reps_asistidas]" value="${existente.reps_asistidas??''}" placeholder="Ej: 3" class="w-full border rounded px-2 py-1 text-sm">
                        </div>
                    </div>
                    <input type="number" name="${nameBase}[peso]" value="${existente.peso??''}" placeholder="Peso" class="w-full border rounded px-2 py-1 text-sm">
                    <p class="w-full text-xs text-gray-400 mt-0.5">Llegar al fallo → compañero ayuda a completar reps extra</p>
                </div>

                <div class="metodo-fields gap-2" data-metodo="parciales" style="${show('parciales')}">
                    <div class="w-full flex gap-2">
                        <div class="w-1/2">
                            <p class="text-xs text-gray-400 mb-1">Reps</p>
                            <input type="number" name="${nameBase}[reps]" value="${existente.reps??''}" placeholder="Reps" class="w-full border rounded px-2 py-1 text-sm">
                        </div>
                        <div class="w-1/2">
                            <p class="text-xs text-gray-400 mb-1">Peso</p>
                            <input type="number" name="${nameBase}[peso]" value="${existente.peso??''}" placeholder="Peso" class="w-full border rounded px-2 py-1 text-sm">
                        </div>
                    </div>
                    <p class="w-full text-xs text-gray-400 mt-0.5">Solo una parte del recorrido del movimiento</p>
                </div>

                <div class="metodo-fields gap-2" data-metodo="negativas" style="${show('negativas')}">
                    <div class="w-full flex gap-2">
                        <div class="w-1/2">
                            <p class="text-xs text-gray-400 mb-1">Reps</p>
                            <input type="number" name="${nameBase}[reps]" value="${existente.reps??''}" placeholder="Reps" class="w-full border rounded px-2 py-1 text-sm">
                        </div>
                        <div class="w-1/2">
                            <p class="text-xs text-gray-400 mb-1">Peso</p>
                            <input type="number" name="${nameBase}[peso]" value="${existente.peso??''}" placeholder="Peso" class="w-full border rounded px-2 py-1 text-sm">
                        </div>
                    </div>
                    <p class="w-full text-xs text-gray-400 mt-0.5">Enfocarse en la fase excéntrica (bajada lenta y controlada)</p>
                </div>

            </div>
        </div>`;
}

/* ---- CAMBIAR MÉTODO ---- */
function cambiarMetodo(select) {
    const serie = select.closest('[data-serie]');
    const metodoActivo = select.value;
    serie.querySelectorAll('.metodo-fields').forEach(div => {
        if (div.dataset.metodo === metodoActivo) {
            div.style.display = 'flex';
            div.style.flexWrap = 'wrap';
        } else {
            div.style.display = 'none';
        }
    });
}

/* ---- CALCULAR 40% AUTOMÁTICO ---- */
function calcular40(input) {
    const serie = input.closest('[data-serie]');
    const peso10 = parseFloat(input.value) || 0;
    const resultado = Math.round(peso10 * 0.6);
    const campo21 = serie.querySelector('.peso-21-result');
    if (campo21) campo21.value = resultado > 0 ? resultado : '';
}



/* ---- CAMBIAR MÉTODO ---- */
function cambiarMetodo(select) {
    const serie = select.closest('[data-serie]');
    const metodoActivo = select.value;
    serie.querySelectorAll('.metodo-fields').forEach(div => {
        if (div.dataset.metodo === metodoActivo) {
            div.style.display = 'flex';
            div.style.flexWrap = 'wrap';
        } else {
            div.style.display = 'none';
        }
    });
}

/* ---- CALCULAR 40% AUTOMÁTICO ---- */
function calcular40(input) {
    const serie = input.closest('[data-serie]');
    const peso10 = parseFloat(input.value) || 0;
    const resultado = Math.round(peso10 * 0.6);
    const campo21 = serie.querySelector('.peso-21-result');
    if (campo21) campo21.value = resultado > 0 ? resultado : '';
}

/* ---- CAMBIAR MÉTODO ---- */
function cambiarMetodo(select) {
    const serie = select.closest('[data-serie]');
    const metodoActivo = select.value;
    serie.querySelectorAll('.metodo-fields').forEach(div => {
        if (div.dataset.metodo === metodoActivo) {
            div.style.display = 'flex';
            div.style.flexWrap = 'wrap';
        } else {
            div.style.display = 'none';
        }
    });
}

/* ---- CALCULAR 40% AUTOMÁTICO ---- */
function calcular40(input) {
    const serie = input.closest('[data-serie]');
    const peso10 = parseFloat(input.value) || 0;
    const resultado = Math.round(peso10 * 0.6);
    const campo21 = serie.querySelector('.peso-21-result');
    if (campo21) campo21.value = resultado > 0 ? resultado : '';
}

/* ---- GENERAR SERIES POR BLOQUE ---- */
function generarSeriesBloque(input, grupo, cantidad) {
    const numSeries = parseInt(input.value) || 0;

    for (let i = 0; i < cantidad; i++) {
        const container = document.querySelector(
            `.series-container[data-grupo="${grupo}"][data-ej="${i}"]`
        );
        if (!container) continue;

        const existentes = [];
        container.querySelectorAll('[data-serie]').forEach(row => {
            existentes.push({
                metodo:   row.querySelector('select')?.value ?? 'normal',
                reps:     row.querySelector('input[placeholder="Reps"]')?.value ?? '',
                peso:     row.querySelector('input[placeholder="Peso"]')?.value ?? '',
                peso1:    row.querySelector('input[placeholder="Peso 1"]')?.value ?? '',
                peso2:    row.querySelector('input[placeholder="Peso 2"]')?.value ?? '',
                peso3:    row.querySelector('input[placeholder="Peso 3"]')?.value ?? '',
                peso_ini: row.querySelector('input[placeholder="Peso inicial"]')?.value ?? '',
                peso_fin: row.querySelector('input[placeholder="Peso final"]')?.value ?? '',
                peso_10:  row.querySelector('input[placeholder="Peso ×10"]')?.value ?? '',
                peso_21:  row.querySelector('input[placeholder="Peso ×21s"]')?.value ?? '',
            });
        });

        container.innerHTML = '';

        for (let s = 0; s < numSeries; s++) {
            const nameBase = `bloques[${grupo}][ejercicios][${i}][series][${s}]`;
            container.insertAdjacentHTML('beforeend', htmlSerie(nameBase, existentes[s] ?? {}));
        }
    }
}

/* ---- AGREGAR BLOQUE ---- */
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
            <div class="mb-3">
                <input type="number" min="1" placeholder="Cantidad de series"
                    class="border px-2 py-1 w-40"
                    onchange="generarSeriesBloque(this, '${grupo}', ${cantidad})">
            </div>`;

    for (let i = 0; i < cantidad; i++) {
        const ejId = `ej-${grupo}-${i}`;
        html += `
            <div class="grid grid-cols-8 gap-2 mb-2">
                <div class="col-span-3">
                    <select class="segmento-select w-full border px-2 py-1"
                        data-ej="${ejId}"
                        name="bloques[${grupo}][ejercicios][${i}][segmento]">
                        <option value="">-- Segmento --</option>
                        ${Object.keys(ejerciciosPorGrupo).map(s => `<option value="${s}">${s}</option>`).join('')}
                    </select>
                </div>
                <div class="col-span-5">
                    <select id="${ejId}"
                        name="bloques[${grupo}][ejercicios][${i}][ejercicio_id]"
                        class="w-full border px-2 py-1">
                        <option value="">-- Ejercicio --</option>
                    </select>
                </div>
                <div class="col-span-8">
                    <div class="series-container" data-grupo="${grupo}" data-ej="${i}"></div>
                </div>
            </div>`;
    }

    html += '</div>';
    contenedor.insertAdjacentHTML('beforeend', html);
    actualizarOrden();
}
</script>

@endsection