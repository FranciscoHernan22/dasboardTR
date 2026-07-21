{{-- DESTINO: resources/views/ejercicios/index.blade.php --}}
@extends('layouts.entrenador')
@section('titulo','Ejercicios')
@section('contenido')

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet">

<style>

:root {
    --bg:#f4f5f7; --surface:#ffffff; --border:#e2e5ea; --border2:#d0d5dd;
    --text:#111827; --muted:#6b7280; --accent:#2563eb; --accent-l:#eff6ff;
    --danger:#ef4444; --radius:10px;
}
* { box-sizing:border-box; }
body, .entrenador-content { font-family:'DM Sans',sans-serif; background:var(--bg); color:var(--text); }

.page-header { display:flex; align-items:center; gap:10px; margin-bottom:16px; padding-bottom:12px; border-bottom:2px solid var(--border); flex-wrap:wrap; }
.page-header h2 { font-size:1.1rem; font-weight:700; margin:0; }
.badge { font-size:0.63rem; font-weight:700; background:var(--accent-l); color:var(--accent); border:1px solid #bfdbfe; padding:2px 8px; border-radius:99px; text-transform:uppercase; letter-spacing:.05em; }
.btn-nuevo-ej { margin-left:auto; display:inline-flex; align-items:center; gap:6px; background:var(--accent); color:white; font-family:'DM Sans',sans-serif; font-size:0.85rem; font-weight:600; padding:8px 18px; border:none; border-radius:var(--radius); cursor:pointer; box-shadow:0 2px 8px rgba(37,99,235,.3); transition:all .14s; }
.btn-nuevo-ej:hover { background:#1d4ed8; transform:translateY(-1px); }

.flash { border-radius:8px; padding:10px 14px; font-size:0.83rem; margin-bottom:14px; }
.flash-ok { background:#f0fdf4; border:1px solid #bbf7d0; color:#065f46; }
.flash-error { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }
.flash-error ul { margin:4px 0 0 18px; padding:0; }

#buscador { width:100%; border:1px solid var(--border2); border-radius:8px; padding:9px 12px; font-size:0.85rem; font-family:'DM Sans',sans-serif; color:var(--text); background:white; margin-bottom:12px; }
#buscador:focus { outline:none; border-color:var(--accent); }

.nav-segmentos { display:flex; flex-wrap:wrap; gap:6px; margin-bottom:22px; }
.nav-segmentos .pill { padding:5px 13px; border:1.5px solid var(--border2); border-radius:99px; background:white; color:var(--muted); font-size:0.74rem; font-weight:600; cursor:pointer; transition:all .12s; white-space:nowrap; font-family:'DM Sans',sans-serif; }
.nav-segmentos .pill:hover { border-color:var(--accent); color:var(--accent); background:var(--accent-l); }
.nav-segmentos .pill span { opacity:.6; font-weight:500; }
.nav-segmentos .pill.activa { background:var(--accent); border-color:var(--accent); color:white; }
.nav-segmentos .pill.activa span { opacity:.85; color:white; }

.sin-resultados { display:none; text-align:center; padding:50px 20px; color:var(--muted); font-size:0.88rem; background:white; border:1.5px dashed var(--border2); border-radius:var(--radius); }
.sin-resultados.visible { display:block; }

.segmento-seccion { margin-bottom:26px; scroll-margin-top:16px; }
.segmento-titulo { display:flex; align-items:center; gap:8px; margin:0 0 10px; font-size:0.95rem; font-weight:700; color:var(--text); }
.segmento-titulo .conteo { font-size:0.65rem; font-weight:700; color:var(--accent); background:var(--accent-l); border:1px solid #bfdbfe; padding:2px 8px; border-radius:99px; }

.ejercicios-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(180px, 1fr)); gap:14px; }
.ej-card { background:var(--surface); border:1.5px solid var(--border); border-radius:var(--radius); overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,.05); display:flex; flex-direction:column; transition:box-shadow .15s, border-color .15s; }
.ej-card:hover { border-color:var(--border2); box-shadow:0 4px 14px rgba(0,0,0,.08); }
.ej-card-img { position:relative; width:100%; aspect-ratio:1/1; background:#f1f3f6; display:flex; align-items:center; justify-content:center; overflow:hidden; }
.ej-card-img img { width:100%; height:100%; object-fit:cover; }
.ej-card-noimg { color:#c4cad3; font-size:1.8rem; display:flex; align-items:center; justify-content:center; width:100%; height:100%; }
.ej-card-body { padding:10px 12px 4px; flex:1; }
.ej-card-nombre { font-size:0.82rem; font-weight:600; color:var(--text); line-height:1.3; }
.ej-card-actions { display:flex; gap:6px; padding:8px 10px 10px; }
.ej-btn-editar { flex:1; display:flex; align-items:center; justify-content:center; gap:4px; padding:6px; border:1px solid var(--border2); border-radius:6px; background:white; color:var(--muted); font-size:0.7rem; font-weight:600; cursor:pointer; font-family:'DM Sans',sans-serif; transition:all .12s; }
.ej-btn-editar:hover { border-color:var(--accent); color:var(--accent); background:var(--accent-l); }
.ej-btn-eliminar { display:flex; align-items:center; justify-content:center; width:30px; border:1px solid #fecaca; border-radius:6px; background:white; color:var(--danger); cursor:pointer; transition:all .12s; }
.ej-btn-eliminar:hover { background:var(--danger); color:white; border-color:var(--danger); }

/* Badge de video sobre la tarjeta */
.ej-video-badge { position:absolute; top:6px; right:6px; width:26px; height:26px; border-radius:50%; background:rgba(17,24,39,.65); color:#fff; display:flex; align-items:center; justify-content:center; font-size:0.75rem; cursor:pointer; border:none; backdrop-filter:blur(2px); transition:background .12s, transform .12s; z-index:2; }
.ej-video-badge:hover { background:var(--accent); transform:scale(1.08); }

.ej-empty { text-align:center; padding:50px 20px; color:var(--muted); font-size:0.88rem; background:white; border:1.5px dashed var(--border2); border-radius:var(--radius); }

/* Modal */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:10000; align-items:center; justify-content:center; padding:16px; }
.modal-overlay.open { display:flex; }
.modal-box { background:white; border-radius:14px; width:100%; max-width:440px; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,.2); animation:modalIn .18s ease; }
@keyframes modalIn { from{transform:translateY(12px);opacity:0} to{transform:translateY(0);opacity:1} }
.modal-header { display:flex; align-items:center; justify-content:space-between; padding:18px 20px 14px; border-bottom:1px solid var(--border); }
.modal-header h3 { font-size:1rem; font-weight:700; margin:0; }
.modal-close { width:28px; height:28px; border-radius:7px; background:#f3f4f6; border:none; cursor:pointer; font-size:1rem; color:var(--muted); display:flex; align-items:center; justify-content:center; }
.modal-close:hover { background:#fee2e2; color:var(--danger); }
.modal-body { padding:18px 20px; display:flex; flex-direction:column; gap:14px; }

.ej-form-media { display:flex; gap:14px; justify-content:center; flex-wrap:wrap; }

.ej-form-imgwrap, .ej-form-videowrap { display:flex; flex-direction:column; align-items:center; gap:6px; }
.ej-form-imgpreview, .ej-form-videopreview { width:120px; height:120px; border-radius:12px; border:1.5px dashed var(--border2); background:#f8f9fb; display:flex; align-items:center; justify-content:center; cursor:pointer; overflow:hidden; transition:border-color .12s; position:relative; }
.ej-form-imgpreview:hover, .ej-form-videopreview:hover { border-color:var(--accent); }
.ej-form-imgpreview img, .ej-form-videopreview video { width:100%; height:100%; object-fit:cover; }
#imgPreviewPlaceholder, #videoPreviewPlaceholder { display:flex; flex-direction:column; align-items:center; gap:4px; color:var(--muted); font-size:0.68rem; font-weight:600; text-align:center; }
#imgPreviewPlaceholder i, #videoPreviewPlaceholder i { font-size:1.4rem; }
.media-label { font-size:0.65rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.04em; }
.video-play-overlay { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,.25); color:#fff; font-size:1.3rem; pointer-events:none; }

.campo-form { display:flex; flex-direction:column; gap:5px; }
.campo-form label { font-size:0.72rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.04em; }
.campo-form input[type="text"] { border:1px solid var(--border2); border-radius:8px; padding:9px 11px; font-size:0.88rem; font-family:'DM Sans',sans-serif; color:var(--text); }
.campo-form input[type="text"]:focus { outline:none; border-color:var(--accent); }
.campo-form select { border:1px solid var(--border2); border-radius:8px; padding:9px 11px; font-size:0.88rem; font-family:'DM Sans',sans-serif; color:var(--text); background:white; }
.campo-form select:focus { outline:none; border-color:var(--accent); }

.modal-footer-ej { display:flex; gap:8px; padding:0 20px 20px; }
.btn-cancelar-ej { flex:1; padding:9px; border:1px solid var(--border2); border-radius:8px; background:white; color:var(--muted); font-size:0.85rem; font-weight:600; cursor:pointer; font-family:'DM Sans',sans-serif; }
.btn-cancelar-ej:hover { background:#f3f4f6; }
.btn-guardar-ej { flex:2; padding:9px; border:none; border-radius:8px; background:var(--accent); color:white; font-size:0.85rem; font-weight:600; cursor:pointer; font-family:'DM Sans',sans-serif; }
.btn-guardar-ej:hover { background:#1d4ed8; }

/* Modal lightbox de video */
.modal-video-box { max-width:520px; }
.modal-video-body { padding:16px 18px 18px; }
#videoVerPlayer { width:100%; max-height:70vh; border-radius:8px; background:#000; display:block; }

@media (max-width: 640px) {
    .page-header { gap:8px; }
    .page-header h2 { font-size:1rem; }
    .btn-nuevo-ej { width:100%; justify-content:center; margin-left:0; }
    .ejercicios-grid { grid-template-columns: repeat(2, 1fr); gap:10px; }
    .ej-card-nombre { font-size:0.78rem; }
    .nav-segmentos { gap:5px; }
    .nav-segmentos .pill { font-size:0.7rem; padding:4px 10px; }
    .modal-box { max-height:95vh; border-radius:10px; }
    .modal-body { padding:14px 16px; gap:12px; }
    .modal-footer-ej { padding:0 16px 16px; }
    #buscador { font-size:0.82rem; }
    .ej-form-imgpreview, .ej-form-videopreview { width:100px; height:100px; }
}

@media (max-width: 360px) {
    .ejercicios-grid { grid-template-columns: 1fr 1fr; gap:8px; }
    .ej-card-actions { gap:4px; }
    .ej-btn-editar { font-size:0.65rem; padding:5px; }
}
</style>

@php
    $r2Url = env('AWS_URL');
@endphp

<div class="page-header">
    <h2>Ejercicios</h2>
    <span class="badge">{{ $totalEjercicios }} ejercicios</span>
    <button type="button" class="btn-nuevo-ej" onclick="abrirModalEjercicio('crear')">＋ Agregar ejercicio</button>
</div>

@if(session('success'))
<div class="flash flash-ok">✅ {{ session('success') }}</div>
@endif
@if($errors->any())
<div class="flash flash-error">
    ⚠️ Revisa lo siguiente:
    <ul>
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<input type="text" id="buscador" placeholder="🔍 Buscar ejercicio…" oninput="filtrarEjercicios()">

@if($porSegmento->isNotEmpty())
<div class="nav-segmentos" id="filtroSegmentos">
    <button type="button" class="pill activa" data-segmento="" onclick="filtrarPorSegmento(this)">
        Todos <span>({{ $totalEjercicios }})</span>
    </button>
    @foreach($porSegmento as $segmento => $items)
    <button type="button" class="pill" data-segmento="{{ $segmento }}" onclick="filtrarPorSegmento(this)">
        {{ $segmentosFijos[$segmento] ?? $segmento }} <span>({{ $items->count() }})</span>
    </button>
    @endforeach
</div>
@endif

<div id="contenidoEjercicios">
@forelse($porSegmento as $segmento => $items)
<div class="segmento-seccion" id="seg-{{ $loop->index }}" data-segmento-seccion data-segmento="{{ $segmento }}">
    <h3 class="segmento-titulo">{{ $segmentosFijos[$segmento] ?? $segmento }} <span class="conteo">{{ $items->count() }}</span></h3>
    <div class="ejercicios-grid">
        @foreach($items as $ej)
        <div class="ej-card" data-nombre="{{ strtolower($ej->nombre) }}">
            <div class="ej-card-img">
                @if($ej->video)
                    <button type="button" class="ej-video-badge" title="Ver video" onclick="verVideoEjercicio('{{ $r2Url . '/' . $ej->video }}')">
                        <i class="ti ti-player-play-filled"></i>
                    </button>
                @endif
                @if($ej->imagen)
                    <img src="{{ $r2Url . '/' . $ej->imagen }}" alt="{{ $ej->nombre }}">
                @else
                    <div class="ej-card-noimg"><i class="ti ti-photo"></i></div>
                @endif
            </div>
            <div class="ej-card-body">
                <div class="ej-card-nombre">{{ $ej->nombre }}</div>
            </div>
            <div class="ej-card-actions">
                <button type="button" class="ej-btn-editar"
                    data-id="{{ $ej->id }}"
                    data-nombre="{{ $ej->nombre }}"
                    data-segmento="{{ $ej->segmento }}"
                    data-imagen="{{ $ej->imagen ? $r2Url . '/' . $ej->imagen : '' }}"
                    data-video="{{ $ej->video ? $r2Url . '/' . $ej->video : '' }}"
                    onclick="abrirDesdeBtn(this)">
                    <i class="ti ti-pencil"></i> Editar
                </button>
                <form method="POST" action="{{ route('entrenador.ejercicios.destroy',$ej->id) }}"
                      onsubmit="return confirm('¿Eliminar «{{ $ej->nombre }}»? Esta acción no se puede deshacer.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="ej-btn-eliminar" title="Eliminar"><i class="ti ti-trash"></i></button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@empty
<div class="ej-empty">Aún no tienes ejercicios. Agrega el primero con el botón de arriba ↑</div>
@endforelse
</div>

<div class="sin-resultados" id="sinResultados">No hay ejercicios que coincidan con ese filtro o búsqueda.</div>

{{-- MODAL CREAR / EDITAR --}}
<div class="modal-overlay" id="modalEjercicio" onclick="if(event.target===this) cerrarModalEjercicio()">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="modalEjTitulo">＋ Nuevo ejercicio</h3>
            <button type="button" class="modal-close" onclick="cerrarModalEjercicio()">✕</button>
        </div>
        <form id="formEjercicio" method="POST" enctype="multipart/form-data" action="{{ route('entrenador.ejercicios.store') }}">
            @csrf
            <input type="hidden" name="_method" id="metodoEjercicio" value="POST">

            <div class="modal-body">
                <div class="ej-form-media">
                    <div class="ej-form-imgwrap">
                        <span class="media-label">Imagen</span>
                        <label for="inputImagen" class="ej-form-imgpreview" id="imgPreviewWrap">
                            <img id="imgPreview" src="" alt="" style="display:none;">
                            <span id="imgPreviewPlaceholder"><i class="ti ti-camera-plus"></i>Imagen</span>
                        </label>
                        <input type="file" id="inputImagen" name="imagen" accept="image/*" style="display:none;" onchange="previewImagen(this)">
                    </div>

                    <div class="ej-form-videowrap">
                        <span class="media-label">Video</span>
                        <label for="inputVideo" class="ej-form-videopreview" id="videoPreviewWrap">
                            <video id="videoPreview" muted playsinline preload="metadata" style="display:none;"></video>
                            <span id="videoPreviewPlaceholder"><i class="ti ti-video-plus"></i>Video</span>
                        </label>
                        <input type="file" id="inputVideo" name="video" accept="video/*" style="display:none;" onchange="previewVideo(this)">
                    </div>
                </div>

                <div class="campo-form">
                    <label>Nombre del ejercicio</label>
                    <input type="text" name="nombre" id="inputNombre" required maxlength="120" placeholder="Ej: Press banca con barra">
                </div>

                <div class="campo-form">
                    <label>Segmento / grupo muscular</label>
                    <select name="segmento" id="inputSegmento" required>
                        <option value="">-- Selecciona un segmento --</option>
                        @foreach($segmentosFijos as $valor => $label)
                            <option value="{{ $valor }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="modal-footer-ej">
                <button type="button" class="btn-cancelar-ej" onclick="cerrarModalEjercicio()">Cancelar</button>
                <button type="submit" class="btn-guardar-ej" id="btnGuardarEj">Guardar ejercicio</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL VER VIDEO (lightbox) --}}
<div class="modal-overlay" id="modalVerVideo" onclick="if(event.target===this) cerrarVerVideo()">
    <div class="modal-box modal-video-box">
        <div class="modal-header">
            <h3>🎬 Video del ejercicio</h3>
            <button type="button" class="modal-close" onclick="cerrarVerVideo()">✕</button>
        </div>
        <div class="modal-video-body">
            <video id="videoVerPlayer" controls playsinline></video>
        </div>
    </div>
</div>

<script>
const EJ_UPDATE_URL = "{{ route('entrenador.ejercicios.update', ['ejercicio' => 'EJID']) }}";
const EJ_STORE_URL  = "{{ route('entrenador.ejercicios.store') }}";

let videoObjectUrl = null; // para liberar memoria del preview de video en el form

function asegurarOpcionSegmento(select, valor) {
    if (!valor) return;
    const existe = [...select.options].some(o => o.value === valor);
    if (!existe) {
        const opt = document.createElement('option');
        opt.value = valor;
        opt.textContent = valor + ' (anterior)';
        select.appendChild(opt);
    }
}

function resetPreviewVideo() {
    const videoPreview = document.getElementById('videoPreview');
    const placeholder  = document.getElementById('videoPreviewPlaceholder');
    if (videoObjectUrl) { URL.revokeObjectURL(videoObjectUrl); videoObjectUrl = null; }
    videoPreview.pause();
    videoPreview.removeAttribute('src');
    videoPreview.load();
    videoPreview.style.display = 'none';
    placeholder.style.display = 'flex';
}

function abrirModalEjercicio(modo, data = {}) {
    const form           = document.getElementById('formEjercicio');
    const titulo         = document.getElementById('modalEjTitulo');
    const metodo         = document.getElementById('metodoEjercicio');
    const imgPreview     = document.getElementById('imgPreview');
    const imgPlaceholder = document.getElementById('imgPreviewPlaceholder');
    const videoPreview   = document.getElementById('videoPreview');
    const videoPlaceholder = document.getElementById('videoPreviewPlaceholder');

    form.reset();
    document.getElementById('inputImagen').value = '';
    document.getElementById('inputVideo').value = '';
    resetPreviewVideo();

    if (modo === 'crear') {
        titulo.textContent = '＋ Nuevo ejercicio';
        metodo.value = 'POST';
        form.action  = EJ_STORE_URL;
        imgPreview.style.display = 'none';
        imgPlaceholder.style.display = 'flex';
    } else {
        titulo.textContent = '✏️ Editar ejercicio';
        metodo.value = 'PUT';
        form.action  = EJ_UPDATE_URL.replace('EJID', data.id);
        document.getElementById('inputNombre').value = data.nombre || '';
        const selectSeg = document.getElementById('inputSegmento');
        asegurarOpcionSegmento(selectSeg, data.segmento);
        selectSeg.value = data.segmento || '';
        if (data.imagen) {
            imgPreview.src = data.imagen;
            imgPreview.style.display = 'block';
            imgPlaceholder.style.display = 'none';
        } else {
            imgPreview.style.display = 'none';
            imgPlaceholder.style.display = 'flex';
        }
        if (data.video) {
            videoPreview.src = data.video;
            videoPreview.style.display = 'block';
            videoPlaceholder.style.display = 'none';
        }
    }

    document.getElementById('modalEjercicio').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function cerrarModalEjercicio() {
    document.getElementById('modalEjercicio').classList.remove('open');
    document.body.style.overflow = '';
    resetPreviewVideo();
}

function previewImagen(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        const img = document.getElementById('imgPreview');
        img.src = e.target.result;
        img.style.display = 'block';
        document.getElementById('imgPreviewPlaceholder').style.display = 'none';
    };
    reader.readAsDataURL(file);
}

function previewVideo(input) {
    const file = input.files[0];
    if (!file) return;
    if (videoObjectUrl) URL.revokeObjectURL(videoObjectUrl);
    videoObjectUrl = URL.createObjectURL(file);
    const video = document.getElementById('videoPreview');
    video.src = videoObjectUrl;
    video.style.display = 'block';
    document.getElementById('videoPreviewPlaceholder').style.display = 'none';
}

function verVideoEjercicio(url) {
    const player = document.getElementById('videoVerPlayer');
    player.src = url;
    document.getElementById('modalVerVideo').classList.add('open');
    document.body.style.overflow = 'hidden';
    player.play().catch(() => {});
}

function cerrarVerVideo() {
    const player = document.getElementById('videoVerPlayer');
    player.pause();
    player.removeAttribute('src');
    player.load();
    document.getElementById('modalVerVideo').classList.remove('open');
    document.body.style.overflow = '';
}

function filtrarPorSegmento(btn) {
    document.querySelectorAll('#filtroSegmentos .pill').forEach(p => p.classList.remove('activa'));
    btn.classList.add('activa');
    filtrarEjercicios();
}

function filtrarEjercicios() {
    const texto          = document.getElementById('buscador').value.toLowerCase().trim();
    const segmentoActivo = document.querySelector('#filtroSegmentos .pill.activa')?.dataset.segmento || '';

    let algunaSeccionVisible = false;

    document.querySelectorAll('[data-segmento-seccion]').forEach(seccion => {
        const esSegmentoCorrecto = !segmentoActivo || segmentoActivo === seccion.dataset.segmento;

        if (!esSegmentoCorrecto) {
            seccion.style.display = 'none';
            return;
        }

        let algunaVisible = false;
        seccion.querySelectorAll('.ej-card').forEach(card => {
            const coincide = card.dataset.nombre.includes(texto);
            card.style.display = coincide ? '' : 'none';
            if (coincide) algunaVisible = true;
        });
        seccion.style.display = algunaVisible ? '' : 'none';
        if (algunaVisible) algunaSeccionVisible = true;
    });

    document.getElementById('sinResultados').classList.toggle('visible', !algunaSeccionVisible);
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        cerrarModalEjercicio();
        cerrarVerVideo();
    }
});

@if($errors->any())
document.addEventListener('DOMContentLoaded', () => abrirModalEjercicio('crear'));
@endif

function abrirDesdeBtn(btn) {
    abrirModalEjercicio('editar', {
        id:       btn.dataset.id,
        nombre:   btn.dataset.nombre,
        segmento: btn.dataset.segmento,
        imagen:   btn.dataset.imagen,
        video:    btn.dataset.video
    });
}
</script>

@endsection