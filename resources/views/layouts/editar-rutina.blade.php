@extends('layouts.entrenador')

@section('titulo','Editar Rutina')

@section('contenido')

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

<style>
:root {
    --bg:      #f4f5f7;
    --surface: #ffffff;
    --border:  #e2e5ea;
    --border2: #d0d5dd;
    --text:    #111827;
    --muted:   #6b7280;
    --accent:  #2563eb;
    --accent-l:#eff6ff;
    --danger:  #ef4444;
    --radius:  10px;
    --ej-a: #ffffff;
    --ej-b: #f8f9fb;
    --ej-c: #f4f6f9;
    --ej-d: #f0f3f7;
}
* { box-sizing: border-box; }
body, .entrenador-content {
    font-family: 'DM Sans', sans-serif;
    background: var(--bg);
    color: var(--text);
}
.page-header {
    display: flex; align-items: center; gap: 10px;
    margin-bottom: 16px; padding-bottom: 12px;
    border-bottom: 2px solid var(--border);
}
.page-header h2 { font-size: 1.1rem; font-weight: 700; margin: 0; }
.badge {
    font-size: 0.63rem; font-weight: 700; background: var(--accent-l); color: var(--accent);
    border: 1px solid #bfdbfe; padding: 2px 8px; border-radius: 99px;
    text-transform: uppercase; letter-spacing: .05em;
}
.btn-metodos {
    margin-left: auto;
    display: inline-flex; align-items: center; gap: 5px;
    background: white; color: var(--muted);
    border: 1px solid var(--border2); border-radius: 7px;
    padding: 5px 12px; font-size: 0.75rem; font-weight: 600;
    cursor: pointer; transition: all .13s; font-family: 'DM Sans', sans-serif;
}
.btn-metodos:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-l); }

/* ── MODAL ── */
.modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,.45); z-index: 10000;
    align-items: center; justify-content: center; padding: 16px;
}
.modal-overlay.open { display: flex; }
.modal-box {
    background: white; border-radius: 14px;
    width: 100%; max-width: 620px; max-height: 88vh;
    overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,.2);
    animation: modalIn .18s ease;
}
@keyframes modalIn {
    from { transform: translateY(12px); opacity: 0; }
    to   { transform: translateY(0);    opacity: 1; }
}
.modal-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 20px 14px; border-bottom: 1px solid var(--border);
    position: sticky; top: 0; background: white; z-index: 1;
    border-radius: 14px 14px 0 0;
}
.modal-header h3 { font-size: 1rem; font-weight: 700; margin: 0; }
.modal-close {
    width: 28px; height: 28px; border-radius: 7px; background: #f3f4f6;
    border: none; cursor: pointer; font-size: 1rem; color: var(--muted);
    display: flex; align-items: center; justify-content: center; transition: background .12s;
}
.modal-close:hover { background: #fee2e2; color: var(--danger); }
.modal-body { padding: 16px 20px 20px; display: flex; flex-direction: column; gap: 10px; }
.metodo-card { border: 1px solid var(--border); border-radius: 9px; overflow: hidden; }
.metodo-card-header {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px; background: #fafbfc; border-bottom: 1px solid var(--border);
}
.metodo-tag {
    font-size: 0.65rem; font-weight: 700; letter-spacing: .06em;
    text-transform: uppercase; padding: 2px 8px; border-radius: 99px; flex-shrink: 0;
}
.tag-normal    { background: #dbeafe; color: #1d4ed8; }
.tag-888       { background: #fce7f3; color: #9d174d; }
.tag-restpause { background: #fef3c7; color: #92400e; }
.tag-21s       { background: #d1fae5; color: #065f46; }
.tag-10_21     { background: #ede9fe; color: #5b21b6; }
.tag-isometria { background: #fef9c3; color: #713f12; }
.tag-forzadas  { background: #fee2e2; color: #991b1b; }
.tag-parciales { background: #e0f2fe; color: #075985; }
.tag-negativas { background: #f0fdf4; color: #14532d; }
.metodo-card-nombre { font-size: 0.875rem; font-weight: 700; color: var(--text); }
.metodo-card-body { padding: 10px 14px; font-size: 0.82rem; color: var(--muted); line-height: 1.6; }
.metodo-card-body b { color: var(--text); }

/* ── Add block bar ── */
.add-block-bar { display: flex; gap: 8px; margin-bottom: 14px; }
.add-block-btn {
    flex: 1; padding: 7px 4px; background: var(--surface);
    border: 1.5px dashed var(--border2); border-radius: 7px;
    font-size: 0.72rem; font-weight: 600; color: var(--muted); cursor: pointer; transition: all .13s;
}
.add-block-btn:hover { background: var(--accent-l); border-color: var(--accent); color: var(--accent); }

/* ── Bloque ── */
.bloque {
    background: var(--surface); border: 1.5px solid var(--border);
    border-radius: var(--radius); margin-bottom: 10px;
    box-shadow: 0 1px 4px rgba(0,0,0,.06); overflow: visible;
}
.bloque-header {
    display: flex; align-items: center; gap: 8px; padding: 7px 12px;
    border-bottom: 1px solid var(--border); background: #f5f6f8;
    border-radius: var(--radius) var(--radius) 0 0;
}
.bloque-tipo {
    font-size: 0.6rem; font-weight: 700; letter-spacing: .08em;
    text-transform: uppercase; padding: 2px 8px; border-radius: 99px; flex-shrink: 0;
}
.tipo-monoserie { background:#dbeafe; color:#1d4ed8; }
.tipo-biserie   { background:#d1fae5; color:#065f46; }
.tipo-triserie  { background:#fef3c7; color:#92400e; }
.tipo-circuito  { background:#fce7f3; color:#9d174d; }
.bloque-series-count {
    display: flex; align-items: center; gap: 5px;
    font-size: 0.72rem; color: var(--muted); margin-left: auto;
}
.bloque-series-count input {
    width: 42px; border: 1px solid var(--border2); border-radius: 5px;
    padding: 2px 5px; font-size: 0.74rem; font-family: 'DM Mono', monospace;
    text-align: center; color: var(--text);
}
.bloque-series-count input:focus { outline: none; border-color: var(--accent); }
.btn-remove {
    width: 24px; height: 24px; border-radius: 5px; background: #fee2e2;
    border: none; color: var(--danger); cursor: pointer; font-size: 0.75rem;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.btn-remove:hover { background: #fca5a5; }

.series-header-row { display: flex; border-bottom: 2px solid var(--border); background: #f0f2f5; }
.series-header-row .col-info-header {
    width: 265px; flex-shrink: 0; border-right: 1px solid var(--border);
    padding: 5px 10px; font-size: 0.6rem; font-weight: 700; color: var(--muted);
    text-transform: uppercase; letter-spacing: .06em; display: flex; align-items: center;
}
.series-header-row .col-series-headers { flex: 1; display: flex; padding: 0; min-width: 0; }
.serie-header-col {
    flex: 1; text-align: center; padding: 5px 4px;
    font-size: 0.65rem; font-weight: 700; color: var(--accent);
    background: var(--accent-l); border-right: 1px solid #bfdbfe;
    letter-spacing: .04em; text-transform: uppercase;
}
.serie-header-col:last-child { border-right: none; }

.ejercicio-row {
    display: flex; border-bottom: 1px solid var(--border);
    min-height: 54px; align-items: stretch;
}
.ejercicio-row:last-of-type { border-bottom: none; }
.ej-letra {
    width: 22px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;
    font-size: 0.65rem; font-weight: 800; border-right: 2px solid var(--border);
}
.ej-letra-a { color: #1d4ed8; background: #eff6ff; }
.ej-letra-b { color: #065f46; background: #f0fdf4; }
.ej-letra-c { color: #92400e; background: #fffbeb; }
.ej-letra-d { color: #9d174d; background: #fdf2f8; }
.ej-bg-a { background: var(--ej-a); }
.ej-bg-b { background: var(--ej-b); }
.ej-bg-c { background: var(--ej-c); }
.ej-bg-d { background: var(--ej-d); }
.col-segmento  { width: 110px; flex-shrink: 0; padding: 7px 9px; border-right: 1px solid var(--border); }
.col-ejercicio { width: 133px; flex-shrink: 0; padding: 7px 9px; border-right: 1px solid var(--border); }
.col-series    { flex: 1; padding: 6px 6px; min-width: 0; display: flex; align-items: stretch; }
.field-label { font-size: 0.58rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .06em; margin-bottom: 3px; }
.segmento-select { width: 100%; border: 1px solid var(--border2); border-radius: 5px; padding: 4px 5px; font-size: 0.73rem; font-family: 'DM Sans', sans-serif; color: var(--text); background: white; }
.segmento-select:focus { outline: none; border-color: var(--accent); }
.ej-select-wrapper { position: relative; user-select: none; }
.ej-select-trigger { display: flex; align-items: center; gap: 5px; border: 1px solid var(--border2); border-radius: 5px; padding: 3px 5px; cursor: pointer; background: white; min-height: 32px; transition: border-color .12s; }
.ej-select-trigger:hover { border-color: var(--accent); }
.ej-select-trigger img { width: 30px; height: 30px; object-fit: cover; border-radius: 4px; flex-shrink: 0; }
.ej-trigger-nombre { font-size: 0.7rem; font-weight: 600; color: var(--text); flex: 1; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-height: 1.2; }
.ej-trigger-placeholder { font-size: 0.7rem; color: var(--muted); flex: 1; }
.ej-trigger-arrow { color: var(--muted); font-size: 0.55rem; flex-shrink: 0; }
.ej-select-dropdown { display: none; position: absolute; top: calc(100% + 3px); left: 0; width: 260px; background: white; border: 1.5px solid var(--border); border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,.14); z-index: 9999; max-height: 280px; overflow-y: auto; }
.ej-select-dropdown.open { display: block; }
.ej-select-option { display: flex; align-items: center; gap: 8px; padding: 6px 10px; cursor: pointer; font-size: 0.8rem; border-bottom: 1px solid #f3f4f6; transition: background .1s; }
.ej-select-option:last-child { border-bottom: none; }
.ej-select-option:hover    { background: var(--accent-l); }
.ej-select-option.selected { background: var(--accent-l); font-weight: 600; color: var(--accent); }
.ej-select-option img { width: 46px; height: 46px; object-fit: cover; border-radius: 5px; flex-shrink: 0; }
.ej-no-img { width: 46px; height: 46px; border-radius: 5px; background: var(--bg); display: flex; align-items: center; justify-content: center; color: var(--muted); font-size: 0.58rem; border: 1px dashed var(--border2); flex-shrink: 0; }
.series-cols { display: flex; flex-direction: row; gap: 5px; width: 100%; align-items: stretch; }
.serie-col { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 3px; background: white; border: 1px solid var(--border); border-radius: 6px; padding: 5px 4px; min-width: 0; }
.metodo-select { width: 100%; border: 1px solid var(--border2); border-radius: 4px; padding: 2px 3px; font-size: 0.62rem; font-family: 'DM Sans', sans-serif; color: var(--muted); background: white; text-align: center; }
.metodo-select:focus { outline: none; border-color: var(--accent); }
.metodo-fields { display: none; flex-direction: column; gap: 3px; width: 100%; }
.metodo-fields.active { display: flex; }
.campo-wrap { display: flex; flex-direction: column; align-items: center; gap: 1px; width: 100%; }
.campo-wrap label { font-size: 0.54rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .04em; }
.campo-input { width: 100%; border: 1px solid var(--border2); border-radius: 4px; padding: 3px 4px; font-size: 0.78rem; font-family: 'DM Mono', monospace; color: var(--text); text-align: center; }
.campo-input:focus { outline: none; border-color: var(--accent); }
.metodo-nota { font-size: 0.57rem; color: var(--accent); background: var(--accent-l); border-radius: 3px; padding: 2px 4px; text-align: center; width: 100%; line-height: 1.3; }
.btn-guardar {
    display: inline-flex; align-items: center; gap: 6px; background: var(--accent); color: white;
    font-family: 'DM Sans', sans-serif; font-size: 0.87rem; font-weight: 600;
    padding: 9px 24px; border: none; border-radius: var(--radius); cursor: pointer;
    box-shadow: 0 2px 8px rgba(37,99,235,.3); transition: all .14s; margin-top: 14px;
}
.btn-guardar:hover { background: #1d4ed8; transform: translateY(-1px); }
.btn-guardar:disabled { background: #93c5fd; cursor: not-allowed; transform: none; }
.btn-pdf {
    display: inline-flex; align-items: center; gap: 6px; background: white; color: var(--accent);
    font-family: 'DM Sans', sans-serif; font-size: 0.87rem; font-weight: 600;
    padding: 9px 22px; border: 1.5px solid var(--accent); border-radius: var(--radius);
    cursor: pointer; text-decoration: none; transition: all .14s; margin-top: 14px; margin-left: 10px;
}
.btn-pdf:hover { background: var(--accent-l); }

/* ── Peso + unidad ── */
.peso-group { display: flex; align-items: center; gap: 2px; width: 100%; }
.peso-group .campo-input { flex: 1; min-width: 0; }
.unidad-select {
    width: 34px; flex-shrink: 0;
    border: 1px solid var(--border2); border-radius: 4px;
    padding: 3px 1px; font-size: 0.58rem; font-family: 'DM Sans', sans-serif;
    color: var(--muted); background: white; text-align: center; cursor: pointer;
}
.unidad-select:focus { outline: none; border-color: var(--accent); }
</style>

{{-- ══ MODAL DE MÉTODOS ══ --}}
<div class="modal-overlay" id="modalMetodos" onclick="if(event.target===this) cerrarModal()">
    <div class="modal-box">
        <div class="modal-header">
            <h3>📚 Métodos de entrenamiento</h3>
            <button class="modal-close" onclick="cerrarModal()">✕</button>
        </div>
        <div class="modal-body">
            <div class="metodo-card">
                <div class="metodo-card-header"><span class="metodo-tag tag-normal">Normal</span><span class="metodo-card-nombre">Serie normal</span></div>
                <div class="metodo-card-body">La forma más básica de entrenamiento. Se realizan un número determinado de repeticiones con un peso fijo, descansando entre cada serie. <b>Ideal para construir fuerza y masa muscular.</b></div>
            </div>
            <div class="metodo-card">
                <div class="metodo-card-header"><span class="metodo-tag tag-888">Descendente</span><span class="metodo-card-nombre">Triple bajada de peso</span></div>
                <div class="metodo-card-body">3 bloques del mismo número de repeticiones (definido por el entrenador) bajando el peso en cada uno sin descanso. <b>Ej: 8-8-8, 6-6-6, 12-12-12...</b></div>
            </div>
            <div class="metodo-card">
                <div class="metodo-card-header"><span class="metodo-tag tag-restpause">Rest-pause</span><span class="metodo-card-nombre">Pausa y continúa</span></div>
                <div class="metodo-card-body">Se lleva la serie al fallo muscular, luego se hace una pausa corta (10–20 seg) y se continúa. <b>Permite acumular más repeticiones de alta intensidad en una sola serie.</b></div>
            </div>
            <div class="metodo-card">
                <div class="metodo-card-header"><span class="metodo-tag tag-21s">3 Rangos</span><span class="metodo-card-nombre">Tres rangos de movimiento</span></div>
                <div class="metodo-card-body">3 bloques iguales de repeticiones: mitad inferior, mitad superior y recorrido completo. <b>Por defecto 7+7+7 pero el entrenador puede definir cualquier número.</b></div>
            </div>
            <div class="metodo-card">
                <div class="metodo-card-header"><span class="metodo-tag tag-10_21">10 + 21s</span><span class="metodo-card-nombre">Combinación de volumen e intensidad</span></div>
                <div class="metodo-card-body">10 repeticiones completas, luego −40% de peso y se ejecutan los 21s (7+7+7). <b>Combina fuerza con pump en la misma serie.</b></div>
            </div>
            <div class="metodo-card">
                <div class="metodo-card-header"><span class="metodo-tag tag-isometria">Isometría + ROM</span><span class="metodo-card-nombre">Tensión estática y dinámica</span></div>
                <div class="metodo-card-body">Un brazo mantiene contracción isométrica mientras el otro ejecuta repeticiones. Luego se alterna y finalmente ambos trabajan juntos. <b>Mejora la conexión neuromuscular.</b></div>
            </div>
            <div class="metodo-card">
                <div class="metodo-card-header"><span class="metodo-tag tag-forzadas">Forzadas</span><span class="metodo-card-nombre">Repeticiones asistidas</span></div>
                <div class="metodo-card-body">Se lleva al fallo y un compañero asiste para completar repeticiones adicionales. <b>Requiere compañero de entrenamiento.</b></div>
            </div>
            <div class="metodo-card">
                <div class="metodo-card-header"><span class="metodo-tag tag-parciales">Parciales</span><span class="metodo-card-nombre">Rango de movimiento reducido</span></div>
                <div class="metodo-card-body">Solo una porción del recorrido, generalmente la más difícil. <b>Útil para sobrecargar un punto específico o continuar tras el fallo.</b></div>
            </div>
            <div class="metodo-card">
                <div class="metodo-card-header"><span class="metodo-tag tag-negativas">Negativas</span><span class="metodo-card-nombre">Fase excéntrica controlada</span></div>
                <div class="metodo-card-body">Fase de bajada muy lenta y controlada (4–6 segundos). <b>Genera mayor daño muscular y adaptaciones de fuerza superiores.</b></div>
            </div>
        </div>
    </div>
</div>

{{-- ══ PÁGINA ══ --}}
<div class="page-header">
    <h2>{{ $cliente->name }}</h2>
    <span class="badge">Semana {{ $semana }} · Día {{ $dia }}</span>
    <button class="btn-metodos" onclick="abrirModal()">❓ Métodos</button>
</div>

<form method="POST" action="{{ route('entrenador.rutina.guardar', [$cliente->id,$semana,$dia]) }}" id="form-rutina">
@csrf
{{-- Un solo campo hidden que lleva todo el JSON --}}
<input type="hidden" name="datos_json" id="datos_json">

 

<div id="contenedor-bloques">

@foreach($bloques as $grupo => $rutinasGrupo)
@php
    $tipo      = $rutinasGrupo->first()->tipo;
    $orden     = $rutinasGrupo->first()->orden;
    $cantidad  = count($rutinasGrupo);
    $seriesRaw = $rutinasGrupo->first()->series ?? [];
    if (is_string($seriesRaw)) $seriesRaw = json_decode($seriesRaw, true) ?? [];
    $numSeries = count($seriesRaw);
    $numeros   = ['1','2','3','4'];
@endphp



<div class="bloque" data-grupo="{{ $grupo }}" data-tipo="{{ $tipo }}">
    <div class="bloque-header">
        <span class="bloque-tipo tipo-{{ strtolower($tipo) }}">{{ strtoupper($tipo) }}</span>
        <div class="bloque-series-count">
            Series:
            <input type="number" min="1" value="{{ $numSeries }}" placeholder="–"
                onchange="generarSeriesBloque(this, '{{ $grupo }}', {{ $cantidad }})">
        </div>
        <button type="button" class="btn-remove"
            onclick="this.closest('.bloque').remove(); actualizarOrden();">✕</button>
    </div>

    <div class="series-header-row" data-header="{{ $grupo }}">
        <div class="col-info-header">Ejercicio</div>
        <div class="col-series-headers">
            @for($s = 0; $s < $numSeries; $s++)
                <div class="serie-header-col">S{{ $s + 1 }}</div>
            @endfor
        </div>
    </div>

    @foreach($rutinasGrupo as $i => $rutina)
    @php
        $seriesRaw = $rutina->series ?? [];
        if (is_string($seriesRaw)) $seriesRaw = json_decode($seriesRaw, true) ?? [];
        $series    = is_array($seriesRaw) ? $seriesRaw : [];
        $ejActual  = ($ejerciciosPorGrupo[$rutina->segmento] ?? collect())->firstWhere('id', $rutina->ejercicio_id);
        $imgActual = $ejActual->imagen ?? null;
        $num       = $numeros[$i] ?? ($i + 1);
        $bgClass   = ['ej-bg-a','ej-bg-b','ej-bg-c','ej-bg-d'][$i] ?? 'ej-bg-a';
        $letraClass= ['ej-letra-a','ej-letra-b','ej-letra-c','ej-letra-d'][$i] ?? 'ej-letra-a';
    @endphp

    <div class="ejercicio-row {{ $bgClass }}">
        <div class="ej-letra {{ $letraClass }}">{{ $num }}</div>

        <div class="col-segmento">
            <div class="field-label">Segmento</div>
            <select class="segmento-select"
                data-ej="ej-{{ $grupo }}-{{ $i }}"
                onchange="onSegmentoChange(this)">
                @foreach($ejerciciosPorGrupo as $seg => $list)
                    <option value="{{ $seg }}" {{ $seg==$rutina->segmento?'selected':'' }}>{{ $seg }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-ejercicio">
            <div class="field-label">Ejercicio</div>
            <input type="hidden"
                id="ej-{{ $grupo }}-{{ $i }}"
                class="ejercicio-id-input"
                value="{{ $rutina->ejercicio_id }}">
            <div class="ej-select-wrapper" data-target="ej-{{ $grupo }}-{{ $i }}">
                <div class="ej-select-trigger" onclick="toggleDropdown(this)">
                    @if($imgActual)
                        <img src="{{ asset('storage/'.$imgActual) }}" alt="">
                    @else
                        <img src="" alt="" style="display:none;">
                    @endif
                    <div class="{{ $ejActual ? 'ej-trigger-nombre' : 'ej-trigger-placeholder' }}">
                        {{ $ejActual->nombre ?? '-- Ejercicio --' }}
                    </div>
                    <span class="ej-trigger-arrow">▼</span>
                </div>
                <div class="ej-select-dropdown">
                    @foreach($ejerciciosPorGrupo[$rutina->segmento] ?? [] as $ej)
                    <div class="ej-select-option {{ $ej->id==$rutina->ejercicio_id?'selected':'' }}"
                         data-value="{{ $ej->id }}"
                         data-nombre="{{ $ej->nombre }}"
                         data-imagen="{{ $ej->imagen ? asset('storage/'.$ej->imagen) : '' }}"
                         onclick="seleccionarEjercicio(this)">
                        @if($ej->imagen)
                            <img src="{{ asset('storage/'.$ej->imagen) }}" alt="{{ $ej->nombre }}">
                        @else
                            <div class="ej-no-img">Sin img</div>
                        @endif
                        <span>{{ $ej->nombre }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-series">
            <div class="series-cols" data-grupo="{{ $grupo }}" data-ej="{{ $i }}">
                @foreach($series as $s => $serie)
                @php $metodo = $serie['metodo'] ?? 'normal'; @endphp
                <div class="serie-col" data-serie>
                    <select class="metodo-select" onchange="cambiarMetodo(this)">
                        <option value="normal"    {{ $metodo==='normal'    ?'selected':'' }}>Normal</option>
                        <option value="888"       {{ $metodo==='888'       ?'selected':'' }}>Descend.</option>
                        <option value="restpause" {{ $metodo==='restpause' ?'selected':'' }}>Rest-pause</option>
                        <option value="21s"       {{ $metodo==='21s'       ?'selected':'' }}>3 Rangos</option>
                        <option value="10_21"     {{ $metodo==='10_21'     ?'selected':'' }}>10+21s</option>
                        <option value="isometria" {{ $metodo==='isometria' ?'selected':'' }}>Isometría</option>
                        <option value="forzadas"  {{ $metodo==='forzadas'  ?'selected':'' }}>Forzadas</option>
                        <option value="parciales" {{ $metodo==='parciales' ?'selected':'' }}>Parciales</option>
                        <option value="negativas" {{ $metodo==='negativas' ?'selected':'' }}>Negativas</option>
                    </select>

                    {{-- NORMAL --}}
                    <div class="metodo-fields {{ $metodo==='normal'?'active':'' }}" data-metodo="normal">
                        <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" data-key="reps" value="{{ $serie['reps']??'' }}" placeholder="–"></div>
                        <div class="campo-wrap"><label>Peso</label>
                            <div class="peso-group">
                                <input class="campo-input" type="number" step="0.5" data-key="peso" value="{{ $serie['peso']??'' }}" placeholder="–">
                                <select class="unidad-select" data-key="unidad">
                                    <option value="kg" {{ ($serie['unidad']??'kg')==='kg'?'selected':'' }}>kg</option>
                                    <option value="lb" {{ ($serie['unidad']??'kg')==='lb'?'selected':'' }}>lb</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- DESCENDENTE --}}
                    <div class="metodo-fields {{ $metodo==='888'?'active':'' }}" data-metodo="888">
                        <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" min="1" data-key="reps_888" value="{{ $serie['reps_888']??8 }}" placeholder="8" oninput="actualizar888Nota(this)"></div>
                        <div class="campo-wrap"><label>P1</label>
                            <div class="peso-group">
                                <input class="campo-input" type="number" step="0.5" data-key="peso1" value="{{ $serie['peso1']??'' }}" placeholder="–">
                                <select class="unidad-select" data-key="unidad1">
                                    <option value="kg" {{ ($serie['unidad1']??'kg')==='kg'?'selected':'' }}>kg</option>
                                    <option value="lb" {{ ($serie['unidad1']??'kg')==='lb'?'selected':'' }}>lb</option>
                                </select>
                            </div>
                        </div>
                        <div class="campo-wrap"><label>P2</label>
                            <div class="peso-group">
                                <input class="campo-input" type="number" step="0.5" data-key="peso2" value="{{ $serie['peso2']??'' }}" placeholder="–">
                                <select class="unidad-select" data-key="unidad2">
                                    <option value="kg" {{ ($serie['unidad2']??'kg')==='kg'?'selected':'' }}>kg</option>
                                    <option value="lb" {{ ($serie['unidad2']??'kg')==='lb'?'selected':'' }}>lb</option>
                                </select>
                            </div>
                        </div>
                        <div class="campo-wrap"><label>P3</label>
                            <div class="peso-group">
                                <input class="campo-input" type="number" step="0.5" data-key="peso3" value="{{ $serie['peso3']??'' }}" placeholder="–">
                                <select class="unidad-select" data-key="unidad3">
                                    <option value="kg" {{ ($serie['unidad3']??'kg')==='kg'?'selected':'' }}>kg</option>
                                    <option value="lb" {{ ($serie['unidad3']??'kg')==='lb'?'selected':'' }}>lb</option>
                                </select>
                            </div>
                        </div>
                        <div class="metodo-nota nota-888">{{ $serie['reps_888']??8 }} c/u·desc.</div>
                    </div>

                    {{-- REST-PAUSE --}}
                    <div class="metodo-fields {{ $metodo==='restpause'?'active':'' }}" data-metodo="restpause">
                        <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" data-key="reps_rp" value="{{ $serie['reps']??'' }}" placeholder="–"></div>
                        <div class="campo-wrap"><label>Peso</label>
                            <div class="peso-group">
                                <input class="campo-input" type="number" step="0.5" data-key="peso_rp" value="{{ $serie['peso']??'' }}" placeholder="–">
                                <select class="unidad-select" data-key="unidad_rp">
                                    <option value="kg" {{ ($serie['unidad_rp']??'kg')==='kg'?'selected':'' }}>kg</option>
                                    <option value="lb" {{ ($serie['unidad_rp']??'kg')==='lb'?'selected':'' }}>lb</option>
                                </select>
                            </div>
                        </div>
                        <div class="campo-wrap"><label>Desc(s)</label><input class="campo-input" type="number" data-key="descanso" value="{{ $serie['descanso']??15 }}" placeholder="15"></div>
                        <div class="metodo-nota">Fallo→pausa</div>
                    </div>

                    {{-- 3 RANGOS --}}
                    <div class="metodo-fields {{ $metodo==='21s'?'active':'' }}" data-metodo="21s">
                        @php $r21 = $serie['reps_21s']??7; @endphp
                        <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" min="1" data-key="reps_21s" value="{{ $r21 }}" placeholder="7" oninput="actualizar21sNota(this)"></div>
                        <div class="campo-wrap"><label>Peso</label>
                            <div class="peso-group">
                                <input class="campo-input" type="number" step="0.5" data-key="peso_21s" value="{{ $serie['peso_21s']??$serie['peso']??'' }}" placeholder="–">
                                <select class="unidad-select" data-key="unidad_21s">
                                    <option value="kg" {{ ($serie['unidad_21s']??'kg')==='kg'?'selected':'' }}>kg</option>
                                    <option value="lb" {{ ($serie['unidad_21s']??'kg')==='lb'?'selected':'' }}>lb</option>
                                </select>
                            </div>
                        </div>
                        <div class="metodo-nota nota-21s">{{ $r21 }}+{{ $r21 }}+{{ $r21 }}</div>
                    </div>

                    {{-- 10+21 --}}
                    <div class="metodo-fields {{ $metodo==='10_21'?'active':'' }}" data-metodo="10_21">
                        <div class="campo-wrap"><label>P×10</label>
                            <div class="peso-group">
                                <input class="campo-input" type="number" step="0.5" data-key="peso_10" value="{{ $serie['peso_10']??'' }}" placeholder="–" oninput="calcular40(this)">
                                <select class="unidad-select" data-key="unidad_10">
                                    <option value="kg" {{ ($serie['unidad_10']??'kg')==='kg'?'selected':'' }}>kg</option>
                                    <option value="lb" {{ ($serie['unidad_10']??'kg')==='lb'?'selected':'' }}>lb</option>
                                </select>
                            </div>
                        </div>
                        <div class="campo-wrap"><label>P×21s</label>
                            <div class="peso-group">
                                <input class="campo-input peso-21-result" type="number" step="0.5" data-key="peso_21" value="{{ $serie['peso_21']??'' }}" placeholder="Auto">
                                <select class="unidad-select" data-key="unidad_21">
                                    <option value="kg" {{ ($serie['unidad_21']??'kg')==='kg'?'selected':'' }}>kg</option>
                                    <option value="lb" {{ ($serie['unidad_21']??'kg')==='lb'?'selected':'' }}>lb</option>
                                </select>
                            </div>
                        </div>
                        <div class="metodo-nota">−40%→21s</div>
                    </div>

                    {{-- ISOMETRÍA --}}
                    <div class="metodo-fields {{ $metodo==='isometria'?'active':'' }}" data-metodo="isometria">
                        <div class="campo-wrap"><label>Peso</label>
                            <div class="peso-group">
                                <input class="campo-input" type="number" step="0.5" data-key="peso_iso" value="{{ $serie['peso_iso']??$serie['peso']??'' }}" placeholder="–">
                                <select class="unidad-select" data-key="unidad_iso">
                                    <option value="kg" {{ ($serie['unidad_iso']??'kg')==='kg'?'selected':'' }}>kg</option>
                                    <option value="lb" {{ ($serie['unidad_iso']??'kg')==='lb'?'selected':'' }}>lb</option>
                                </select>
                            </div>
                        </div>
                        <div class="campo-wrap"><label>R/brazo</label><input class="campo-input" type="number" data-key="reps_brazo" value="{{ $serie['reps_brazo']??4 }}" placeholder="4"></div>
                        <div class="campo-wrap"><label>R/ambos</label><input class="campo-input" type="number" data-key="reps_ambos" value="{{ $serie['reps_ambos']??8 }}" placeholder="8"></div>
                    </div>

                    {{-- FORZADAS --}}
                    <div class="metodo-fields {{ $metodo==='forzadas'?'active':'' }}" data-metodo="forzadas">
                        <div class="campo-wrap"><label>R.solo</label><input class="campo-input" type="number" data-key="reps_fz" value="{{ $serie['reps']??'' }}" placeholder="–"></div>
                        <div class="campo-wrap"><label>R.asist</label><input class="campo-input" type="number" data-key="reps_asistidas" value="{{ $serie['reps_asistidas']??'' }}" placeholder="–"></div>
                        <div class="campo-wrap"><label>Peso</label>
                            <div class="peso-group">
                                <input class="campo-input" type="number" step="0.5" data-key="peso_fz" value="{{ $serie['peso']??'' }}" placeholder="–">
                                <select class="unidad-select" data-key="unidad_fz">
                                    <option value="kg" {{ ($serie['unidad_fz']??'kg')==='kg'?'selected':'' }}>kg</option>
                                    <option value="lb" {{ ($serie['unidad_fz']??'kg')==='lb'?'selected':'' }}>lb</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- PARCIALES --}}
                    <div class="metodo-fields {{ $metodo==='parciales'?'active':'' }}" data-metodo="parciales">
                        <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" data-key="reps_pc" value="{{ $serie['reps']??'' }}" placeholder="–"></div>
                        <div class="campo-wrap"><label>Peso</label>
                            <div class="peso-group">
                                <input class="campo-input" type="number" step="0.5" data-key="peso_pc" value="{{ $serie['peso']??'' }}" placeholder="–">
                                <select class="unidad-select" data-key="unidad_pc">
                                    <option value="kg" {{ ($serie['unidad_pc']??'kg')==='kg'?'selected':'' }}>kg</option>
                                    <option value="lb" {{ ($serie['unidad_pc']??'kg')==='lb'?'selected':'' }}>lb</option>
                                </select>
                            </div>
                        </div>
                        <div class="metodo-nota">Parcial</div>
                    </div>

                    {{-- NEGATIVAS --}}
                    <div class="metodo-fields {{ $metodo==='negativas'?'active':'' }}" data-metodo="negativas">
                        <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" data-key="reps_ng" value="{{ $serie['reps']??'' }}" placeholder="–"></div>
                        <div class="campo-wrap"><label>Peso</label>
                            <div class="peso-group">
                                <input class="campo-input" type="number" step="0.5" data-key="peso_ng" value="{{ $serie['peso']??'' }}" placeholder="–">
                                <select class="unidad-select" data-key="unidad_ng">
                                    <option value="kg" {{ ($serie['unidad_ng']??'kg')==='kg'?'selected':'' }}>kg</option>
                                    <option value="lb" {{ ($serie['unidad_ng']??'kg')==='lb'?'selected':'' }}>lb</option>
                                </select>
                            </div>
                        </div>
                        <div class="metodo-nota">Excéntrica</div>
                    </div>

                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach
</div>
@endforeach

</div>{{-- fin contenedor-bloques --}}

{{-- ↓↓ Barra duplicada abajo ↓↓ --}}
<div class="add-block-bar" style="margin-top: 10px;">
    <button type="button" onclick="agregarBloque('monoserie',1)" class="add-block-btn">＋ Lineal</button>
    <button type="button" onclick="agregarBloque('biserie',2)"   class="add-block-btn">＋ Biserie</button>
    <button type="button" onclick="agregarBloque('triserie',3)"  class="add-block-btn">＋ Triserie</button>
    <button type="button" onclick="agregarBloque('circuito',4)"  class="add-block-btn">＋ Circuito</button>
</div>



<a href="{{ route('entrenador.rutina.pdf', [$cliente->id, $semana, $dia]) }}"
   target="_blank" class="btn-pdf">📄 Exportar PDF</a>
<button type="button" onclick="guardarRutina()" class="btn-guardar" id="btn-guardar">
    💾 &nbsp;Guardar rutina
</button>
</form>

<script>
const ejerciciosPorGrupo = @json($ejerciciosPorGrupo);
let contador = Date.now();
const contenedor = document.getElementById('contenedor-bloques');

/* ══════════════════════════════════════════
   GUARDAR — serializa todo como 1 JSON
══════════════════════════════════════════ */
function guardarRutina() {
    const btn = document.getElementById('btn-guardar');
    btn.disabled = true;
    btn.textContent = '⏳ Guardando...';

    const bloques = {};
    let orden = 0;

    document.querySelectorAll('#contenedor-bloques .bloque').forEach(bloque => {
        const grupo = bloque.dataset.grupo;
        const tipo  = bloque.dataset.tipo;
        if (!grupo) return;

        bloques[grupo] = { tipo, orden: orden++, ejercicios: {} };

        bloque.querySelectorAll('.ejercicio-row').forEach((ejRow, i) => {
            const segmento     = ejRow.querySelector('.segmento-select')?.value ?? '';
            const ejercicio_id = ejRow.querySelector('.ejercicio-id-input')?.value ?? '';
            const series       = [];

            ejRow.querySelectorAll('[data-serie]').forEach(col => {
                const metodo = col.querySelector('.metodo-select')?.value ?? 'normal';
                const s = { metodo };

                // Recolectar todos los data-key del col
                col.querySelectorAll('[data-key]').forEach(el => {
                    s[el.dataset.key] = el.value;
                });

                series.push(s);
            });

            bloques[grupo].ejercicios[i] = { segmento, ejercicio_id, series };
        });
    });

    document.getElementById('datos_json').value = JSON.stringify({ bloques });
    document.getElementById('form-rutina').submit();
}

/* ── MODAL ── */
function abrirModal()  { document.getElementById('modalMetodos').classList.add('open'); document.body.style.overflow = 'hidden'; }
function cerrarModal() { document.getElementById('modalMetodos').classList.remove('open'); document.body.style.overflow = ''; }
document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarModal(); });

/* ── DROPDOWN ── */
function toggleDropdown(trigger) {
    const wrapper  = trigger.closest('.ej-select-wrapper');
    const dropdown = wrapper.querySelector('.ej-select-dropdown');
    const isOpen   = dropdown.classList.contains('open');
    document.querySelectorAll('.ej-select-dropdown.open').forEach(d => d.classList.remove('open'));
    if (!isOpen) dropdown.classList.add('open');
}
function seleccionarEjercicio(option) {
    const wrapper = option.closest('.ej-select-wrapper');
    const trigger = wrapper.querySelector('.ej-select-trigger');
    const hidden  = document.getElementById(wrapper.dataset.target);
    hidden.value  = option.dataset.value;
    const img     = trigger.querySelector('img');
    const label   = trigger.querySelector('.ej-trigger-nombre, .ej-trigger-placeholder');
    if (option.dataset.imagen) { img.src = option.dataset.imagen; img.style.display = 'block'; }
    else                       { img.src = ''; img.style.display = 'none'; }
    label.className   = 'ej-trigger-nombre';
    label.textContent = option.dataset.nombre;
    wrapper.querySelectorAll('.ej-select-option').forEach(o => o.classList.remove('selected'));
    option.classList.add('selected');
    wrapper.querySelector('.ej-select-dropdown').classList.remove('open');
}
document.addEventListener('click', e => {
    if (!e.target.closest('.ej-select-wrapper'))
        document.querySelectorAll('.ej-select-dropdown.open').forEach(d => d.classList.remove('open'));
});

/* ── SEGMENTO ── */
function onSegmentoChange(select) {
    const ejId    = select.dataset.ej;
    const seg     = select.value;
    const wrapper = document.querySelector(`.ej-select-wrapper[data-target="${ejId}"]`);
    if (!wrapper) return;
    const trigger  = wrapper.querySelector('.ej-select-trigger');
    const img      = trigger.querySelector('img');
    const label    = trigger.querySelector('.ej-trigger-nombre, .ej-trigger-placeholder');
    const dropdown = wrapper.querySelector('.ej-select-dropdown');
    const hidden   = document.getElementById(ejId);
    hidden.value = ''; img.src = ''; img.style.display = 'none';
    label.className = 'ej-trigger-placeholder'; label.textContent = '-- Ejercicio --';
    dropdown.innerHTML = '';
    (ejerciciosPorGrupo[seg] ?? []).forEach(e => {
        const url = e.imagen ? `/storage/${e.imagen}` : '';
        const div = document.createElement('div');
        div.className = 'ej-select-option';
        div.dataset.value  = e.id;
        div.dataset.nombre = e.nombre;
        div.dataset.imagen = url;
        div.onclick = () => seleccionarEjercicio(div);
        div.innerHTML = url
            ? `<img src="${url}" alt="${e.nombre}"><span>${e.nombre}</span>`
            : `<div class="ej-no-img">Sin img</div><span>${e.nombre}</span>`;
        dropdown.appendChild(div);
    });
}
document.addEventListener('change', e => {
    if (e.target.classList.contains('segmento-select')) onSegmentoChange(e.target);
});

/* ── ORDEN ── */
function actualizarOrden() {
    document.querySelectorAll('#contenedor-bloques .bloque').forEach((b, i) => {
        b.dataset.orden = i;
    });
}

/* ── MÉTODO ── */
function cambiarMetodo(select) {
    select.closest('.serie-col').querySelectorAll('.metodo-fields').forEach(d =>
        d.classList.toggle('active', d.dataset.metodo === select.value));
}

/* ── 40% ── */
function calcular40(input) {
    const peso10 = parseFloat(input.value) || 0;
    const campo  = input.closest('.serie-col').querySelector('.peso-21-result');
    if (campo) campo.value = peso10 > 0 ? Math.round(peso10 * 0.6 * 2) / 2 : '';
}

/* ── NOTA DESCENDENTE ── */
function actualizar888Nota(input) {
    const nota = input.closest('.metodo-fields').querySelector('.nota-888');
    if (nota) nota.textContent = `${input.value || '?'} c/u·desc.`;
}

/* ── NOTA 3 RANGOS ── */
function actualizar21sNota(input) {
    const nota = input.closest('.metodo-fields').querySelector('.nota-21s');
    const r = input.value || '?';
    if (nota) nota.textContent = `${r}+${r}+${r}`;
}

/* ── CABECERA ── */
function actualizarHeader(grupo, numSeries) {
    const header = document.querySelector(`.series-header-row[data-header="${grupo}"] .col-series-headers`);
    if (!header) return;
    header.innerHTML = '';
    for (let s = 0; s < numSeries; s++) {
        const div = document.createElement('div');
        div.className   = 'serie-header-col';
        div.textContent = `S${s + 1}`;
        header.appendChild(div);
    }
}

/* ── HTML SERIE-COLUMNA ── */
function htmlSerieCol(ex = {}) {
    const m = ex.metodo ?? 'normal';
    const a = (k) => m === k ? 'active' : '';
    const v = (k, def = '') => ex[k] ?? def;

    const pesoGroup = (label, pesoKey, unidadKey) => `
        <div class="campo-wrap">
            <label>${label}</label>
            <div class="peso-group">
                <input class="campo-input" type="number" step="0.5"
                    data-key="${pesoKey}" value="${v(pesoKey)}" placeholder="–">
                <select class="unidad-select" data-key="${unidadKey}">
                    <option value="kg" ${v(unidadKey,'kg')==='kg'?'selected':''}>kg</option>
                    <option value="lb" ${v(unidadKey,'kg')==='lb'?'selected':''}>lb</option>
                </select>
            </div>
        </div>`;

    const r21 = v('reps_21s', '7');

    return `
    <div class="serie-col" data-serie>
        <select class="metodo-select" onchange="cambiarMetodo(this)">
            <option value="normal"    ${m==='normal'    ?'selected':''}>Normal</option>
            <option value="888"       ${m==='888'       ?'selected':''}>Descend.</option>
            <option value="restpause" ${m==='restpause' ?'selected':''}>Rest-pause</option>
            <option value="21s"       ${m==='21s'       ?'selected':''}>3 Rangos</option>
            <option value="10_21"     ${m==='10_21'     ?'selected':''}>10+21s</option>
            <option value="isometria" ${m==='isometria' ?'selected':''}>Isometría</option>
            <option value="forzadas"  ${m==='forzadas'  ?'selected':''}>Forzadas</option>
            <option value="parciales" ${m==='parciales' ?'selected':''}>Parciales</option>
            <option value="negativas" ${m==='negativas' ?'selected':''}>Negativas</option>
        </select>

        <div class="metodo-fields ${a('normal')}" data-metodo="normal">
            <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" data-key="reps" value="${v('reps')}" placeholder="–"></div>
            ${pesoGroup('Peso','peso','unidad')}
        </div>

        <div class="metodo-fields ${a('888')}" data-metodo="888">
            <div class="campo-wrap"><label>Reps</label>
                <input class="campo-input" type="number" min="1" data-key="reps_888"
                    value="${v('reps_888','8')}" placeholder="8" oninput="actualizar888Nota(this)">
            </div>
            ${pesoGroup('P1','peso1','unidad1')}
            ${pesoGroup('P2','peso2','unidad2')}
            ${pesoGroup('P3','peso3','unidad3')}
            <div class="metodo-nota nota-888">${v('reps_888','8')} c/u·desc.</div>
        </div>

        <div class="metodo-fields ${a('restpause')}" data-metodo="restpause">
            <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" data-key="reps_rp" value="${v('reps_rp')}" placeholder="–"></div>
            ${pesoGroup('Peso','peso_rp','unidad_rp')}
            <div class="campo-wrap"><label>Desc(s)</label><input class="campo-input" type="number" data-key="descanso" value="${v('descanso','15')}" placeholder="15"></div>
            <div class="metodo-nota">Fallo→pausa</div>
        </div>

        <div class="metodo-fields ${a('21s')}" data-metodo="21s">
            <div class="campo-wrap"><label>Reps</label>
                <input class="campo-input" type="number" min="1" data-key="reps_21s"
                    value="${r21}" placeholder="7" oninput="actualizar21sNota(this)">
            </div>
            ${pesoGroup('Peso','peso_21s','unidad_21s')}
            <div class="metodo-nota nota-21s">${r21}+${r21}+${r21}</div>
        </div>

        <div class="metodo-fields ${a('10_21')}" data-metodo="10_21">
            <div class="campo-wrap"><label>P×10</label>
                <div class="peso-group">
                    <input class="campo-input" type="number" step="0.5" data-key="peso_10"
                        value="${v('peso_10')}" placeholder="–" oninput="calcular40(this)">
                    <select class="unidad-select" data-key="unidad_10">
                        <option value="kg" ${v('unidad_10','kg')==='kg'?'selected':''}>kg</option>
                        <option value="lb" ${v('unidad_10','kg')==='lb'?'selected':''}>lb</option>
                    </select>
                </div>
            </div>
            <div class="campo-wrap"><label>P×21s</label>
                <div class="peso-group">
                    <input class="campo-input peso-21-result" type="number" step="0.5" data-key="peso_21"
                        value="${v('peso_21')}" placeholder="Auto">
                    <select class="unidad-select" data-key="unidad_21">
                        <option value="kg" ${v('unidad_21','kg')==='kg'?'selected':''}>kg</option>
                        <option value="lb" ${v('unidad_21','kg')==='lb'?'selected':''}>lb</option>
                    </select>
                </div>
            </div>
            <div class="metodo-nota">−40%→21s</div>
        </div>

        <div class="metodo-fields ${a('isometria')}" data-metodo="isometria">
            ${pesoGroup('Peso','peso_iso','unidad_iso')}
            <div class="campo-wrap"><label>R/brazo</label><input class="campo-input" type="number" data-key="reps_brazo" value="${v('reps_brazo','4')}" placeholder="4"></div>
            <div class="campo-wrap"><label>R/ambos</label><input class="campo-input" type="number" data-key="reps_ambos" value="${v('reps_ambos','8')}" placeholder="8"></div>
        </div>

        <div class="metodo-fields ${a('forzadas')}" data-metodo="forzadas">
            <div class="campo-wrap"><label>R.solo</label><input class="campo-input" type="number" data-key="reps_fz" value="${v('reps_fz')}" placeholder="–"></div>
            <div class="campo-wrap"><label>R.asist</label><input class="campo-input" type="number" data-key="reps_asistidas" value="${v('reps_asistidas')}" placeholder="–"></div>
            ${pesoGroup('Peso','peso_fz','unidad_fz')}
        </div>

        <div class="metodo-fields ${a('parciales')}" data-metodo="parciales">
            <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" data-key="reps_pc" value="${v('reps_pc')}" placeholder="–"></div>
            ${pesoGroup('Peso','peso_pc','unidad_pc')}
            <div class="metodo-nota">Parcial</div>
        </div>

        <div class="metodo-fields ${a('negativas')}" data-metodo="negativas">
            <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" data-key="reps_ng" value="${v('reps_ng')}" placeholder="–"></div>
            ${pesoGroup('Peso','peso_ng','unidad_ng')}
            <div class="metodo-nota">Excéntrica</div>
        </div>
    </div>`;
}

/* ── GENERAR SERIES ── */
function generarSeriesBloque(input, grupo, cantidad) {
    const n = parseInt(input.value) || 0;
    actualizarHeader(grupo, n);
    for (let i = 0; i < cantidad; i++) {
        const container = document.querySelector(`.series-cols[data-grupo="${grupo}"][data-ej="${i}"]`);
        if (!container) continue;
        const exArr = [];
        container.querySelectorAll('[data-serie]').forEach(col => {
            const ex = { metodo: col.querySelector('.metodo-select')?.value ?? 'normal' };
            col.querySelectorAll('[data-key]').forEach(el => { ex[el.dataset.key] = el.value; });
            exArr.push(ex);
        });
        container.innerHTML = '';
        for (let s = 0; s < n; s++)
            container.insertAdjacentHTML('beforeend', htmlSerieCol(exArr[s] ?? {}));
    }
}

/* ── AGREGAR BLOQUE ── */
function agregarBloque(tipo, cantidad) {
    const grupo  = 'G' + contador++;
    const nums   = ['1','2','3','4'];
    const letraClasses = ['ej-letra-a','ej-letra-b','ej-letra-c','ej-letra-d'];
    const bgClasses    = ['ej-bg-a','ej-bg-b','ej-bg-c','ej-bg-d'];
    const opts = Object.keys(ejerciciosPorGrupo).map(s => `<option value="${s}">${s}</option>`).join('');

    let html = `
        <div class="bloque" data-grupo="${grupo}" data-tipo="${tipo}">
            <div class="bloque-header">
                <span class="bloque-tipo tipo-${tipo.toLowerCase()}">${tipo.toUpperCase()}</span>
                <div class="bloque-series-count">Series:
                    <input type="number" min="1" placeholder="–"
                        onchange="generarSeriesBloque(this, '${grupo}', ${cantidad})">
                </div>
                <button type="button" class="btn-remove"
                    onclick="this.closest('.bloque').remove(); actualizarOrden();">✕</button>
            </div>
            <div class="series-header-row" data-header="${grupo}">
                <div class="col-info-header">Ejercicio</div>
                <div class="col-series-headers"></div>
            </div>`;

    for (let i = 0; i < cantidad; i++) {
        const ejId   = `ej-${grupo}-${i}`;
        const num    = nums[i] ?? (i + 1);
        const lClass = letraClasses[i] ?? 'ej-letra-a';
        const bgClass= bgClasses[i]    ?? 'ej-bg-a';
        html += `
            <div class="ejercicio-row ${bgClass}">
                <div class="ej-letra ${lClass}">${num}</div>
                <div class="col-segmento">
                    <div class="field-label">Segmento</div>
                    <select class="segmento-select" data-ej="${ejId}" onchange="onSegmentoChange(this)">
                        <option value="">-- Segmento --</option>${opts}
                    </select>
                </div>
                <div class="col-ejercicio">
                    <div class="field-label">Ejercicio</div>
                    <input type="hidden" id="${ejId}" class="ejercicio-id-input" value="">
                    <div class="ej-select-wrapper" data-target="${ejId}">
                        <div class="ej-select-trigger" onclick="toggleDropdown(this)">
                            <img src="" alt="" style="display:none;">
                            <span class="ej-trigger-placeholder">-- Ejercicio --</span>
                            <span class="ej-trigger-arrow">▼</span>
                        </div>
                        <div class="ej-select-dropdown"></div>
                    </div>
                </div>
                <div class="col-series">
                    <div class="series-cols" data-grupo="${grupo}" data-ej="${i}"></div>
                </div>
            </div>`;
    }

    html += '</div>';
    contenedor.insertAdjacentHTML('beforeend', html);
    actualizarOrden();
}
</script>

@endsection

