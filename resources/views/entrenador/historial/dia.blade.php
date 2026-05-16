@php
  $nombresMes = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio',
                 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
  $diasNombre = ['','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
@endphp

@extends('layouts.entrenador')
@section('titulo', $diasNombre[$dia] . ' · Sem ' . $sem)
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
.btn-back {
    display: inline-flex; align-items: center; gap: 5px;
    background: white; color: var(--muted);
    border: 1px solid var(--border2); border-radius: 7px;
    padding: 5px 12px; font-size: 0.75rem; font-weight: 600;
    cursor: pointer; text-decoration: none; transition: all .13s;
    font-family: 'DM Sans', sans-serif; margin-left: auto;
}
.btn-back:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-l); }
.btn-editar {
    display: inline-flex; align-items: center; gap: 6px;
    background: var(--accent); color: white;
    font-family: 'DM Sans', sans-serif; font-size: 0.87rem; font-weight: 600;
    padding: 9px 24px; border: none; border-radius: var(--radius);
    cursor: pointer; text-decoration: none;
    box-shadow: 0 2px 8px rgba(37,99,235,.3); transition: all .14s;
}
.btn-editar:hover { background: #1d4ed8; transform: translateY(-1px); }
.btn-crear {
    display: inline-flex; align-items: center; gap: 6px;
    background: white; color: var(--accent);
    font-family: 'DM Sans', sans-serif; font-size: 0.87rem; font-weight: 600;
    padding: 9px 22px; border: 1.5px solid var(--accent);
    border-radius: var(--radius); cursor: pointer; text-decoration: none;
    transition: all .14s;
}
.btn-crear:hover { background: var(--accent-l); }

/* Bloques */
.bloque {
    background: var(--surface); border: 1.5px solid var(--border);
    border-radius: var(--radius); margin-bottom: 10px;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
}
.bloque-header {
    display: flex; align-items: center; gap: 8px; padding: 7px 12px;
    border-bottom: 1px solid var(--border); background: #f5f6f8;
    border-radius: var(--radius) var(--radius) 0 0;
}
.bloque-tipo {
    font-size: 0.6rem; font-weight: 700; letter-spacing: .08em;
    text-transform: uppercase; padding: 2px 8px; border-radius: 99px;
}
.tipo-monoserie { background:#dbeafe; color:#1d4ed8; }
.tipo-biserie   { background:#d1fae5; color:#065f46; }
.tipo-triserie  { background:#fef3c7; color:#92400e; }
.tipo-circuito  { background:#fce7f3; color:#9d174d; }

/* Filas ejercicio */
.ejercicio-row {
    display: flex; border-bottom: 1px solid var(--border);
    min-height: 54px; align-items: stretch;
}
.ejercicio-row:last-child { border-bottom: none; }
.ej-letra {
    width: 22px; flex-shrink: 0; display: flex; align-items: center;
    justify-content: center; font-size: 0.65rem; font-weight: 800;
    border-right: 2px solid var(--border);
}
.ej-letra-a { color:#1d4ed8; background:#eff6ff; }
.ej-letra-b { color:#065f46; background:#f0fdf4; }
.ej-letra-c { color:#92400e; background:#fffbeb; }
.ej-letra-d { color:#9d174d; background:#fdf2f8; }
.ej-bg-a { background:#ffffff; }
.ej-bg-b { background:#f8f9fb; }
.ej-bg-c { background:#f4f6f9; }
.ej-bg-d { background:#f0f3f7; }

.col-info {
    width: 265px; flex-shrink: 0; padding: 10px 12px;
    border-right: 1px solid var(--border);
    display: flex; flex-direction: column; justify-content: center; gap: 2px;
}
.ej-nombre { font-size: 0.78rem; font-weight: 700; color: var(--text); }
.ej-segmento { font-size: 0.65rem; color: var(--muted); }

.col-series-view {
    flex: 1; display: flex; align-items: center;
    gap: 6px; padding: 8px 10px; flex-wrap: wrap;
}
.serie-chip {
    display: flex; flex-direction: column; align-items: center; gap: 2px;
    background: white; border: 1px solid var(--border);
    border-radius: 6px; padding: 5px 10px; min-width: 54px;
}
.serie-chip-num {
    font-size: 0.55rem; font-weight: 700; color: var(--accent);
    text-transform: uppercase; letter-spacing: .05em;
}
.serie-chip-val {
    font-size: 0.75rem; font-weight: 700; color: var(--text);
    font-family: 'DM Mono', monospace;
}
.serie-chip-sub {
    font-size: 0.6rem; color: var(--muted);
    font-family: 'DM Mono', monospace;
}

/* Sin rutina */
.empty-state {
    background: var(--surface); border: 1.5px solid var(--border);
    border-radius: var(--radius); padding: 60px 20px;
    display: flex; flex-direction: column; align-items: center; gap: 10px;
    color: var(--muted);
}
.empty-state svg { opacity: .35; }
.empty-state p { font-size: 0.9rem; margin: 0; }
</style>

{{-- HEADER --}}
<div class="page-header">
    <a href="{{ route('entrenador.historial.mes', [$cliente->id, $anio, $mes]) }}"
       style="font-size:1.2rem; color:var(--muted); text-decoration:none; line-height:1;">‹</a>
    <h2>{{ $diasNombre[$dia] }}</h2>
    <span class="badge">Semana {{ $sem }} · {{ $nombresMes[$mes] }} {{ $anio }}</span>
    <span class="badge" style="background:#f0fdf4;color:#065f46;border-color:#bbf7d0">
        {{ $cliente->name }}
    </span>
    @if($rutina)
<a href="{{ route('entrenador.rutina.editar', [$cliente->id, $sem, $dia]) }}"
           class="btn-editar" style="margin-left:auto">
            ✏️ &nbsp;Editar rutina
        </a>
    @else
<a href="{{ route('entrenador.rutina.editar', [$cliente->id, $sem, $dia]) }}"
           class="btn-crear" style="margin-left:auto">
            ＋ &nbsp;Crear rutina
        </a>
    @endif
</div>

{{-- CONTENIDO --}}
@if($rutina && $bloques->count())

    @php
        $letras      = ['ej-letra-a','ej-letra-b','ej-letra-c','ej-letra-d'];
        $bgClasses   = ['ej-bg-a','ej-bg-b','ej-bg-c','ej-bg-d'];
        $tiposBloque = ['monoserie','biserie','triserie','circuito'];
    @endphp

    @foreach($bloques as $bloque)
    <div class="bloque">

        {{-- Header bloque --}}
        <div class="bloque-header">
            <span class="bloque-tipo tipo-{{ $bloque['tipo'] }}">
                {{ strtoupper($bloque['tipo']) }}
            </span>
            <span style="font-size:0.7rem;color:var(--muted);margin-left:4px">
                {{ count($bloque['ejercicios']) }}
                {{ count($bloque['ejercicios']) === 1 ? 'ejercicio' : 'ejercicios' }}
            </span>
        </div>

        {{-- Ejercicios --}}
        @foreach($bloque['ejercicios'] as $i => $ej)
        @php
            $lClass = $letras[$i]    ?? 'ej-letra-a';
            $bgCls  = $bgClasses[$i] ?? 'ej-bg-a';
            $nums   = ['A','B','C','D'];
            $letra  = $nums[$i] ?? ($i+1);
        @endphp
        <div class="ejercicio-row {{ $bgCls }}">
            <div class="ej-letra {{ $lClass }}">{{ $letra }}</div>

            <div class="col-info">
                <div class="ej-nombre">{{ $ej['nombre'] ?? '–' }}</div>
                <div class="ej-segmento">{{ $ej['segmento'] ?? '' }}</div>
            </div>

            <div class="col-series-view">
                @foreach($ej['series'] as $s => $serie)
                @php $metodo = $serie['metodo'] ?? 'normal'; @endphp
                <div class="serie-chip">
                    <span class="serie-chip-num">S{{ $s + 1 }}</span>
                    @if($metodo === 'normal')
                        <span class="serie-chip-val">{{ $serie['reps'] ?? '–' }} reps</span>
                        <span class="serie-chip-sub">{{ $serie['peso'] ?? '–' }} {{ $serie['unidad'] ?? 'kg' }}</span>
                    @elseif($metodo === '888')
                        <span class="serie-chip-val">{{ $serie['reps_888'] ?? 8 }}×3</span>
                        <span class="serie-chip-sub">desc.</span>
                    @elseif($metodo === 'restpause')
                        <span class="serie-chip-val">{{ $serie['reps'] ?? '–' }} reps</span>
                        <span class="serie-chip-sub">rest-pause</span>
                    @elseif($metodo === '21s')
                        <span class="serie-chip-val">{{ $serie['reps_21s'] ?? 7 }}×3</span>
                        <span class="serie-chip-sub">3 rangos</span>
                    @elseif($metodo === '10_21')
                        <span class="serie-chip-val">10+21s</span>
                        <span class="serie-chip-sub">{{ $serie['peso_10'] ?? '–' }}kg</span>
                    @elseif($metodo === 'isometria')
                        <span class="serie-chip-val">iso+rom</span>
                        <span class="serie-chip-sub">{{ $serie['peso'] ?? '–' }} {{ $serie['unidad_iso'] ?? 'kg' }}</span>
                    @elseif($metodo === 'forzadas')
                        <span class="serie-chip-val">{{ $serie['reps'] ?? '–' }}+{{ $serie['reps_asistidas'] ?? '–' }}</span>
                        <span class="serie-chip-sub">forzadas</span>
                    @elseif($metodo === 'parciales')
                        <span class="serie-chip-val">{{ $serie['reps'] ?? '–' }} reps</span>
                        <span class="serie-chip-sub">parcial</span>
                    @elseif($metodo === 'negativas')
                        <span class="serie-chip-val">{{ $serie['reps'] ?? '–' }} reps</span>
                        <span class="serie-chip-sub">negat.</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

    </div>
    @endforeach

@else

    <div class="empty-state">
        <svg class="w-12 h-12" fill="none" stroke="currentColor"
             stroke-width="1.5" viewBox="0 0 24 24" style="width:48px;height:48px">
            <rect x="3" y="4" width="18" height="18" rx="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        <p>Sin rutina registrada para este día</p>
<a href="{{ route('entrenador.rutina.editar', [$cliente->id, $sem, $dia]) }}"
           class="btn-crear" style="margin-top:6px">
            ＋ &nbsp;Crear rutina
        </a>
    </div>

@endif

@endsection