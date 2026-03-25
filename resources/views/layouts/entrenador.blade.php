<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <header class="mb-4">
   <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

</header>
  <button
    @click="open = !open"
    class="bg-slate-900 text-white px-3 py-2 rounded rounded-t-md rounded-b-none
 hover:bg-slate-700
           flex items-center justify-between transition-all duration-100"
    :class="open ? 'w-64' : 'w-16'"
>
    <!-- ICONO A LA IZQUIERDA -->
    <span class="text-xl">☰</span>

    <!-- NOMBRE A LA DERECHA -->
     <span
        x-show="open"
        x-transition
        class="flex-1 text-center whitespace-nowrap"
    >
        Francisco
    </span>
</button>
    
    
    <title>@yield('titulo')</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100" x-data="{ open: true }">

  <!-- PERFIL (SIEMPRE VISIBLE) -->
    <div class="absolute top-4 right-4 z-50">
@include('layouts.perfil-entrenador')
    </div>
    
<div class="flex min-h-screen">

    <!-- SIDEBAR -->
<aside
    class="bg-slate-900 text-white flex flex-col transition-all duration-100"
    :class="open ? 'w-64' : 'w-16'"
>
       
        <nav class="flex-1 p-4 space-y-2">
            <a href="{{ route('entrenador.clientes') }}"
            class="flex items-center gap-2 px-4 py-2 rounded hover:bg-slate-700">
                <span>👥</span>
                <span x-show="open">Clientes</span>
            </a>
        </nav>

        <!-- LOGOUT -->
        <form method="POST" action="{{ route('entrenador.logout') }}" class="p-4" x-show="open">
            @csrf
            <button class="w-full bg-red-600 hover:bg-red-700 py-2 rounded">
                🚪 Cerrar sesión
            </button>
        </form>

    </aside>

    <!-- CONTENIDO -->
    <main class="flex-1 p-8">
        @yield('contenido')
    </main>



</div>

</body>
</html>


<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

