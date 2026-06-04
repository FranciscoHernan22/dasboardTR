@extends('layouts.app')

@section('titulo')
  Plataforma para entrenadores profesionales
@endsection

@section('contenido')

{{-- ── HERO ── --}}
<div class="text-center py-20 px-4 bg-white border-b border-slate-100">

  <span class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 text-xs
               font-bold uppercase tracking-wide px-4 py-1.5 rounded-full
               border border-blue-200 mb-6">
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2"
         viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round"
        d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
    </svg>
    Plataforma para entrenadores profesionales
  </span>

  <h1 class="text-4xl md:text-6xl font-extrabold text-slate-900 tracking-tight
             leading-tight mb-5">
    Entrena más clientes,<br>
    <span class="text-blue-600">en menos tiempo</span>
  </h1>

  <p class="text-slate-500 text-lg md:text-xl max-w-2xl mx-auto mb-10 leading-relaxed">
    Crea rutinas de hipertrofia y fuerza, gestiona pagos, monitorea el progreso
    de cada cliente y envía entrenamientos directo a su app — todo desde un solo lugar.
  </p>

  <div class="flex gap-3 justify-center flex-wrap">
    <a href="{{ route('register') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-3.5
              rounded-xl transition-colors text-sm uppercase tracking-wide">
      Comenzar gratis
    </a>
    <a href="#como-funciona"
       class="bg-white border border-slate-200 hover:bg-slate-50 text-slate-700
              font-semibold px-8 py-3.5 rounded-xl transition-colors text-sm">
      Ver cómo funciona
    </a>
  </div>
</div>

{{-- ── STATS ── --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto py-10 px-4">
  @foreach([
    ['+500', 'Entrenadores activos'],
    ['+12k', 'Clientes gestionados'],
    ['+80k', 'Rutinas creadas'],
    ['98%',  'Satisfacción'],
  ] as [$num, $label])
  <div class="bg-white border border-slate-200 rounded-2xl p-5 text-center">
    <p class="text-3xl font-extrabold text-slate-900">{{ $num }}</p>
    <p class="text-sm text-slate-400 mt-1">{{ $label }}</p>
  </div>
  @endforeach
</div>

{{-- ── FEATURES ── --}}
<div class="max-w-5xl mx-auto px-4 pb-16">

  <div class="text-center mb-10">
    <h2 class="text-3xl font-extrabold text-slate-900 mb-2">Todo lo que necesitas</h2>
    <p class="text-slate-500">Una plataforma completa para hacer crecer tu negocio como entrenador</p>
  </div>

  <div class="grid md:grid-cols-3 gap-5">

    <div class="bg-white border border-slate-200 rounded-2xl p-6">
      <div class="w-11 h-11 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
             stroke-width="1.75" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25
               2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25
               2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25
               2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18
               A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0
               01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0
               012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5
               18v-2.25z"/>
        </svg>
      </div>
      <h3 class="font-bold text-slate-800 mb-2">Bloques personalizados</h3>
      <p class="text-sm text-slate-400 leading-relaxed">
        Diseña bloques de entrenamiento de hipertrofia, fuerza o cualquier método.
        Totalmente a tu gusto y reutilizables.
      </p>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-6">
      <div class="w-11 h-11 bg-green-100 rounded-xl flex items-center justify-center mb-4">
        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
             stroke-width="1.75" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952
               4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07
               M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766
               l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75
               0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625
               2.625 0 015.25 0z"/>
        </svg>
      </div>
      <h3 class="font-bold text-slate-800 mb-2">Gestión de clientes</h3>
      <p class="text-sm text-slate-400 leading-relaxed">
        Agrega, monitorea y gestiona todos tus clientes desde un panel claro.
        Historial, progreso y asistencia en un vistazo.
      </p>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-6">
      <div class="w-11 h-11 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
             stroke-width="1.75" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5
               A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3
               0V3h3V1.5m-3 0h3m-3 8.25h3m-3 3.75h3m-6 3.75h.008v.008H9.75v-.008z"/>
        </svg>
      </div>
      <h3 class="font-bold text-slate-800 mb-2">App para el cliente</h3>
      <p class="text-sm text-slate-400 leading-relaxed">
        Tu cliente recibe el entrenamiento en su celular, marca los ejercicios
        realizados, registra sus pesos y reporta inasistencias.
      </p>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-6">
      <div class="w-11 h-11 bg-orange-100 rounded-xl flex items-center justify-center mb-4">
        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor"
             stroke-width="1.75" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75
               4.25h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5
               4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
        </svg>
      </div>
      <h3 class="font-bold text-slate-800 mb-2">Pagos automáticos</h3>
      <p class="text-sm text-slate-400 leading-relaxed">
        Cobra a tus clientes directamente desde la plataforma.
        Suscripciones, pagos únicos y recordatorios automáticos.
      </p>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-6">
      <div class="w-11 h-11 bg-red-100 rounded-xl flex items-center justify-center mb-4">
        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor"
             stroke-width="1.75" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3
               12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483
               4.483 0 01-.923 1.785A5.969 5.969 0 006 21c1.282 0 2.47-.402
               3.445-1.087.81.22 1.668.337 2.555.337z"/>
        </svg>
      </div>
      <h3 class="font-bold text-slate-800 mb-2">Retroalimentación</h3>
      <p class="text-sm text-slate-400 leading-relaxed">
        Comentarios por día, bloque y ejercicio. Canal directo
        entre entrenador y cliente para ajustar en tiempo real.
      </p>
    </div>

   {{-- Reemplaza la tarjeta "Videos e imágenes" por esta --}}
<div class="bg-white border border-slate-200 rounded-2xl p-6">
  <div class="w-11 h-11 bg-teal-100 rounded-xl flex items-center justify-center mb-4">
    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor"
         stroke-width="1.75" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round"
        d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5l-3.9 19.5m-2.1-19.5l-3.9 19.5"/>
    </svg>
  </div>
  <h3 class="font-bold text-slate-800 mb-2">Biblioteca de ejercicios</h3>
  <p class="text-sm text-slate-400 leading-relaxed">
    Agrega tus propios ejercicios con nombre, descripción,
    videos e imágenes de demostración. Construye tu biblioteca
    personalizada y reutilízala en cualquier rutina.
  </p>
</div>

  </div>
</div>

{{-- ── ENTRENADOR VS CLIENTE ── --}}
<div class="bg-white border-y border-slate-200 py-16 px-4">
  <div class="max-w-4xl mx-auto">

    <div class="text-center mb-10">
      <h2 class="text-3xl font-extrabold text-slate-900 mb-2">
        Entrenador y cliente, cada uno en su espacio
      </h2>
      <p class="text-slate-500">Dos experiencias diseñadas para trabajar juntas</p>
    </div>

    <div class="grid md:grid-cols-2 gap-6">

      <div class="bg-blue-50 border border-blue-200 rounded-2xl p-7">
        <span class="inline-block bg-blue-600 text-white text-xs font-bold
                     uppercase tracking-wide px-3 py-1 rounded-full mb-5">
          Entrenador
        </span>
        <ul class="space-y-3">
          @foreach([
            'Crear bloques de hipertrofia y fuerza',
'Biblioteca propia de ejercicios con video e imágenes',
            'Enviar rutinas directo a la app del cliente',
            'Monitorear progreso y asistencia',
            'Comentar por día, bloque y ejercicio',
            'Gestionar pagos automáticamente',
            'Usar plantillas para ahorrar tiempo',
          ] as $item)
          <li class="flex items-center gap-3 text-sm text-blue-900">
            <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none"
                 stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
            </svg>
            {{ $item }}
          </li>
          @endforeach
        </ul>
      </div>

      <div class="bg-green-50 border border-green-200 rounded-2xl p-7">
        <span class="inline-block bg-green-600 text-white text-xs font-bold
                     uppercase tracking-wide px-3 py-1 rounded-full mb-5">
          Cliente
        </span>
        <ul class="space-y-3">
          @foreach([
            'Ver su entrenamiento del día en la app',
            'Marcar ejercicios como realizados',
            'Registrar sus pesos y marcas personales',
            'Reportar inasistencias con motivo',
            'Enviar comentarios al entrenador',
'Ver videos e imágenes de cada ejercicio',
            'Consultar su historial de progreso',
          ] as $item)
          <li class="flex items-center gap-3 text-sm text-green-900">
            <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none"
                 stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
            </svg>
            {{ $item }}
          </li>
          @endforeach
        </ul>
      </div>

    </div>
  </div>
</div>

{{-- ── CÓMO FUNCIONA ── --}}
<div id="como-funciona" class="max-w-4xl mx-auto py-16 px-4">

  <div class="text-center mb-10">
    <h2 class="text-3xl font-extrabold text-slate-900 mb-2">¿Cómo funciona?</h2>
    <p class="text-slate-500">En 4 pasos ya estás entrenando clientes de forma profesional</p>
  </div>

  <div class="grid md:grid-cols-4 gap-5">
    @foreach([
      ['1', 'Crea tu cuenta', 'Registro rápido, sin tarjeta de crédito necesaria.'],
      ['2', 'Agrega tus clientes', 'Invítalos y ellos descargan su app al instante.'],
      ['3', 'Diseña las rutinas', 'Bloques, ejercicios, series, repeticiones y notas.'],
      ['4', 'Monitorea y cobra', 'Progreso en tiempo real y pagos automáticos.'],
    ] as [$n, $title, $desc])
    <div class="bg-white border border-slate-200 rounded-2xl p-6 text-center">
      <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center
                  justify-center font-extrabold text-lg mx-auto mb-4">
        {{ $n }}
      </div>
      <h3 class="font-bold text-slate-800 mb-2">{{ $title }}</h3>
      <p class="text-sm text-slate-400 leading-relaxed">{{ $desc }}</p>
    </div>
    @endforeach
  </div>
</div>

{{-- ── CTA FINAL ── --}}
<div class="max-w-4xl mx-auto pb-16 px-4">
  <div class="bg-blue-900 rounded-3xl p-12 text-center">
    <h2 class="text-3xl font-extrabold text-white mb-3">
      Lleva tu negocio al siguiente nivel
    </h2>
    <p class="text-blue-300 mb-8 leading-relaxed max-w-xl mx-auto">
      Sin tarjeta de crédito. Configura tu cuenta en menos de 2 minutos
      y empieza a gestionar más clientes de forma profesional.
    </p>
    <a href="{{ route('register') }}"
       class="inline-block bg-white text-blue-900 font-bold px-10 py-4
              rounded-xl hover:bg-blue-50 transition-colors text-sm uppercase
              tracking-wide">
      Crear cuenta gratis
    </a>
  </div>
</div>

@endsection