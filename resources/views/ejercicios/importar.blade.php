{{-- DESTINO: resources/views/ejercicios/importar.blade.php --}}
@extends('layouts.entrenador')
@section('titulo','Importar / editar ejercicios')
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

#impBuscador { width:100%; border:1px solid var(--border2); border-radius:8px; padding:9px 12px; font-size:0.85rem; font-family:'DM Sans',sans-serif; color:var(--text); background:white; margin-bottom:10px; }
#impBuscador:focus { outline:none; border-color:var(--accent); }

.imp-nav-segmentos { display:flex; flex-wrap:wrap; gap:6px; margin-bottom:14px; }
.imp-nav-segmentos .pill { padding:5px 13px; border:1.5px solid var(--border2); border-radius:99px; background:white; color:var(--muted); font-size:0.74rem; font-weight:600; cursor:pointer; transition:all .12s; white-space:nowrap; font-family:'DM Sans',sans-serif; }
.imp-nav-segmentos .pill:hover { border-color:var(--accent); color:var(--accent); background:var(--accent-l); }
.imp-nav-segmentos .pill.activa { background:var(--accent); border-color:var(--accent); color:white; }

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

.imp-file-btn { display:inline-flex; align-items:center; gap:5px; padding:5px 9px; border:1px solid var(--border2); border-radius:7px; background:white; font-size:0.72rem; font-weight:600; color:var(--muted); cursor:pointer; white-space:nowrap; max-width:120px; overflow:hidden; text-overflow:ellipsis; }
.imp-file-btn:hover { border-color:var(--accent); color:var(--accent); }
.imp-file-btn.ok { border-color:#bbf7d0; color:var(--ok); background:#f0fdf4; }
.imp-file-btn.subiendo { border-color:#bfdbfe; color:var(--accent); background:var(--accent-l); }
.imp-file-btn.error { border-color:#fecaca; color:var(--danger); background:#fef2f2; }
.imp-file-btn.tiene-actual { border-color:#bfdbfe; background:var(--accent-l); color:var(--accent); }
.imp-file-btn img { width:20px; height:20px; border-radius:4px; object-fit:cover; }

.imp-btn-quitar { width:26px; height:26px; border:1px solid #fecaca; border-radius:6px; background:white; color:var(--danger); cursor:pointer; }
.imp-btn-quitar:hover { background:var(--danger); color:white; }
.imp-btn-quitar:disabled { opacity:.25; cursor:not-allowed; }

.imp-fila-oculta { display:none !important; }

.imp-sin-resultados { display:none; text-align:center; padding:40px 20px; color:var(--muted); font-size:0.85rem; background:var(--surface); border:1.5px dashed var(--border2); border-radius:var(--radius); margin-bottom:14px; }
.imp-sin-resultados.visible { display:block; }

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

<div class="imp-header">
    <h2>📥 Importar / editar ejercicios en lote</h2>
    <a href="{{ route('entrenador.ejercicios.index') }}">← Volver al listado</a>
</div>

<input type="text" id="impBuscador" placeholder="🔍 Buscar por nombre…" oninput="filtrarFilas()">

<div class="imp-nav-segmentos" id="impFiltroSegmentos">
    <button type="button" class="pill activa" data-segmento="" onclick="filtrarPorSegmento(this)">Todos</button>
    @foreach($segmentosFijos as $valor => $label)
        <button type="button" class="pill" data-segmento="{{ $valor }}" onclick="filtrarPorSegmento(this)">{{ $label }}</button>
    @endforeach
</div>

<div class="imp-barra-progreso" id="barraProgreso" style="display:none;">
    <span id="barraProgresoTexto">Subiendo videos… 0 de 0</span>
    <div class="track"><div class="fill" id="barraProgresoFill"></div></div>
</div>

<div class="imp-tabla-wrap">
    <table class="imp-tabla">
        <thead>
            <tr>
                <th style="width:32%">Nombre</th>
                <th style="width:20%">Segmento</th>
                <th style="width:130px">Imagen</th>
                <th style="width:130px">Video</th>
                <th style="width:36px"></th>
            </tr>
        </thead>
        <tbody id="tbodyFilas"></tbody>
    </table>
</div>

<div class="imp-sin-resultados" id="impSinResultados">No hay ejercicios que coincidan con ese filtro o búsqueda.</div>

<div class="imp-acciones">
    <button type="button" class="btn-agregar-fila" onclick="agregarFila()">＋ Agregar fila</button>
    <button type="button" class="btn-agregar-varias" onclick="agregarVariasFilas()">＋ Agregar varias</button>
</div>

<div class="imp-guardar-wrap">
    <button type="button" class="btn-guardar-lote" id="btnGuardarLote" onclick="guardarLote()">Guardar cambios</button>
</div>
<div class="imp-status" id="impStatus"></div>

<template id="tplSegmentoOptions">
    <option value="">-- Segmento --</option>
    @foreach($segmentosFijos as $valor => $label)
        <option value="{{ $valor }}">{{ $label }}</option>
    @endforeach
</template>

@php
    $ejerciciosParaJs = $ejercicios->map(function ($e) {
        return [
            'id'       => $e->id,
            'nombre'   => $e->nombre,
            'segmento' => $e->segmento,
            'imagen'   => $e->imagen,
            'video'    => $e->video,
        ];
    });
@endphp

<script type="module">
// Misma librería que usa el editor individual (index.blade.php), cargada
// desde nuestro propio dominio por la misma razón: el worker interno de
// ffmpeg.wasm importa archivos relativos que no resuelven bien desde un CDN.
import { FFmpeg } from "{{ asset('vendor/ffmpeg-wasm/ffmpeg/index.js') }}";
import { fetchFile } from "{{ asset('vendor/ffmpeg-wasm/util/index.js') }}";
window.__FFmpegLib = { FFmpeg, fetchFile };
</script>

<script>
const SUBIR_VIDEO_URL   = "{{ route('entrenador.ejercicios.subirVideoTemporal') }}";
const IMPORTAR_LOTE_URL = "{{ route('entrenador.ejercicios.importarLote') }}";
const CSRF_TOKEN = "{{ csrf_token() }}";
const R2_URL = "{{ $r2Url }}";

// Ejercicios existentes, mandados desde el controlador
const EJERCICIOS_EXISTENTES = @json($ejerciciosParaJs);

let filaIndex = 0;
let videosSubiendo = 0;
let videosTotalLote = 0;
let colaSubida = [];
const MAX_CONCURRENTES = 3;
let subidasActivas = 0;

// --- Estado de compresión (ffmpeg.wasm es UNA sola instancia, por eso la
// compresión va en fila de a un video a la vez; la SUBIDA sí va en paralelo) ---
let ffmpegInstance    = null;
let ffmpegCargando    = null;
let colaCompresion    = [];
let comprimiendoActivo = false;

function segmentoOptionsHTML() {
    return document.getElementById('tplSegmentoOptions').innerHTML;
}

/**
 * Agrega una fila. Si "datos" viene con id, es una fila de un ejercicio
 * ya existente (precargada); si no, es una fila nueva vacía.
 */
function agregarFila(datos) {
    datos = datos || null;
    const idx = filaIndex++;
    const esExistente = !!(datos && datos.id);
    const tbody = document.getElementById('tbodyFilas');
    const tr = document.createElement('tr');
    tr.dataset.fila = idx;

    // Si es una fila NUEVA (sin datos) y hay un filtro de segmento activo,
    // la fila nace con ese segmento ya seleccionado.
    const segmentoFiltroActivo = document.querySelector('#impFiltroSegmentos .pill.activa')?.dataset.segmento || '';

    const nombreVal   = (datos && datos.nombre) ? datos.nombre : '';
    const segmentoVal = (datos && datos.segmento) ? datos.segmento : (!esExistente ? segmentoFiltroActivo : '');
    const imagenUrl   = (datos && datos.imagen) ? (R2_URL + '/' + datos.imagen) : '';
    const videoUrl     = (datos && datos.video) ? (R2_URL + '/' + datos.video) : '';

    let opciones = segmentoOptionsHTML();
    if (segmentoVal) {
        opciones = opciones.replace('value="' + segmentoVal + '"', 'value="' + segmentoVal + '" selected');
    }

    tr.innerHTML =
        '<td>' +
            '<input type="text" name="filas[' + idx + '][nombre]" value="' + nombreVal.replace(/"/g, '&quot;') + '" placeholder="Nombre del ejercicio" required oninput="filtrarFilas()">' +
        '</td>' +
        '<td>' +
            '<select name="filas[' + idx + '][segmento]" required onchange="filtrarFilas()">' + opciones + '</select>' +
        '</td>' +
        '<td>' +
            '<label class="imp-file-btn ' + (imagenUrl ? 'tiene-actual' : '') + '" id="btnImg_' + idx + '">' +
                (imagenUrl ? '<img src="' + imagenUrl + '" alt="">' : '<i class="ti ti-camera-plus"></i>') +
                (imagenUrl ? 'Cambiar' : 'Imagen') +
                '<input type="file" name="filas[' + idx + '][imagen]" accept="image/*" style="display:none;" onchange="marcarImagenLista(' + idx + ', this)">' +
            '</label>' +
            '<input type="hidden" name="filas[' + idx + '][imagen_original]" value="' + ((datos && datos.imagen) ? datos.imagen : '') + '">' +
        '</td>' +
        '<td>' +
            '<label class="imp-file-btn ' + (videoUrl ? 'tiene-actual' : '') + '" id="btnVideo_' + idx + '">' +
                '<i class="ti ' + (videoUrl ? 'ti-player-play-filled' : 'ti-video-plus') + '"></i>' +
                (videoUrl ? 'Cambiar' : 'Video') +
                '<input type="file" accept="video/*" style="display:none;" onchange="subirVideoFila(' + idx + ', this)">' +
            '</label>' +
            '<input type="hidden" name="filas[' + idx + '][video_path]" id="videoPath_' + idx + '">' +
            '<input type="hidden" name="filas[' + idx + '][video_original]" value="' + ((datos && datos.video) ? datos.video : '') + '">' +
        '</td>' +
        '<td>' +
            '<button type="button" class="imp-btn-quitar" onclick="quitarFila(this, ' + esExistente + ')" ' + (esExistente ? 'title="Los ejercicios guardados se eliminan desde el listado"' : '') + '>' +
                '<i class="ti ti-x"></i>' +
            '</button>' +
        '</td>';

    if (esExistente) {
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'filas[' + idx + '][id]';
        idInput.value = datos.id;
        tr.appendChild(idInput);
    }

    tbody.appendChild(tr);
    filtrarFilas();
}

function quitarFila(btn, esExistente) {
    if (esExistente) {
        alert('Este ejercicio ya está guardado. Para eliminarlo por completo usa el botón de eliminar en el listado normal — aquí solo puedes quitarlo de esta pantalla sin borrarlo.');
    }
    btn.closest('tr').remove();
    filtrarFilas();
}

function agregarVariasFilas() {
    const n = parseInt(prompt('¿Cuántas filas nuevas quieres agregar?', '10'), 10);
    if (!n || n < 1) return;
    for (let i = 0; i < n; i++) agregarFila();
}

function marcarImagenLista(idx, input) {
    const btn = document.getElementById('btnImg_' + idx);
    if (input.files[0]) {
        btn.classList.add('ok');
        btn.classList.remove('tiene-actual');
        btn.innerHTML = '<i class="ti ti-check"></i> ' + input.files[0].name.slice(0, 14);
        btn.appendChild(input);
    }
}

/* ─────────── FILTRO por nombre + segmento ─────────── */

function filtrarPorSegmento(btn) {
    document.querySelectorAll('#impFiltroSegmentos .pill').forEach(function (p) { p.classList.remove('activa'); });
    btn.classList.add('activa');
    filtrarFilas();
}

function filtrarFilas() {
    const texto = document.getElementById('impBuscador').value.toLowerCase().trim();
    const segmentoActivo = document.querySelector('#impFiltroSegmentos .pill.activa')?.dataset.segmento || '';

    let algunaVisible = false;

    document.querySelectorAll('#tbodyFilas tr').forEach(function (tr) {
        const nombreInput = tr.querySelector('input[name$="[nombre]"]');
        const segmentoSelect = tr.querySelector('select[name$="[segmento]"]');

        const nombre = nombreInput ? nombreInput.value.toLowerCase() : '';
        const segmento = segmentoSelect ? segmentoSelect.value : '';

        const coincideTexto = !texto || nombre.includes(texto);
        const coincideSegmento = !segmentoActivo || segmento === segmentoActivo;
        const visible = coincideTexto && coincideSegmento;

        tr.classList.toggle('imp-fila-oculta', !visible);
        if (visible) algunaVisible = true;
    });

    document.getElementById('impSinResultados').classList.toggle('visible', !algunaVisible);
}

/* ─────────── Subida de video en segundo plano ─────────── */

function actualizarBarraProgreso() {
    const barra = document.getElementById('barraProgreso');
    const texto = document.getElementById('barraProgresoTexto');
    const fill  = document.getElementById('barraProgresoFill');
    const pendientes = videosSubiendo;

    if (videosTotalLote === 0) { barra.style.display = 'none'; return; }

    barra.style.display = 'flex';
    const completados = videosTotalLote - pendientes;
    texto.textContent = pendientes > 0
        ? ('Comprimiendo y subiendo videos… ' + completados + ' de ' + videosTotalLote)
        : ('✓ ' + videosTotalLote + ' video(s) listo(s)');
    fill.style.width = ((completados / videosTotalLote) * 100) + '%';

    document.getElementById('btnGuardarLote').disabled = pendientes > 0;
}

function subirVideoFila(idx, input) {
    const file = input.files[0];
    if (!file) return;
    videosSubiendo++;
    videosTotalLote++;
    actualizarBarraProgreso();
    colaCompresion.push({ idx: idx, file: file, input: input });
    procesarColaCompresion();
}

async function procesarColaCompresion() {
    if (comprimiendoActivo || colaCompresion.length === 0) return;
    comprimiendoActivo = true;
    const item = colaCompresion.shift();
    await comprimirYEncolarSubida(item);
    comprimiendoActivo = false;
    procesarColaCompresion();
}

async function comprimirYEncolarSubida(item) {
    const idx = item.idx, file = item.file, input = item.input;
    const btn = document.getElementById('btnVideo_' + idx);
    btn.classList.add('subiendo');
    btn.classList.remove('ok', 'error', 'tiene-actual');
    btn.innerHTML = '<i class="ti ti-loader-2"></i> Comprimiendo…';

    let archivoFinal = file;
    try {
        archivoFinal = await comprimirVideo(file);
    } catch (err) {
        // Si falla la compresión (ej. archivo raro), seguimos con el
        // original tal cual — mejor subir algo pesado que no subir nada.
        console.warn('[compresión de video] fallo, se sube el original sin comprimir:', err);
    }

    colaSubida.push({ idx: idx, file: archivoFinal, input: input });
    procesarColaSubida();
}

async function comprimirVideo(file) {
    const ffmpeg = await cargarFFmpeg();
    const { fetchFile } = window.__FFmpegLib;

    const nombreEntrada = 'entrada_' + Date.now() + '_' + Math.random().toString(36).slice(2) + extensionDe(file.name);
    const nombreSalida  = 'salida_' + Date.now() + '_' + Math.random().toString(36).slice(2) + '.mp4';

    await ffmpeg.writeFile(nombreEntrada, await fetchFile(file));

    let blob;
    try {
        // Mismos parámetros que ya usa el editor individual: máx. 1280px
        // de ancho, calidad razonable — reduce videos de celular de
        // 200-300MB a 10-20MB en la mayoría de los casos.
        await ffmpeg.exec([
            '-i', nombreEntrada,
            '-vf', "scale='min(1280,iw)':-2",
            '-c:v', 'libx264', '-preset', 'veryfast', '-crf', '26',
            '-c:a', 'aac', '-b:a', '128k',
            '-movflags', '+faststart',
            nombreSalida
        ]);
        const data = await ffmpeg.readFile(nombreSalida);
        if (!data || !data.length) throw new Error('salida vacía');
        blob = new Blob([data.buffer], { type: 'video/mp4' });
    } finally {
        await ffmpeg.deleteFile(nombreEntrada).catch(function () {});
        await ffmpeg.deleteFile(nombreSalida).catch(function () {});
    }

    return new File([blob], 'video-comprimido.mp4', { type: 'video/mp4' });
}

function extensionDe(nombre) {
    const m = (nombre || '').match(/\.[0-9a-z]+$/i);
    return m ? m[0] : '.mp4';
}

async function cargarFFmpeg() {
    if (ffmpegInstance) return ffmpegInstance;
    if (ffmpegCargando) return ffmpegCargando;
    ffmpegCargando = (async function () {
        if (!window.__FFmpegLib) {
            throw new Error('La librería de compresión (ffmpeg.wasm) no cargó. Revisa la consola (F12) y confirma que los archivos existen en /vendor/ffmpeg-wasm/.');
        }
        const { FFmpeg } = window.__FFmpegLib;
        const ffmpeg = new FFmpeg();
        await ffmpeg.load({
            coreURL: "{{ asset('vendor/ffmpeg-wasm/core/ffmpeg-core.js') }}",
            wasmURL: "{{ asset('vendor/ffmpeg-wasm/core/ffmpeg-core.wasm') }}",
            classWorkerURL: "{{ asset('vendor/ffmpeg-wasm/ffmpeg/worker.js') }}",
        });
        ffmpegInstance = ffmpeg;
        return ffmpeg;
    })();
    try {
        return await ffmpegCargando;
    } catch (e) {
        ffmpegCargando = null;
        throw e;
    }
}

function procesarColaSubida() {
    while (subidasActivas < MAX_CONCURRENTES && colaSubida.length > 0) {
        const item = colaSubida.shift();
        subidasActivas++;
        ejecutarSubidaVideo(item).finally(function () {
            subidasActivas--;
            procesarColaSubida();
        });
    }
}

async function ejecutarSubidaVideo(item) {
    const idx = item.idx, file = item.file, input = item.input;
    const btn = document.getElementById('btnVideo_' + idx);
    btn.classList.add('subiendo');
    btn.classList.remove('ok', 'error', 'tiene-actual');
    btn.innerHTML = '<i class="ti ti-loader-2"></i> Subiendo…';

    try {
        const fd = new FormData();
        fd.append('video', file);
        fd.append('_token', CSRF_TOKEN);

        const res = await fetch(SUBIR_VIDEO_URL, {
            method: 'POST',
            body: fd,
            headers: { 'Accept': 'application/json' }
        });

        if (res.redirected || res.url.includes('/login')) {
            throw new Error('Tu sesión expiró. Recarga la página (guarda antes lo que puedas) y vuelve a intentar.');
        }
        if (res.status === 422) {
            const errores = await res.json();
            const primerError = Object.values(errores.errors || {})[0]?.[0] || 'Video inválido.';
            throw new Error(primerError);
        }
        if (!res.ok) throw new Error('fallo del servidor');
        const data = await res.json();

        document.getElementById('videoPath_' + idx).value = data.path;
        btn.classList.remove('subiendo');
        btn.classList.add('ok');
        btn.innerHTML = '<i class="ti ti-check"></i> ' + file.name.slice(0, 14);
        btn.appendChild(input);
    } catch (err) {
        console.error('[subir video]', err);
        btn.classList.remove('subiendo');
        btn.classList.add('error');
        btn.innerHTML = '<i class="ti ti-alert-triangle"></i> Reintentar';
        btn.appendChild(input);
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
        status.textContent = 'No hay filas para guardar.';
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
    // Importante: se manda TODO (incluso filas ocultas por el filtro), no solo lo visible
    document.querySelectorAll('#tbodyFilas input, #tbodyFilas select').forEach(function (el) {
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
            const data = await res.json().catch(function () { return {}; });
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

// Carga primero los ejercicios existentes, luego 3 filas nuevas vacías
document.addEventListener('DOMContentLoaded', function () {
    EJERCICIOS_EXISTENTES.forEach(function (ej) { agregarFila(ej); });
    for (let i = 0; i < 3; i++) agregarFila();
});
</script>

@endsection