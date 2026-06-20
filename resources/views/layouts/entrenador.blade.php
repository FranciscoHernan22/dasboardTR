<!DOCTYPE html>
{{-- DESTINO: resources/views/layouts/entrenador.blade.php
     Esta es tu versión ORIGINAL, antes de los cambios de "Ejercicios por entrenador".
     Reemplaza tu layout actual con este. --}}
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('titulo')</title>
  @vite('resources/css/app.css')
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>
    .sidebar { transition: width .2s ease; }
  </style>
</head>

<body
  class="bg-gray-50 text-gray-900 antialiased"
  x-data="{
    open: localStorage.getItem('sidebar') !== 'false',
    toggle() {
      this.open = !this.open;
      localStorage.setItem('sidebar', this.open);
    }
  }"
>

<div class="flex min-h-screen">

  {{-- ─── SIDEBAR ─── --}}
  <aside
    class="sidebar bg-slate-900 text-white flex flex-col flex-shrink-0 overflow-hidden"
    :class="open ? 'w-56' : 'w-0'"
  >
    {{-- Logo --}}
    <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-700/50">
      <div class="w-7 h-7 rounded-lg bg-blue-600 flex items-center justify-center flex-shrink-0">
        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
        </svg>
      </div>
      <span class="text-sm font-semibold text-white whitespace-nowrap">GymApp</span>
    </div>

    {{-- Nav --}}
   {{-- Nav --}}
<nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
  <p class="px-3 py-1 text-[10px] font-medium text-slate-500 uppercase tracking-wider whitespace-nowrap">Principal</p>

  <a href="{{ route('entrenador.clientes') }}"
     class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm whitespace-nowrap transition-colors duration-100
            {{ request()->routeIs('entrenador.clientes') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
    </svg>
    <span>Clientes</span>
  </a>

  <a href="{{ route('entrenador.plantillas.index') }}"
     class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm whitespace-nowrap transition-colors duration-100
            {{ request()->routeIs('entrenador.plantillas.*') ? 'bg-blue-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
    <span>Plantillas</span>
  </a>

  <a href="#"
     class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm whitespace-nowrap text-slate-400 hover:bg-slate-800 hover:text-white transition-colors duration-100">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
    </svg>
    <span>Rutinas</span>
  </a>

  <a href="#"
     class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm whitespace-nowrap text-slate-400 hover:bg-slate-800 hover:text-white transition-colors duration-100">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5l-3.9 19.5m-2.1-19.5l-3.9 19.5"/>
    </svg>
    <span>Ejercicios</span>
  </a>

  <p class="px-3 pt-4 pb-1 text-[10px] font-medium text-slate-500 uppercase tracking-wider whitespace-nowrap">Reportes</p>

  <a href="#"
     class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm whitespace-nowrap text-slate-400 hover:bg-slate-800 hover:text-white transition-colors duration-100">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/>
    </svg>
    <span>Progreso</span>
  </a>
</nav>

    {{-- Usuario + logout --}}
    
  </aside>

  {{-- ─── MAIN ─── --}}
  <div class="flex-1 flex flex-col min-w-0">

    {{-- Topbar — botón + usuario a la derecha --}}

    

    {{-- Contenido --}}
    <main class="flex-1 p-6 overflow-auto">
      @yield('contenido')
    </main>

  </div>
</div>

</body>
</html>