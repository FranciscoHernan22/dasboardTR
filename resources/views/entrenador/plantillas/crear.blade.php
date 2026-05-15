@extends('layouts.entrenador')
@section('titulo', 'Nueva Plantilla')
@section('contenido')

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

<style>
:root {
    --bg:#f4f5f7; --surface:#ffffff; --border:#e2e5ea; --border2:#d0d5dd;
    --text:#111827; --muted:#6b7280; --accent:#2563eb; --accent-l:#eff6ff;
    --danger:#ef4444; --radius:10px;
    --ej-a:#ffffff; --ej-b:#f8f9fb; --ej-c:#f4f6f9; --ej-d:#f0f3f7;
    --ej-e:#eef0f4; --ej-f:#eaf0f6; --ej-g:#e8edf2; --ej-h:#e5ebf0;
    --ej-i:#e2e8ee; --ej-j:#dfe5ec; --ej-k:#dce2ea; --ej-l:#d9dfe8;
}
* { box-sizing: border-box; }
body, .entrenador-content { font-family:'DM Sans',sans-serif; background:var(--bg); color:var(--text); }
.page-header { display:flex; align-items:center; gap:10px; margin-bottom:16px; padding-bottom:12px; border-bottom:2px solid var(--border); }
.page-header h2 { font-size:1.1rem; font-weight:700; margin:0; }
.badge { font-size:0.63rem; font-weight:700; background:var(--accent-l); color:var(--accent); border:1px solid #bfdbfe; padding:2px 8px; border-radius:99px; text-transform:uppercase; letter-spacing:.05em; }
.nombre-plantilla-wrap { background:var(--surface); border:1.5px solid var(--border); border-radius:var(--radius); padding:14px 16px; margin-bottom:14px; display:flex; flex-direction:column; gap:8px; }
.nombre-plantilla-wrap label { font-size:0.72rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.06em; }
.nombre-input { width:100%; border:1px solid var(--border2); border-radius:6px; padding:8px 10px; font-size:0.9rem; font-family:'DM Sans',sans-serif; color:var(--text); }
.nombre-input:focus { outline:none; border-color:var(--accent); }
.desc-input { width:100%; border:1px solid var(--border2); border-radius:6px; padding:6px 10px; font-size:0.82rem; font-family:'DM Sans',sans-serif; color:var(--text); resize:none; }
.desc-input:focus { outline:none; border-color:var(--accent); }
.add-block-bar { display:flex; gap:8px; margin-bottom:14px; flex-wrap:wrap; }
.add-block-btn { flex:1; min-width:80px; padding:7px 4px; background:var(--surface); border:1.5px dashed var(--border2); border-radius:7px; font-size:0.72rem; font-weight:600; color:var(--muted); cursor:pointer; transition:all .13s; }
.add-block-btn:hover { background:var(--accent-l); border-color:var(--accent); color:var(--accent); }

/* Modal circuito */
.modal-circ-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:10000; align-items:center; justify-content:center; }
.modal-circ-overlay.open { display:flex; }
.modal-circ-box { background:white; border-radius:14px; width:100%; max-width:320px; padding:24px; box-shadow:0 20px 60px rgba(0,0,0,.2); }
.modal-circ-box h3 { font-size:1rem; font-weight:700; margin:0 0 6px; }
.modal-circ-box p { font-size:0.8rem; color:var(--muted); margin:0 0 16px; }
.circ-num-input { width:100%; border:1.5px solid var(--border2); border-radius:8px; padding:10px; font-size:1.2rem; font-family:'DM Mono',monospace; text-align:center; color:var(--text); margin-bottom:14px; }
.circ-num-input:focus { outline:none; border-color:var(--accent); }
.circ-btns { display:flex; gap:8px; }
.circ-btn-cancel { flex:1; padding:8px; border:1px solid var(--border2); border-radius:7px; background:white; color:var(--muted); font-size:0.82rem; font-weight:600; cursor:pointer; font-family:'DM Sans',sans-serif; }
.circ-btn-ok { flex:1; padding:8px; border:none; border-radius:7px; background:var(--accent); color:white; font-size:0.82rem; font-weight:600; cursor:pointer; font-family:'DM Sans',sans-serif; }
.circ-btn-ok:hover { background:#1d4ed8; }

.bloque { background:var(--surface); border:1.5px solid var(--border); border-radius:var(--radius); margin-bottom:10px; box-shadow:0 1px 4px rgba(0,0,0,.06); overflow:visible; }
.bloque-header { display:flex; align-items:center; gap:8px; padding:7px 12px; border-bottom:1px solid var(--border); background:#f5f6f8; border-radius:var(--radius) var(--radius) 0 0; }
.bloque-tipo { font-size:0.6rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; padding:2px 8px; border-radius:99px; flex-shrink:0; }
.tipo-monoserie { background:#dbeafe; color:#1d4ed8; }
.tipo-biserie   { background:#d1fae5; color:#065f46; }
.tipo-triserie  { background:#fef3c7; color:#92400e; }
.tipo-circuito  { background:#fce7f3; color:#9d174d; }
.bloque-series-count { display:flex; align-items:center; gap:5px; font-size:0.72rem; color:var(--muted); margin-left:auto; }
.bloque-series-count input { width:42px; border:1px solid var(--border2); border-radius:5px; padding:2px 5px; font-size:0.74rem; font-family:'DM Mono',monospace; text-align:center; color:var(--text); }
.btn-remove { width:24px; height:24px; border-radius:5px; background:#fee2e2; border:none; color:var(--danger); cursor:pointer; font-size:0.75rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.btn-remove:hover { background:#fca5a5; }
.series-header-row { display:flex; border-bottom:2px solid var(--border); background:#f0f2f5; }
.series-header-row .col-info-header { width:265px; flex-shrink:0; border-right:1px solid var(--border); padding:5px 10px; font-size:0.6rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.06em; display:flex; align-items:center; }
.series-header-row .col-series-headers { flex:1; display:flex; padding:0; min-width:0; }
.serie-header-col { flex:1; text-align:center; padding:5px 4px; font-size:0.65rem; font-weight:700; color:var(--accent); background:var(--accent-l); border-right:1px solid #bfdbfe; letter-spacing:.04em; text-transform:uppercase; }
.serie-header-col:last-child { border-right:none; }
.ejercicio-row { display:flex; border-bottom:1px solid var(--border); min-height:54px; align-items:stretch; }
.ejercicio-row:last-of-type { border-bottom:none; }
.ej-letra { width:22px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:0.65rem; font-weight:800; border-right:2px solid var(--border); }
.ej-letra-a { color:#1d4ed8; background:#eff6ff; }
.ej-letra-b { color:#065f46; background:#f0fdf4; }
.ej-letra-c { color:#92400e; background:#fffbeb; }
.ej-letra-d { color:#9d174d; background:#fdf2f8; }
.ej-letra-e { color:#1d4ed8; background:#e0f2fe; }
.ej-letra-f { color:#065f46; background:#dcfce7; }
.ej-letra-g { color:#92400e; background:#fef9c3; }
.ej-letra-h { color:#9d174d; background:#fce7f3; }
.ej-letra-i { color:#1e40af; background:#dbeafe; }
.ej-letra-j { color:#166534; background:#d1fae5; }
.ej-letra-k { color:#854d0e; background:#fef3c7; }
.ej-letra-l { color:#831843; background:#fdf2f8; }
.ej-bg-a { background:var(--ej-a); } .ej-bg-b { background:var(--ej-b); }
.ej-bg-c { background:var(--ej-c); } .ej-bg-d { background:var(--ej-d); }
.ej-bg-e { background:var(--ej-e); } .ej-bg-f { background:var(--ej-f); }
.ej-bg-g { background:var(--ej-g); } .ej-bg-h { background:var(--ej-h); }
.ej-bg-i { background:var(--ej-i); } .ej-bg-j { background:var(--ej-j); }
.ej-bg-k { background:var(--ej-k); } .ej-bg-l { background:var(--ej-l); }
.col-segmento { width:110px; flex-shrink:0; padding:7px 9px; border-right:1px solid var(--border); }
.col-ejercicio { width:133px; flex-shrink:0; padding:7px 9px; border-right:1px solid var(--border); }
.col-series { flex:1; padding:6px 6px; min-width:0; display:flex; align-items:stretch; }
.field-label { font-size:0.58rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.06em; margin-bottom:3px; }
.segmento-select { width:100%; border:1px solid var(--border2); border-radius:5px; padding:4px 5px; font-size:0.73rem; font-family:'DM Sans',sans-serif; color:var(--text); background:white; }
.ej-select-wrapper { position:relative; user-select:none; }
.ej-select-trigger { display:flex; align-items:center; gap:5px; border:1px solid var(--border2); border-radius:5px; padding:3px 5px; cursor:pointer; background:white; min-height:32px; transition:border-color .12s; }
.ej-select-trigger:hover { border-color:var(--accent); }
.ej-select-trigger img { width:30px; height:30px; object-fit:cover; border-radius:4px; flex-shrink:0; }
.ej-trigger-nombre { font-size:0.7rem; font-weight:600; color:var(--text); flex:1; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; line-height:1.2; }
.ej-trigger-placeholder { font-size:0.7rem; color:var(--muted); flex:1; }
.ej-trigger-arrow { color:var(--muted); font-size:0.55rem; flex-shrink:0; }
.ej-select-dropdown { display:none; position:absolute; top:calc(100% + 3px); left:0; width:260px; background:white; border:1.5px solid var(--border); border-radius:8px; box-shadow:0 8px 24px rgba(0,0,0,.14); z-index:9999; max-height:280px; overflow-y:auto; }
.ej-select-dropdown.open { display:block; }
.ej-select-option { display:flex; align-items:center; gap:8px; padding:6px 10px; cursor:pointer; font-size:0.8rem; border-bottom:1px solid #f3f4f6; transition:background .1s; }
.ej-select-option:hover { background:var(--accent-l); }
.ej-select-option.selected { background:var(--accent-l); font-weight:600; color:var(--accent); }
.ej-select-option img { width:46px; height:46px; object-fit:cover; border-radius:5px; flex-shrink:0; }
.ej-no-img { width:46px; height:46px; border-radius:5px; background:var(--bg); display:flex; align-items:center; justify-content:center; color:var(--muted); font-size:0.58rem; border:1px dashed var(--border2); flex-shrink:0; }
.series-cols { display:flex; flex-direction:row; gap:5px; width:100%; align-items:stretch; }
.serie-col { flex:1; display:flex; flex-direction:column; align-items:center; gap:3px; background:white; border:1px solid var(--border); border-radius:6px; padding:5px 4px; min-width:0; }
.metodo-select { width:100%; border:1px solid var(--border2); border-radius:4px; padding:2px 3px; font-size:0.62rem; font-family:'DM Sans',sans-serif; color:var(--muted); background:white; text-align:center; }
.metodo-fields { display:none; flex-direction:column; gap:3px; width:100%; }
.metodo-fields.active { display:flex; }
.campo-wrap { display:flex; flex-direction:column; align-items:center; gap:1px; width:100%; }
.campo-wrap label { font-size:0.54rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.04em; }
.campo-input { width:100%; border:1px solid var(--border2); border-radius:4px; padding:3px 4px; font-size:0.78rem; font-family:'DM Mono',monospace; color:var(--text); text-align:center; }
.campo-input:focus { outline:none; border-color:var(--accent); }
.metodo-nota { font-size:0.57rem; color:var(--accent); background:var(--accent-l); border-radius:3px; padding:2px 4px; text-align:center; width:100%; line-height:1.3; }
.btn-guardar { display:inline-flex; align-items:center; gap:6px; background:var(--accent); color:white; font-family:'DM Sans',sans-serif; font-size:0.87rem; font-weight:600; padding:9px 24px; border:none; border-radius:var(--radius); cursor:pointer; box-shadow:0 2px 8px rgba(37,99,235,.3); transition:all .14s; margin-top:14px; }
.btn-guardar:hover { background:#1d4ed8; transform:translateY(-1px); }
.btn-guardar:disabled { background:#93c5fd; cursor:not-allowed; transform:none; }
.peso-group { display:flex; align-items:center; gap:2px; width:100%; }
.peso-group .campo-input { flex:1; min-width:0; }
.unidad-select { width:34px; flex-shrink:0; border:1px solid var(--border2); border-radius:4px; padding:3px 1px; font-size:0.58rem; font-family:'DM Sans',sans-serif; color:var(--muted); background:white; text-align:center; cursor:pointer; }
</style>

{{-- Modal cantidad ejercicios circuito --}}
<div class="modal-circ-overlay" id="modalCircuito" onclick="if(event.target===this)cerrarModalCircuito()">
    <div class="modal-circ-box">
        <h3>Circuito</h3>
        <p>¿Cuántos ejercicios? (2 – 12)</p>
        <input type="number" class="circ-num-input" id="circuitoNum"
               min="2" max="12" value="4" placeholder="4">
        <div class="circ-btns">
            <button class="circ-btn-cancel" onclick="cerrarModalCircuito()">Cancelar</button>
            <button class="circ-btn-ok"     onclick="confirmarCircuito()">Agregar</button>
        </div>
    </div>
</div>

{{-- HEADER --}}
<div class="page-header">
    <a href="{{ route('entrenador.plantillas.index') }}"
       style="font-size:1.2rem;color:var(--muted);text-decoration:none;line-height:1">‹</a>
    <h2>Nueva plantilla</h2>
    <span class="badge">Generador</span>
</div>

<form method="POST" action="{{ route('entrenador.plantillas.guardar') }}" id="form-plantilla">
@csrf
<input type="hidden" name="datos_json" id="datos_json">

<div class="nombre-plantilla-wrap">
    <div>
        <label>Nombre de la plantilla *</label>
        <input type="text" name="nombre" class="nombre-input"
               placeholder="Ej: Hipertrofia Push · 4 series" required>
    </div>
    <div>
        <label>Descripción (opcional)</label>
        <textarea name="descripcion" class="desc-input" rows="2"
                  placeholder="Ej: Rutina de empuje para nivel intermedio"></textarea>
    </div>
</div>

<div id="contenedor-bloques"></div>

<div class="add-block-bar">
    <button type="button" onclick="agregarBloque('monoserie',1)" class="add-block-btn">＋ Lineal</button>
    <button type="button" onclick="agregarBloque('biserie',2)"   class="add-block-btn">＋ Biserie</button>
    <button type="button" onclick="agregarBloque('triserie',3)"  class="add-block-btn">＋ Triserie</button>
    <button type="button" onclick="abrirModalCircuito()"         class="add-block-btn">＋ Circuito</button>
</div>

<button type="button" onclick="guardarPlantilla()" class="btn-guardar" id="btn-guardar">
    💾 &nbsp;Guardar plantilla
</button>
</form>

<script>
const ejerciciosPorGrupo = @json($ejerciciosPorGrupo);
let contador = Date.now();
const contenedor = document.getElementById('contenedor-bloques');

/* ── Modal circuito ── */
function abrirModalCircuito() {
    document.getElementById('circuitoNum').value = 4;
    document.getElementById('modalCircuito').classList.add('open');
    setTimeout(() => document.getElementById('circuitoNum').focus(), 50);
}
function cerrarModalCircuito() {
    document.getElementById('modalCircuito').classList.remove('open');
}
function confirmarCircuito() {
    const n = parseInt(document.getElementById('circuitoNum').value) || 4;
    const cantidad = Math.min(12, Math.max(2, n));
    cerrarModalCircuito();
    agregarBloque('circuito', cantidad);
}
document.getElementById('circuitoNum').addEventListener('keydown', e => {
    if (e.key === 'Enter') confirmarCircuito();
    if (e.key === 'Escape') cerrarModalCircuito();
});

/* ── Guardar ── */
function guardarPlantilla() {
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
                col.querySelectorAll('[data-key]').forEach(el => { s[el.dataset.key] = el.value; });
                series.push(s);
            });

            bloques[grupo].ejercicios[i] = { segmento, ejercicio_id, series };
        });
    });

    document.getElementById('datos_json').value = JSON.stringify({ bloques });
    document.getElementById('form-plantilla').submit();
}

/* ── Helpers dropdown ── */
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
    else { img.src = ''; img.style.display = 'none'; }
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

function actualizarOrden() {
    document.querySelectorAll('#contenedor-bloques .bloque').forEach((b, i) => { b.dataset.orden = i; });
}

function cambiarMetodo(select) {
    select.closest('.serie-col').querySelectorAll('.metodo-fields').forEach(d =>
        d.classList.toggle('active', d.dataset.metodo === select.value));
}

function calcular40(input) {
    const peso10 = parseFloat(input.value) || 0;
    const campo  = input.closest('.serie-col').querySelector('.peso-21-result');
    if (campo) campo.value = peso10 > 0 ? Math.round(peso10 * 0.6 * 2) / 2 : '';
}

function actualizar888Nota(input) {
    const nota = input.closest('.metodo-fields').querySelector('.nota-888');
    if (nota) nota.textContent = `${input.value || '?'} c/u·desc.`;
}

function actualizar21sNota(input) {
    const nota = input.closest('.metodo-fields').querySelector('.nota-21s');
    const r = input.value || '?';
    if (nota) nota.textContent = `${r}+${r}+${r}`;
}

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

function htmlSerieCol(ex = {}) {
    const m = ex.metodo ?? 'normal';
    const a = (k) => m === k ? 'active' : '';
    const v = (k, def = '') => ex[k] ?? def;
    const pesoGroup = (label, pesoKey, unidadKey) => `
        <div class="campo-wrap"><label>${label}</label>
            <div class="peso-group">
                <input class="campo-input" type="number" step="0.5" data-key="${pesoKey}" value="${v(pesoKey)}" placeholder="–">
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
            <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" min="1" data-key="reps_888" value="${v('reps_888','8')}" placeholder="8" oninput="actualizar888Nota(this)"></div>
            ${pesoGroup('P1','peso1','unidad1')}${pesoGroup('P2','peso2','unidad2')}${pesoGroup('P3','peso3','unidad3')}
            <div class="metodo-nota nota-888">${v('reps_888','8')} c/u·desc.</div>
        </div>
        <div class="metodo-fields ${a('restpause')}" data-metodo="restpause">
            <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" data-key="reps_rp" value="${v('reps_rp')}" placeholder="–"></div>
            ${pesoGroup('Peso','peso_rp','unidad_rp')}
            <div class="campo-wrap"><label>Desc(s)</label><input class="campo-input" type="number" data-key="descanso" value="${v('descanso','15')}" placeholder="15"></div>
            <div class="metodo-nota">Fallo→pausa</div>
        </div>
        <div class="metodo-fields ${a('21s')}" data-metodo="21s">
            <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" min="1" data-key="reps_21s" value="${r21}" placeholder="7" oninput="actualizar21sNota(this)"></div>
            ${pesoGroup('Peso','peso_21s','unidad_21s')}
            <div class="metodo-nota nota-21s">${r21}+${r21}+${r21}</div>
        </div>
        <div class="metodo-fields ${a('10_21')}" data-metodo="10_21">
            <div class="campo-wrap"><label>P×10</label>
                <div class="peso-group">
                    <input class="campo-input" type="number" step="0.5" data-key="peso_10" value="${v('peso_10')}" placeholder="–" oninput="calcular40(this)">
                    <select class="unidad-select" data-key="unidad_10">
                        <option value="kg" ${v('unidad_10','kg')==='kg'?'selected':''}>kg</option>
                        <option value="lb" ${v('unidad_10','kg')==='lb'?'selected':''}>lb</option>
                    </select>
                </div>
            </div>
            <div class="campo-wrap"><label>P×21s</label>
                <div class="peso-group">
                    <input class="campo-input peso-21-result" type="number" step="0.5" data-key="peso_21" value="${v('peso_21')}" placeholder="Auto">
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

/* ── letras y colores hasta 12 ── */
const NUMS   = ['1','2','3','4','5','6','7','8','9','10','11','12'];
const LETRAS = ['ej-letra-a','ej-letra-b','ej-letra-c','ej-letra-d',
                'ej-letra-e','ej-letra-f','ej-letra-g','ej-letra-h',
                'ej-letra-i','ej-letra-j','ej-letra-k','ej-letra-l'];
const BGS    = ['ej-bg-a','ej-bg-b','ej-bg-c','ej-bg-d',
                'ej-bg-e','ej-bg-f','ej-bg-g','ej-bg-h',
                'ej-bg-i','ej-bg-j','ej-bg-k','ej-bg-l'];

function agregarBloque(tipo, cantidad) {
    const grupo = 'G' + contador++;
    const opts  = Object.keys(ejerciciosPorGrupo).map(s => `<option value="${s}">${s}</option>`).join('');

    let html = `
        <div class="bloque" data-grupo="${grupo}" data-tipo="${tipo}">
            <div class="bloque-header">
                <span class="bloque-tipo tipo-${tipo.toLowerCase()}">${tipo.toUpperCase()}
                    ${tipo === 'circuito' ? `<span style="opacity:.7;font-size:.55rem"> · ${cantidad} ej.</span>` : ''}
                </span>
                <div class="bloque-series-count">Series:
                    <input type="number" min="1" placeholder="–" onchange="generarSeriesBloque(this,'${grupo}',${cantidad})">
                </div>
                <button type="button" class="btn-remove" onclick="this.closest('.bloque').remove();actualizarOrden();">✕</button>
            </div>
            <div class="series-header-row" data-header="${grupo}">
                <div class="col-info-header">Ejercicio</div>
                <div class="col-series-headers"></div>
            </div>`;

    for (let i = 0; i < cantidad; i++) {
        const ejId   = `ej-${grupo}-${i}`;
        const lClass = LETRAS[i % LETRAS.length];
        const bgClass= BGS[i % BGS.length];
        html += `
            <div class="ejercicio-row ${bgClass}">
                <div class="ej-letra ${lClass}">${NUMS[i] ?? (i+1)}</div>
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