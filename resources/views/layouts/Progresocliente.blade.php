@extends('layouts.entrenador')
@section('titulo', 'Progreso — ' . $cliente->name)
@section('contenido')

<div class="flex items-center gap-2 text-xs text-gray-400 mb-4">
    <a href="{{ route('entrenador.clientes') }}" class="hover:text-gray-600">Clientes</a>
    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    <span class="text-gray-600 font-medium">Progreso</span>
</div>

<div class="flex items-center gap-3 mb-6">
    <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-sm font-bold">
        {{ strtoupper(substr($cliente->name, 0, 2)) }}
    </div>
    <div>
        <h1 class="text-lg font-bold text-gray-900">{{ $cliente->name }}</h1>
        <p class="text-sm text-gray-500">{{ $cliente->email }}</p>
    </div>
</div>

@if(session('success'))
<div class="flex items-center gap-2 bg-green-50 border border-green-200 rounded-lg px-4 py-3 mb-4">
    <div class="w-2 h-2 rounded-full bg-green-500 flex-shrink-0"></div>
    <span class="text-sm text-green-700">{{ session('success') }}</span>
</div>
@endif

<div class="flex flex-wrap gap-1 bg-white border border-gray-200 rounded-xl p-1 mb-5 w-fit">
    <button class="tab-btn active px-4 py-2 rounded-lg text-sm font-semibold transition-colors" onclick="cambiarTab('resumen', this)">Resumen</button>
    <button class="tab-btn px-4 py-2 rounded-lg text-sm font-semibold transition-colors" onclick="cambiarTab('fisico', this)">Físico</button>
    <button class="tab-btn px-4 py-2 rounded-lg text-sm font-semibold transition-colors" onclick="cambiarTab('rendimiento', this)">Rendimiento</button>
    <button class="tab-btn px-4 py-2 rounded-lg text-sm font-semibold transition-colors" onclick="cambiarTab('constancia', this)">Constancia</button>
    <button class="tab-btn px-4 py-2 rounded-lg text-sm font-semibold transition-colors" onclick="cambiarTab('videos', this)">Videos</button>
    <button class="tab-btn px-4 py-2 rounded-lg text-sm font-semibold transition-colors" onclick="cambiarTab('notas', this)">Notas</button>
</div>

{{-- ── TAB: RESUMEN ── --}}
<div id="tab-resumen" class="tab-panel active">
    @if($medidaMasReciente && $medidaMasAntigua && $medidaMasReciente->id !== $medidaMasAntigua->id)
        @php
            $deltaPeso = $medidaMasReciente->peso - $medidaMasAntigua->peso;
            $deltaCintura = $medidaMasReciente->cintura - $medidaMasAntigua->cintura;
            $deltaPesoEjercicio = ($pesoMaximo ?? 0) - ($pesoInicial ?? 0);
        @endphp
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
            <p class="text-sm text-blue-800 leading-relaxed">
                Comparando <strong>{{ $medidaMasAntigua->mes->translatedFormat('F Y') }}</strong> con <strong>{{ $medidaMasReciente->mes->translatedFormat('F Y') }}</strong>:
                @if($deltaPeso)
                    {{ $deltaPeso < 0 ? 'bajó' : 'subió' }} <strong>{{ abs(round($deltaPeso, 1)) }} kg</strong>
                @endif
                @if($deltaCintura)
                    , {{ $deltaCintura < 0 ? 'perdió' : 'ganó' }} <strong>{{ abs(round($deltaCintura)) }} cm de cintura</strong>
                @endif
                @if($ejercicioPrincipal && $deltaPesoEjercicio != 0)
                    y {{ $deltaPesoEjercicio > 0 ? 'subió' : 'bajó' }} <strong>{{ abs(round($deltaPesoEjercicio)) }} kg en {{ $ejercicioPrincipal }}</strong>
                @endif
                @if($porcentajeConstancia !== null)
                    . Constancia del mes: <strong>{{ $porcentajeConstancia }}%</strong>
                @endif
                .
            </p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @php
                $metricas = [
                    ['label' => 'Peso corporal', 'valor' => $medidaMasReciente->peso ? $medidaMasReciente->peso . ' kg' : '—', 'delta' => $deltaPeso ?? null, 'unidad' => 'kg', 'positivoEs' => 'menos'],
                    ['label' => 'Cintura', 'valor' => $medidaMasReciente->cintura ? $medidaMasReciente->cintura . ' cm' : '—', 'delta' => $deltaCintura ?? null, 'unidad' => 'cm', 'positivoEs' => 'menos'],
                    ['label' => $ejercicioPrincipal ?? 'Ejercicio principal', 'valor' => $pesoMaximo ? $pesoMaximo . ' kg' : '—', 'delta' => $deltaPesoEjercicio ?? null, 'unidad' => 'kg', 'positivoEs' => 'mas'],
                    ['label' => 'Constancia', 'valor' => $porcentajeConstancia !== null ? $porcentajeConstancia . '%' : '—', 'delta' => null, 'unidad' => '', 'positivoEs' => 'mas'],
                ];
            @endphp
            @foreach($metricas as $m)
            <div class="bg-white border border-gray-200 rounded-xl p-3.5">
                <p class="text-xs text-gray-400 mb-1">{{ $m['label'] }}</p>
                <p class="text-lg font-bold text-gray-900">{{ $m['valor'] }}</p>
                @if($m['delta'])
                    @php
                        $mejora = $m['positivoEs'] === 'menos' ? $m['delta'] < 0 : $m['delta'] > 0;
                    @endphp
                    <p class="text-[11px] font-semibold flex items-center gap-0.5 mt-0.5 {{ $mejora ? 'text-green-600' : 'text-red-500' }}">
                        {{ $m['delta'] > 0 ? '+' : '' }}{{ round($m['delta'], 1) }} {{ $m['unidad'] }}
                    </p>
                @else
                    <p class="text-[11px] text-gray-400 font-semibold mt-0.5">Sin comparación</p>
                @endif
            </div>
            @endforeach
        </div>
    @else
        <div class="bg-white border border-gray-200 rounded-xl p-8 text-center">
            <p class="text-sm text-gray-400">Necesitas al menos dos meses de medidas registradas para ver un resumen comparativo.</p>
        </div>
    @endif
</div>

{{-- ── TAB: FÍSICO ── --}}
<div id="tab-fisico" class="tab-panel">
    <div class="bg-white border border-gray-200 rounded-xl p-4 mb-4">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-bold text-gray-900">Fotos por mes</h2>
            <button type="button" onclick="document.getElementById('modalFoto').style.display='flex'"
                class="text-xs font-semibold text-blue-600 hover:text-blue-700">+ Subir foto</button>
        </div>
        @if($fotos->isEmpty())
            <p class="text-sm text-gray-400 text-center py-8">Todavía no hay fotos registradas.</p>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                @foreach($fotos as $mesKey => $fotosDelMes)
                <div class="bg-gray-100 rounded-lg overflow-hidden">
                    <img src="{{ $fotosDelMes->first()->url }}" class="w-full h-24 object-cover">
                    <p class="text-[11px] text-center text-gray-500 py-1.5">{{ \Carbon\Carbon::parse($mesKey)->translatedFormat('M Y') }}</p>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-4">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-bold text-gray-900">Medidas corporales</h2>
            <button type="button" onclick="document.getElementById('modalMedida').style.display='flex'"
                class="text-xs font-semibold text-blue-600 hover:text-blue-700">+ Registrar mes</button>
        </div>

        @if($medidas->isEmpty())
            <p class="text-sm text-gray-400 text-center py-8">Todavía no hay medidas registradas.</p>
        @else
            <canvas id="chartFisico" height="80"></canvas>

            <table class="w-full text-xs mt-4 border-t border-gray-100 pt-3">
                <thead>
                    <tr class="text-gray-400">
                        <th class="text-left font-medium pb-2">Mes</th>
                        <th class="text-right font-medium pb-2">Peso</th>
                        <th class="text-right font-medium pb-2">Cintura</th>
                        <th class="text-right font-medium pb-2">Cadera</th>
                        <th class="text-right font-medium pb-2">% grasa</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @foreach($medidas->sortByDesc('mes') as $medida)
                    <tr class="border-t border-gray-50">
                        <td class="py-1.5 font-medium">{{ $medida->mes->translatedFormat('F Y') }}</td>
                        <td class="text-right">{{ $medida->peso ? $medida->peso . ' kg' : '—' }}</td>
                        <td class="text-right">{{ $medida->cintura ? $medida->cintura . ' cm' : '—' }}</td>
                        <td class="text-right">{{ $medida->cadera ? $medida->cadera . ' cm' : '—' }}</td>
                        <td class="text-right">{{ $medida->grasa_corporal ? $medida->grasa_corporal . '%' : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

{{-- ── TAB: RENDIMIENTO ── --}}
<div id="tab-rendimiento" class="tab-panel">

    {{-- 1RM vigente por ejercicio --}}
    <div class="bg-white border border-gray-200 rounded-xl p-4 mb-4">
        <h2 class="text-sm font-bold text-gray-900 mb-3">1RM estimado por ejercicio</h2>

        @if($estimaciones1RM->isEmpty())
            <p class="text-sm text-gray-400 text-center py-6">
                Todavía no hay ningún 1RM calculado. Se genera automáticamente cuando el cliente completa series normales, rest-pause, forzadas o el primer tramo de un 888.
            </p>
        @else
            <div class="overflow-x-auto -mx-1">
                <table class="w-full text-xs min-w-[520px]">
                    <thead>
                        <tr class="text-gray-400">
                            <th class="text-left font-medium pb-2 px-1">Ejercicio</th>
                            <th class="text-right font-medium pb-2 px-1">1RM estimado</th>
                            <th class="text-center font-medium pb-2 px-1">Confianza</th>
                            <th class="text-right font-medium pb-2 px-1">Basado en</th>
                            <th class="text-right font-medium pb-2 px-1">Actualizado</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @foreach($estimaciones1RM as $est)
                        @php
                            $nivelInfo = [
                                'A' => ['label' => 'Alta',  'bg' => 'bg-green-100',  'text' => 'text-green-700'],
                                'B' => ['label' => 'Media', 'bg' => 'bg-amber-100',  'text' => 'text-amber-700'],
                                'C' => ['label' => 'Baja',  'bg' => 'bg-gray-100',   'text' => 'text-gray-500'],
                            ][$est->nivel_confianza] ?? ['label' => $est->nivel_confianza, 'bg' => 'bg-gray-100', 'text' => 'text-gray-500'];
                        @endphp
                        <tr class="border-t border-gray-50">
                            <td class="py-2 px-1 font-medium text-gray-900">{{ $est->ejercicio->nombre ?? 'Ejercicio eliminado' }}</td>
                            <td class="text-right px-1 font-bold text-gray-900">{{ rtrim(rtrim(number_format($est->valor_1rm_kg, 1), '0'), '.') }} kg</td>
                            <td class="text-center px-1">
                                <span class="inline-block px-2 py-0.5 rounded-full font-semibold {{ $nivelInfo['bg'] }} {{ $nivelInfo['text'] }}">
                                    {{ $nivelInfo['label'] }}
                                </span>
                            </td>
                            <td class="text-right px-1 text-gray-500">{{ $est->reps_base }} reps × {{ rtrim(rtrim(number_format($est->peso_base, 1), '0'), '.') }} {{ $est->unidad_base }}</td>
                            <td class="text-right px-1 text-gray-400">{{ $est->fecha_calculo?->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Evolución de peso por ejercicio --}}
    <div class="bg-white border border-gray-200 rounded-xl p-4">
        <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
            <h2 class="text-sm font-bold text-gray-900">Evolución de peso</h2>
            @if(count($historialPesoPorEjercicio) > 0)
            <select id="selectEjercicioProgreso" onchange="cambiarEjercicioProgreso(this.value)"
                class="text-xs border border-gray-200 rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-blue-500 bg-gray-50 text-gray-700">
                @foreach($historialPesoPorEjercicio as $ejercicioId => $data)
                    <option value="{{ $ejercicioId }}">{{ $data['nombre'] }}</option>
                @endforeach
            </select>
            @endif
        </div>

        @if(count($historialPesoPorEjercicio) === 0)
            <p class="text-sm text-gray-400 text-center py-8">
                Todavía no hay series completadas con peso registrado. La gráfica aparece en cuanto el cliente marque al menos dos días de un mismo ejercicio.
            </p>
        @else
            <canvas id="chartProgresoPeso" height="90"></canvas>
        @endif
    </div>
</div>

{{-- ── TAB: CONSTANCIA ── --}}
<div id="tab-constancia" class="tab-panel">
    <div class="grid grid-cols-2 gap-3 mb-4">
        <div class="bg-white border border-gray-200 rounded-xl p-4 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400 mb-1">Racha actual</p>
                <p class="text-xl font-bold text-gray-900">{{ $rachaSemanas }} {{ $rachaSemanas === 1 ? 'semana' : 'semanas' }}</p>
            </div>
            <div class="w-11 h-11 rounded-full bg-amber-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2c-1.5 4-5 6-5 10a5 5 0 0010 0c0-1-.3-2-1-3 .8 2-1 3-1 3 .5-2-.5-3.5-2-4.5C13.5 9 13 5.5 12 2z"/></svg>
            </div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <p class="text-xs text-gray-400 mb-1">Sesiones completadas (30 días)</p>
            <p class="text-xl font-bold text-gray-900">{{ $porcentajeConstancia !== null ? $porcentajeConstancia . '%' : '—' }}</p>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-4">
        <h2 class="text-sm font-bold text-gray-900 mb-3">Últimas 12 semanas</h2>
        @if($sesiones->isEmpty())
            <p class="text-sm text-gray-400 text-center py-4">Todavía no hay sesiones registradas.</p>
        @else
            <div class="grid grid-cols-12 gap-1.5">
                @foreach($sesiones as $sesion)
                <div class="w-full aspect-square rounded-sm {{ $sesion->completada ? 'bg-green-500' : 'bg-gray-100' }}"
                     title="{{ $sesion->fecha->format('d/m/Y') }}"></div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- ── TAB: VIDEOS ── --}}
<div id="tab-videos" class="tab-panel">
    @forelse($videos as $ejercicio => $clips)
        @php
            $antes = $clips->firstWhere('tipo', 'antes');
            $ahora = $clips->firstWhere('tipo', 'ahora');
        @endphp
        <div class="bg-white border border-gray-200 rounded-xl p-4 mb-3">
            <h2 class="text-sm font-bold text-gray-900 mb-3">{{ $ejercicio }}</h2>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    @if($antes)
                        <a href="{{ $antes->url }}" target="_blank" class="block bg-gray-100 rounded-lg h-32 flex items-center justify-center text-gray-400 hover:text-blue-500 transition-colors">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </a>
                    @else
                        <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-lg h-32 flex items-center justify-center text-xs text-gray-300">Sin video</div>
                    @endif
                    <p class="text-xs text-gray-500 mt-1.5">Antes</p>
                </div>
                <div>
                    @if($ahora)
                        <a href="{{ $ahora->url }}" target="_blank" class="block bg-gray-100 rounded-lg h-32 flex items-center justify-center text-gray-400 hover:text-blue-500 transition-colors">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </a>
                    @else
                        <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-lg h-32 flex items-center justify-center text-xs text-gray-300">Sin video</div>
                    @endif
                    <p class="text-xs text-gray-500 mt-1.5">Ahora</p>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white border border-gray-200 rounded-xl p-8 text-center mb-3">
            <p class="text-sm text-gray-400">Todavía no hay videos registrados.</p>
        </div>
    @endforelse
</div>

{{-- ── TAB: NOTAS ── --}}
<div id="tab-notas" class="tab-panel">
    <div class="bg-white border border-gray-200 rounded-xl p-4 mb-4">
        @forelse($notas as $nota)
        <div class="flex items-start gap-3 {{ !$loop->last ? 'mb-4 pb-4 border-b border-gray-100' : '' }}">
            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between mb-0.5">
                    <p class="text-sm font-semibold text-gray-900">{{ $nota->created_at->translatedFormat('j F Y') }}</p>
                    @if($nota->etiqueta)
                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 font-semibold">{{ $nota->etiqueta }}</span>
                    @endif
                </div>
                <p class="text-sm text-gray-600">{{ $nota->contenido }}</p>
            </div>
        </div>
        @empty
        <p class="text-sm text-gray-400 text-center py-8">Todavía no hay notas registradas.</p>
        @endforelse
    </div>
    <button type="button" onclick="document.getElementById('modalNota').style.display='flex'"
        class="w-full py-2.5 border-2 border-dashed border-gray-200 rounded-xl text-sm text-gray-400 hover:border-blue-300 hover:text-blue-500 transition-colors">
        + Agregar nota
    </button>
</div>

{{-- ── Modal: registrar medidas del mes ── --}}
<div id="modalMedida" onclick="if(event.target===this)this.style.display='none'" style="display:none;"
    class="fixed inset-0 bg-black/40 z-[10000] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md max-h-[92vh] overflow-y-auto shadow-2xl">
        <div class="flex items-center justify-between px-5 pt-5 pb-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">Registrar medidas</h3>
            <button onclick="document.getElementById('modalMedida').style.display='none'"
                class="w-7 h-7 rounded-lg bg-gray-100 hover:bg-red-50 hover:text-red-500 flex items-center justify-center text-gray-400 text-sm">✕</button>
        </div>
        <form method="POST" action="{{ route('entrenador.progreso.medida.store', $cliente->id) }}" class="p-5 flex flex-col gap-3">
            @csrf
            <div class="flex flex-col gap-1">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Mes</label>
                <input type="month" name="mes" required class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Peso (kg)</label>
                    <input type="number" step="0.1" name="peso" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">% grasa</label>
                    <input type="number" step="0.1" name="grasa_corporal" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Cintura (cm)</label>
                    <input type="number" step="0.1" name="cintura" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Cadera (cm)</label>
                    <input type="number" step="0.1" name="cadera" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Pecho (cm)</label>
                    <input type="number" step="0.1" name="pecho" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Brazo (cm)</label>
                    <input type="number" step="0.1" name="brazo" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalMedida').style.display='none'"
                    class="flex-1 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-500 hover:bg-gray-50">Cancelar</button>
                <button type="submit" class="flex-[2] py-2.5 bg-blue-600 hover:bg-blue-700 rounded-lg text-sm font-semibold text-white">Guardar</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Modal: subir foto ── --}}
<div id="modalFoto" onclick="if(event.target===this)this.style.display='none'" style="display:none;"
    class="fixed inset-0 bg-black/40 z-[10000] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl">
        <div class="flex items-center justify-between px-5 pt-5 pb-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">Subir foto de progreso</h3>
            <button onclick="document.getElementById('modalFoto').style.display='none'"
                class="w-7 h-7 rounded-lg bg-gray-100 hover:bg-red-50 hover:text-red-500 flex items-center justify-center text-gray-400 text-sm">✕</button>
        </div>
        <form method="POST" action="{{ route('entrenador.progreso.foto.store', $cliente->id) }}" enctype="multipart/form-data" class="p-5 flex flex-col gap-3">
            @csrf
            <div class="flex flex-col gap-1">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Mes</label>
                <input type="month" name="mes" required class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Foto</label>
                <input type="file" name="foto" accept="image/*" required class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Ángulo (opcional)</label>
                <select name="angulo" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">Sin especificar</option>
                    <option value="frente">Frente</option>
                    <option value="perfil">Perfil</option>
                    <option value="espalda">Espalda</option>
                </select>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalFoto').style.display='none'"
                    class="flex-1 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-500 hover:bg-gray-50">Cancelar</button>
                <button type="submit" class="flex-[2] py-2.5 bg-blue-600 hover:bg-blue-700 rounded-lg text-sm font-semibold text-white">Subir</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Modal: agregar nota ── --}}
<div id="modalNota" onclick="if(event.target===this)this.style.display='none'" style="display:none;"
    class="fixed inset-0 bg-black/40 z-[10000] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl">
        <div class="flex items-center justify-between px-5 pt-5 pb-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">Agregar nota</h3>
            <button onclick="document.getElementById('modalNota').style.display='none'"
                class="w-7 h-7 rounded-lg bg-gray-100 hover:bg-red-50 hover:text-red-500 flex items-center justify-center text-gray-400 text-sm">✕</button>
        </div>
        <form method="POST" action="{{ route('entrenador.progreso.nota.store', $cliente->id) }}" class="p-5 flex flex-col gap-3">
            @csrf
            <div class="flex flex-col gap-1">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Etiqueta (opcional)</label>
                <input type="text" name="etiqueta" placeholder="Ej. Energía alta, Molestia, Evaluación"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Nota</label>
                <textarea name="contenido" rows="4" required class="border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalNota').style.display='none'"
                    class="flex-1 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-500 hover:bg-gray-50">Cancelar</button>
                <button type="submit" class="flex-[2] py-2.5 bg-blue-600 hover:bg-blue-700 rounded-lg text-sm font-semibold text-white">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<style>
    .tab-btn.active { background: #2563eb; color: white; }
    .tab-btn:not(.active) { color: #6b7280; }
    .tab-panel { display: none; }
    .tab-panel.active { display: block; }
</style>
<script>
function cambiarTab(id, btn) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + id).classList.add('active');
    btn.classList.add('active');
}

@if($medidas->isNotEmpty())
new Chart(document.getElementById('chartFisico'), {
    type: 'line',
    data: {
        labels: {!! json_encode($medidas->map(fn($m) => $m->mes->translatedFormat('M Y'))->values()) !!},
        datasets: [{
            label: 'Peso (kg)',
            data: {!! json_encode($medidas->pluck('peso')->values()) !!},
            borderColor: '#7c3aed',
            backgroundColor: 'rgba(124,58,237,0.08)',
            fill: true,
            tension: 0.3,
            pointRadius: 4,
            pointBackgroundColor: '#7c3aed'
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: { y: { grid: { color: '#f3f4f6' } }, x: { grid: { display: false } } }
    }
});
@endif

// ── Evolución de peso por ejercicio (pestaña Rendimiento) ──
@if(count($historialPesoPorEjercicio) > 0)
const historialPesoPorEjercicio = {!! json_encode(collect($historialPesoPorEjercicio)->mapWithKeys(function ($data, $ejercicioId) {
    return [$ejercicioId => [
        'nombre'  => $data['nombre'],
        'fechas'  => array_keys($data['puntos']),
        'valores' => array_values($data['puntos']),
    ]];
})) !!};

let chartProgresoPeso = null;

function dibujarChartProgresoPeso(ejercicioId) {
    const info = historialPesoPorEjercicio[ejercicioId];
    if (!info) return;

    const ctx = document.getElementById('chartProgresoPeso');
    if (!ctx) return;

    if (chartProgresoPeso) chartProgresoPeso.destroy();

    chartProgresoPeso = new Chart(ctx, {
        type: 'line',
        data: {
            labels: info.fechas.map(f => {
                const d = new Date(f + 'T00:00:00');
                return d.toLocaleDateString('es-MX', { day: '2-digit', month: 'short' });
            }),
            datasets: [{
                label: info.nombre,
                data: info.valores,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37,99,235,0.08)',
                fill: true,
                tension: 0.3,
                pointRadius: 4,
                pointBackgroundColor: '#2563eb'
            }]
        },
        options: {
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => `${ctx.parsed.y} kg` } }
            },
            scales: { y: { grid: { color: '#f3f4f6' } }, x: { grid: { display: false } } }
        }
    });
}

function cambiarEjercicioProgreso(ejercicioId) {
    dibujarChartProgresoPeso(ejercicioId);
}

document.addEventListener('DOMContentLoaded', () => {
    const primerId = Object.keys(historialPesoPorEjercicio)[0];
    if (primerId) dibujarChartProgresoPeso(primerId);
});
@endif
</script>

@endsection