@extends('layouts.entrenador')
@section('titulo','Editar Rutina')
@section('contenido')
@php $r2Url = env('AWS_URL'); @endphp


<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet">

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
.page-header { display:flex; align-items:center; gap:10px; margin-bottom:16px; padding-bottom:12px; border-bottom:2px solid var(--border); flex-wrap:wrap; }
.page-header h2 { font-size:1.1rem; font-weight:700; margin:0; }
.badge { font-size:0.63rem; font-weight:700; background:var(--accent-l); color:var(--accent); border:1px solid #bfdbfe; padding:2px 8px; border-radius:99px; text-transform:uppercase; letter-spacing:.05em; }
.btn-metodos { display:inline-flex; align-items:center; gap:5px; background:white; color:var(--muted); border:1px solid var(--border2); border-radius:7px; padding:5px 12px; font-size:0.75rem; font-weight:600; cursor:pointer; transition:all .13s; font-family:'DM Sans',sans-serif; }
.btn-metodos:hover { border-color:var(--accent); color:var(--accent); background:var(--accent-l); }
.nota-sesion-card { background:#fffdf5; border:1.5px solid #fde68a; border-radius:var(--radius); padding:10px 14px; margin-bottom:14px; display:flex; flex-direction:column; gap:6px; }
.nota-sesion-label { display:flex; align-items:center; gap:6px; font-size:0.7rem; font-weight:700; color:#92400e; text-transform:uppercase; letter-spacing:.06em; }
.nota-sesion-label i { font-size:0.9rem; color:#d97706; }
.nota-sesion-textarea { width:100%; border:1px solid #fde68a; border-radius:7px; padding:7px 10px; font-size:0.82rem; font-family:'DM Sans',sans-serif; color:var(--text); background:white; resize:vertical; min-height:52px; line-height:1.5; box-sizing:border-box; transition:border-color .13s; }
.nota-sesion-textarea:focus { outline:none; border-color:#f59e0b; }
.nota-sesion-textarea::placeholder { color:#d1a054; }
.nota-ej-input-wrap { background:#fffbeb; border-left:2px solid #f59e0b; border-radius:0 4px 4px 0; padding:5px 8px; display:flex; align-items:flex-start; gap:6px; margin-top:6px; }
.nota-ej-input-wrap i { font-size:13px; color:#d97706; margin-top:2px; flex-shrink:0; }
.nota-ej-input { width:100%; border:none; background:transparent; padding:0; font-size:0.66rem; font-family:'DM Sans',sans-serif; color:#92400e; line-height:1.5; resize:none; overflow:hidden; display:block; box-sizing:border-box; }
.nota-ej-input:focus { outline:none; }
.nota-ej-input::placeholder { color:#d1a054; }
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:10000; align-items:center; justify-content:center; padding:16px; }
.modal-overlay.open { display:flex; }
.modal-box { background:white; border-radius:14px; width:100%; max-width:620px; max-height:88vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,.2); animation:modalIn .18s ease; }
@keyframes modalIn { from{transform:translateY(12px);opacity:0} to{transform:translateY(0);opacity:1} }
.modal-header { display:flex; align-items:center; justify-content:space-between; padding:18px 20px 14px; border-bottom:1px solid var(--border); position:sticky; top:0; background:white; z-index:1; border-radius:14px 14px 0 0; }
.modal-header h3 { font-size:1rem; font-weight:700; margin:0; }
.modal-close { width:28px; height:28px; border-radius:7px; background:#f3f4f6; border:none; cursor:pointer; font-size:1rem; color:var(--muted); display:flex; align-items:center; justify-content:center; }
.modal-close:hover { background:#fee2e2; color:var(--danger); }
.modal-body { padding:16px 20px 20px; display:flex; flex-direction:column; gap:10px; }
.metodo-card { border:1px solid var(--border); border-radius:9px; overflow:hidden; }
.metodo-card-header { display:flex; align-items:center; gap:10px; padding:10px 14px; background:#fafbfc; border-bottom:1px solid var(--border); }
.metodo-tag { font-size:0.65rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; padding:2px 8px; border-radius:99px; flex-shrink:0; }
.tag-normal{background:#dbeafe;color:#1d4ed8} .tag-888{background:#fce7f3;color:#9d174d} .tag-restpause{background:#fef3c7;color:#92400e}
.tag-21s{background:#d1fae5;color:#065f46} .tag-10_21{background:#ede9fe;color:#5b21b6} .tag-isometria{background:#fef9c3;color:#713f12}
.tag-forzadas{background:#fee2e2;color:#991b1b} .tag-parciales{background:#e0f2fe;color:#075985} .tag-negativas{background:#f0fdf4;color:#14532d}
.metodo-card-nombre { font-size:0.875rem; font-weight:700; color:var(--text); }
.metodo-card-body { padding:10px 14px; font-size:0.82rem; color:var(--muted); line-height:1.6; }
.metodo-card-body b { color:var(--text); }
.modal-circ-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:10001; align-items:center; justify-content:center; }
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
.add-block-bar { display:flex; gap:8px; margin-bottom:14px; flex-wrap:wrap; }
.add-block-btn { flex:1; min-width:80px; padding:7px 4px; background:var(--surface); border:1.5px dashed var(--border2); border-radius:7px; font-size:0.72rem; font-weight:600; color:var(--muted); cursor:pointer; transition:all .13s; }
.add-block-btn:hover { background:var(--accent-l); border-color:var(--accent); color:var(--accent); }
.bloque { background:var(--surface); border:1.5px solid var(--border); border-radius:var(--radius); margin-bottom:10px; box-shadow:0 1px 4px rgba(0,0,0,.06); overflow:visible; }
.bloque-header { display:flex; align-items:center; gap:8px; padding:7px 12px; border-bottom:1px solid var(--border); background:#f5f6f8; border-radius:var(--radius) var(--radius) 0 0; flex-wrap:wrap; }
.bloque-tipo { font-size:0.6rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; padding:2px 8px; border-radius:99px; flex-shrink:0; }
.tipo-monoserie{background:#dbeafe;color:#1d4ed8} .tipo-biserie{background:#d1fae5;color:#065f46}
.tipo-triserie{background:#fef3c7;color:#92400e} .tipo-circuito{background:#fce7f3;color:#9d174d}
.bloque-series-count { display:flex; align-items:center; gap:5px; font-size:0.72rem; color:var(--muted); margin-left:auto; }
.bloque-series-count input { width:42px; border:1px solid var(--border2); border-radius:5px; padding:2px 5px; font-size:0.74rem; font-family:'DM Mono',monospace; text-align:center; color:var(--text); }
.btn-remove { width:24px; height:24px; border-radius:5px; background:#fee2e2; border:none; color:var(--danger); cursor:pointer; font-size:0.75rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.btn-remove:hover { background:#fca5a5; }
.bloque-footer { border-top:1px solid var(--border); background:#f9fafb; padding:6px 12px; border-radius:0 0 var(--radius) var(--radius); display:flex; align-items:center; gap:8px; }
.series-header-row { display:flex; border-bottom:2px solid var(--border); background:#f0f2f5; }
.series-header-row .col-info-header { width:265px; flex-shrink:0; border-right:1px solid var(--border); padding:5px 10px; font-size:0.6rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.06em; display:flex; align-items:center; }
.series-header-row .col-series-headers { flex:1; display:flex; padding:0; min-width:0; }
.serie-header-col { flex:1; text-align:center; padding:5px 4px; font-size:0.65rem; font-weight:700; color:var(--accent); background:var(--accent-l); border-right:1px solid #bfdbfe; letter-spacing:.04em; text-transform:uppercase; }
.serie-header-col:last-child { border-right:none; }
.ejercicio-row { display:flex; border-bottom:1px solid var(--border); min-height:54px; align-items:stretch; overflow:visible; }
.ejercicio-row:last-of-type { border-bottom:none; }
.ej-letra { width:22px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:0.65rem; font-weight:800; border-right:2px solid var(--border); }
.ej-letra-a{color:#1d4ed8;background:#eff6ff} .ej-letra-b{color:#065f46;background:#f0fdf4}
.ej-letra-c{color:#92400e;background:#fffbeb} .ej-letra-d{color:#9d174d;background:#fdf2f8}
.ej-letra-e{color:#1d4ed8;background:#e0f2fe} .ej-letra-f{color:#065f46;background:#dcfce7}
.ej-letra-g{color:#92400e;background:#fef9c3} .ej-letra-h{color:#9d174d;background:#fce7f3}
.ej-letra-i{color:#1e40af;background:#dbeafe} .ej-letra-j{color:#166534;background:#d1fae5}
.ej-letra-k{color:#854d0e;background:#fef3c7} .ej-letra-l{color:#831843;background:#fdf2f8}
.ej-bg-a{background:var(--ej-a)} .ej-bg-b{background:var(--ej-b)} .ej-bg-c{background:var(--ej-c)} .ej-bg-d{background:var(--ej-d)}
.ej-bg-e{background:var(--ej-e)} .ej-bg-f{background:var(--ej-f)} .ej-bg-g{background:var(--ej-g)} .ej-bg-h{background:var(--ej-h)}
.ej-bg-i{background:var(--ej-i)} .ej-bg-j{background:var(--ej-j)} .ej-bg-k{background:var(--ej-k)} .ej-bg-l{background:var(--ej-l)}
.col-segmento { width:110px; flex-shrink:0; padding:7px 9px; border-right:1px solid var(--border); }
.col-ejercicio { width:133px; flex-shrink:0; padding:7px 9px; border-right:1px solid var(--border); overflow:visible; position:relative; }
.col-series { flex:1; padding:6px; min-width:0; display:flex; align-items:stretch; }
.field-label { font-size:0.58rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.06em; margin-bottom:3px; }
.segmento-select { width:100%; border:1px solid var(--border2); border-radius:5px; padding:4px 5px; font-size:0.73rem; font-family:'DM Sans',sans-serif; color:var(--text); background:white; }
.ej-select-wrapper { position:relative; user-select:none; }
.ej-select-trigger { display:flex; align-items:center; gap:5px; border:1px solid var(--border2); border-radius:5px; padding:3px 5px; cursor:pointer; background:white; min-height:32px; transition:border-color .12s; }
.ej-select-trigger:hover { border-color:var(--accent); }
.ej-select-trigger img { width:30px; height:30px; object-fit:cover; border-radius:4px; flex-shrink:0; }
.ej-trigger-nombre { font-size:0.7rem; font-weight:600; color:var(--text); flex:1; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; line-height:1.2; }
.ej-trigger-placeholder { font-size:0.7rem; color:var(--muted); flex:1; }
.ej-trigger-arrow { color:var(--muted); font-size:0.55rem; flex-shrink:0; }
.ej-select-dropdown { display:none; position:absolute; top:calc(100% + 3px); left:0; width:260px; background:white; border:1.5px solid var(--border); border-radius:8px; box-shadow:0 8px 24px rgba(0,0,0,.14); z-index:8000; max-height:280px; overflow-y:auto; }
.ej-select-dropdown.open { display:block; }
.ej-select-option { display:flex; align-items:center; gap:8px; padding:6px 10px; cursor:pointer; font-size:0.8rem; border-bottom:1px solid #f3f4f6; transition:background .1s; }
.ej-select-option:last-child { border-bottom:none; }
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
.peso-group { display:flex; align-items:center; gap:2px; width:100%; }
.peso-group .campo-input { flex:1; min-width:0; }
.unidad-select { width:34px; flex-shrink:0; border:1px solid var(--border2); border-radius:4px; padding:3px 1px; font-size:0.58rem; font-family:'DM Sans',sans-serif; color:var(--muted); background:white; text-align:center; cursor:pointer; }
.tempo-wrap { width:100%; margin-top:4px; }
.tempo-toggle { width:100%; padding:3px 5px; border:1px dashed var(--border2); border-radius:5px; background:white; font-size:0.6rem; font-weight:600; color:var(--muted); cursor:pointer; font-family:'DM Sans',sans-serif; display:flex; align-items:center; justify-content:center; gap:3px; transition:all .13s; }
.tempo-toggle:hover,.tempo-toggle.active { border-color:var(--accent); color:var(--accent); background:var(--accent-l); border-style:solid; }
.tempo-fields { display:none; margin-top:4px; }
.tempo-fields.open { display:block; }
.tempo-row { display:flex; align-items:flex-end; gap:2px; width:100%; }
.tempo-cell { flex:1; display:flex; flex-direction:column; align-items:center; gap:1px; }
.tempo-icon { font-size:0.8rem; line-height:1; }
.tempo-label { font-size:0.46rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.03em; text-align:center; line-height:1.2; }
.tempo-input { width:100%; text-align:center; }
.tempo-unit { font-size:0.48rem; color:var(--muted); }
.tempo-sep { font-size:0.7rem; color:var(--border2); font-weight:700; padding-bottom:8px; }
.tempo-preview { margin-top:4px; text-align:center; font-size:0.6rem; font-weight:700; color:var(--accent); background:var(--accent-l); border-radius:4px; padding:2px 4px; font-family:'DM Mono',monospace; letter-spacing:.04em; }
.rir-wrap { width:100%; margin-top:4px; }
.rir-toggle { width:100%; padding:3px 5px; border:1px dashed var(--border2); border-radius:5px; background:white; font-size:0.6rem; font-weight:600; color:var(--muted); cursor:pointer; font-family:'DM Sans',sans-serif; display:flex; align-items:center; justify-content:center; gap:3px; transition:all .13s; }
.rir-toggle:hover,.rir-toggle.active { border-color:#7c3aed; color:#7c3aed; background:#f5f3ff; border-style:solid; }
.rir-fields { display:none; margin-top:4px; }
.rir-fields.open { display:block; }
.rir-mode-row { display:flex; gap:3px; width:100%; margin-bottom:4px; }
.rir-mode-btn { flex:1; padding:3px 2px; border:1px solid var(--border2); border-radius:4px; background:white; font-size:0.6rem; font-weight:700; color:var(--muted); cursor:pointer; font-family:'DM Sans',sans-serif; text-align:center; transition:all .1s; }
.rir-mode-btn:hover { border-color:#7c3aed; color:#7c3aed; }
.rir-mode-btn.sel { background:#7c3aed; color:white; border-color:#7c3aed; }
.rir-scale { font-size:0.48rem; color:var(--muted); text-align:center; margin-top:2px; line-height:1.3; }
.rir-preview { margin-top:4px; text-align:center; font-size:0.6rem; font-weight:700; color:#7c3aed; background:#f5f3ff; border-radius:4px; padding:2px 4px; font-family:'DM Mono',monospace; }
.btn-guardar { display:inline-flex; align-items:center; gap:6px; background:var(--accent); color:white; font-family:'DM Sans',sans-serif; font-size:0.87rem; font-weight:600; padding:9px 24px; border:none; border-radius:var(--radius); cursor:pointer; box-shadow:0 2px 8px rgba(37,99,235,.3); transition:all .14s; margin-top:14px; }
.btn-guardar:hover { background:#1d4ed8; transform:translateY(-1px); }
.btn-guardar:disabled { background:#93c5fd; cursor:not-allowed; transform:none; }
.btn-pdf { display:inline-flex; align-items:center; gap:6px; background:white; color:var(--accent); font-family:'DM Sans',sans-serif; font-size:0.87rem; font-weight:600; padding:9px 22px; border:1.5px solid var(--accent); border-radius:var(--radius); cursor:pointer; text-decoration:none; transition:all .14px; margin-top:14px; margin-left:10px; }
.btn-pdf:hover { background:var(--accent-l); }
.bloque { transition:border-color .15s, opacity .15s; }
.bloque.dragging { opacity:.4; }
.bloque.drag-over { border-color:var(--accent); box-shadow:0 0 0 2px var(--accent-l); }
.bloque-drag-handle { width:16px; display:flex; align-items:center; justify-content:center; cursor:grab; color:#d1d5db; font-size:1rem; flex-shrink:0; user-select:none; }
.bloque-drag-handle:active { cursor:grabbing; }
.bloque-drag-handle:hover { color:#9ca3af; }

/* ── Fila descanso por serie ── */
.descanso-row {
    display:flex; border-top:2px solid #bbf7d0;
    background:#f0fdf4; align-items:stretch;
}
.descanso-row-label {
    width:265px; flex-shrink:0; border-right:1px solid #bbf7d0;
    padding:8px 10px; font-size:0.6rem; font-weight:700; color:#065f46;
    text-transform:uppercase; letter-spacing:.06em;
    display:flex; align-items:center; gap:5px;
}
.descanso-row-cols { flex:1; display:flex; min-width:0; }
.descanso-serie-cell {
    flex:1; display:flex; flex-direction:column; align-items:center;
    justify-content:center; gap:4px; padding:7px 5px;
    border-right:1px solid #bbf7d0;
}
.descanso-serie-cell:last-child { border-right:none; }
.desc-inputs { display:flex; align-items:center; gap:3px; width:100%; }
.desc-select-min {
    flex:1.2; border:1px solid #a7f3d0; border-radius:5px;
    padding:3px 2px; font-size:0.68rem; font-family:'DM Mono',monospace;
    color:#065f46; background:white; text-align:center; min-width:0; cursor:pointer;
}
.desc-select-min:focus { outline:none; border-color:#059669; }
.desc-input-seg {
    flex:1; border:1px solid #a7f3d0; border-radius:5px;
    padding:3px 4px; font-size:0.68rem; font-family:'DM Mono',monospace;
    color:#065f46; background:white; text-align:center; min-width:0;
    -moz-appearance:textfield;
}
.desc-input-seg::-webkit-outer-spin-button,
.desc-input-seg::-webkit-inner-spin-button { -webkit-appearance:none; margin:0; }
.desc-input-seg:focus { outline:none; border-color:#059669; }
.desc-input-seg::placeholder { color:#a7f3d0; }
.desc-input-seg.error { border-color:#fca5a5 !important; background:#fff5f5; color:#ef4444; }
.desc-sep { font-size:0.75rem; font-weight:700; color:#6ee7b7; flex-shrink:0; line-height:1; }
.desc-preview {
    font-size:0.58rem; font-weight:700; border-radius:99px;
    padding:2px 8px; font-family:'DM Mono',monospace;
    text-align:center; white-space:nowrap;
}
.desc-preview.has-val { color:#475569; background:#e2e8f0; border:1px solid #cbd5e1; }
.desc-preview.no-val  { color:#cbd5e1; background:transparent; border:1px solid #e2e8f0; }

/* ── Copiar / reset serie ── */
.serie-header-col { flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:3px; padding:4px; font-size:0.65rem; font-weight:700; color:var(--accent); background:var(--accent-l); border-right:1px solid #bfdbfe; letter-spacing:.04em; text-transform:uppercase; }
.serie-header-col:last-child { border-right:none; }
.s-hcol-actions { display:flex; gap:3px; align-items:center; margin-top:1px; }
.btn-igualar-serie { padding:2px 6px; border:1px solid #bfdbfe; border-radius:4px; background:white; font-size:0.55rem; font-weight:700; color:#2563eb; cursor:pointer; font-family:'DM Sans',sans-serif; display:flex; align-items:center; gap:2px; transition:all .12s; white-space:nowrap; }
.btn-igualar-serie:hover { background:#2563eb; color:white; border-color:#2563eb; }
.btn-igualar-serie.done { background:#dcfce7; color:#16a34a; border-color:#86efac; }
.btn-reset-serie { padding:2px 5px; border:1px solid #fecaca; border-radius:4px; background:white; font-size:0.55rem; font-weight:700; color:#ef4444; cursor:pointer; font-family:'DM Sans',sans-serif; display:flex; align-items:center; transition:all .12s; }
.btn-reset-serie:hover { background:#ef4444; color:white; border-color:#ef4444; }
.btn-copiar-todas-bloque { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border:1px solid #bfdbfe; border-radius:5px; background:white; font-size:0.62rem; font-weight:700; color:#2563eb; cursor:pointer; font-family:'DM Sans',sans-serif; transition:all .12s; }
.btn-copiar-todas-bloque:hover { background:#2563eb; color:white; border-color:#2563eb; }
.btn-copiar-todas-bloque.done { background:#dcfce7; color:#16a34a; border-color:#86efac; }
.serie-col-flash-blue { background:#dbeafe !important; border-color:#2563eb !important; }
.serie-col-flash-red  { background:#fee2e2 !important; border-color:#fca5a5 !important; }
.bloque-toast { position:absolute; top:8px; left:50%; transform:translateX(-50%); font-size:0.68rem; font-weight:700; padding:4px 14px; border-radius:99px; opacity:0; transition:opacity .18s; pointer-events:none; white-space:nowrap; font-family:'DM Sans',sans-serif; z-index:100; }
.bloque-toast.blue { background:#1d4ed8; color:white; }
.bloque-toast.red  { background:#ef4444; color:white; }
.bloque-toast.show { opacity:1; }

/* ── Dropdown tipo bloque ── */
.bloque-tipo { cursor:pointer; user-select:none; position:relative; transition:filter .13s; }
.bloque-tipo:hover { filter:brightness(.92); }
.tipo-dropdown {
    display:none; position:fixed;
    background:white; border:1.5px solid var(--border2); border-radius:8px;
    box-shadow:0 12px 32px rgba(0,0,0,.22); z-index:9000; min-width:130px; overflow:hidden;
    filter:none;
}
.tipo-dropdown.open { display:block; }
.tipo-dropdown-item {
    display:flex; align-items:center; gap:7px; padding:7px 12px;
    font-size:0.72rem; font-weight:600; cursor:pointer; color:var(--text);
    border-bottom:1px solid var(--border); transition:background .1s;
}
.tipo-dropdown-item:last-child { border-bottom:none; }
.tipo-dropdown-item:hover { background:var(--accent-l); color:var(--accent); }
.tipo-dropdown-item.activo { background:var(--accent-l); color:var(--accent); }
.tipo-dropdown-item.activo:not(.circuito-item) { pointer-events:none; }
.tipo-dropdown-item.circuito-item { cursor:pointer; }
.tipo-dot { width:8px; height:8px; border-radius:99px; flex-shrink:0; }

/* ── Modal elegir ejercicio a eliminar ── */
.modal-eliminar-overlay {
    display:none; position:fixed; inset:0; background:rgba(0,0,0,.45);
    z-index:10010; align-items:center; justify-content:center; padding:16px;
}
.modal-eliminar-overlay.open { display:flex; }
.modal-eliminar-box {
    background:white; border-radius:14px; width:100%; max-width:360px;
    box-shadow:0 20px 60px rgba(0,0,0,.2); overflow:hidden;
}
.modal-eliminar-header {
    padding:14px 18px; border-bottom:1px solid var(--border);
    font-size:0.9rem; font-weight:700; color:var(--text);
}
.modal-eliminar-body { padding:12px 18px 16px; display:flex; flex-direction:column; gap:6px; }
.modal-eliminar-sub { font-size:0.75rem; color:var(--muted); margin-bottom:4px; }
.ej-eliminar-btn {
    display:flex; align-items:center; gap:8px; padding:8px 12px;
    border:1.5px solid var(--border); border-radius:7px; background:white;
    cursor:pointer; font-family:'DM Sans',sans-serif; font-size:0.78rem;
    color:var(--text); transition:all .12s; text-align:left;
}
.ej-eliminar-btn:hover { border-color:var(--danger); background:#fff5f5; color:var(--danger); }
.ej-eliminar-letra {
    width:20px; height:20px; border-radius:4px; display:flex; align-items:center;
    justify-content:center; font-size:0.6rem; font-weight:800; flex-shrink:0;
}
.modal-eliminar-cancel {
    margin-top:4px; padding:7px; border:1px solid var(--border2); border-radius:7px;
    background:white; color:var(--muted); font-size:0.78rem; font-weight:600;
    cursor:pointer; font-family:'DM Sans',sans-serif; width:100%;
}
.modal-eliminar-cancel:hover { background:#f3f4f6; }
</style>

{{-- MODAL MÉTODOS --}}
<div class="modal-overlay" id="modalMetodos" onclick="if(event.target===this) cerrarModal()">
    <div class="modal-box">
        <div class="modal-header">
            <h3>📚 Métodos de entrenamiento</h3>
            <button class="modal-close" onclick="cerrarModal()">✕</button>
        </div>
        <div class="modal-body">
            <div class="metodo-card"><div class="metodo-card-header"><span class="metodo-tag tag-normal">Normal</span><span class="metodo-card-nombre">Serie normal</span></div><div class="metodo-card-body">Repeticiones con peso fijo, descanso entre series. <b>Ideal para fuerza y masa muscular.</b></div></div>
            <div class="metodo-card"><div class="metodo-card-header"><span class="metodo-tag tag-888">Descendente</span><span class="metodo-card-nombre">Triple bajada de peso</span></div><div class="metodo-card-body">3 bloques del mismo número de reps bajando el peso sin descanso. <b>Ej: 8-8-8...</b></div></div>
            <div class="metodo-card"><div class="metodo-card-header"><span class="metodo-tag tag-restpause">Rest-pause</span><span class="metodo-card-nombre">Pausa y continúa</span></div><div class="metodo-card-body">Al fallo, pausa corta (10–20 seg) y continúa. <b>Acumula más reps de alta intensidad.</b></div></div>
            <div class="metodo-card"><div class="metodo-card-header"><span class="metodo-tag tag-21s">3 Rangos</span><span class="metodo-card-nombre">Tres rangos de movimiento</span></div><div class="metodo-card-body">Mitad inferior, mitad superior y recorrido completo. <b>Por defecto 7+7+7.</b></div></div>
            <div class="metodo-card"><div class="metodo-card-header"><span class="metodo-tag tag-10_21">10 + 21s</span><span class="metodo-card-nombre">Volumen e intensidad</span></div><div class="metodo-card-body">10 reps completas, luego −40% y 21s. <b>Fuerza + pump.</b></div></div>
            <div class="metodo-card"><div class="metodo-card-header"><span class="metodo-tag tag-isometria">Isometría + ROM</span><span class="metodo-card-nombre">Tensión estática y dinámica</span></div><div class="metodo-card-body">Un brazo isométrico mientras el otro trabaja dinámicamente. <b>Conexión neuromuscular.</b></div></div>
            <div class="metodo-card"><div class="metodo-card-header"><span class="metodo-tag tag-forzadas">Forzadas</span><span class="metodo-card-nombre">Repeticiones asistidas</span></div><div class="metodo-card-body">Al fallo, compañero asiste reps adicionales. <b>Requiere compañero.</b></div></div>
            <div class="metodo-card"><div class="metodo-card-header"><span class="metodo-tag tag-parciales">Parciales</span><span class="metodo-card-nombre">Rango reducido</span></div><div class="metodo-card-body">Solo la porción más difícil del recorrido. <b>Sobrecargar punto específico.</b></div></div>
            <div class="metodo-card"><div class="metodo-card-header"><span class="metodo-tag tag-negativas">Negativas</span><span class="metodo-card-nombre">Fase excéntrica controlada</span></div><div class="metodo-card-body">Bajada muy lenta (4–6 seg). <b>Mayor daño muscular.</b></div></div>
        </div>
    </div>
</div>

{{-- MODAL CIRCUITO --}}
<div class="modal-circ-overlay" id="modalCircuito" onclick="if(event.target===this)cerrarModalCircuito()">
    <div class="modal-circ-box">
        <h3>Circuito</h3>
        <p>¿Cuántos ejercicios? (4 – 12)</p>
        <input type="number" class="circ-num-input" id="circuitoNum" min="4" max="12" value="4">
        <div class="circ-btns">
            <button class="circ-btn-cancel" onclick="cerrarModalCircuito()">Cancelar</button>
            <button class="circ-btn-ok" onclick="confirmarCircuito()">Agregar</button>
        </div>
    </div>
</div>

{{-- MODAL ELIMINAR EJERCICIO --}}
<div class="modal-eliminar-overlay" id="modalEliminarEj" onclick="if(event.target===this)cerrarModalEliminar()">
    <div class="modal-eliminar-box">
        <div class="modal-eliminar-header">¿Cuál ejercicio eliminar?</div>
        <div class="modal-eliminar-body">
            <p class="modal-eliminar-sub">Al reducir el tipo, uno de los ejercicios se eliminará. Elige cuál:</p>
            <div id="eliminar-ej-lista"></div>
            <button class="modal-eliminar-cancel" onclick="cerrarModalEliminar()">Cancelar</button>
        </div>
    </div>
</div>

{{-- DROPDOWN TIPO GLOBAL --}}
<div class="tipo-dropdown" id="tipo-dd-global" onclick="event.stopPropagation()"></div>

{{-- MODAL PLANTILLA --}}
<div id="modalPlantilla" onclick="if(event.target===this)cerrarModalPlantilla()"
    style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:10002;align-items:center;justify-content:center;padding:16px;">
    <div style="background:white;border-radius:14px;width:100%;max-width:460px;box-shadow:0 20px 60px rgba(0,0,0,.2);">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #e2e5ea;border-radius:14px 14px 0 0;">
            <h3 style="font-size:1rem;font-weight:700;margin:0;">📋 Aplicar plantilla</h3>
            <button onclick="cerrarModalPlantilla()" style="width:28px;height:28px;border-radius:7px;background:#f3f4f6;border:none;cursor:pointer;font-size:1rem;color:#6b7280;">✕</button>
        </div>
        <div style="padding:16px 20px 20px;display:flex;flex-direction:column;gap:14px;">
            <div>
                <label style="font-size:0.72rem;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:6px;">Plantilla</label>
                <select id="selectPlantilla" onchange="mostrarInfoPlantilla()"
                    style="width:100%;border:1px solid #d0d5dd;border-radius:7px;padding:8px 10px;font-size:0.85rem;font-family:'DM Sans',sans-serif;color:#111827;">
                    <option value="">-- Selecciona una plantilla --</option>
                    @foreach($plantillas as $pt)
                        <option value="{{ $pt->id }}">{{ $pt->nombre }} · {{ count($pt->bloques ?? []) }} días</option>
                    @endforeach
                </select>
            </div>
            <div id="infoPlantilla" style="display:none;background:#f5f3ff;border:1px solid #ddd6fe;border-radius:8px;padding:10px 12px;font-size:0.82rem;color:#5b21b6;line-height:1.6;"></div>
            <div style="display:flex;gap:10px;">
                <div style="flex:1;">
                    <label style="font-size:0.72rem;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:6px;">Semana inicio</label>
                    <input type="number" id="plantillaSemanaInicio" min="1" max="16" value="{{ $semana }}"
                        style="width:100%;border:1px solid #d0d5dd;border-radius:7px;padding:8px 10px;font-size:0.9rem;font-family:'DM Mono',monospace;color:#111827;text-align:center;">
                </div>
                <div style="flex:1;">
                    <label style="font-size:0.72rem;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:6px;">Día inicio</label>
                    <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:3px;">
                        @foreach(['L','M','X','J','V','S','D'] as $idx => $letra)
                        <button type="button" onclick="setDiaInicioPlantilla({{ $idx+1 }}, this)"
                            data-dia-plt="{{ $idx+1 }}"
                            style="padding:5px 2px;border:1.5px solid {{ ($idx+1)==$dia ? '#7c3aed' : '#d0d5dd' }};border-radius:5px;background:{{ ($idx+1)==$dia ? '#f5f3ff' : 'white' }};color:{{ ($idx+1)==$dia ? '#7c3aed' : '#6b7280' }};font-size:0.72rem;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;">
                            {{ $letra }}
                        </button>
                        @endforeach
                    </div>
                    <input type="hidden" id="plantillaDiaInicio" value="{{ $dia }}">
                </div>
            </div>
            <div style="background:#fefce8;border:1px solid #fde047;border-radius:7px;padding:10px 12px;font-size:0.78rem;color:#854d0e;">
                ⚠️ Los días de la plantilla se pegarán <strong>consecutivamente</strong> desde la semana y día elegidos. Reemplaza cualquier rutina existente en esos días.
            </div>
            <form id="formAplicarPlantilla" method="POST" action="">
                @csrf
                <input type="hidden" name="cliente_id" value="{{ $cliente->id }}">
                <input type="hidden" name="semana_inicio" id="hiddenSemanaInicio">
                <input type="hidden" name="dia_inicio" id="hiddenDiaInicio">
                <button type="button" onclick="submitPlantillaCompleta()"
                    style="width:100%;padding:10px;background:#7c3aed;color:white;border:none;border-radius:8px;font-size:0.9rem;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;"
                    onmouseover="this.style.background='#6d28d9'" onmouseout="this.style.background='#7c3aed'">
                    ✅ Aplicar plantilla completa
                </button>
            </form>
        </div>
    </div>
</div>

{{-- MODAL COPIAR SEMANA --}}
<div id="modalCopiarSemana" onclick="if(event.target===this)cerrarModalCopiarSemana()"
    style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:10002;align-items:center;justify-content:center;padding:16px;">
    <div style="background:white;border-radius:14px;width:100%;max-width:400px;box-shadow:0 20px 60px rgba(0,0,0,.2);">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #e2e5ea;border-radius:14px 14px 0 0;">
            <h3 style="font-size:1rem;font-weight:700;margin:0;">📅 Copiar semana</h3>
            <button type="button" onclick="cerrarModalCopiarSemana()" style="width:28px;height:28px;border-radius:7px;background:#f3f4f6;border:none;cursor:pointer;font-size:1rem;color:#6b7280;">✕</button>
        </div>
        <form method="POST" action="{{ route('entrenador.rutina.copiarSemana', $cliente->id) }}">
            @csrf
            <div style="padding:16px 20px 20px;display:flex;flex-direction:column;gap:14px;">
                <div style="display:flex;gap:10px;">
                    <div style="flex:1;">
                        <label style="font-size:0.72rem;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:6px;">Semana origen</label>
                        <input type="number" name="semana_origen" min="1" max="52" value="{{ $semana }}" required
                            style="width:100%;border:1px solid #d0d5dd;border-radius:7px;padding:8px 10px;font-size:0.9rem;font-family:'DM Mono',monospace;color:#111827;text-align:center;">
                    </div>
                    <div style="flex:1;">
                        <label style="font-size:0.72rem;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:6px;">Semana destino</label>
                        <input type="number" name="semana_destino" min="1" max="52" required
                            style="width:100%;border:1px solid #d0d5dd;border-radius:7px;padding:8px 10px;font-size:0.9rem;font-family:'DM Mono',monospace;color:#111827;text-align:center;">
                    </div>
                </div>
                <div style="background:#fefce8;border:1px solid #fde047;border-radius:7px;padding:10px 12px;font-size:0.78rem;color:#854d0e;">
                    ⚠️ Esto <strong>reemplazará</strong> cualquier rutina existente en la semana destino con la rutina completa (los 7 días) de la semana origen.
                </div>
                <button type="submit"
                    style="width:100%;padding:10px;background:#059669;color:white;border:none;border-radius:8px;font-size:0.9rem;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;"
                    onmouseover="this.style.background='#047857'" onmouseout="this.style.background='#059669'">
                    ✅ Copiar semana
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL BORRAR HISTORIAL --}}
<div id="modalBorrarHistorial" onclick="if(event.target===this)cerrarModalBorrarHistorial()"
    style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:10002;align-items:center;justify-content:center;padding:16px;">
    <div style="background:white;border-radius:14px;width:100%;max-width:400px;box-shadow:0 20px 60px rgba(0,0,0,.2);">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #e2e5ea;border-radius:14px 14px 0 0;">
            <h3 style="font-size:1rem;font-weight:700;margin:0;color:#ef4444;">🗑️ Borrar historial</h3>
            <button type="button" onclick="cerrarModalBorrarHistorial()" style="width:28px;height:28px;border-radius:7px;background:#f3f4f6;border:none;cursor:pointer;font-size:1rem;color:#6b7280;">✕</button>
        </div>
        <form method="POST" action="{{ route('entrenador.rutina.borrarHistorial', $cliente->id) }}">
            @csrf
            <div style="padding:16px 20px 20px;display:flex;flex-direction:column;gap:14px;">
                <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:7px;padding:10px 12px;font-size:0.8rem;color:#991b1b;">
                    ⚠️ Esto borrará <strong>TODAS</strong> las rutinas de <strong>{{ $cliente->name }}</strong> en todas las semanas. Esta acción no se puede deshacer.
                </div>
                <div>
                    <label style="font-size:0.72rem;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:6px;">
                        Escribe "{{ $cliente->name }}" para confirmar
                    </label>
                    <input type="text" name="confirmar_nombre" required autocomplete="off"
                        style="width:100%;border:1px solid #d0d5dd;border-radius:7px;padding:8px 10px;font-size:0.85rem;color:#111827;">
                </div>
                <button type="submit"
                    style="width:100%;padding:10px;background:#ef4444;color:white;border:none;border-radius:8px;font-size:0.9rem;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;"
                    onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
                    Borrar historial permanentemente
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══ PÁGINA ══ --}}
<div class="page-header">
    <h2>{{ $cliente->name }}</h2>
    <span class="badge">Semana {{ $semana }} · Día {{ $dia }}</span>
    <button class="btn-metodos" onclick="abrirModal()">❓ Métodos</button>
    <button class="btn-metodos" onclick="abrirModalPlantilla()" style="border-color:#7c3aed;color:#7c3aed;">📋 Plantilla</button>
    <button class="btn-metodos" onclick="abrirModalCopiarSemana()" style="border-color:#059669;color:#059669;">📅 Copiar semana</button>
    <button class="btn-metodos" onclick="abrirModalBorrarHistorial()" style="border-color:#ef4444;color:#ef4444;">🗑️ Borrar historial</button>
</div>

@php
    $diasCortoNav = ['L','M','X','J','V','S','D'];
    $totalSemanas = $cliente->plan->semanas ?? 16;
@endphp

<div class="flex items-center gap-1.5 mb-4 relative" id="wp-nav">
    @php $prevDia=$dia-1; $prevSem=$semana; if($prevDia<1){$prevDia=7;$prevSem--;} @endphp
    <a href="{{ $prevSem>=1 ? route('entrenador.rutina.editar',[$cliente->id,$prevSem,$prevDia]) : '#' }}"
       class="w-6 h-6 flex items-center justify-center rounded border border-gray-200 bg-gray-50 text-gray-400 hover:border-blue-400 hover:text-blue-500 transition-colors text-xs flex-shrink-0 {{ $prevSem<1 ? 'opacity-30 pointer-events-none' : '' }}">‹</a>

    <div id="semTrigger" onclick="toggleSemDropdown(event)"
         class="flex items-center gap-1 px-2.5 py-1 rounded-md border border-gray-200 bg-gray-50 cursor-pointer text-[11px] font-semibold text-gray-500 hover:border-blue-400 hover:text-blue-500 transition-colors flex-shrink-0 select-none whitespace-nowrap">
        <span>Sem {{ $semana }}</span>
        <i class="ti ti-chevron-down text-[10px] transition-transform duration-150" id="semChevron"></i>
    </div>

    <div id="semDropdown" onclick="event.stopPropagation()"
         class="hidden absolute top-full left-0 mt-1 bg-white border border-gray-200 rounded-lg p-1.5 grid grid-cols-4 gap-1 z-50 shadow-md min-w-[180px]">
        @for($s=1; $s<=$totalSemanas; $s++)
            @php $tieneSem = collect($diasConRutina ?? [])->contains(fn($c)=>str_starts_with($c,$s.'-')); @endphp
            <a href="{{ route('entrenador.rutina.editar',[$cliente->id,$s,$dia]) }}"
               class="text-center py-1.5 rounded border text-[11px] font-medium transition-colors
                      {{ $s==$semana ? 'bg-blue-50 border-blue-300 text-blue-600' : ($tieneSem ? 'border-blue-200 text-gray-500 hover:bg-blue-50 hover:text-blue-600' : 'border-gray-200 text-gray-400 hover:bg-gray-50') }}">
                S{{ $s }}
            </a>
        @endfor
    </div>

    <div class="flex gap-1 flex-1">
        @foreach($diasCortoNav as $i => $letra)
            @php $numDia=$i+1; $esActivo=$numDia==(int)$dia; $tiene=in_array($semana.'-'.$numDia,$diasConRutina??[]); @endphp
            <a href="{{ route('entrenador.rutina.editar',[$cliente->id,$semana,$numDia]) }}"
               class="flex-1 flex flex-col items-center gap-0.5 py-1.5 rounded-md transition-colors
                      {{ $esActivo ? 'border border-blue-400 bg-blue-50' : 'border border-transparent hover:bg-gray-50 hover:border-gray-200' }}">
                <span class="text-[9px] uppercase tracking-wide leading-none {{ $esActivo ? 'text-blue-600 font-bold' : 'text-gray-400' }}">{{ $letra }}</span>
                <span class="text-[11px] font-bold leading-none {{ $esActivo ? 'text-blue-700' : 'text-gray-700' }}">{{ $numDia }}</span>
                <div class="w-1 h-1 rounded-full {{ $esActivo ? 'bg-blue-500' : ($tiene ? 'bg-blue-300' : 'bg-transparent') }}"></div>
            </a>
        @endforeach
    </div>

    @php $nextDia=$dia+1; $nextSem=$semana; if($nextDia>7){$nextDia=1;$nextSem++;} @endphp
    <a href="{{ $nextSem<=$totalSemanas ? route('entrenador.rutina.editar',[$cliente->id,$nextSem,$nextDia]) : '#' }}"
       class="w-6 h-6 flex items-center justify-center rounded border border-gray-200 bg-gray-50 text-gray-400 hover:border-blue-400 hover:text-blue-500 transition-colors text-xs flex-shrink-0 {{ $nextSem>$totalSemanas ? 'opacity-30 pointer-events-none' : '' }}">›</a>
</div>

<script>
    const R2_URL = "{{ env('AWS_URL') }}";

function toggleSemDropdown(e){
    e.stopPropagation();
    const dd=document.getElementById('semDropdown'), ch=document.getElementById('semChevron');
    const open=dd.classList.toggle('hidden');
    ch.style.transform=open?'':'rotate(180deg)';
}
document.addEventListener('click',()=>{
    document.getElementById('semDropdown')?.classList.add('hidden');
    document.getElementById('semChevron').style.transform='';
});
</script>

<form method="POST" action="{{ route('entrenador.rutina.guardar', [$cliente->id,$semana,$dia]) }}" id="form-rutina">
@csrf
<input type="hidden" name="datos_json" id="datos_json">

<div class="nota-sesion-card">
    <div class="nota-sesion-label">
        <i class="ti ti-clipboard-text"></i>
        Nota de la sesión
    </div>
    <textarea id="nota-sesion" class="nota-sesion-textarea"
        placeholder="Contexto del día, observaciones generales, indicaciones para el cliente…">{{ $notaSesion ?? '' }}</textarea>
</div>

<div id="contenedor-bloques">
@foreach($bloques as $grupo => $rutinasGrupo)
@php
    $tipo       = $rutinasGrupo->first()->tipo;
    $cantidad   = count($rutinasGrupo);
    $seriesRaw  = $rutinasGrupo->first()->series ?? [];
    if (is_string($seriesRaw)) $seriesRaw = json_decode($seriesRaw, true) ?? [];
    $numSeries  = count($seriesRaw);
    $LETRAS_PHP = ['ej-letra-a','ej-letra-b','ej-letra-c','ej-letra-d','ej-letra-e','ej-letra-f','ej-letra-g','ej-letra-h','ej-letra-i','ej-letra-j','ej-letra-k','ej-letra-l'];
    $BGS_PHP    = ['ej-bg-a','ej-bg-b','ej-bg-c','ej-bg-d','ej-bg-e','ej-bg-f','ej-bg-g','ej-bg-h','ej-bg-i','ej-bg-j','ej-bg-k','ej-bg-l'];
    $NUMS_PHP   = ['1','2','3','4','5','6','7','8','9','10','11','12'];
    $bloqueData = $rutinasGrupo->first();
    $descSeries = $bloqueData->descansos_serie ?? [];
    if (is_string($descSeries)) $descSeries = json_decode($descSeries, true) ?? [];
@endphp
<div class="bloque" data-grupo="{{ $grupo }}" data-tipo="{{ $tipo }}" style="position:relative;">
    <div class="bloque-toast blue" id="toast-{{ $grupo }}"></div>
    <div class="bloque-header" style="overflow:visible;position:relative;z-index:10;">
        <div class="bloque-drag-handle" title="Arrastrar" style="width:10px;font-size:0.75rem;flex-shrink:0;cursor:grab;color:#d1d5db;display:flex;align-items:center;">⠿</div>
        <span class="bloque-tipo tipo-{{ strtolower($tipo) }}" style="flex-shrink:0;"
              onclick="toggleTipoDropdown(this,'{{ $grupo }}',{{ $cantidad }})">
            {{ strtoupper($tipo) }}
            @if(strtolower($tipo) === 'circuito')<span class="circuito-cant" style="opacity:.7;font-size:.55rem"> · {{ $cantidad }} ej.</span>@endif
            <i class="ti ti-chevron-down" style="font-size:0.55rem;margin-left:2px;vertical-align:1px"></i>
        </span>
        <div class="bloque-series-count" style="margin-left:auto;flex-shrink:0;">
            Series:
            <input type="number" min="1" value="{{ $numSeries }}" placeholder="–"
                onchange="generarSeriesBloque(this,'{{ $grupo }}',{{ $cantidad }})">
        </div>
        <button type="button" class="btn-copiar-todas-bloque"
            onclick="copiarS1ATodas('{{ $grupo }}',{{ $cantidad }},{{ $numSeries }})">
            <i class="ti ti-copy" style="font-size:11px"></i> S1 → todas
        </button>
        <button type="button" class="btn-remove" onclick="this.closest('.bloque').remove();actualizarOrden();">✕</button>
    </div>

    <div class="series-header-row" data-header="{{ $grupo }}">
        <div class="col-info-header">Ejercicio</div>
        <div class="col-series-headers">
            @for($s = 0; $s < $numSeries; $s++)
            <div class="serie-header-col">
                <span>S{{ $s+1 }}</span>
                <div class="s-hcol-actions">
                    @if($s === 0)
                        <span style="font-size:0.5rem;color:#93c5fd;font-weight:600">ref</span>
                    @else
                        <button type="button" class="btn-igualar-serie"
                            onclick="igualarSerieBloque('{{ $grupo }}',{{ $cantidad }},{{ $s }})">
                            <i class="ti ti-copy" style="font-size:9px"></i> =S1
                        </button>
                    @endif
                    <button type="button" class="btn-reset-serie"
                        onclick="resetSerieBloque('{{ $grupo }}',{{ $cantidad }},{{ $s }})">
                        <i class="ti ti-x" style="font-size:9px"></i>
                    </button>
                </div>
            </div>
            @endfor
        </div>
    </div>

    @foreach($rutinasGrupo as $i => $rutina)
    @php
        $seriesRaw  = $rutina->series ?? [];
        if (is_string($seriesRaw)) $seriesRaw = json_decode($seriesRaw, true) ?? [];
        $series     = is_array($seriesRaw) ? $seriesRaw : [];
        $ejActual   = ($ejerciciosPorGrupo[$rutina->segmento] ?? collect())->firstWhere('id', $rutina->ejercicio_id);
        $imgActual  = $ejActual->imagen ?? null;
        $bgClass    = $BGS_PHP[$i]    ?? 'ej-bg-a';
        $letraClass = $LETRAS_PHP[$i] ?? 'ej-letra-a';
        $numLetra   = $NUMS_PHP[$i]   ?? ($i+1);
        $notaEj     = $rutina->nota_ej ?? '';
    @endphp
    <div class="ejercicio-row {{ $bgClass }}">
        <div class="ej-letra {{ $letraClass }}">{{ $numLetra }}</div>
        <div class="col-segmento">
            <div class="field-label">Segmento</div>
            <select class="segmento-select" data-ej="ej-{{ $grupo }}-{{ $i }}" onchange="onSegmentoChange(this)">
                @foreach($ejerciciosPorGrupo as $seg => $list)
                    <option value="{{ $seg }}" {{ $seg==$rutina->segmento?'selected':'' }}>{{ $seg }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-ejercicio">
            <div class="field-label">Ejercicio</div>
            <input type="hidden" id="ej-{{ $grupo }}-{{ $i }}" class="ejercicio-id-input" value="{{ $rutina->ejercicio_id }}">
            <div class="ej-select-wrapper" data-target="ej-{{ $grupo }}-{{ $i }}">
                <div class="ej-select-trigger" onclick="toggleDropdown(this)">
                    @if($imgActual)<img src="{{ $r2Url.'/'.$imgActual }}" alt="">@else<img src="" alt="" style="display:none;">@endif
                    <div class="{{ $ejActual?'ej-trigger-nombre':'ej-trigger-placeholder' }}">{{ $ejActual->nombre ?? '-- Ejercicio --' }}</div>
                    <span class="ej-trigger-arrow">▼</span>
                </div>
                <div class="ej-select-dropdown">
                    @foreach($ejerciciosPorGrupo[$rutina->segmento] ?? [] as $ej)
                    <div class="ej-select-option {{ $ej->id==$rutina->ejercicio_id?'selected':'' }}"
                         data-value="{{ $ej->id }}" data-nombre="{{ $ej->nombre }}"
                         data-imagen="{{ $ej->imagen ? $r2Url.'/'.$ej->imagen : '' }}"
                         onclick="seleccionarEjercicio(this)">

                    @if($ej->imagen)<img src="{{ $r2Url.'/'.$ej->imagen }}" alt="{{ $ej->nombre }}">@else<div class="ej-no-img">Sin img</div>@endif                         <span>{{ $ej->nombre }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="nota-ej-input-wrap">
                <i class="ti ti-pencil"></i>
                <textarea class="nota-ej-input" placeholder="nota…">{{ $notaEj }}</textarea>
            </div>
        </div>
        <div class="col-series">
            <div class="series-cols" data-grupo="{{ $grupo }}" data-ej="{{ $i }}">
                @foreach($series as $s => $serie)
                @php
                    $metodo      = $serie['metodo'] ?? 'normal';
                    $tempoActivo = !empty($serie['tempo_activo']) && $serie['tempo_activo'] !== '0';
                    $tE = $serie['tempo_excentrica'] ?? ''; $tP = $serie['tempo_pausa'] ?? ''; $tC = $serie['tempo_concentrica'] ?? '';
                    $rirActivo = !empty($serie['rir_activo']) && $serie['rir_activo'] !== '0';
                    $rirModo = $serie['rir_modo'] ?? 'rir'; $rirVal = $serie['rir_valor'] ?? '';
                @endphp
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
                    <div class="metodo-fields {{ $metodo==='normal'?'active':'' }}" data-metodo="normal">
                        <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" data-key="reps" value="{{ $serie['reps']??'' }}" placeholder="–"></div>
                        <div class="campo-wrap"><label>Peso</label><div class="peso-group"><input class="campo-input" type="number" step="0.5" data-key="peso" value="{{ $serie['peso']??'' }}" placeholder="–"><select class="unidad-select" data-key="unidad"><option value="kg" {{ ($serie['unidad']??'kg')==='kg'?'selected':'' }}>kg</option><option value="lb" {{ ($serie['unidad']??'kg')==='lb'?'selected':'' }}>lb</option></select></div></div>
                    </div>
                    <div class="metodo-fields {{ $metodo==='888'?'active':'' }}" data-metodo="888">
                        <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" min="1" data-key="reps_888" value="{{ $serie['reps_888']??8 }}" placeholder="8" oninput="actualizar888Nota(this)"></div>
                        <div class="campo-wrap"><label>P1</label><div class="peso-group"><input class="campo-input" type="number" step="0.5" data-key="peso1" value="{{ $serie['peso1']??'' }}" placeholder="–"><select class="unidad-select" data-key="unidad1"><option value="kg" {{ ($serie['unidad1']??'kg')==='kg'?'selected':'' }}>kg</option><option value="lb">lb</option></select></div></div>
                        <div class="campo-wrap"><label>P2</label><div class="peso-group"><input class="campo-input" type="number" step="0.5" data-key="peso2" value="{{ $serie['peso2']??'' }}" placeholder="–"><select class="unidad-select" data-key="unidad2"><option value="kg" {{ ($serie['unidad2']??'kg')==='kg'?'selected':'' }}>kg</option><option value="lb">lb</option></select></div></div>
                        <div class="campo-wrap"><label>P3</label><div class="peso-group"><input class="campo-input" type="number" step="0.5" data-key="peso3" value="{{ $serie['peso3']??'' }}" placeholder="–"><select class="unidad-select" data-key="unidad3"><option value="kg" {{ ($serie['unidad3']??'kg')==='kg'?'selected':'' }}>kg</option><option value="lb">lb</option></select></div></div>
                        <div class="metodo-nota nota-888">{{ $serie['reps_888']??8 }} c/u·desc.</div>
                    </div>
                    <div class="metodo-fields {{ $metodo==='restpause'?'active':'' }}" data-metodo="restpause">
                        <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" data-key="reps_rp" value="{{ $serie['reps']??'' }}" placeholder="–"></div>
                        <div class="campo-wrap"><label>Peso</label><div class="peso-group"><input class="campo-input" type="number" step="0.5" data-key="peso_rp" value="{{ $serie['peso']??'' }}" placeholder="–"><select class="unidad-select" data-key="unidad_rp"><option value="kg" {{ ($serie['unidad_rp']??'kg')==='kg'?'selected':'' }}>kg</option><option value="lb">lb</option></select></div></div>
                        <div class="campo-wrap"><label>Desc(s)</label><input class="campo-input" type="number" data-key="descanso" value="{{ $serie['descanso']??15 }}" placeholder="15"></div>
                        <div class="metodo-nota">Fallo→pausa</div>
                    </div>
                    <div class="metodo-fields {{ $metodo==='21s'?'active':'' }}" data-metodo="21s">
                        @php $r21 = $serie['reps_21s']??7; @endphp
                        <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" min="1" data-key="reps_21s" value="{{ $r21 }}" placeholder="7" oninput="actualizar21sNota(this)"></div>
                        <div class="campo-wrap"><label>Peso</label><div class="peso-group"><input class="campo-input" type="number" step="0.5" data-key="peso_21s" value="{{ $serie['peso_21s']??$serie['peso']??'' }}" placeholder="–"><select class="unidad-select" data-key="unidad_21s"><option value="kg" {{ ($serie['unidad_21s']??'kg')==='kg'?'selected':'' }}>kg</option><option value="lb">lb</option></select></div></div>
                        <div class="metodo-nota nota-21s">{{ $r21 }}+{{ $r21 }}+{{ $r21 }}</div>
                    </div>
                    <div class="metodo-fields {{ $metodo==='10_21'?'active':'' }}" data-metodo="10_21">
                        <div class="campo-wrap"><label>P×10</label><div class="peso-group"><input class="campo-input" type="number" step="0.5" data-key="peso_10" value="{{ $serie['peso_10']??'' }}" placeholder="–" oninput="calcular40(this)"><select class="unidad-select" data-key="unidad_10"><option value="kg" {{ ($serie['unidad_10']??'kg')==='kg'?'selected':'' }}>kg</option><option value="lb">lb</option></select></div></div>
                        <div class="campo-wrap"><label>P×21s</label><div class="peso-group"><input class="campo-input peso-21-result" type="number" step="0.5" data-key="peso_21" value="{{ $serie['peso_21']??'' }}" placeholder="Auto"><select class="unidad-select" data-key="unidad_21"><option value="kg" {{ ($serie['unidad_21']??'kg')==='kg'?'selected':'' }}>kg</option><option value="lb">lb</option></select></div></div>
                        <div class="metodo-nota">−40%→21s</div>
                    </div>
                    <div class="metodo-fields {{ $metodo==='isometria'?'active':'' }}" data-metodo="isometria">
                        <div class="campo-wrap"><label>Peso</label><div class="peso-group"><input class="campo-input" type="number" step="0.5" data-key="peso_iso" value="{{ $serie['peso_iso']??$serie['peso']??'' }}" placeholder="–"><select class="unidad-select" data-key="unidad_iso"><option value="kg" {{ ($serie['unidad_iso']??'kg')==='kg'?'selected':'' }}>kg</option><option value="lb">lb</option></select></div></div>
                        <div class="campo-wrap"><label>R/brazo</label><input class="campo-input" type="number" data-key="reps_brazo" value="{{ $serie['reps_brazo']??4 }}" placeholder="4"></div>
                        <div class="campo-wrap"><label>R/ambos</label><input class="campo-input" type="number" data-key="reps_ambos" value="{{ $serie['reps_ambos']??8 }}" placeholder="8"></div>
                    </div>
                    <div class="metodo-fields {{ $metodo==='forzadas'?'active':'' }}" data-metodo="forzadas">
                        <div class="campo-wrap"><label>R.solo</label><input class="campo-input" type="number" data-key="reps_fz" value="{{ $serie['reps']??'' }}" placeholder="–"></div>
                        <div class="campo-wrap"><label>R.asist</label><input class="campo-input" type="number" data-key="reps_asistidas" value="{{ $serie['reps_asistidas']??'' }}" placeholder="–"></div>
                        <div class="campo-wrap"><label>Peso</label><div class="peso-group"><input class="campo-input" type="number" step="0.5" data-key="peso_fz" value="{{ $serie['peso']??'' }}" placeholder="–"><select class="unidad-select" data-key="unidad_fz"><option value="kg" {{ ($serie['unidad_fz']??'kg')==='kg'?'selected':'' }}>kg</option><option value="lb">lb</option></select></div></div>
                    </div>
                    <div class="metodo-fields {{ $metodo==='parciales'?'active':'' }}" data-metodo="parciales">
                        <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" data-key="reps_pc" value="{{ $serie['reps']??'' }}" placeholder="–"></div>
                        <div class="campo-wrap"><label>Peso</label><div class="peso-group"><input class="campo-input" type="number" step="0.5" data-key="peso_pc" value="{{ $serie['peso']??'' }}" placeholder="–"><select class="unidad-select" data-key="unidad_pc"><option value="kg" {{ ($serie['unidad_pc']??'kg')==='kg'?'selected':'' }}>kg</option><option value="lb">lb</option></select></div></div>
                        <div class="metodo-nota">Parcial</div>
                    </div>
                    <div class="metodo-fields {{ $metodo==='negativas'?'active':'' }}" data-metodo="negativas">
                        <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" data-key="reps_ng" value="{{ $serie['reps']??'' }}" placeholder="–"></div>
                        <div class="campo-wrap"><label>Peso</label><div class="peso-group"><input class="campo-input" type="number" step="0.5" data-key="peso_ng" value="{{ $serie['peso']??'' }}" placeholder="–"><select class="unidad-select" data-key="unidad_ng"><option value="kg" {{ ($serie['unidad_ng']??'kg')==='kg'?'selected':'' }}>kg</option><option value="lb">lb</option></select></div></div>
                        <div class="metodo-nota">Excéntrica</div>
                    </div>
                    <div class="tempo-wrap">
                        <button type="button" class="tempo-toggle {{ $tempoActivo?'active':'' }}" onclick="toggleTempo(this)">⏱ <span>{{ $tempoActivo ? $tE.'-'.$tP.'-'.$tC : 'Tempo' }}</span></button>
                        <input type="hidden" data-key="tempo_activo" value="{{ $tempoActivo?'1':'0' }}">
                        <div class="tempo-fields {{ $tempoActivo?'open':'' }}">
                            <div class="tempo-row">
                                <div class="tempo-cell"><div class="tempo-icon">↓</div><div class="tempo-label">Excén<br>trica</div><input class="campo-input tempo-input" type="number" min="0" max="10" data-key="tempo_excentrica" value="{{ $tE }}" placeholder="0" oninput="actualizarTempoLabel(this)"><div class="tempo-unit">seg</div></div>
                                <div class="tempo-sep">–</div>
                                <div class="tempo-cell"><div class="tempo-icon">⏸</div><div class="tempo-label">Pausa<br>abajo</div><input class="campo-input tempo-input" type="number" min="0" max="10" data-key="tempo_pausa" value="{{ $tP }}" placeholder="0" oninput="actualizarTempoLabel(this)"><div class="tempo-unit">seg</div></div>
                                <div class="tempo-sep">–</div>
                                <div class="tempo-cell"><div class="tempo-icon">↑</div><div class="tempo-label">Concén<br>trica</div><input class="campo-input tempo-input" type="number" min="0" max="10" data-key="tempo_concentrica" value="{{ $tC }}" placeholder="0" oninput="actualizarTempoLabel(this)"><div class="tempo-unit">seg</div></div>
                            </div>
                            <div class="tempo-preview">{{ $tempoActivo ? $tE.' – '.$tP.' – '.$tC : '↓ – ⏸ – ↑' }}</div>
                        </div>
                    </div>
                    <div class="rir-wrap">
                        <button type="button" class="rir-toggle {{ $rirActivo?'active':'' }}" onclick="toggleRir(this)">🎯 <span>{{ $rirActivo && $rirVal ? ($rirModo==='rir'?'RIR ':'RPE ').$rirVal : 'RIR/RPE' }}</span></button>
                        <input type="hidden" data-key="rir_activo" value="{{ $rirActivo?'1':'0' }}">
                        <div class="rir-fields {{ $rirActivo?'open':'' }}">
                            <div class="rir-mode-row">
                                <button type="button" class="rir-mode-btn {{ $rirModo==='rir'?'sel':'' }}" onclick="setRirModo(this,'rir')">RIR</button>
                                <button type="button" class="rir-mode-btn {{ $rirModo==='rpe'?'sel':'' }}" onclick="setRirModo(this,'rpe')">RPE</button>
                            </div>
                            <input type="hidden" data-key="rir_modo" value="{{ $rirModo }}">
                            <input class="campo-input" type="number" min="0" max="10" step="0.5" data-key="rir_valor" value="{{ $rirVal }}" placeholder="–" oninput="actualizarRirLabel(this)">
                            <div class="rir-scale">{{ $rirModo==='rir' ? 'RIR 0 = fallo · RIR 2 = 2 reps reserva' : 'RPE 10 = fallo · RPE 7 = moderado' }}</div>
                            <div class="rir-preview">{{ $rirActivo && $rirVal ? ($rirModo==='rir'?'RIR ':'RPE ').$rirVal : '–' }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach

    {{-- Fila descanso por serie --}}
    @if($numSeries > 0)
    <div class="descanso-row" data-descanso-row="{{ $grupo }}">
        <div class="descanso-row-label">
            <i class="ti ti-moon" style="font-size:13px"></i>
            Descanso
        </div>
        <div class="descanso-row-cols">
            @for($s = 0; $s < $numSeries; $s++)
            @php
                $segTotal = (int)($descSeries[$s]['valor'] ?? 0);
                $minVal   = (int)floor($segTotal / 60);
                $segVal   = $segTotal % 60;
                $preview  = $segTotal === 0 ? '–'
                    : ($minVal === 0 ? $segVal.'s'
                    : ($segVal === 0 ? $minVal.'m' : $minVal.'m '.$segVal.'s'));
                $prevClass = $segTotal > 0 ? 'has-val' : 'no-val';
            @endphp
            <div class="descanso-serie-cell">
                <div class="desc-inputs">
                    <select class="desc-select-min"
                        data-desc-min="{{ $grupo }}-{{ $s }}"
                        onchange="onDescChange(this,'{{ $grupo }}','{{ $s }}')">
                        @for($m = 0; $m <= 10; $m++)
                            <option value="{{ $m }}" {{ $m===$minVal?'selected':'' }}>{{ $m }}m</option>
                        @endfor
                    </select>
                    <span class="desc-sep">:</span>
                    <input type="number" class="desc-input-seg"
                           min="0" max="59"
                           data-desc-seg="{{ $grupo }}-{{ $s }}"
                           value="{{ $segVal > 0 ? $segVal : '' }}"
                           placeholder="00"
                           oninput="onDescChange(this,'{{ $grupo }}','{{ $s }}')">
                </div>
                <div class="desc-preview {{ $prevClass }}" id="desc-prev-{{ $grupo }}-{{ $s }}">{{ $preview }}</div>
            </div>
            @endfor
        </div>
    </div>
    @endif

    <div class="bloque-footer">
        <span style="font-size:0.68rem;color:var(--muted);">
            <i class="ti ti-info-circle" style="font-size:12px;vertical-align:-1px;margin-right:3px"></i>
            Descanso configurado por serie
        </span>
    </div>
</div>
@endforeach
</div>

<div class="add-block-bar" style="margin-top:10px;">
    <button type="button" onclick="agregarBloque('monoserie',1)" class="add-block-btn">＋ Lineal</button>
    <button type="button" onclick="agregarBloque('biserie',2)"   class="add-block-btn">＋ Biserie</button>
    <button type="button" onclick="agregarBloque('triserie',3)"  class="add-block-btn">＋ Triserie</button>
    <button type="button" onclick="abrirModalCircuito()"         class="add-block-btn">＋ Circuito</button>
</div>

<a href="{{ route('entrenador.rutina.pdf', [$cliente->id,$semana,$dia]) }}" target="_blank" class="btn-pdf">📄 Exportar PDF</a>
<button type="button" onclick="guardarRutina()" class="btn-guardar" id="btn-guardar">💾 &nbsp;Guardar rutina</button>
</form>

<script>
const ejerciciosPorGrupo = @json($ejerciciosPorGrupo);
const plantillasData     = @json($plantillas->keyBy('id'));
let contador = Date.now();
const contenedor = document.getElementById('contenedor-bloques');
const NUMS   = ['1','2','3','4','5','6','7','8','9','10','11','12'];
const LETRAS = ['ej-letra-a','ej-letra-b','ej-letra-c','ej-letra-d','ej-letra-e','ej-letra-f','ej-letra-g','ej-letra-h','ej-letra-i','ej-letra-j','ej-letra-k','ej-letra-l'];
const BGS    = ['ej-bg-a','ej-bg-b','ej-bg-c','ej-bg-d','ej-bg-e','ej-bg-f','ej-bg-g','ej-bg-h','ej-bg-i','ej-bg-j','ej-bg-k','ej-bg-l'];

/* ── Modales ── */
function abrirModal()  { document.getElementById('modalMetodos').classList.add('open'); document.body.style.overflow='hidden'; }
function cerrarModal() { document.getElementById('modalMetodos').classList.remove('open'); document.body.style.overflow=''; }
document.addEventListener('keydown', e=>{ if(e.key==='Escape'){ cerrarModal(); cerrarModalCircuito(); cerrarModalPlantilla(); cerrarModalEliminar(); cerrarModalCopiarSemana(); cerrarModalBorrarHistorial(); } });
function abrirModalCircuito() { document.getElementById('circuitoNum').value=4; document.getElementById('modalCircuito').classList.add('open'); setTimeout(()=>document.getElementById('circuitoNum').focus(),50); }
function cerrarModalCircuito() { document.getElementById('modalCircuito').classList.remove('open'); }
function confirmarCircuito() {
    const n = Math.min(12, Math.max(4, parseInt(document.getElementById('circuitoNum').value) || 4));
    const pending = _pendingCircuitoCambio; // guardar ANTES de cerrar
    _pendingCircuitoCambio = null;
    cerrarModalCircuito();

    if (pending) {
        const { grupo, cantidadActual } = pending;
        if (n === cantidadActual) return; // misma cantidad, nada que hacer
        if (n > cantidadActual) {
            // Agregar ejercicios
            aplicarCambioTipo(grupo, 'circuito', n, cantidadActual);
        } else {
            // Reducir — ir directo al modal de selección sin pasar por cambiarTipoBloque
            const bloque = document.querySelector(`.bloque[data-grupo="${grupo}"]`);
            const lista = document.getElementById('eliminar-ej-lista');
            lista.innerHTML = '';
            const letrasColors = [
                {bg:'#eff6ff',color:'#1d4ed8'},{bg:'#f0fdf4',color:'#065f46'},
                {bg:'#fffbeb',color:'#92400e'},{bg:'#fdf2f8',color:'#9d174d'},
                {bg:'#e0f2fe',color:'#1d4ed8'},{bg:'#dcfce7',color:'#065f46'},
                {bg:'#fef9c3',color:'#92400e'},{bg:'#fce7f3',color:'#9d174d'},
                {bg:'#dbeafe',color:'#1e40af'},{bg:'#d1fae5',color:'#166534'},
                {bg:'#fef3c7',color:'#854d0e'},{bg:'#fdf2f8',color:'#831843'},
            ];
            bloque.querySelectorAll('.ejercicio-row').forEach((row, i) => {
                const nombre = row.querySelector('.ej-trigger-nombre')?.textContent
                    || row.querySelector('.ej-trigger-placeholder')?.textContent
                    || `Ejercicio ${i+1}`;
                const segmento = row.querySelector('.segmento-select')?.value || '';
                const lc = letrasColors[i] || letrasColors[0];
                const letraLabel = ['A','B','C','D','E','F','G','H','I','J','K','L'][i] || (i+1);
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'ej-eliminar-btn';
                btn.innerHTML = `
                    <span class="ej-eliminar-letra" style="background:${lc.bg};color:${lc.color}">${letraLabel}</span>
                    <span><strong style="display:block;font-size:0.78rem">${nombre}</strong>
                    <span style="font-size:0.65rem;color:var(--muted)">${segmento}</span></span>
                    <i class="ti ti-trash" style="font-size:13px;color:#ef4444;margin-left:auto" aria-hidden="true"></i>`;
                btn.onclick = () => {
                    cerrarModalEliminar();
                    eliminarEjRow(grupo, bloque.querySelectorAll('.ejercicio-row'));
                    const remaining = bloque.querySelectorAll('.ejercicio-row').length;
                    if (remaining > n) {
                        // Guardar pending y volver a preguntar
                        _pendingCircuitoCambio = { grupo, cantidadActual: remaining };
                        setTimeout(() => confirmarCircuitoReducir(grupo, n, remaining), 200);
                    } else {
                        aplicarCambioTipo(grupo, 'circuito', n, remaining);
                    }
                };
                // Capturar índice en closure
                const idx = i;
                btn.onclick = () => {
                    cerrarModalEliminar();
                    eliminarEjRow(grupo, idx);
                    const remaining = bloque.querySelectorAll('.ejercicio-row').length;
                    if (remaining > n) {
                        setTimeout(() => confirmarCircuitoReducir(grupo, n, remaining), 200);
                    } else {
                        aplicarCambioTipo(grupo, 'circuito', n, remaining);
                    }
                };
                lista.appendChild(btn);
            });
            document.getElementById('modalEliminarEj').classList.add('open');
        }
        return;
    }

    agregarBloque('circuito', n);
}
document.getElementById('circuitoNum').addEventListener('keydown', e=>{ if(e.key==='Enter') confirmarCircuito(); if(e.key==='Escape') cerrarModalCircuito(); });
function abrirModalPlantilla()  { document.getElementById('modalPlantilla').style.display='flex'; }
function cerrarModalPlantilla() { document.getElementById('modalPlantilla').style.display='none'; }
function abrirModalCopiarSemana()  { document.getElementById('modalCopiarSemana').style.display='flex'; }
function cerrarModalCopiarSemana() { document.getElementById('modalCopiarSemana').style.display='none'; }
function abrirModalBorrarHistorial()  { document.getElementById('modalBorrarHistorial').style.display='flex'; }
function cerrarModalBorrarHistorial() { document.getElementById('modalBorrarHistorial').style.display='none'; }
function mostrarInfoPlantilla() {
    const plantillaId=document.getElementById('selectPlantilla').value, info=document.getElementById('infoPlantilla');
    if(!plantillaId){ info.style.display='none'; return; }
    const plantilla=plantillasData[plantillaId], numDias=Object.keys(plantilla?.bloques??{}).length;
    info.style.display='block'; info.innerHTML=`📅 <strong>${plantilla.nombre}</strong><br>${numDias} días de entrenamiento`;
}
function setDiaInicioPlantilla(dia, btn) {
    document.getElementById('plantillaDiaInicio').value=dia;
    document.querySelectorAll('#modalPlantilla [data-dia-plt]').forEach(b=>{ b.style.borderColor='#d0d5dd'; b.style.background='white'; b.style.color='#6b7280'; });
    btn.style.borderColor='#7c3aed'; btn.style.background='#f5f3ff'; btn.style.color='#7c3aed';
}
function submitPlantillaCompleta() {
    const plantillaId=document.getElementById('selectPlantilla').value;
    if(!plantillaId){ alert('Selecciona una plantilla'); return; }
    document.getElementById('hiddenSemanaInicio').value=document.getElementById('plantillaSemanaInicio').value;
    document.getElementById('hiddenDiaInicio').value=document.getElementById('plantillaDiaInicio').value;
    const form=document.getElementById('formAplicarPlantilla');
    form.action=`/entrenador/plantillas/${plantillaId}/aplicar`; form.submit();
}

/* ── Auto-expand nota ── */
function autoExpandNota(el) { el.style.height='0'; el.style.height=el.scrollHeight+'px'; }
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.nota-ej-input').forEach(el => { autoExpandNota(el); el.addEventListener('input', () => autoExpandNota(el)); });
});
document.getElementById('contenedor-bloques').addEventListener('input', e => {
    if (e.target.classList.contains('nota-ej-input')) autoExpandNota(e.target);
});

/* ── Descanso por serie ── */
function formatearTiempo(seg) {
    seg = parseInt(seg) || 0;
    if (seg === 0) return '–';
    const m = Math.floor(seg / 60), s = seg % 60;
    if (m === 0) return s + 's';
    if (s === 0) return m + 'm';
    return m + 'm ' + s + 's';
}
function onDescChange(el, grupo, s) {
    const segInput = document.querySelector(`[data-desc-seg="${grupo}-${s}"]`);
    // Validar máximo 59 segundos
    if (segInput && el === segInput) {
        let segVal = parseInt(segInput.value) || 0;
        if (segVal > 59) {
            segInput.value = 59;
            segInput.classList.add('error');
            setTimeout(() => segInput.classList.remove('error'), 800);
        } else {
            segInput.classList.remove('error');
        }
    }
    const m     = parseInt(document.querySelector(`[data-desc-min="${grupo}-${s}"]`)?.value) || 0;
    const seg   = parseInt(segInput?.value) || 0;
    const total = m * 60 + seg;
    const prev  = document.getElementById(`desc-prev-${grupo}-${s}`);
    if (!prev) return;
    prev.textContent = formatearTiempo(total);
    prev.className   = 'desc-preview ' + (total > 0 ? 'has-val' : 'no-val');
}
function htmlDescSerieCell(grupo, s, segTotal) {
    segTotal = parseInt(segTotal) || 0;
    const minVal = Math.floor(segTotal / 60), segVal = segTotal % 60;
    let optsMin = '';
    for (let m = 0; m <= 10; m++) optsMin += `<option value="${m}" ${m===minVal?'selected':''}>${m}m</option>`;
    const preview = formatearTiempo(segTotal), prevClass = segTotal > 0 ? 'has-val' : 'no-val';
    return `<div class="descanso-serie-cell">
        <div class="desc-inputs">
            <select class="desc-select-min" data-desc-min="${grupo}-${s}" onchange="onDescChange(this,'${grupo}','${s}')">${optsMin}</select>
            <span class="desc-sep">:</span>
            <input type="number" class="desc-input-seg" min="0" max="59"
                   data-desc-seg="${grupo}-${s}"
                   value="${segVal > 0 ? segVal : ''}" placeholder="00"
                   oninput="onDescChange(this,'${grupo}','${s}')">
        </div>
        <div class="desc-preview ${prevClass}" id="desc-prev-${grupo}-${s}">${preview}</div>
    </div>`;
}
function regenerarDescansoRow(grupo, numSeries, valores) {
    const bloque = document.querySelector(`.bloque[data-grupo="${grupo}"]`);
    if (!bloque) return;
    bloque.querySelector('.descanso-row')?.remove();
    if (numSeries < 1) return;
    let celdas = '';
    for (let s = 0; s < numSeries; s++) {
        const seg = parseInt(valores?.[s]?.valor) || 0;
        celdas += htmlDescSerieCell(grupo, s, seg);
    }
    const fila = `<div class="descanso-row" data-descanso-row="${grupo}">
        <div class="descanso-row-label"><i class="ti ti-moon" style="font-size:13px"></i> Descanso</div>
        <div class="descanso-row-cols">${celdas}</div>
    </div>`;
    bloque.querySelector('.bloque-footer').insertAdjacentHTML('beforebegin', fila);
}

/* ── Tempo ── */
function toggleTempo(btn) {
    const wrap=btn.closest('.tempo-wrap'), fields=wrap.querySelector('.tempo-fields'), hidden=wrap.querySelector('[data-key="tempo_activo"]'), isOpen=fields.classList.contains('open');
    fields.classList.toggle('open',!isOpen); btn.classList.toggle('active',!isOpen); hidden.value=isOpen?'0':'1';
    if(isOpen){btn.querySelector('span').textContent='Tempo';}else{actualizarTempoLabel(wrap.querySelector('[data-key="tempo_excentrica"]'));}
}
function actualizarTempoLabel(input) {
    const wrap=input.closest('.tempo-wrap');
    const tE=wrap.querySelector('[data-key="tempo_excentrica"]')?.value||'0';
    const tP=wrap.querySelector('[data-key="tempo_pausa"]')?.value||'0';
    const tC=wrap.querySelector('[data-key="tempo_concentrica"]')?.value||'0';
    const btn=wrap.querySelector('.tempo-toggle span'), prev=wrap.querySelector('.tempo-preview');
    if(btn) btn.textContent=`${tE}–${tP}–${tC}`; if(prev) prev.textContent=`${tE} – ${tP} – ${tC}`;
}

/* ── RIR/RPE ── */
function toggleRir(btn) {
    const wrap=btn.closest('.rir-wrap'), fields=wrap.querySelector('.rir-fields'), hidden=wrap.querySelector('[data-key="rir_activo"]'), isOpen=fields.classList.contains('open');
    fields.classList.toggle('open',!isOpen); btn.classList.toggle('active',!isOpen); hidden.value=isOpen?'0':'1';
    if(isOpen){btn.querySelector('span').textContent='RIR/RPE';}else{actualizarRirLabel(wrap.querySelector('[data-key="rir_valor"]'));}
}
function actualizarRirLabel(input) {
    const wrap=input.closest('.rir-wrap'), modo=wrap.querySelector('[data-key="rir_modo"]')?.value||'rir', val=wrap.querySelector('[data-key="rir_valor"]')?.value||'';
    const label=modo==='rir'?'RIR':'RPE', btn=wrap.querySelector('.rir-toggle span'), prev=wrap.querySelector('.rir-preview'), scale=wrap.querySelector('.rir-scale');
    if(btn) btn.textContent=val?`${label} ${val}`:'RIR/RPE';
    if(prev) prev.textContent=val?`${label} ${val}`:'–';
    if(scale) scale.textContent=modo==='rir'?'RIR 0 = fallo · RIR 2 = 2 reps reserva':'RPE 10 = fallo · RPE 7 = moderado';
}
function setRirModo(btn, modo) {
    const wrap=btn.closest('.rir-wrap'), hidden=wrap.querySelector('[data-key="rir_modo"]');
    if(hidden) hidden.value=modo;
    wrap.querySelectorAll('.rir-mode-btn').forEach(b=>b.classList.remove('sel')); btn.classList.add('sel');
    actualizarRirLabel(wrap.querySelector('[data-key="rir_valor"]'));
}

/* ── GUARDAR ── */
function guardarRutina() {
    const btn = document.getElementById('btn-guardar');
    btn.disabled = true; btn.textContent = '⏳ Guardando...';
    const notaSesion = document.getElementById('nota-sesion')?.value || '';
    const bloques = {};
    let orden = 0;
    document.querySelectorAll('#contenedor-bloques .bloque').forEach(bloque => {
        const grupo = bloque.dataset.grupo, tipo = bloque.dataset.tipo;
        if (!grupo) return;
        const descansosSerie = [];
        bloque.querySelectorAll('.descanso-serie-cell').forEach((cell, s) => {
            const m   = parseInt(cell.querySelector(`[data-desc-min="${grupo}-${s}"]`)?.value) || 0;
            const seg = parseInt(cell.querySelector(`[data-desc-seg="${grupo}-${s}"]`)?.value) || 0;
            const total = m * 60 + seg;
            descansosSerie.push({ valor: total > 0 ? String(total) : '' });
        });
        bloques[grupo] = { tipo, orden: orden++, descansos_serie: descansosSerie, ejercicios: {} };
        bloque.querySelectorAll('.ejercicio-row').forEach((ejRow, i) => {
            const segmento     = ejRow.querySelector('.segmento-select')?.value    ?? '';
            const ejercicio_id = ejRow.querySelector('.ejercicio-id-input')?.value ?? '';
            const nota_ej      = ejRow.querySelector('.nota-ej-input')?.value      ?? '';
            const series = [];
            ejRow.querySelectorAll('[data-serie]').forEach(col => {
                const metodo = col.querySelector('.metodo-select')?.value ?? 'normal';
                const s = { metodo };
                col.querySelectorAll('[data-key]').forEach(el => { s[el.dataset.key] = el.value; });
                series.push(s);
            });
            bloques[grupo].ejercicios[i] = { segmento, ejercicio_id, nota_ej, series };
        });
    });
    document.getElementById('datos_json').value = JSON.stringify({ nota_sesion: notaSesion, bloques });
    document.getElementById('form-rutina').submit();
}

/* ── Dropdown ejercicios ── */
function toggleDropdown(trigger) {
    const wrapper=trigger.closest('.ej-select-wrapper'), dropdown=wrapper.querySelector('.ej-select-dropdown'), isOpen=dropdown.classList.contains('open');
    document.querySelectorAll('.ej-select-dropdown.open').forEach(d=>d.classList.remove('open'));
    if(!isOpen) dropdown.classList.add('open');
}
function seleccionarEjercicio(option) {
    const wrapper=option.closest('.ej-select-wrapper'), trigger=wrapper.querySelector('.ej-select-trigger'), hidden=document.getElementById(wrapper.dataset.target);
    hidden.value=option.dataset.value;
    const img=trigger.querySelector('img'), label=trigger.querySelector('.ej-trigger-nombre,.ej-trigger-placeholder');
    if(option.dataset.imagen){img.src=option.dataset.imagen;img.style.display='block';}else{img.src='';img.style.display='none';}
    label.className='ej-trigger-nombre'; label.textContent=option.dataset.nombre;
    wrapper.querySelectorAll('.ej-select-option').forEach(o=>o.classList.remove('selected')); option.classList.add('selected');
    wrapper.querySelector('.ej-select-dropdown').classList.remove('open');
}
document.addEventListener('click', e=>{ if(!e.target.closest('.ej-select-wrapper')) document.querySelectorAll('.ej-select-dropdown.open').forEach(d=>d.classList.remove('open')); });
function onSegmentoChange(select) {
    const ejId=select.dataset.ej, seg=select.value, wrapper=document.querySelector(`.ej-select-wrapper[data-target="${ejId}"]`);
    if(!wrapper) return;
    const trigger=wrapper.querySelector('.ej-select-trigger'), img=trigger.querySelector('img'), label=trigger.querySelector('.ej-trigger-nombre,.ej-trigger-placeholder');
    const dropdown=wrapper.querySelector('.ej-select-dropdown'), hidden=document.getElementById(ejId);
    hidden.value=''; img.src=''; img.style.display='none'; label.className='ej-trigger-placeholder'; label.textContent='-- Ejercicio --'; dropdown.innerHTML='';
    (ejerciciosPorGrupo[seg]??[]).forEach(e=>{
        const url=e.imagen?`${R2_URL}/${e.imagen}`:'', div=document.createElement('div');
        div.className='ej-select-option'; div.dataset.value=e.id; div.dataset.nombre=e.nombre; div.dataset.imagen=url; div.onclick=()=>seleccionarEjercicio(div);
        div.innerHTML=url?`<img src="${url}" alt="${e.nombre}"><span>${e.nombre}</span>`:`<div class="ej-no-img">Sin img</div><span>${e.nombre}</span>`;
        dropdown.appendChild(div);
    });
}
document.addEventListener('change', e=>{ if(e.target.classList.contains('segmento-select')) onSegmentoChange(e.target); });

/* ── Utilidades ── */
function actualizarOrden(){ document.querySelectorAll('#contenedor-bloques .bloque').forEach((b,i)=>{ b.dataset.orden=i; }); }
function cambiarMetodo(select){ select.closest('.serie-col').querySelectorAll('.metodo-fields').forEach(d=>d.classList.toggle('active',d.dataset.metodo===select.value)); }
function calcular40(input){ const p=parseFloat(input.value)||0, c=input.closest('.serie-col').querySelector('.peso-21-result'); if(c) c.value=p>0?Math.round(p*.6*2)/2:''; }
function actualizar888Nota(input){ const n=input.closest('.metodo-fields').querySelector('.nota-888'); if(n) n.textContent=`${input.value||'?'} c/u·desc.`; }
function actualizar21sNota(input){ const n=input.closest('.metodo-fields').querySelector('.nota-21s'), r=input.value||'?'; if(n) n.textContent=`${r}+${r}+${r}`; }
function actualizarHeader(grupo, numSeries) {
    const header = document.querySelector(`.series-header-row[data-header="${grupo}"] .col-series-headers`);
    if (!header) return;
    header.innerHTML = '';
    const cantidad = document.querySelectorAll(`.bloque[data-grupo="${grupo}"] .ejercicio-row`).length || 1;
    for (let s = 0; s < numSeries; s++) {
        const d = document.createElement('div');
        d.className = 'serie-header-col';
        const acciones = s === 0
            ? `<span style="font-size:0.5rem;color:#93c5fd;font-weight:600">ref</span>
               <button type="button" class="btn-reset-serie" onclick="resetSerieBloque('${grupo}',${cantidad},0)">
                   <i class="ti ti-x" style="font-size:9px"></i>
               </button>`
            : `<button type="button" class="btn-igualar-serie" onclick="igualarSerieBloque('${grupo}',${cantidad},${s})">
                   <i class="ti ti-copy" style="font-size:9px"></i> =S1
               </button>
               <button type="button" class="btn-reset-serie" onclick="resetSerieBloque('${grupo}',${cantidad},${s})">
                   <i class="ti ti-x" style="font-size:9px"></i>
               </button>`;
        d.innerHTML = `<span>S${s+1}</span><div class="s-hcol-actions">${acciones}</div>`;
        header.appendChild(d);
    }
}

/* ── Copiar S1 / Reset serie ── */
function showBloqueToast(grupo, msg, color) {
    const t = document.getElementById(`toast-${grupo}`);
    if (!t) return;
    t.textContent = msg;
    t.className = `bloque-toast ${color} show`;
    setTimeout(() => t.classList.remove('show'), 1800);
}

function flashSerieCol(grupo, ejIdx, serieIdx, color) {
    const rows = document.querySelectorAll(`.bloque[data-grupo="${grupo}"] .ejercicio-row`);
    const row = rows[ejIdx];
    if (!row) return;
    const col = row.querySelectorAll('[data-serie]')[serieIdx];
    if (!col) return;
    col.classList.add(color === 'blue' ? 'serie-col-flash-blue' : 'serie-col-flash-red');
    setTimeout(() => col.classList.remove('serie-col-flash-blue', 'serie-col-flash-red'), 350);
}

function copiarS1AColumna(grupo, cantidadEjs, serieIdx) {
    const bloque = document.querySelector(`.bloque[data-grupo="${grupo}"]`);
    if (!bloque) return;
    bloque.querySelectorAll('.ejercicio-row').forEach((row, ejIdx) => {
        const cols  = row.querySelectorAll('[data-serie]');
        const colS1 = cols[0], colDst = cols[serieIdx];
        if (!colS1 || !colDst) return;
        const metodoS1 = colS1.querySelector('.metodo-select')?.value ?? 'normal';
        const metodoDst = colDst.querySelector('.metodo-select');
        if (metodoDst) { metodoDst.value = metodoS1; cambiarMetodo(metodoDst); }
        colS1.querySelectorAll('[data-key]').forEach(el => {
            const dest = colDst.querySelector(`[data-key="${el.dataset.key}"]`);
            if (dest) dest.value = el.value;
        });
        flashSerieCol(grupo, ejIdx, serieIdx, 'blue');
    });
}

function resetColumna(grupo, serieIdx) {
    const bloque = document.querySelector(`.bloque[data-grupo="${grupo}"]`);
    if (!bloque) return;
    bloque.querySelectorAll('.ejercicio-row').forEach((row, ejIdx) => {
        const col = row.querySelectorAll('[data-serie]')[serieIdx];
        if (!col) return;
        col.querySelectorAll('[data-key]').forEach(el => { if (el.tagName === 'INPUT') el.value = ''; });
        const metodo = col.querySelector('.metodo-select');
        if (metodo) { metodo.value = 'normal'; cambiarMetodo(metodo); }
        flashSerieCol(grupo, ejIdx, serieIdx, 'red');
    });
}

function igualarSerieBloque(grupo, cantidadEjs, serieIdx) {
    copiarS1AColumna(grupo, cantidadEjs, serieIdx);
    const hdr = document.querySelector(`.series-header-row[data-header="${grupo}"] .col-series-headers`);
    const btn = hdr?.querySelectorAll('.serie-header-col')?.[serieIdx]?.querySelector('.btn-igualar-serie');
    if (btn) {
        btn.classList.add('done');
        btn.innerHTML = '<i class="ti ti-check" style="font-size:9px"></i> ok';
        setTimeout(() => { btn.classList.remove('done'); btn.innerHTML = '<i class="ti ti-copy" style="font-size:9px"></i> =S1'; }, 2000);
    }
    showBloqueToast(grupo, `S${serieIdx+1} igualada a S1`, 'blue');
}

function resetSerieBloque(grupo, cantidadEjs, serieIdx) {
    resetColumna(grupo, serieIdx);
    showBloqueToast(grupo, `S${serieIdx+1} limpiada`, 'red');
}

function copiarS1ATodas(grupo, cantidadEjs, numSeriesParam) {
    const bloque = document.querySelector(`.bloque[data-grupo="${grupo}"]`);
    if (!bloque) return;
    // Leer numSeries real en el momento de ejecutar
    const numSeries = bloque.querySelectorAll('.ejercicio-row:first-child [data-serie]').length
        || parseInt(bloque.querySelector('.bloque-series-count input')?.value) || 0;
    if (numSeries < 2) { showBloqueToast(grupo, 'Agrega al menos 2 series', 'red'); return; }
    const hdr = document.querySelector(`.series-header-row[data-header="${grupo}"] .col-series-headers`);
    for (let s = 1; s < numSeries; s++) {
        copiarS1AColumna(grupo, cantidadEjs, s);
        const btn = hdr?.querySelectorAll('.serie-header-col')?.[s]?.querySelector('.btn-igualar-serie');
        if (btn) {
            btn.classList.add('done');
            btn.innerHTML = '<i class="ti ti-check" style="font-size:9px"></i> ok';
            setTimeout(() => { btn.classList.remove('done'); btn.innerHTML = '<i class="ti ti-copy" style="font-size:9px"></i> =S1'; }, 2000);
        }
    }
    const btnTodas = bloque.querySelector('.btn-copiar-todas-bloque');
    if (btnTodas) {
        btnTodas.classList.add('done');
        btnTodas.innerHTML = '<i class="ti ti-check" style="font-size:11px"></i> Copiado';
        setTimeout(() => { btnTodas.classList.remove('done'); btnTodas.innerHTML = '<i class="ti ti-copy" style="font-size:11px"></i> S1 → todas'; }, 2000);
    }
    showBloqueToast(grupo, 'S1 copiada a todas las series', 'blue');
}

/* ── HTML serie col ── */
function htmlSerieCol(ex={}) {
    const m=ex.metodo??'normal', a=k=>m===k?'active':'', v=(k,d='')=>ex[k]??d;
    const pg=(lbl,pk,uk)=>`<div class="campo-wrap"><label>${lbl}</label><div class="peso-group"><input class="campo-input" type="number" step="0.5" data-key="${pk}" value="${v(pk)}" placeholder="–"><select class="unidad-select" data-key="${uk}"><option value="kg" ${v(uk,'kg')==='kg'?'selected':''}>kg</option><option value="lb" ${v(uk,'kg')==='lb'?'selected':''}>lb</option></select></div></div>`;
    const r21=v('reps_21s','7'), tA=ex.tempo_activo==='1'||ex.tempo_activo===true;
    const tE=v('tempo_excentrica',''), tP=v('tempo_pausa',''), tC=v('tempo_concentrica','');
    const rA=ex.rir_activo==='1'||ex.rir_activo===true, rM=v('rir_modo','rir'), rV=v('rir_valor','');
    return `<div class="serie-col" data-serie>
        <select class="metodo-select" onchange="cambiarMetodo(this)">
            <option value="normal" ${m==='normal'?'selected':''}>Normal</option>
            <option value="888" ${m==='888'?'selected':''}>Descend.</option>
            <option value="restpause" ${m==='restpause'?'selected':''}>Rest-pause</option>
            <option value="21s" ${m==='21s'?'selected':''}>3 Rangos</option>
            <option value="10_21" ${m==='10_21'?'selected':''}>10+21s</option>
            <option value="isometria" ${m==='isometria'?'selected':''}>Isometría</option>
            <option value="forzadas" ${m==='forzadas'?'selected':''}>Forzadas</option>
            <option value="parciales" ${m==='parciales'?'selected':''}>Parciales</option>
            <option value="negativas" ${m==='negativas'?'selected':''}>Negativas</option>
        </select>
        <div class="metodo-fields ${a('normal')}" data-metodo="normal">
            <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" data-key="reps" value="${v('reps')}" placeholder="–"></div>${pg('Peso','peso','unidad')}
        </div>
        <div class="metodo-fields ${a('888')}" data-metodo="888">
            <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" min="1" data-key="reps_888" value="${v('reps_888','8')}" placeholder="8" oninput="actualizar888Nota(this)"></div>
            ${pg('P1','peso1','unidad1')}${pg('P2','peso2','unidad2')}${pg('P3','peso3','unidad3')}
            <div class="metodo-nota nota-888">${v('reps_888','8')} c/u·desc.</div>
        </div>
        <div class="metodo-fields ${a('restpause')}" data-metodo="restpause">
            <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" data-key="reps_rp" value="${v('reps_rp')}" placeholder="–"></div>
            ${pg('Peso','peso_rp','unidad_rp')}
            <div class="campo-wrap"><label>Desc(s)</label><input class="campo-input" type="number" data-key="descanso" value="${v('descanso','15')}" placeholder="15"></div>
            <div class="metodo-nota">Fallo→pausa</div>
        </div>
        <div class="metodo-fields ${a('21s')}" data-metodo="21s">
            <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" min="1" data-key="reps_21s" value="${r21}" placeholder="7" oninput="actualizar21sNota(this)"></div>
            ${pg('Peso','peso_21s','unidad_21s')}<div class="metodo-nota nota-21s">${r21}+${r21}+${r21}</div>
        </div>
        <div class="metodo-fields ${a('10_21')}" data-metodo="10_21">
            <div class="campo-wrap"><label>P×10</label><div class="peso-group"><input class="campo-input" type="number" step="0.5" data-key="peso_10" value="${v('peso_10')}" placeholder="–" oninput="calcular40(this)"><select class="unidad-select" data-key="unidad_10"><option value="kg" ${v('unidad_10','kg')==='kg'?'selected':''}>kg</option><option value="lb">lb</option></select></div></div>
            <div class="campo-wrap"><label>P×21s</label><div class="peso-group"><input class="campo-input peso-21-result" type="number" step="0.5" data-key="peso_21" value="${v('peso_21')}" placeholder="Auto"><select class="unidad-select" data-key="unidad_21"><option value="kg" ${v('unidad_21','kg')==='kg'?'selected':''}>kg</option><option value="lb">lb</option></select></div></div>
            <div class="metodo-nota">−40%→21s</div>
        </div>
        <div class="metodo-fields ${a('isometria')}" data-metodo="isometria">
            ${pg('Peso','peso_iso','unidad_iso')}
            <div class="campo-wrap"><label>R/brazo</label><input class="campo-input" type="number" data-key="reps_brazo" value="${v('reps_brazo','4')}" placeholder="4"></div>
            <div class="campo-wrap"><label>R/ambos</label><input class="campo-input" type="number" data-key="reps_ambos" value="${v('reps_ambos','8')}" placeholder="8"></div>
        </div>
        <div class="metodo-fields ${a('forzadas')}" data-metodo="forzadas">
            <div class="campo-wrap"><label>R.solo</label><input class="campo-input" type="number" data-key="reps_fz" value="${v('reps_fz')}" placeholder="–"></div>
            <div class="campo-wrap"><label>R.asist</label><input class="campo-input" type="number" data-key="reps_asistidas" value="${v('reps_asistidas')}" placeholder="–"></div>
            ${pg('Peso','peso_fz','unidad_fz')}
        </div>
        <div class="metodo-fields ${a('parciales')}" data-metodo="parciales">
            <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" data-key="reps_pc" value="${v('reps_pc')}" placeholder="–"></div>
            ${pg('Peso','peso_pc','unidad_pc')}<div class="metodo-nota">Parcial</div>
        </div>
        <div class="metodo-fields ${a('negativas')}" data-metodo="negativas">
            <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" data-key="reps_ng" value="${v('reps_ng')}" placeholder="–"></div>
            ${pg('Peso','peso_ng','unidad_ng')}<div class="metodo-nota">Excéntrica</div>
        </div>
        <div class="tempo-wrap">
            <button type="button" class="tempo-toggle ${tA?'active':''}" onclick="toggleTempo(this)">⏱ <span>${tA?tE+'-'+tP+'-'+tC:'Tempo'}</span></button>
            <input type="hidden" data-key="tempo_activo" value="${tA?'1':'0'}">
            <div class="tempo-fields ${tA?'open':''}">
                <div class="tempo-row">
                    <div class="tempo-cell"><div class="tempo-icon">↓</div><div class="tempo-label">Excén<br>trica</div><input class="campo-input tempo-input" type="number" min="0" max="10" data-key="tempo_excentrica" value="${tE}" placeholder="0" oninput="actualizarTempoLabel(this)"><div class="tempo-unit">seg</div></div>
                    <div class="tempo-sep">–</div>
                    <div class="tempo-cell"><div class="tempo-icon">⏸</div><div class="tempo-label">Pausa<br>abajo</div><input class="campo-input tempo-input" type="number" min="0" max="10" data-key="tempo_pausa" value="${tP}" placeholder="0" oninput="actualizarTempoLabel(this)"><div class="tempo-unit">seg</div></div>
                    <div class="tempo-sep">–</div>
                    <div class="tempo-cell"><div class="tempo-icon">↑</div><div class="tempo-label">Concén<br>trica</div><input class="campo-input tempo-input" type="number" min="0" max="10" data-key="tempo_concentrica" value="${tC}" placeholder="0" oninput="actualizarTempoLabel(this)"><div class="tempo-unit">seg</div></div>
                </div>
                <div class="tempo-preview">${tA&&(tE||tP||tC)?tE+' – '+tP+' – '+tC:'↓ – ⏸ – ↑'}</div>
            </div>
        </div>
        <div class="rir-wrap">
            <button type="button" class="rir-toggle ${rA?'active':''}" onclick="toggleRir(this)">🎯 <span>${rA&&rV?(rM==='rir'?'RIR ':'RPE ')+rV:'RIR/RPE'}</span></button>
            <input type="hidden" data-key="rir_activo" value="${rA?'1':'0'}">
            <div class="rir-fields ${rA?'open':''}">
                <div class="rir-mode-row">
                    <button type="button" class="rir-mode-btn ${rM==='rir'?'sel':''}" onclick="setRirModo(this,'rir')">RIR</button>
                    <button type="button" class="rir-mode-btn ${rM==='rpe'?'sel':''}" onclick="setRirModo(this,'rpe')">RPE</button>
                </div>
                <input type="hidden" data-key="rir_modo" value="${rM}">
                <input class="campo-input" type="number" min="0" max="10" step="0.5" data-key="rir_valor" value="${rV}" placeholder="–" oninput="actualizarRirLabel(this)">
                <div class="rir-scale">${rM==='rir'?'RIR 0 = fallo · RIR 2 = 2 reps reserva':'RPE 10 = fallo · RPE 7 = moderado'}</div>
                <div class="rir-preview">${rA&&rV?(rM==='rir'?'RIR ':'RPE ')+rV:'–'}</div>
            </div>
        </div>
    </div>`;
}

function bloqueFooterHTML() {
    return `<div class="bloque-footer">
        <span style="font-size:0.68rem;color:var(--muted);">
            <i class="ti ti-info-circle" style="font-size:12px;vertical-align:-1px;margin-right:3px"></i>
            Descanso configurado por serie
        </span>
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
for (let s = 0; s < n; s++) container.insertAdjacentHTML('beforeend', htmlSerieCol(exArr[s] ?? {}));
    }
    // Capturar descansos ya ingresados antes de reconstruir la fila
    const bloqueEl = document.querySelector(`.bloque[data-grupo="${grupo}"]`);
    const valoresDescPrevios = [];
    bloqueEl?.querySelectorAll('.descanso-serie-cell').forEach((cell, s) => {
        const m   = parseInt(cell.querySelector(`[data-desc-min="${grupo}-${s}"]`)?.value) || 0;
        const seg = parseInt(cell.querySelector(`[data-desc-seg="${grupo}-${s}"]`)?.value) || 0;
        valoresDescPrevios.push({ valor: m * 60 + seg });
    });
    regenerarDescansoRow(grupo, n, valoresDescPrevios);
}

function agregarBloque(tipo, cantidad) {
    const grupo='G'+contador++, opts=Object.keys(ejerciciosPorGrupo).map(s=>`<option value="${s}">${s}</option>`).join('');
    let html=`<div class="bloque" data-grupo="${grupo}" data-tipo="${tipo}" style="position:relative;">
        <div class="bloque-toast blue" id="toast-${grupo}"></div>
        <div class="bloque-header">
            <div class="bloque-drag-handle" title="Arrastrar">⠿</div>
            <span class="bloque-tipo tipo-${tipo.toLowerCase()}" style="flex-shrink:0;"
                  onclick="toggleTipoDropdown(this,'${grupo}',${cantidad})">
                ${tipo.toUpperCase()}${tipo==='circuito'?`<span class="circuito-cant" style="opacity:.7;font-size:.55rem"> · ${cantidad} ej.</span>`:''}
                <i class="ti ti-chevron-down" style="font-size:0.55rem;margin-left:2px;vertical-align:1px"></i>
            </span>
            <div class="bloque-series-count" style="margin-left:auto;flex-shrink:0;">Series:<input type="number" min="1" placeholder="–" onchange="generarSeriesBloque(this,'${grupo}',${cantidad})"></div>
            <button type="button" class="btn-copiar-todas-bloque" onclick="copiarS1ATodas('${grupo}',${cantidad},0)">
                <i class="ti ti-copy" style="font-size:11px"></i> S1 → todas
            </button>
            <button type="button" class="btn-remove" onclick="this.closest('.bloque').remove();actualizarOrden();">✕</button>
        </div>
        <div class="series-header-row" data-header="${grupo}"><div class="col-info-header">Ejercicio</div><div class="col-series-headers"></div></div>`;
    for(let i=0;i<cantidad;i++){
        const ejId=`ej-${grupo}-${i}`, lClass=LETRAS[i%LETRAS.length], bgClass=BGS[i%BGS.length];
        html+=`<div class="ejercicio-row ${bgClass}">
            <div class="ej-letra ${lClass}">${NUMS[i]??(i+1)}</div>
            <div class="col-segmento"><div class="field-label">Segmento</div>
                <select class="segmento-select" data-ej="${ejId}" onchange="onSegmentoChange(this)">
                    <option value="">-- Segmento --</option>${opts}
                </select>
            </div>
            <div class="col-ejercicio"><div class="field-label">Ejercicio</div>
                <input type="hidden" id="${ejId}" class="ejercicio-id-input" value="">
                <div class="ej-select-wrapper" data-target="${ejId}">
                    <div class="ej-select-trigger" onclick="toggleDropdown(this)">
                        <img src="" alt="" style="display:none;">
                        <span class="ej-trigger-placeholder">-- Ejercicio --</span>
                        <span class="ej-trigger-arrow">▼</span>
                    </div>
                    <div class="ej-select-dropdown"></div>
                </div>
                <div class="nota-ej-input-wrap">
                    <i class="ti ti-pencil"></i>
                    <textarea class="nota-ej-input" placeholder="nota…"></textarea>
                </div>
            </div>
            <div class="col-series"><div class="series-cols" data-grupo="${grupo}" data-ej="${i}"></div></div>
        </div>`;
    }
    html += bloqueFooterHTML() + '</div>';
    contenedor.insertAdjacentHTML('beforeend', html);
    contenedor.querySelectorAll('.nota-ej-input').forEach(el => {
        autoExpandNota(el);
        if(!el._expandBound){ el.addEventListener('input',()=>autoExpandNota(el)); el._expandBound=true; }
    });
    actualizarOrden();
}

/* ── Cambiar tipo de bloque ── */
const TIPO_CONFIG = {
    monoserie: { label:'LINEAL',    cls:'tipo-monoserie', max:1  },
    biserie:   { label:'BISERIE',   cls:'tipo-biserie',   max:2  },
    triserie:  { label:'TRISERIE',  cls:'tipo-triserie',  max:3  },
    circuito:  { label:'CIRCUITO',  cls:'tipo-circuito',  max:12 },
};

let _pendingTipoCambio = null;
let _pendingCircuitoCambio = null;

let _tipoDropdownGrupo = null;

function toggleTipoDropdown(badge, grupo, cantidad) {
    const dd = document.getElementById('tipo-dd-global');
    const isOpen = dd.classList.contains('open') && _tipoDropdownGrupo === grupo;

    // Cerrar siempre primero
    dd.classList.remove('open');
    _tipoDropdownGrupo = null;

    if (isOpen) return;

    // Leer tipo actual
    const bloque = document.querySelector(`.bloque[data-grupo="${grupo}"]`);
    const tipoActual = bloque?.dataset.tipo ?? '';
    const cantActual = bloque?.querySelectorAll('.ejercicio-row').length ?? cantidad;

    const tipos = [
        { key:'monoserie', label:'Lineal',   dot:'#dbeafe', max:1 },
        { key:'biserie',   label:'Biserie',  dot:'#d1fae5', max:2 },
        { key:'triserie',  label:'Triserie', dot:'#fef3c7', max:3 },
        { key:'circuito',  label:'Circuito', dot:'#fce7f3', max:12, isCircuito:true },
    ];

    dd.innerHTML = tipos.map(t => {
        const activo = tipoActual === t.key;
        const clases = ['tipo-dropdown-item', activo ? 'activo' : '', t.isCircuito ? 'circuito-item' : ''].filter(Boolean).join(' ');
        return `<div class="${clases}"
            onclick="cambiarTipoBloque('${grupo}','${t.key}',${t.max},${cantActual})">
            <span class="tipo-dot" style="background:${t.dot}"></span> ${t.label}
        </div>`;
    }).join('');

    // Posicionar con fixed
    const rect = badge.getBoundingClientRect();
    dd.style.top  = (rect.bottom + 4) + 'px';
    dd.style.left = rect.left + 'px';
    dd.classList.add('open');
    _tipoDropdownGrupo = grupo;
}

document.addEventListener('click', e => {
    if (!e.target.closest('#tipo-dd-global') && !e.target.closest('.bloque-tipo')) {
        document.getElementById('tipo-dd-global')?.classList.remove('open');
        _tipoDropdownGrupo = null;
    }
});
window.addEventListener('scroll', () => {
    document.getElementById('tipo-dd-global')?.classList.remove('open');
    _tipoDropdownGrupo = null;
}, true);

function cambiarTipoBloque(grupo, nuevoTipo, nuevaCantidad, cantidadActual) {
    const bloque = document.querySelector(`.bloque[data-grupo="${grupo}"]`);
    if (!bloque) return;
    document.getElementById('tipo-dd-global')?.classList.remove('open');
    _tipoDropdownGrupo = null;

    const tipoActual = bloque.dataset.tipo;
    // Circuito siempre abre modal para cambiar cantidad, incluso si ya es circuito
    if (nuevoTipo !== 'circuito' && tipoActual === nuevoTipo) return;

    const ejRows = bloque.querySelectorAll('.ejercicio-row');
    const actualCount = ejRows.length;

    // Circuito siempre pide cantidad con el modal
    if (nuevoTipo === 'circuito') {
        _pendingCircuitoCambio = { grupo, cantidadActual: actualCount };
        setTimeout(() => {
            document.getElementById('circuitoNum').value = Math.max(4, actualCount);
            document.getElementById('modalCircuito').classList.add('open');
            setTimeout(() => document.getElementById('circuitoNum').focus(), 50);
        }, 50);
        return;
    }

    if (nuevaCantidad >= actualCount) {
        aplicarCambioTipo(grupo, nuevoTipo, nuevaCantidad, actualCount);
        return;
    }

    // Hay que eliminar — mostrar modal de selección
    const lista = document.getElementById('eliminar-ej-lista');
    lista.innerHTML = '';

    const letrasColors = [
        {bg:'#eff6ff',color:'#1d4ed8'},{bg:'#f0fdf4',color:'#065f46'},
        {bg:'#fffbeb',color:'#92400e'},{bg:'#fdf2f8',color:'#9d174d'},
        {bg:'#e0f2fe',color:'#1d4ed8'},{bg:'#dcfce7',color:'#065f46'},
        {bg:'#fef9c3',color:'#92400e'},{bg:'#fce7f3',color:'#9d174d'},
        {bg:'#dbeafe',color:'#1e40af'},{bg:'#d1fae5',color:'#166534'},
        {bg:'#fef3c7',color:'#854d0e'},{bg:'#fdf2f8',color:'#831843'},
    ];

    ejRows.forEach((row, i) => {
        const nombre = row.querySelector('.ej-trigger-nombre')?.textContent
            || row.querySelector('.ej-trigger-placeholder')?.textContent
            || `Ejercicio ${i+1}`;
        const segmento = row.querySelector('.segmento-select')?.value || '';
        const lc = letrasColors[i] || letrasColors[0];
        const letraLabel = ['A','B','C','D','E','F','G','H','I','J','K','L'][i] || (i+1);
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'ej-eliminar-btn';
        btn.innerHTML = `
            <span class="ej-eliminar-letra" style="background:${lc.bg};color:${lc.color}">${letraLabel}</span>
            <span><strong style="display:block;font-size:0.78rem">${nombre}</strong>
            <span style="font-size:0.65rem;color:var(--muted)">${segmento}</span></span>
            <i class="ti ti-trash" style="font-size:13px;color:#ef4444;margin-left:auto" aria-hidden="true"></i>`;
        btn.onclick = () => {
            cerrarModalEliminar();
            eliminarEjRow(grupo, i);
            const remaining = bloque.querySelectorAll('.ejercicio-row').length;
            if (remaining > nuevaCantidad) {
                setTimeout(() => cambiarTipoBloque(grupo, nuevoTipo, nuevaCantidad, remaining), 200);
            } else {
                aplicarCambioTipo(grupo, nuevoTipo, nuevaCantidad, remaining);
            }
        };
        lista.appendChild(btn);
    });

    _pendingTipoCambio = { grupo, nuevoTipo, nuevaCantidad };
    document.getElementById('modalEliminarEj').classList.add('open');
}

function eliminarEjRow(grupo, idx) {
    const bloque = document.querySelector(`.bloque[data-grupo="${grupo}"]`);
    const rows = bloque?.querySelectorAll('.ejercicio-row');
    if (rows && rows[idx]) rows[idx].remove();
    // Reasignar letras y colores
    const newRows = bloque.querySelectorAll('.ejercicio-row');
    newRows.forEach((row, i) => {
        const letra = row.querySelector('.ej-letra');
        if (letra) {
            letra.className = `ej-letra ${LETRAS[i] || 'ej-letra-a'}`;
            letra.textContent = NUMS[i] || (i+1);
        }
        row.className = `ejercicio-row ${BGS[i] || 'ej-bg-a'}`;
    });
}

function aplicarCambioTipo(grupo, nuevoTipo, nuevaCantidad, actualCount) {
    const bloque = document.querySelector(`.bloque[data-grupo="${grupo}"]`);
    if (!bloque) return;
    const cfg = TIPO_CONFIG[nuevoTipo];

    // Actualizar data-tipo
    bloque.dataset.tipo = nuevoTipo;

    // Actualizar badge
    const badge = bloque.querySelector('.bloque-tipo');
    if (badge) {
        Object.values(TIPO_CONFIG).forEach(c => badge.classList.remove(c.cls));
        badge.classList.add(cfg.cls);
        // Actualizar texto del label (primer text node)
        const labelNode = [...badge.childNodes].find(n => n.nodeType === 3 && n.textContent.trim());
        if (labelNode) labelNode.textContent = cfg.label + ' ';
        // Span de cantidad para circuito
        let spanCant = badge.querySelector('.circuito-cant');
        if (nuevoTipo === 'circuito') {
            if (!spanCant) {
                spanCant = document.createElement('span');
                spanCant.className = 'circuito-cant';
                spanCant.style.cssText = 'opacity:.7;font-size:.55rem';
                // Insertar antes del icono chevron
                const chevron = badge.querySelector('.ti-chevron-down');
                if (chevron) chevron.before(spanCant);
                else badge.appendChild(spanCant);
            }
            spanCant.textContent = ` · ${nuevaCantidad} ej.`;
        } else {
            spanCant?.remove();
        }
        // El dropdown global se regenera en cada apertura, no hay que actualizar items aquí
    }

    // Si faltan ejercicios (subió de tipo), agregar filas
    const ejRows = bloque.querySelectorAll('.ejercicio-row');
    const numSeries = bloque.querySelector('.bloque-series-count input')?.value || 0;
    const opts = Object.keys(ejerciciosPorGrupo).map(s=>`<option value="${s}">${s}</option>`).join('');
    for (let i = ejRows.length; i < nuevaCantidad; i++) {
        const ejId = `ej-${grupo}-${i}`;
        const lClass = LETRAS[i % LETRAS.length];
        const bgClass = BGS[i % BGS.length];
        let newRow = `<div class="ejercicio-row ${bgClass}">
            <div class="ej-letra ${lClass}">${NUMS[i] ?? (i+1)}</div>
            <div class="col-segmento"><div class="field-label">Segmento</div>
                <select class="segmento-select" data-ej="${ejId}" onchange="onSegmentoChange(this)">
                    <option value="">-- Segmento --</option>${opts}
                </select>
            </div>
            <div class="col-ejercicio"><div class="field-label">Ejercicio</div>
                <input type="hidden" id="${ejId}" class="ejercicio-id-input" value="">
                <div class="ej-select-wrapper" data-target="${ejId}">
                    <div class="ej-select-trigger" onclick="toggleDropdown(this)">
                        <img src="" alt="" style="display:none;">
                        <span class="ej-trigger-placeholder">-- Ejercicio --</span>
                        <span class="ej-trigger-arrow">▼</span>
                    </div>
                    <div class="ej-select-dropdown"></div>
                </div>
                <div class="nota-ej-input-wrap">
                    <i class="ti ti-pencil"></i>
                    <textarea class="nota-ej-input" placeholder="nota…"></textarea>
                </div>
            </div>
            <div class="col-series"><div class="series-cols" data-grupo="${grupo}" data-ej="${i}"></div></div>
        </div>`;
        bloque.querySelector('.descanso-row, .bloque-footer').insertAdjacentHTML('beforebegin', newRow);
        // Generar series vacías
        const container = bloque.querySelector(`.series-cols[data-grupo="${grupo}"][data-ej="${i}"]`);
        if (container) {
            for (let s = 0; s < numSeries; s++) container.insertAdjacentHTML('beforeend', htmlSerieCol({}));
        }
    }

    // Reasignar letras/colores
    bloque.querySelectorAll('.ejercicio-row').forEach((row, i) => {
        const letra = row.querySelector('.ej-letra');
        if (letra) { letra.className = `ej-letra ${LETRAS[i]||'ej-letra-a'}`; letra.textContent = NUMS[i]||(i+1); }
        row.className = `ejercicio-row ${BGS[i]||'ej-bg-a'}`;
    });

    // Actualizar nota expand
    bloque.querySelectorAll('.nota-ej-input').forEach(el => {
        autoExpandNota(el);
        if (!el._expandBound) { el.addEventListener('input', () => autoExpandNota(el)); el._expandBound = true; }
    });

    showBloqueToast(grupo, `Cambiado a ${cfg.label.toLowerCase()}`, 'blue');
}

function confirmarCircuitoReducir(grupo, targetN, currentN) {
    const bloque = document.querySelector(`.bloque[data-grupo="${grupo}"]`);
    if (!bloque) return;
    const lista = document.getElementById('eliminar-ej-lista');
    lista.innerHTML = '';
    const letrasColors = [
        {bg:'#eff6ff',color:'#1d4ed8'},{bg:'#f0fdf4',color:'#065f46'},
        {bg:'#fffbeb',color:'#92400e'},{bg:'#fdf2f8',color:'#9d174d'},
        {bg:'#e0f2fe',color:'#1d4ed8'},{bg:'#dcfce7',color:'#065f46'},
        {bg:'#fef9c3',color:'#92400e'},{bg:'#fce7f3',color:'#9d174d'},
        {bg:'#dbeafe',color:'#1e40af'},{bg:'#d1fae5',color:'#166534'},
        {bg:'#fef3c7',color:'#854d0e'},{bg:'#fdf2f8',color:'#831843'},
    ];
    bloque.querySelectorAll('.ejercicio-row').forEach((row, i) => {
        const nombre = row.querySelector('.ej-trigger-nombre')?.textContent
            || row.querySelector('.ej-trigger-placeholder')?.textContent
            || `Ejercicio ${i+1}`;
        const segmento = row.querySelector('.segmento-select')?.value || '';
        const lc = letrasColors[i] || letrasColors[0];
        const letraLabel = ['A','B','C','D','E','F','G','H','I','J','K','L'][i] || (i+1);
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'ej-eliminar-btn';
        btn.innerHTML = `
            <span class="ej-eliminar-letra" style="background:${lc.bg};color:${lc.color}">${letraLabel}</span>
            <span><strong style="display:block;font-size:0.78rem">${nombre}</strong>
            <span style="font-size:0.65rem;color:var(--muted)">${segmento}</span></span>
            <i class="ti ti-trash" style="font-size:13px;color:#ef4444;margin-left:auto" aria-hidden="true"></i>`;
        const idx = i;
        btn.onclick = () => {
            cerrarModalEliminar();
            eliminarEjRow(grupo, idx);
            const remaining = bloque.querySelectorAll('.ejercicio-row').length;
            if (remaining > targetN) {
                setTimeout(() => confirmarCircuitoReducir(grupo, targetN, remaining), 200);
            } else {
                aplicarCambioTipo(grupo, 'circuito', targetN, remaining);
            }
        };
        lista.appendChild(btn);
    });
    document.getElementById('modalEliminarEj').classList.add('open');
}

function cerrarModalEliminar() {
    document.getElementById('modalEliminarEj').classList.remove('open');
    _pendingTipoCambio = null;
    _pendingCircuitoCambio = null;
}

/* ── Drag & drop ── */
(function(){
    let dragged = null, scrollInterval = null;
    function startScroll(dir) { if(scrollInterval) return; scrollInterval=setInterval(()=>window.scrollBy(0,dir*10),16); }
    function stopScroll() { clearInterval(scrollInterval); scrollInterval=null; }
    contenedor.addEventListener('mousedown', e => {
        const handle=e.target.closest('.bloque-drag-handle'); if(!handle) return;
        const bloque=handle.closest('.bloque'); bloque.setAttribute('draggable','true');
        bloque.addEventListener('dragend', ()=>{ bloque.setAttribute('draggable','false'); bloque.classList.remove('dragging'); document.querySelectorAll('.bloque.drag-over').forEach(b=>b.classList.remove('drag-over')); stopScroll(); actualizarOrden(); }, {once:true});
    });
    contenedor.addEventListener('dragstart', e => { dragged=e.target.closest('.bloque'); if(!dragged) return; dragged.classList.add('dragging'); e.dataTransfer.effectAllowed='move'; });
    let lastMove=0;
    contenedor.addEventListener('dragover', e => {
        e.preventDefault();
        const zone=100;
        if(e.clientY<zone) startScroll(-1); else if(e.clientY>window.innerHeight-zone) startScroll(1); else stopScroll();
        const now=Date.now(); if(now-lastMove<50) return; lastMove=now;
        const target=e.target.closest('.bloque'); if(!target||target===dragged) return;
        document.querySelectorAll('.bloque.drag-over').forEach(b=>b.classList.remove('drag-over'));
        target.classList.add('drag-over');
        const rect=target.getBoundingClientRect();
        if(e.clientY<rect.top+rect.height/2) contenedor.insertBefore(dragged,target); else contenedor.insertBefore(dragged,target.nextSibling);
    });
    contenedor.addEventListener('dragleave', e => { const t=e.target.closest('.bloque'); if(t) t.classList.remove('drag-over'); });
    contenedor.addEventListener('drop', e => { e.preventDefault(); document.querySelectorAll('.bloque.drag-over').forEach(b=>b.classList.remove('drag-over')); stopScroll(); });
    document.addEventListener('dragend', stopScroll);
})();
</script>

@endsection