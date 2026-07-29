{{-- DESTINO: resources/views/ejercicios/importar.blade.php --}}
@extends('layouts.entrenador')
@section('titulo','Importar ejercicios')
@section('contenido')

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet">

<style>
:root { --bg:#f4f5f7; --surface:#fff; --border:#e2e5ea; --border2:#d0d5dd; --text:#111827; --muted:#6b7280; --accent:#2563eb; --accent-l:#eff6ff; --danger:#ef4444; --ok:#059669; --radius:10px; }
* { box-sizing:border-box; }
body, .entrenador-content { font-family:'DM Sans',sans-serif; background:var(--bg); color:var(--text); }

.imp-header { display:flex; align-items:center; gap:10px; margin-bottom:16px; flex-wrap:wrap; }
.imp-header h2 { font-size:1.1rem; font-weight:700; margin:0; }
.imp-header a { margin-left:auto; font-size:0.82rem; color:var(--muted); text-decoration:none; }
.imp-header a:hover { color:var(--accent); }

.imp-barra-progreso { display:flex; align-items:center; gap:10px; background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:10px 14px; margin-bottom:14px; font-size:0.8rem; color:var(--muted); }
.imp-barra-progreso .track { flex:1; height:6px; background:#e5e7eb; border-radius:99px; overflow:hidden; }
.imp-barra-progreso .fill { height:100%; background:var(--accent); width:0%; transition:width .2s; }

.imp-tabla-wrap { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; margin-bottom:14px; }
.imp-tabla { width:100%; border-collapse:collapse; font-size:0.82rem; }
.imp-tabla th { text-align:left; background:#fafbfc; padding:9px 10px; font-size:0.68rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.04em; border-bottom:1px solid var(--border); }
.imp-tabla td { padding:7px 10px; border-bottom:1px solid var(--border); vertical-align:middle; }
.imp-tabla tr:last-child td { border-bottom:none; }
.imp-tabla input[type="text"], .imp-tabla select { width:100%; border:1px solid var(--border2); border-radius:7px; padding:6px 8px; font-size:0.8rem; font-family:'DM Sans',sans-serif; }
.imp-tabla input[type="text"]:focus, .imp-tabla select:focus { outline:none; border-color:var(--accent); }

.imp-file-btn { display:inline-flex; align-items:center; gap:5px; padding:5px 9px; border:1px solid var(--border2); border-radius:7px; background:white; font-size:0.72rem; font-weight:600; color:var(--muted); cursor:pointer; white-space:nowrap; }
.imp-file-btn:hover { border-color:var(--accent); color:var(--accent); }
.imp-file-btn.ok { border-color:#bbf7d0; color:var(--ok); background:#f0fdf4; }
.imp-file-btn.subiendo { border-color:#bfdbfe; color:var(--accent); background:var(--accent-l); }
.imp-file-btn.error { border-color:#fecaca; color:var(--danger); background:#fef2f2; }

.imp-btn-quitar { width:26px; height:26px; border:1px solid #fecaca; border-radius:6px; background:white; color:var(--danger); cursor:pointer; }
.imp-btn-quitar:hover { background:var(--danger); color:white; }

.imp-acciones { display:flex; gap:8px; align-items:center; }
.btn-agregar-fila { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border:1.5px dashed var(--border2); border-radius:var(--radius); background:white; color:var(--muted); font-size:0.82rem; font-weight:600; cursor:pointer; }
.btn-agregar-fila:hover { border-color:var(--accent); color:var(--accent); }
.btn-agregar-varias { padding:8px 14px; border:1px solid var(--border2); border-radius:var(--radius); background:white; color:var(--muted); font-size:0.82rem; font-weight:600; cursor:pointer; }

.imp-guardar-wrap { margin-top:16px; display:flex; justify-content:flex-end; gap:10px; }
.btn-guardar-lote { padding:10px 22px; border:none; border-radius:var(--radius); background:var(--accent); color:white; font-size:0.88rem; font-weight:700; cursor:pointer; }
.btn-guardar-lote:disabled { opacity:.5; cursor:wait; }

.imp-status { font-size:0.8rem; margin-top:10px; text-align:right; }
.imp-status.error { color:var(--danger); }
.imp-status.ok { color:var(--ok); }
</style>

@php $r2Url = env('AWS_URL'); @endphp

<div class="imp-header">
    <h2>📥 Importar ejercicios en lote</h2>
    <a href="{{ route('entrenador.ejercicios.index') }}">← Volver al listado</a>
</div>

<div class="imp-barra-progreso" id="barraProgreso" style="display:none;">
    <span id="barraProgresoTexto">Subiendo videos… 0 de 0</span>
    <div class="track"><div class="fill" id="barraProgresoFill"></div></div>
</div>

<div class="imp-tabla-wrap">
    <table class="imp-tabla">
        <thead>
            <tr>
                <th style="width:34%">Nombre</th>
                <th style="width:22%">Segmento</th>
                <th style="width:110px">Imagen</th>
                <th style="width:110px">Video</th>
                <th style="width:36px"></th>
            </tr>
        </thead>
        <tbody id="tbodyFilas"></tbody>
    </table>
</div>

<div class="imp-acciones">
    <button type="button" class="btn-agregar-fila" onclick="agregarFila()">＋ Agregar fila</button>
    <button type="button" class="btn-agregar-varias" onclick="agregarVariasFilas()">＋ Agregar varias</button>
</div>

<div class="imp-guardar-wrap">
    <button type="button" class="btn-guardar-lote" id="btnGuardarLote" onclick="guardarLote()">Guardar todo</button>
</div>
<div class="imp-status" id="impStatus"></div>

<template id="tplSegmentoOptions">
    <option value="">-- Segmento --</option>
    @foreach($segmentosFijos as $valor => $label)
        <option value="{{ $valor }}">{{ $label }}</option>
    @endforeach
</template>

<script>
const SUBIR_VIDEO_URL   = "{{ route('entrenador.ejercicios.subirVideoTemporal') }}";
const IMPORTAR_LOTE_URL = "{{ route('entrenador.ejercicios.importarLote') }}";
const CSRF_TOKEN = "{{ csrf_token() }}";

let filaIndex = 0;
let videosSubiendo = 0;   // contador de subidas en curso
let videosTotalLote = 0;  // total de videos que se han intentado subir en la sesión actual
let colaSubida = [];      // cola simple para limitar concurrencia
const MAX_CONCURRENTES = 3;
let subidasActivas = 0;

function segmentoOptionsHTML() {
    return document.getElementById('tplSegmentoOptions').innerHTML;
}

function agregarFila() {
    const idx = filaIndex++;
    const tbody = document.getElementById('tbodyFilas');
    const tr = document.createElement('tr');
    tr.dataset.fila = idx;
    tr.innerHTML = `
        <td><input type="text" name="filas[${idx}][nombre]" placeholder="Nombre del ejercicio" required></td>
        <td>
            <select name="filas[${idx}][segmento]" required>${segmentoOptionsHTML()}</select>
        </td>
        <td>
            <label class="imp-file-btn" id="btnImg_${idx}">
                <i class="ti ti-camera-plus"></i> Imagen
                <input type="file" name="filas[${idx}][imagen]" accept="image/*" style="display:none;" onchange="marcarImagenLista(${idx}, this)">
            </label>
        </td>
        <td>
            <label class="imp-file-btn" id="btnVideo_${idx}">
                <i class="ti ti-video-plus"></i> Video
                <input type="file" accept="video/*" style="display:none;" onchange="subirVideoFila(${idx}, this)">
            </label>
            <input type="hidden" name="filas[${idx}][video_path]" id="videoPath_${idx}">
        </td>
        <td><button type="button" class="imp-btn-quitar" onclick="this.closest('tr').remove()"><i class="ti ti-x"></i></button></td>
    `;
    tbody.appendChild(tr);
}

function agregarVariasFilas() {
    const n = parseInt(prompt('¿Cuántas filas quieres agregar?', '10'), 10);
    if (!n || n < 1) return;
    for (let i = 0; i < n; i++) agregarFila();
}

function marcarImagenLista(idx, input) {
    const btn = document.getElementById(`btnImg_${idx}`);
    if (input.files[0]) {
        btn.classList.add('ok');
        btn.innerHTML = `<i class="ti ti-check"></i> ${input.files[0].name.slice(0,14)}`;
        btn.appendChild(input); // conservar el input dentro del label
    }
}

function actualizarBarraProgreso() {
    const barra = document.getElementById('barraProgreso');
    const texto = document.getElementById('barraProgresoTexto');
    const fill  = document.getElementById('barraProgresoFill');
    const pendientes = videosSubiendo;

    if (videosTotalLote === 0) { barra.style.display = 'none'; return; }

    barra.style.display = 'flex';
    const completados = videosTotalLote - pendientes;
    texto.textContent = pendientes > 0
        ? `Subiendo videos… ${completados} de ${videosTotalLote}`
        : `✓ ${videosTotalLote} video(s) subido(s)`;
    fill.style.width = `${(completados / videosTotalLote) * 100}%`;

    document.getElementById('btnGuardarLote').disabled = pendientes > 0;
}

function subirVideoFila(idx, input) {
    const file = input.files[0];
    if (!file) return;
    colaSubida.push({ idx, file, input });
    videosSubiendo++;
    videosTotalLote++;
    actualizarBarraProgreso();
    procesarColaSubida();
}

function procesarColaSubida() {
    while (subidasActivas < MAX_CONCURRENTES && colaSubida.length > 0) {
        const item = colaSubida.shift();
        subidasActivas++;
        ejecutarSubidaVideo(item).finally(() => {
            subidasActivas--;
            procesarColaSubida();
        });
    }
}

async function ejecutarSubidaVideo({ idx, file, input }) {
    const btn = document.getElementById(`btnVideo_${idx}`);
    btn.classList.add('subiendo');
    btn.classList.remove('ok', 'error');
    btn.innerHTML = `<i class="ti ti-loader-2"></i> Subiendo…`;

    try {
        const fd = new FormData();
        fd.append('video', file);
        fd.append('_token', CSRF_TOKEN);

        const res = await fetch(SUBIR_VIDEO_URL, { method: 'POST', body: fd });
        if (!res.ok) throw new Error('fallo del servidor');
        const data = await res.json();

        document.getElementById(`videoPath_${idx}`).value = data.path;
        btn.classList.remove('subiendo');
        btn.classList.add('ok');
        btn.innerHTML = `<i class="ti ti-check"></i> ${file.name.slice(0,14)}`;
        btn.appendChild(input);
    } catch (err) {
        console.error('[subir video]', err);
        btn.classList.remove('subiendo');
        btn.classList.add('error');
        btn.innerHTML = `<i class="ti ti-alert-triangle"></i> Reintentar`;
        btn.appendChild(input);
        btn.onclick = null; // el label ya reabre el file picker normalmente
    } finally {
        videosSubiendo--;
        actualizarBarraProgreso();
    }
}

async function guardarLote() {
    const filas = document.querySelectorAll('#tbodyFilas tr');
    const status = document.getElementById('impStatus');
    status.className = 'imp-status';

    if (filas.length === 0) {
        status.textContent = 'Agrega al menos una fila.';
        status.classList.add('error');
        return;
    }
    if (videosSubiendo > 0) {
        status.textContent = 'Espera a que terminen de subir los videos.';
        status.classList.add('error');
        return;
    }

    const btn = document.getElementById('btnGuardarLote');
    btn.disabled = true;
    status.textContent = 'Guardando ejercicios…';

    const fd = new FormData();
    fd.append('_token', CSRF_TOKEN);
    document.querySelectorAll('#tbodyFilas input, #tbodyFilas select').forEach(el => {
        if (el.type === 'file') {
            if (el.files[0]) fd.append(el.name, el.files[0]);
        } else if (el.name) {
            fd.append(el.name, el.value);
        }
    });

    try {
        const res = await fetch(IMPORTAR_LOTE_URL, { method: 'POST', body: fd });
        if (res.redirected) {
            window.location.href = res.url;
            return;
        }
        if (!res.ok) {
            const data = await res.json().catch(() => ({}));
            throw new Error(data.message || 'error al guardar');
        }
        window.location.href = "{{ route('entrenador.ejercicios.index') }}";
    } catch (err) {
        console.error('[guardar lote]', err);
        status.textContent = 'Error: ' + err.message;
        status.classList.add('error');
        btn.disabled = false;
    }
}

// Arranca con 5 filas vacías para no partir de cero
document.addEventListener('DOMContentLoaded', () => {
    for (let i = 0; i < 5; i++) agregarFila();
});
</script>

@endsection