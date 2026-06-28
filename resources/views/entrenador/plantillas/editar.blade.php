@extends('layouts.entrenador')
@section('titulo', 'Editar Plantilla')
@section('contenido')

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
.page-header { display:flex; align-items:center; gap:10px; margin-bottom:16px; padding-bottom:12px; border-bottom:2px solid var(--border); }
.page-header h2 { font-size:1.1rem; font-weight:700; margin:0; }
.badge { font-size:0.63rem; font-weight:700; background:var(--accent-l); color:var(--accent); border:1px solid #bfdbfe; padding:2px 8px; border-radius:99px; text-transform:uppercase; letter-spacing:.05em; }
.btn-metodos { margin-left:auto; display:inline-flex; align-items:center; gap:5px; background:white; color:var(--muted); border:1px solid var(--border2); border-radius:7px; padding:5px 12px; font-size:0.75rem; font-weight:600; cursor:pointer; transition:all .13s; font-family:'DM Sans',sans-serif; }
.btn-metodos:hover { border-color:var(--accent); color:var(--accent); background:var(--accent-l); }
.nombre-plantilla-wrap { background:var(--surface); border:1.5px solid var(--border); border-radius:var(--radius); padding:14px 16px; margin-bottom:14px; display:flex; flex-direction:column; gap:8px; }
.nombre-plantilla-wrap label { font-size:0.72rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.06em; }
.nombre-input { width:100%; border:1px solid var(--border2); border-radius:6px; padding:8px 10px; font-size:0.9rem; font-family:'DM Sans',sans-serif; color:var(--text); }
.nombre-input:focus { outline:none; border-color:var(--accent); }
.desc-input { width:100%; border:1px solid var(--border2); border-radius:6px; padding:6px 10px; font-size:0.82rem; font-family:'DM Sans',sans-serif; color:var(--text); resize:none; }
.desc-input:focus { outline:none; border-color:var(--accent); }
.dias-config { background:var(--surface); border:1.5px solid var(--border); border-radius:var(--radius); padding:14px 16px; margin-bottom:14px; display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
.dias-config label { font-size:0.72rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.06em; }
.dias-input { width:70px; border:1px solid var(--border2); border-radius:6px; padding:6px 10px; font-size:1rem; font-family:'DM Mono',monospace; text-align:center; color:var(--text); }
.dias-input:focus { outline:none; border-color:var(--accent); }
.btn-generar-dias { padding:7px 16px; background:var(--accent); color:white; border:none; border-radius:7px; font-size:0.8rem; font-weight:600; cursor:pointer; font-family:'DM Sans',sans-serif; }
.btn-generar-dias:hover { background:#1d4ed8; }
.dias-tabs { display:flex; gap:4px; margin-bottom:0; flex-wrap:wrap; }
.dia-tab { padding:6px 14px; border:1.5px solid var(--border2); border-bottom:none; border-radius:8px 8px 0 0; font-size:0.78rem; font-weight:600; color:var(--muted); cursor:pointer; background:var(--bg); transition:all .12s; }
.dia-tab.active { background:var(--surface); color:var(--accent); border-color:var(--border); border-bottom-color:var(--surface); }
.dia-tab:hover:not(.active) { background:#eef0f3; }
.dias-panels { background:var(--surface); border:1.5px solid var(--border); border-radius:0 8px 8px 8px; padding:14px; margin-bottom:14px; }
.dia-panel { display:none; }
.dia-panel.active { display:block; }
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
.circ-btn-cancel { flex:1; padding:8px; border:1px solid var(--border2); border-radius:7px; background:white; color:var(--muted); font-size:0.82rem; font-weight:600; cursor:pointer; }
.circ-btn-ok { flex:1; padding:8px; border:none; border-radius:7px; background:var(--accent); color:white; font-size:0.82rem; font-weight:600; cursor:pointer; }
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
.bloque-footer { border-top:1px solid var(--border); background:#f9fafb; padding:7px 12px; border-radius:0 0 var(--radius) var(--radius); display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.descanso-inline { display:flex; align-items:center; gap:6px; font-size:0.72rem; color:var(--muted); flex:1; flex-wrap:wrap; }
.descanso-inline span { font-size:0.7rem; }
.descanso-valor-input { width:46px; border:1px solid var(--border2); border-radius:5px; padding:2px 5px; font-size:0.74rem; font-family:'DM Mono',monospace; text-align:center; color:var(--text); }
.descanso-valor-input:focus { outline:none; border-color:#059669; }
.descanso-unit-btn { padding:2px 6px; border:1px solid var(--border2); border-radius:4px; background:white; font-size:0.62rem; font-weight:600; color:var(--muted); cursor:pointer; transition:all .1s; }
.descanso-unit-btn:hover { border-color:#059669; color:#059669; }
.descanso-unit-btn.sel { background:#059669; color:white; border-color:#059669; }
.descanso-badge { font-size:0.62rem; font-weight:700; color:#059669; background:#ecfdf5; border:1px solid #a7f3d0; border-radius:99px; padding:1px 7px; font-family:'DM Mono',monospace; }
.series-header-row { display:flex; border-bottom:2px solid var(--border); background:#f0f2f5; }
.series-header-row .col-info-header { width:265px; flex-shrink:0; border-right:1px solid var(--border); padding:5px 10px; font-size:0.6rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.06em; display:flex; align-items:center; }
.series-header-row .col-series-headers { flex:1; display:flex; padding:0; min-width:0; }
.serie-header-col { flex:1; text-align:center; padding:5px 4px; font-size:0.65rem; font-weight:700; color:var(--accent); background:var(--accent-l); border-right:1px solid #bfdbfe; letter-spacing:.04em; text-transform:uppercase; }
.serie-header-col:last-child { border-right:none; }
.ejercicio-row { display:flex; border-bottom:1px solid var(--border); min-height:54px; align-items:stretch; }
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
.col-ejercicio { width:133px; flex-shrink:0; padding:7px 9px; border-right:1px solid var(--border); }
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
.ej-select-dropdown { display:none; position:absolute; top:calc(100% + 3px); left:0; width:260px; background:white; border:1.5px solid var(--border); border-radius:8px; box-shadow:0 8px 24px rgba(0,0,0,.14); z-index:9999; max-height:280px; overflow-y:auto; }
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
.btn-pdf { display:inline-flex; align-items:center; gap:6px; background:white; color:var(--accent); font-family:'DM Sans',sans-serif; font-size:0.87rem; font-weight:600; padding:9px 22px; border:1.5px solid var(--accent); border-radius:var(--radius); cursor:pointer; text-decoration:none; transition:all .14s; margin-top:14px; margin-left:10px; }
.btn-pdf:hover { background:var(--accent-l); }
</style>

{{-- MODAL MÉTODOS --}}
<div class="modal-overlay" id="modalMetodos" onclick="if(event.target===this)cerrarModal('modalMetodos')">
    <div class="modal-box">
        <div class="modal-header">
            <h3>📚 Métodos de entrenamiento</h3>
            <button class="modal-close" onclick="cerrarModal('modalMetodos')">✕</button>
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
<div class="modal-circ-overlay" id="modalCircuito" onclick="if(event.target===this)cerrarModal('modalCircuito')">
    <div class="modal-circ-box">
        <h3>Circuito</h3>
        <p>¿Cuántos ejercicios? (2 – 12)</p>
        <input type="number" class="circ-num-input" id="circuitoNum" min="2" max="12" value="4">
        <div class="circ-btns">
            <button class="circ-btn-cancel" onclick="cerrarModal('modalCircuito')">Cancelar</button>
            <button class="circ-btn-ok" onclick="confirmarCircuito()">Agregar</button>
        </div>
    </div>
</div>

{{-- HEADER --}}
<div class="page-header">
    <a href="{{ route('entrenador.plantillas.index') }}"
       style="font-size:1.2rem;color:var(--muted);text-decoration:none;line-height:1">‹</a>
    <h2>Editar plantilla</h2>
    <span class="badge">Editando</span>
    <button class="btn-metodos" onclick="abrirModal('modalMetodos')">❓ Métodos</button>
</div>

<form method="POST" action="{{ route('entrenador.plantillas.actualizar', $plantilla->id) }}" id="form-plantilla">
@csrf
<input type="hidden" name="datos_json" id="datos_json">

<div class="nombre-plantilla-wrap">
    <div>
        <label>Nombre de la plantilla *</label>
        <input type="text" name="nombre" class="nombre-input" value="{{ $plantilla->nombre }}" required>
    </div>
    <div>
        <label>Descripción (opcional)</label>
        <textarea name="descripcion" class="desc-input" rows="2">{{ $plantilla->descripcion }}</textarea>
    </div>
</div>

<div class="dias-config">
    <label>Número de días:</label>
    <input type="number" class="dias-input" id="numDias" min="1" max="7" value="{{ count($plantilla->bloques ?? []) }}">
    <button type="button" class="btn-generar-dias" onclick="generarTabs()">Regenerar días</button>
    <span style="font-size:0.75rem;color:var(--muted);">⚠️ Regenerar borra el contenido actual</span>
</div>

<div id="dias-tabs-wrap">
    <div class="dias-tabs" id="dias-tabs"></div>
    <div class="dias-panels" id="dias-panels"></div>
    <button type="button" onclick="guardarPlantilla()" class="btn-guardar" id="btn-guardar">
        💾 &nbsp;Actualizar plantilla
    </button>
    <a href="{{ route('entrenador.plantillas.pdf', $plantilla->id) }}"
       target="_blank" class="btn-pdf">📄 PDF</a>
</div>

</form>

<script>
const ejerciciosPorGrupo = @json($ejerciciosPorGrupo);
const diasExistentes     = @json($plantilla->bloques ?? []);
let contador  = Date.now();
let diaActivo = 1;
let totalDias = 0;

const NUMS   = ['1','2','3','4','5','6','7','8','9','10','11','12'];
const LETRAS = ['ej-letra-a','ej-letra-b','ej-letra-c','ej-letra-d','ej-letra-e','ej-letra-f','ej-letra-g','ej-letra-h','ej-letra-i','ej-letra-j','ej-letra-k','ej-letra-l'];
const BGS    = ['ej-bg-a','ej-bg-b','ej-bg-c','ej-bg-d','ej-bg-e','ej-bg-f','ej-bg-g','ej-bg-h','ej-bg-i','ej-bg-j','ej-bg-k','ej-bg-l'];

/* ── Utilidad: URL de imagen ── */
const R2_URL = "{{ env('AWS_URL') }}";
const imgUrl = img => img ? `${R2_URL}/${img}` : '';

/* ── Modales (generalizados) ── */
function abrirModal(id)  { document.getElementById(id).classList.add('open'); document.body.style.overflow='hidden'; }
function cerrarModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow=''; }
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        cerrarModal('modalMetodos');
        cerrarModal('modalCircuito');
    }
});

/* ── Modal circuito ── */
let _diaCircuito = 1;
function abrirModalCircuito(dia) {
    _diaCircuito = dia;
    document.getElementById('circuitoNum').value = 4;
    abrirModal('modalCircuito');
    setTimeout(() => document.getElementById('circuitoNum').focus(), 50);
}
function confirmarCircuito() {
    const n = Math.min(12, Math.max(2, parseInt(document.getElementById('circuitoNum').value) || 4));
    cerrarModal('modalCircuito');
    agregarBloque('circuito', n, _diaCircuito);
}
document.getElementById('circuitoNum').addEventListener('keydown', e => {
    if (e.key === 'Enter') confirmarCircuito();
    if (e.key === 'Escape') cerrarModal('modalCircuito');
});

/* ── Auto-expand nota ── */
function autoExpandNota(el) { el.style.height = '0'; el.style.height = el.scrollHeight + 'px'; }

/* ── Generar tabs ── */
function generarTabs(diasData = null) {
    totalDias = Math.min(7, Math.max(1, parseInt(document.getElementById('numDias').value) || 7));
    const tabsEl = document.getElementById('dias-tabs');
    const panelsEl = document.getElementById('dias-panels');
    tabsEl.innerHTML = '';
    panelsEl.innerHTML = '';

    for (let d = 1; d <= totalDias; d++) {
        const tab = document.createElement('div');
        tab.className = 'dia-tab' + (d === 1 ? ' active' : '');
        tab.textContent = `Día ${d}`;
        tab.dataset.dia = d;
        tab.onclick = () => activarTab(d);
        tabsEl.appendChild(tab);

        const notaValor = diasData && diasData[d] ? diasData[d].nota_sesion ?? '' : '';
        const panel = document.createElement('div');
        panel.className = 'dia-panel' + (d === 1 ? ' active' : '');
        panel.id = `panel-dia-${d}`;
        panel.dataset.dia = d;
        panel.innerHTML = `
            <div class="nota-sesion-card">
                <div class="nota-sesion-label"><i class="ti ti-clipboard-text"></i> Nota del día ${d}</div>
                <textarea class="nota-sesion-textarea nota-dia" data-dia="${d}" placeholder="Indicaciones generales para este día…">${notaValor}</textarea>
            </div>
            <div id="bloques-dia-${d}"></div>
            <div class="add-block-bar">
                <button type="button" onclick="agregarBloque('monoserie',1,${d})" class="add-block-btn">＋ Lineal</button>
                <button type="button" onclick="agregarBloque('biserie',2,${d})"   class="add-block-btn">＋ Biserie</button>
                <button type="button" onclick="agregarBloque('triserie',3,${d})"  class="add-block-btn">＋ Triserie</button>
                <button type="button" onclick="abrirModalCircuito(${d})"          class="add-block-btn">＋ Circuito</button>
            </div>`;
        panelsEl.appendChild(panel);

        if (diasData && diasData[d]) {
            const bloques = diasData[d].bloques ?? {};
            Object.entries(bloques).forEach(([grupo, bloque]) => precargarBloque(grupo, bloque, d));
        }
    }
    diaActivo = 1;
}

function activarTab(d) {
    document.querySelectorAll('.dia-tab').forEach(t => t.classList.toggle('active', parseInt(t.dataset.dia) === d));
    document.querySelectorAll('.dia-panel').forEach(p => p.classList.toggle('active', parseInt(p.dataset.dia) === d));
    diaActivo = d;
}

/* ── Guardar ── */
function recolectarDias() {
    const dias = {};
    for (let d = 1; d <= totalDias; d++) {
        const contenedor = document.getElementById(`bloques-dia-${d}`);
        if (!contenedor) continue;
        const notaSesion = document.querySelector(`.nota-dia[data-dia="${d}"]`)?.value || '';
        const bloques = {};
        let orden = 0;
        contenedor.querySelectorAll('.bloque').forEach(bloque => {
            const grupo = bloque.dataset.grupo, tipo = bloque.dataset.tipo;
            if (!grupo) return;
            const descInput  = bloque.querySelector(`[data-bloque-descanso-valor="${grupo}"]`);
            const descHidden = bloque.querySelector(`[data-bloque-descanso-unidad-val="${grupo}"]`);
            bloques[grupo] = {
                tipo, orden: orden++,
                descanso_valor: descInput?.value || '',
                descanso_unidad: descHidden?.value || 'seg',
                ejercicios: {}
            };
            bloque.querySelectorAll('.ejercicio-row').forEach((ejRow, i) => {
                const segmento    = ejRow.querySelector('.segmento-select')?.value ?? '';
                const ejercicio_id = ejRow.querySelector('.ejercicio-id-input')?.value ?? '';
                const nota_ej     = ejRow.querySelector('.nota-ej-input')?.value ?? '';
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
        dias[d] = { nombre: `Día ${d}`, nota_sesion: notaSesion, bloques };
    }
    return dias;
}

function guardarPlantilla() {
    const btn = document.getElementById('btn-guardar');
    btn.disabled = true;
    btn.textContent = '⏳ Guardando...';
    document.getElementById('datos_json').value = JSON.stringify({ dias: recolectarDias() });
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
    const img   = trigger.querySelector('img');
    const label = trigger.querySelector('.ej-trigger-nombre,.ej-trigger-placeholder');
    if (option.dataset.imagen) { img.src = option.dataset.imagen; img.style.display = 'block'; }
    else { img.src = ''; img.style.display = 'none'; }
    label.className  = 'ej-trigger-nombre';
    label.textContent = option.dataset.nombre;
    wrapper.querySelectorAll('.ej-select-option').forEach(o => o.classList.remove('selected'));
    option.classList.add('selected');
    wrapper.querySelector('.ej-select-dropdown').classList.remove('open');
}

document.addEventListener('click', e => {
    if (!e.target.closest('.ej-select-wrapper'))
        document.querySelectorAll('.ej-select-dropdown.open').forEach(d => d.classList.remove('open'));
});

function construirOpcionEjercicio(e, ejSeleccionadoId = null) {
    const url = imgUrl(e.imagen);
    const sel = String(e.id) === String(ejSeleccionadoId) ? 'selected' : '';
    const div = document.createElement('div');
    div.className = `ej-select-option ${sel}`;
    div.dataset.value  = e.id;
    div.dataset.nombre = e.nombre;
    div.dataset.imagen = url;
    div.onclick = () => seleccionarEjercicio(div);
    div.innerHTML = url
        ? `<img src="${url}" alt="${e.nombre}"><span>${e.nombre}</span>`
        : `<div class="ej-no-img">Sin img</div><span>${e.nombre}</span>`;
    return div;
}

function onSegmentoChange(select) {
    const ejId    = select.dataset.ej;
    const seg     = select.value;
    const wrapper = document.querySelector(`.ej-select-wrapper[data-target="${ejId}"]`);
    if (!wrapper) return;
    const trigger  = wrapper.querySelector('.ej-select-trigger');
    const img      = trigger.querySelector('img');
    const label    = trigger.querySelector('.ej-trigger-nombre,.ej-trigger-placeholder');
    const dropdown = wrapper.querySelector('.ej-select-dropdown');
    const hidden   = document.getElementById(ejId);
    hidden.value = ''; img.src = ''; img.style.display = 'none';
    label.className = 'ej-trigger-placeholder'; label.textContent = '-- Ejercicio --';
    dropdown.innerHTML = '';
    (ejerciciosPorGrupo[seg] ?? []).forEach(e => dropdown.appendChild(construirOpcionEjercicio(e)));
}

document.addEventListener('change', e => {
    if (e.target.classList.contains('segmento-select')) onSegmentoChange(e.target);
});

/* ── Descanso ── */
function actualizarDescansoBadge(grupo) {
    const input  = document.querySelector(`[data-bloque-descanso-valor="${grupo}"]`);
    const badge  = document.getElementById(`desc-badge-${grupo}`);
    const unidad = document.querySelector(`[data-bloque-descanso-unidad-val="${grupo}"]`)?.value || 'seg';
    if (!input || !badge) return;
    if (input.value) { badge.textContent = `${input.value} ${unidad}`; badge.style.display = ''; }
    else { badge.style.display = 'none'; }
}

function setDescUnidadBloque(btn, grupo, unidad) {
    const hidden = document.querySelector(`[data-bloque-descanso-unidad-val="${grupo}"]`);
    if (hidden) hidden.value = unidad;
    document.querySelectorAll(`[data-bloque-descanso-unidad="${grupo}"]`).forEach(b => b.classList.remove('sel'));
    btn.classList.add('sel');
    actualizarDescansoBadge(grupo);
}

/* ── Tempo ── */
function toggleTempo(btn) {
    const wrap   = btn.closest('.tempo-wrap');
    const fields = wrap.querySelector('.tempo-fields');
    const hidden = wrap.querySelector('[data-key="tempo_activo"]');
    const isOpen = fields.classList.contains('open');
    fields.classList.toggle('open', !isOpen);
    btn.classList.toggle('active', !isOpen);
    hidden.value = isOpen ? '0' : '1';
    if (isOpen) { btn.querySelector('span').textContent = 'Tempo'; }
    else { actualizarTempoLabel(wrap.querySelector('[data-key="tempo_excentrica"]')); }
}

function actualizarTempoLabel(input) {
    const wrap = input.closest('.tempo-wrap');
    const tE = wrap.querySelector('[data-key="tempo_excentrica"]')?.value || '0';
    const tP = wrap.querySelector('[data-key="tempo_pausa"]')?.value || '0';
    const tC = wrap.querySelector('[data-key="tempo_concentrica"]')?.value || '0';
    const btn  = wrap.querySelector('.tempo-toggle span');
    const prev = wrap.querySelector('.tempo-preview');
    if (btn)  btn.textContent  = `${tE}–${tP}–${tC}`;
    if (prev) prev.textContent = `${tE} – ${tP} – ${tC}`;
}

/* ── RIR/RPE ── */
function toggleRir(btn) {
    const wrap   = btn.closest('.rir-wrap');
    const fields = wrap.querySelector('.rir-fields');
    const hidden = wrap.querySelector('[data-key="rir_activo"]');
    const isOpen = fields.classList.contains('open');
    fields.classList.toggle('open', !isOpen);
    btn.classList.toggle('active', !isOpen);
    hidden.value = isOpen ? '0' : '1';
    if (isOpen) { btn.querySelector('span').textContent = 'RIR/RPE'; }
    else { actualizarRirLabel(wrap.querySelector('[data-key="rir_valor"]')); }
}

function actualizarRirLabel(input) {
    const wrap  = input.closest('.rir-wrap');
    const modo  = wrap.querySelector('[data-key="rir_modo"]')?.value || 'rir';
    const val   = wrap.querySelector('[data-key="rir_valor"]')?.value || '';
    const label = modo === 'rir' ? 'RIR' : 'RPE';
    const btn   = wrap.querySelector('.rir-toggle span');
    const prev  = wrap.querySelector('.rir-preview');
    const scale = wrap.querySelector('.rir-scale');
    if (btn)   btn.textContent   = val ? `${label} ${val}` : 'RIR/RPE';
    if (prev)  prev.textContent  = val ? `${label} ${val}` : '–';
    if (scale) scale.textContent = modo === 'rir'
        ? 'RIR 0 = fallo · RIR 2 = 2 reps reserva'
        : 'RPE 10 = fallo · RPE 7 = moderado';
}

function setRirModo(btn, modo) {
    const wrap   = btn.closest('.rir-wrap');
    const hidden = wrap.querySelector('[data-key="rir_modo"]');
    if (hidden) hidden.value = modo;
    wrap.querySelectorAll('.rir-mode-btn').forEach(b => b.classList.remove('sel'));
    btn.classList.add('sel');
    actualizarRirLabel(wrap.querySelector('[data-key="rir_valor"]'));
}

/* ── Utilidades ── */
function actualizarOrden(dia) {
    document.querySelectorAll(`#bloques-dia-${dia} .bloque`).forEach((b, i) => { b.dataset.orden = i; });
}
function cambiarMetodo(select) {
    select.closest('.serie-col').querySelectorAll('.metodo-fields')
        .forEach(d => d.classList.toggle('active', d.dataset.metodo === select.value));
}
function calcular40(input) {
    const p = parseFloat(input.value) || 0;
    const c = input.closest('.serie-col').querySelector('.peso-21-result');
    if (c) c.value = p > 0 ? Math.round(p * .6 * 2) / 2 : '';
}
function actualizar888Nota(input) {
    const n = input.closest('.metodo-fields').querySelector('.nota-888');
    if (n) n.textContent = `${input.value || '?'} c/u·desc.`;
}
function actualizar21sNota(input) {
    const n = input.closest('.metodo-fields').querySelector('.nota-21s');
    const r = input.value || '?';
    if (n) n.textContent = `${r}+${r}+${r}`;
}
function actualizarHeader(grupo, numSeries) {
    const header = document.querySelector(`.series-header-row[data-header="${grupo}"] .col-series-headers`);
    if (!header) return;
    header.innerHTML = '';
    for (let s = 0; s < numSeries; s++) {
        const d = document.createElement('div');
        d.className   = 'serie-header-col';
        d.textContent = `S${s + 1}`;
        header.appendChild(d);
    }
}

/* ── Footer bloque ── */
function bloqueFooterHTML(grupo, descVal = '', descUnidad = 'seg') {
    return `<div class="bloque-footer">
        <span class="descanso-inline">
            <span>💤 Descanso entre series:</span>
            <input type="number" min="0" class="descanso-valor-input" data-bloque-descanso-valor="${grupo}" value="${descVal}" placeholder="0" oninput="actualizarDescansoBadge('${grupo}')">
            <button type="button" class="descanso-unit-btn ${descUnidad === 'seg' ? 'sel' : ''}" data-bloque-descanso-unidad="${grupo}" onclick="setDescUnidadBloque(this,'${grupo}','seg')">seg</button>
            <button type="button" class="descanso-unit-btn ${descUnidad === 'min' ? 'sel' : ''}" data-bloque-descanso-unidad="${grupo}" onclick="setDescUnidadBloque(this,'${grupo}','min')">min</button>
        </span>
        <input type="hidden" data-bloque-descanso-unidad-val="${grupo}" value="${descUnidad}">
        <span class="descanso-badge" id="desc-badge-${grupo}" style="${descVal ? '' : 'display:none'}">${descVal ? descVal + ' ' + descUnidad : ''}</span>
    </div>`;
}

/* ── HTML serie col ── */
function htmlSerieCol(ex = {}) {
    const m  = ex.metodo ?? 'normal';
    const a  = k => m === k ? 'active' : '';
    const v  = (k, d = '') => ex[k] ?? d;
    const pg = (lbl, pk, uk) => `<div class="campo-wrap"><label>${lbl}</label><div class="peso-group"><input class="campo-input" type="number" step="0.5" data-key="${pk}" value="${v(pk)}" placeholder="–"><select class="unidad-select" data-key="${uk}"><option value="kg" ${v(uk, 'kg') === 'kg' ? 'selected' : ''}>kg</option><option value="lb" ${v(uk, 'kg') === 'lb' ? 'selected' : ''}>lb</option></select></div></div>`;
    const r21 = v('reps_21s', '7');
    const tA  = ex.tempo_activo === '1';
    const tE  = v('tempo_excentrica', '');
    const tP  = v('tempo_pausa', '');
    const tC  = v('tempo_concentrica', '');
    const rA  = ex.rir_activo === '1';
    const rM  = v('rir_modo', 'rir');
    const rV  = v('rir_valor', '');

    return `<div class="serie-col" data-serie>
        <select class="metodo-select" onchange="cambiarMetodo(this)">
            <option value="normal"    ${m === 'normal'    ? 'selected' : ''}>Normal</option>
            <option value="888"       ${m === '888'       ? 'selected' : ''}>Descend.</option>
            <option value="restpause" ${m === 'restpause' ? 'selected' : ''}>Rest-pause</option>
            <option value="21s"       ${m === '21s'       ? 'selected' : ''}>3 Rangos</option>
            <option value="10_21"     ${m === '10_21'     ? 'selected' : ''}>10+21s</option>
            <option value="isometria" ${m === 'isometria' ? 'selected' : ''}>Isometría</option>
            <option value="forzadas"  ${m === 'forzadas'  ? 'selected' : ''}>Forzadas</option>
            <option value="parciales" ${m === 'parciales' ? 'selected' : ''}>Parciales</option>
            <option value="negativas" ${m === 'negativas' ? 'selected' : ''}>Negativas</option>
        </select>
        <div class="metodo-fields ${a('normal')}" data-metodo="normal">
            <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" data-key="reps" value="${v('reps')}" placeholder="–"></div>
            ${pg('Peso', 'peso', 'unidad')}
        </div>
        <div class="metodo-fields ${a('888')}" data-metodo="888">
            <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" min="1" data-key="reps_888" value="${v('reps_888', '8')}" placeholder="8" oninput="actualizar888Nota(this)"></div>
            ${pg('P1', 'peso1', 'unidad1')}${pg('P2', 'peso2', 'unidad2')}${pg('P3', 'peso3', 'unidad3')}
            <div class="metodo-nota nota-888">${v('reps_888', '8')} c/u·desc.</div>
        </div>
        <div class="metodo-fields ${a('restpause')}" data-metodo="restpause">
            <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" data-key="reps_rp" value="${v('reps_rp')}" placeholder="–"></div>
            ${pg('Peso', 'peso_rp', 'unidad_rp')}
            <div class="campo-wrap"><label>Desc(s)</label><input class="campo-input" type="number" data-key="descanso" value="${v('descanso', '15')}" placeholder="15"></div>
            <div class="metodo-nota">Fallo→pausa</div>
        </div>
        <div class="metodo-fields ${a('21s')}" data-metodo="21s">
            <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" min="1" data-key="reps_21s" value="${r21}" placeholder="7" oninput="actualizar21sNota(this)"></div>
            ${pg('Peso', 'peso_21s', 'unidad_21s')}
            <div class="metodo-nota nota-21s">${r21}+${r21}+${r21}</div>
        </div>
        <div class="metodo-fields ${a('10_21')}" data-metodo="10_21">
            <div class="campo-wrap"><label>P×10</label><div class="peso-group"><input class="campo-input" type="number" step="0.5" data-key="peso_10" value="${v('peso_10')}" placeholder="–" oninput="calcular40(this)"><select class="unidad-select" data-key="unidad_10"><option value="kg" ${v('unidad_10', 'kg') === 'kg' ? 'selected' : ''}>kg</option><option value="lb">lb</option></select></div></div>
            <div class="campo-wrap"><label>P×21s</label><div class="peso-group"><input class="campo-input peso-21-result" type="number" step="0.5" data-key="peso_21" value="${v('peso_21')}" placeholder="Auto"><select class="unidad-select" data-key="unidad_21"><option value="kg" ${v('unidad_21', 'kg') === 'kg' ? 'selected' : ''}>kg</option><option value="lb">lb</option></select></div></div>
            <div class="metodo-nota">−40%→21s</div>
        </div>
        <div class="metodo-fields ${a('isometria')}" data-metodo="isometria">
            ${pg('Peso', 'peso_iso', 'unidad_iso')}
            <div class="campo-wrap"><label>R/brazo</label><input class="campo-input" type="number" data-key="reps_brazo" value="${v('reps_brazo', '4')}" placeholder="4"></div>
            <div class="campo-wrap"><label>R/ambos</label><input class="campo-input" type="number" data-key="reps_ambos" value="${v('reps_ambos', '8')}" placeholder="8"></div>
        </div>
        <div class="metodo-fields ${a('forzadas')}" data-metodo="forzadas">
            <div class="campo-wrap"><label>R.solo</label><input class="campo-input" type="number" data-key="reps_fz" value="${v('reps_fz')}" placeholder="–"></div>
            <div class="campo-wrap"><label>R.asist</label><input class="campo-input" type="number" data-key="reps_asistidas" value="${v('reps_asistidas')}" placeholder="–"></div>
            ${pg('Peso', 'peso_fz', 'unidad_fz')}
        </div>
        <div class="metodo-fields ${a('parciales')}" data-metodo="parciales">
            <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" data-key="reps_pc" value="${v('reps_pc')}" placeholder="–"></div>
            ${pg('Peso', 'peso_pc', 'unidad_pc')}
            <div class="metodo-nota">Parcial</div>
        </div>
        <div class="metodo-fields ${a('negativas')}" data-metodo="negativas">
            <div class="campo-wrap"><label>Reps</label><input class="campo-input" type="number" data-key="reps_ng" value="${v('reps_ng')}" placeholder="–"></div>
            ${pg('Peso', 'peso_ng', 'unidad_ng')}
            <div class="metodo-nota">Excéntrica</div>
        </div>
        <div class="tempo-wrap">
            <button type="button" class="tempo-toggle ${tA ? 'active' : ''}" onclick="toggleTempo(this)">⏱ <span>${tA ? tE + '-' + tP + '-' + tC : 'Tempo'}</span></button>
            <input type="hidden" data-key="tempo_activo" value="${tA ? '1' : '0'}">
            <div class="tempo-fields ${tA ? 'open' : ''}">
                <div class="tempo-row">
                    <div class="tempo-cell"><div class="tempo-icon">↓</div><div class="tempo-label">Excén<br>trica</div><input class="campo-input tempo-input" type="number" min="0" max="10" data-key="tempo_excentrica" value="${tE}" placeholder="0" oninput="actualizarTempoLabel(this)"><div class="tempo-unit">seg</div></div>
                    <div class="tempo-sep">–</div>
                    <div class="tempo-cell"><div class="tempo-icon">⏸</div><div class="tempo-label">Pausa<br>abajo</div><input class="campo-input tempo-input" type="number" min="0" max="10" data-key="tempo_pausa" value="${tP}" placeholder="0" oninput="actualizarTempoLabel(this)"><div class="tempo-unit">seg</div></div>
                    <div class="tempo-sep">–</div>
                    <div class="tempo-cell"><div class="tempo-icon">↑</div><div class="tempo-label">Concén<br>trica</div><input class="campo-input tempo-input" type="number" min="0" max="10" data-key="tempo_concentrica" value="${tC}" placeholder="0" oninput="actualizarTempoLabel(this)"><div class="tempo-unit">seg</div></div>
                </div>
                <div class="tempo-preview">${tA && (tE || tP || tC) ? tE + ' – ' + tP + ' – ' + tC : '↓ – ⏸ – ↑'}</div>
            </div>
        </div>
        <div class="rir-wrap">
            <button type="button" class="rir-toggle ${rA ? 'active' : ''}" onclick="toggleRir(this)">🎯 <span>${rA && rV ? (rM === 'rir' ? 'RIR ' : 'RPE ') + rV : 'RIR/RPE'}</span></button>
            <input type="hidden" data-key="rir_activo" value="${rA ? '1' : '0'}">
            <div class="rir-fields ${rA ? 'open' : ''}">
                <div class="rir-mode-row">
                    <button type="button" class="rir-mode-btn ${rM === 'rir' ? 'sel' : ''}" onclick="setRirModo(this,'rir')">RIR</button>
                    <button type="button" class="rir-mode-btn ${rM === 'rpe' ? 'sel' : ''}" onclick="setRirModo(this,'rpe')">RPE</button>
                </div>
                <input type="hidden" data-key="rir_modo" value="${rM}">
                <input class="campo-input" type="number" min="0" max="10" step="0.5" data-key="rir_valor" value="${rV}" placeholder="–" oninput="actualizarRirLabel(this)">
                <div class="rir-scale">${rM === 'rir' ? 'RIR 0 = fallo · RIR 2 = 2 reps reserva' : 'RPE 10 = fallo · RPE 7 = moderado'}</div>
                <div class="rir-preview">${rA && rV ? (rM === 'rir' ? 'RIR ' : 'RPE ') + rV : '–'}</div>
            </div>
        </div>
    </div>`;
}

/* ── Regenerar series de un bloque ── */
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
}

/* ── Agregar bloque ── */
function agregarBloque(tipo, cantidad, dia, grupo = null, ejercsData = null, descVal = '', descUnidad = 'seg') {
    const g = grupo ?? ('G' + contador++);
    const contenedor = document.getElementById(`bloques-dia-${dia}`);
    if (!contenedor) return;

    const opts = Object.keys(ejerciciosPorGrupo).map(s => `<option value="${s}">${s}</option>`).join('');

    let html = `<div class="bloque" data-grupo="${g}" data-tipo="${tipo}" data-dia="${dia}">
        <div class="bloque-header">
            <span class="bloque-tipo tipo-${tipo.toLowerCase()}">${tipo.toUpperCase()}${tipo === 'circuito' ? `<span style="opacity:.7;font-size:.55rem"> · ${cantidad} ej.</span>` : ''}</span>
            <div class="bloque-series-count">Series:<input type="number" min="1" placeholder="–" onchange="generarSeriesBloque(this,'${g}',${cantidad})"></div>
            <button type="button" class="btn-remove" onclick="this.closest('.bloque').remove();actualizarOrden(${dia});">✕</button>
        </div>
        <div class="series-header-row" data-header="${g}"><div class="col-info-header">Ejercicio</div><div class="col-series-headers"></div></div>`;

    for (let i = 0; i < cantidad; i++) {
        const ejId      = `ej-${g}-${i}`;
        const lClass    = LETRAS[i % LETRAS.length];
        const bgClass   = BGS[i % BGS.length];
        const ej        = ejercsData ? ejercsData[i] : null;
        const ejsSegmento = ejerciciosPorGrupo[ej?.segmento] ?? [];
        const ejActual  = ej ? ejsSegmento.find(e => String(e.id) === String(ej.ejercicio_id)) : null;
        const ejNombre  = ejActual ? ejActual.nombre : '-- Ejercicio --';
        const ejImagen  = imgUrl(ejActual?.imagen);                          // ✅ FIX: usa imgUrl y ejActual
        const optsSegmento = ej
            ? Object.keys(ejerciciosPorGrupo).map(s => `<option value="${s}" ${s === ej.segmento ? 'selected' : ''}>${s}</option>`).join('')
            : opts;
        const optsEjs = ej
            ? ejsSegmento.map(e => {
                const url = imgUrl(e.imagen);                                // ✅ FIX: usa imgUrl
                const sel = String(e.id) === String(ej.ejercicio_id) ? 'selected' : '';
                return `<div class="ej-select-option ${sel}" data-value="${e.id}" data-nombre="${e.nombre}" data-imagen="${url}" onclick="seleccionarEjercicio(this)">${url ? `<img src="${url}" alt="${e.nombre}">` : '<div class="ej-no-img">Sin img</div>'}<span>${e.nombre}</span></div>`;
              }).join('')
            : '';

        html += `<div class="ejercicio-row ${bgClass}">
            <div class="ej-letra ${lClass}">${NUMS[i] ?? (i + 1)}</div>
            <div class="col-segmento">
                <div class="field-label">Segmento</div>
                <select class="segmento-select" data-ej="${ejId}" onchange="onSegmentoChange(this)">
                    <option value="">-- Segmento --</option>${optsSegmento}
                </select>
            </div>
            <div class="col-ejercicio">
                <div class="field-label">Ejercicio</div>
                <input type="hidden" id="${ejId}" class="ejercicio-id-input" value="${ej?.ejercicio_id ?? ''}">
                <div class="ej-select-wrapper" data-target="${ejId}">
                    <div class="ej-select-trigger" onclick="toggleDropdown(this)">
                        ${ejImagen ? `<img src="${ejImagen}" alt="" style="display:block">` : `<img src="" alt="" style="display:none">`}
                        <span class="${ejActual ? 'ej-trigger-nombre' : 'ej-trigger-placeholder'}">${ejNombre}</span>
                        <span class="ej-trigger-arrow">▼</span>
                    </div>
                    <div class="ej-select-dropdown">${optsEjs}</div>
                </div>
                <div class="nota-ej-input-wrap">
                    <i class="ti ti-pencil"></i>
                    <textarea class="nota-ej-input" placeholder="nota…">${ej?.nota_ej ?? ''}</textarea>
                </div>
            </div>
            <div class="col-series"><div class="series-cols" data-grupo="${g}" data-ej="${i}"></div></div>
        </div>`;
    }

    html += bloqueFooterHTML(g, descVal, descUnidad) + '</div>';
    contenedor.insertAdjacentHTML('beforeend', html);

    if (ejercsData) {
        Object.entries(ejercsData).forEach(([i, ej]) => {
            const container = document.querySelector(`.series-cols[data-grupo="${g}"][data-ej="${i}"]`);
            if (!container) return;
            const series = ej.series ?? [];
            actualizarHeader(g, series.length);
            const seriesInput = document.querySelector(`.bloque[data-grupo="${g}"] .bloque-series-count input`);
            if (seriesInput) seriesInput.value = series.length;
            series.forEach(serie => container.insertAdjacentHTML('beforeend', htmlSerieCol(serie)));
        });
    }

    contenedor.querySelectorAll('.nota-ej-input').forEach(el => {
        autoExpandNota(el);
        if (!el._expandBound) { el.addEventListener('input', () => autoExpandNota(el)); el._expandBound = true; }
    });

    actualizarOrden(dia);
}

function precargarBloque(grupo, bloque, dia) {
    agregarBloque(
        bloque.tipo,
        Object.keys(bloque.ejercicios ?? {}).length,
        dia,
        grupo,
        bloque.ejercicios ?? {},
        bloque.descanso_valor ?? '',
        bloque.descanso_unidad ?? 'seg'
    );
}

/* ── Inicializar ── */
const esDiasNuevo = diasExistentes && Object.values(diasExistentes)[0]?.bloques !== undefined;
if (esDiasNuevo) {
    document.getElementById('numDias').value = Object.keys(diasExistentes).length;
    generarTabs(diasExistentes);
} else {
    document.getElementById('numDias').value = 1;
    generarTabs();
    if (diasExistentes) {
        Object.entries(diasExistentes).forEach(([grupo, bloque]) => precargarBloque(grupo, bloque, 1));
    }
}
</script>

@endsection