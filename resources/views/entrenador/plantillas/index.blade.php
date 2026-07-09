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

/* --- Barra de filtros --- */
.filtros-bar {
    display:flex; flex-wrap:wrap; align-items:center; gap:8px;
    background:var(--surface); border:1.5px solid var(--border);
    border-radius:var(--radius); padding:10px 12px; margin-bottom:14px;
}
.filtros-bar input[type="text"],
.filtros-bar select {
    font-family:'DM Sans',sans-serif; font-size:0.8rem; color:var(--text);
    border:1px solid var(--border2); border-radius:7px; padding:7px 10px;
    background:white; outline:none; transition:border-color .12s;
}
.filtros-bar input[type="text"]:focus,
.filtros-bar select:focus { border-color:var(--accent); }
.filtros-bar input[type="text"] { flex:1; min-width:200px; }
.filtro-count { font-size:0.75rem; color:var(--muted); margin-left:auto; white-space:nowrap; }
.btn-clear {
    font-size:0.75rem; color:var(--muted); background:none; border:none;
    cursor:pointer; text-decoration:underline; padding:0; font-family:'DM Sans',sans-serif;
}
.btn-clear:hover { color:var(--accent); }

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

    <div class="filtros-bar">
        <input type="text" id="filtroTexto" placeholder="🔍 Buscar por nombre o descripción...">

        <select id="filtroTamano">
            <option value="">Cualquier tamaño</option>
            <option value="corta">Corta (1-2 bloques)</option>
            <option value="media">Media (3-4 bloques)</option>
            <option value="larga">Larga (5+ bloques)</option>
        </select>

        <select id="filtroOrden">
            <option value="reciente">Más reciente</option>
            <option value="antigua">Más antigua</option>
            <option value="nombre">Nombre (A-Z)</option>
            <option value="ejercicios">Más ejercicios</option>
        </select>

        <button type="button" class="btn-clear" id="btnLimpiar">Limpiar filtros</button>
        <span class="filtro-count" id="filtroCount"></span>
    </div>

    <div class="plantillas-grid" id="plantillasGrid">
        @foreach($plantillas as $plantilla)
        @php
            $numBloques = count($plantilla->bloques ?? []);
            $numEjercicios = collect($plantilla->bloques ?? [])->sum(fn($b) => count($b['ejercicios'] ?? []));
        @endphp
        <div class="plantilla-card"
             data-nombre="{{ strtolower($plantilla->nombre) }}"
             data-descripcion="{{ strtolower($plantilla->descripcion ?? '') }}"
             data-bloques="{{ $numBloques }}"
             data-ejercicios="{{ $numEjercicios }}"
             data-fecha="{{ $plantilla->created_at->timestamp }}">
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

    <div class="empty-state" id="emptyFiltro" style="display:none; margin-top:8px;">
        <p>No hay plantillas que coincidan con esos filtros</p>
        <button type="button" class="btn-sm" id="btnLimpiar2">Limpiar filtros</button>
    </div>

    <script>
    (function() {
        const grid = document.getElementById('plantillasGrid');
        const cards = Array.from(grid.querySelectorAll('.plantilla-card'));
        const emptyFiltro = document.getElementById('emptyFiltro');
        const count = document.getElementById('filtroCount');

        const inputTexto = document.getElementById('filtroTexto');
        const selTamano = document.getElementById('filtroTamano');
        const selOrden = document.getElementById('filtroOrden');

        function tamanoMatch(bloques, valor) {
            if (!valor) return true;
            if (valor === 'corta') return bloques >= 1 && bloques <= 2;
            if (valor === 'media') return bloques >= 3 && bloques <= 4;
            if (valor === 'larga') return bloques >= 5;
            return true;
        }

        function aplicarFiltros() {
            const texto = inputTexto.value.trim().toLowerCase();
            const tamano = selTamano.value;
            let visibles = 0;

            cards.forEach(card => {
                const nombre = card.dataset.nombre || '';
                const desc = card.dataset.descripcion || '';
                const bloques = parseInt(card.dataset.bloques || '0', 10);

                const matchTexto = !texto || nombre.includes(texto) || desc.includes(texto);
                const matchTamano = tamanoMatch(bloques, tamano);

                const visible = matchTexto && matchTamano;
                card.style.display = visible ? '' : 'none';
                if (visible) visibles++;
            });

            emptyFiltro.style.display = visibles === 0 ? 'flex' : 'none';
            count.textContent = visibles + (visibles === 1 ? ' plantilla' : ' plantillas');
        }

        function ordenar() {
            const valor = selOrden.value;
            const ordenadas = cards.slice().sort((a, b) => {
                if (valor === 'reciente') return b.dataset.fecha - a.dataset.fecha;
                if (valor === 'antigua') return a.dataset.fecha - b.dataset.fecha;
                if (valor === 'nombre') return a.dataset.nombre.localeCompare(b.dataset.nombre);
                if (valor === 'ejercicios') return b.dataset.ejercicios - a.dataset.ejercicios;
                return 0;
            });
            ordenadas.forEach(card => grid.appendChild(card));
        }

        [inputTexto, selTamano].forEach(el => {
            el.addEventListener('input', aplicarFiltros);
            el.addEventListener('change', aplicarFiltros);
        });
        selOrden.addEventListener('change', ordenar);

        function limpiar() {
            inputTexto.value = '';
            selTamano.value = '';
            selOrden.value = 'reciente';
            aplicarFiltros();
            ordenar();
        }
        document.getElementById('btnLimpiar').addEventListener('click', limpiar);
        document.getElementById('btnLimpiar2').addEventListener('click', limpiar);

        // Estado inicial
        ordenar();
        aplicarFiltros();
    })();
    </script>
@endif

@endsection