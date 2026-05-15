@extends('layouts.entrenador')
@section('titulo', 'Plantillas')
@section('contenido')

<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

<style>
:root {
    --bg:#f4f5f7; --surface:#fff; --border:#e2e5ea;
    --border2:#d0d5dd; --text:#111827; --muted:#6b7280;
    --accent:#2563eb; --accent-l:#eff6ff; --danger:#ef4444; --radius:10px;
}
* { box-sizing: border-box; }
body, .entrenador-content { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text); }
.page-header { display:flex; align-items:center; gap:10px; margin-bottom:16px; padding-bottom:12px; border-bottom:2px solid var(--border); }
.page-header h2 { font-size:1.1rem; font-weight:700; margin:0; }
.btn-new {
    margin-left:auto; display:inline-flex; align-items:center; gap:5px;
    background:var(--accent); color:white; border:none; border-radius:7px;
    padding:7px 16px; font-size:0.8rem; font-weight:600; cursor:pointer;
    text-decoration:none; transition:all .13s; font-family:'DM Sans',sans-serif;
}
.btn-new:hover { background:#1d4ed8; }
.plantillas-grid { display:flex; flex-direction:column; gap:8px; }
.plantilla-card {
    background:var(--surface); border:1.5px solid var(--border);
    border-radius:var(--radius); padding:14px 16px;
    display:flex; align-items:center; gap:12px;
    box-shadow:0 1px 4px rgba(0,0,0,.06);
}
.plantilla-card:hover { border-color:var(--border2); }
.plantilla-icon {
    width:38px; height:38px; border-radius:8px;
    background:var(--accent-l); display:flex; align-items:center;
    justify-content:center; font-size:1.1rem; flex-shrink:0;
}
.plantilla-info { flex:1; min-width:0; }
.plantilla-nombre { font-size:0.9rem; font-weight:700; color:var(--text); }
.plantilla-desc { font-size:0.75rem; color:var(--muted); margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.plantilla-meta { font-size:0.68rem; color:var(--muted); margin-top:4px; }
.plantilla-actions { display:flex; gap:6px; flex-shrink:0; }
.btn-sm {
    display:inline-flex; align-items:center; gap:4px;
    padding:5px 12px; border-radius:6px; font-size:0.75rem;
    font-weight:600; cursor:pointer; text-decoration:none;
    border:1px solid var(--border2); background:white;
    color:var(--muted); transition:all .12s; font-family:'DM Sans',sans-serif;
}
.btn-sm:hover { border-color:var(--accent); color:var(--accent); background:var(--accent-l); }
.btn-sm.danger { border-color:#fca5a5; color:var(--danger); }
.btn-sm.danger:hover { background:#fee2e2; }
.empty-state {
    background:var(--surface); border:1.5px dashed var(--border2);
    border-radius:var(--radius); padding:50px 20px;
    display:flex; flex-direction:column; align-items:center; gap:10px; color:var(--muted);
}
.empty-state p { font-size:0.9rem; margin:0; }
</style>

<div class="page-header">
    <h2>Plantillas de entrenamiento</h2>
    <a href="{{ route('entrenador.plantillas.crear') }}" class="btn-new">
        ＋ Nueva plantilla
    </a>
</div>

@if(session('success'))
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:10px 14px;border-radius:8px;font-size:0.82rem;margin-bottom:12px">
        {{ session('success') }}
    </div>
@endif

@if($plantillas->isEmpty())
    <div class="empty-state">
        <svg style="width:40px;height:40px;opacity:.3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <p>No tienes plantillas creadas todavía</p>
        <a href="{{ route('entrenador.plantillas.crear') }}" class="btn-new" style="margin-top:4px">
            ＋ Crear primera plantilla
        </a>
    </div>
@else
    <div class="plantillas-grid">
        @foreach($plantillas as $plantilla)
        @php
            $numBloques = count($plantilla->bloques ?? []);
            $numEjercicios = collect($plantilla->bloques ?? [])->sum(fn($b) => count($b['ejercicios'] ?? []));
        @endphp
        <div class="plantilla-card">
            <div class="plantilla-icon">📋</div>
            <div class="plantilla-info">
                <div class="plantilla-nombre">{{ $plantilla->nombre }}</div>
                @if($plantilla->descripcion)
                    <div class="plantilla-desc">{{ $plantilla->descripcion }}</div>
                @endif
                <div class="plantilla-meta">
                    {{ $numBloques }} {{ $numBloques === 1 ? 'bloque' : 'bloques' }} ·
                    {{ $numEjercicios }} {{ $numEjercicios === 1 ? 'ejercicio' : 'ejercicios' }} ·
                    Creada {{ $plantilla->created_at->diffForHumans() }}
                </div>
            </div>
             

            <div class="plantilla-actions">
    <a href="{{ route('entrenador.plantillas.pdf', $plantilla->id) }}"
       target="_blank" class="btn-sm">
        📄 PDF
    </a>
    <a href="{{ route('entrenador.plantillas.editar', $plantilla->id) }}" class="btn-sm">
        ✏️ Editar
    </a>
    <form method="POST" action="{{ route('entrenador.plantillas.eliminar', $plantilla->id) }}"
          onsubmit="return confirm('¿Eliminar esta plantilla?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-sm danger">🗑 Eliminar</button>
    </form>
</div>

        </div>
        @endforeach
    </div>
@endif

@endsection