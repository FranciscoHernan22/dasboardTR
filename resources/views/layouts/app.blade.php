<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>DevStagram - @yield('titulo')</title>
  @vite('resources/js/app.js')
</head>
<body class="bg-gray-100">

  <header class="p-5 border-b bg-white shadow">
    <div class="container mx-auto flex justify-between items-center">

      <h1 class="text-3xl font-black">Devstagram</h1>

      @auth
        <nav class="flex gap-2 items-center">
          <a class="font-bold uppercase text-gray-600 text-sm" href="">
            Hola: <span class="font-normal">{{ auth()->user()->username }}</span>
          </a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
              class="font-bold uppercase text-gray-600 text-sm hover:text-red-500 transition-colors">
              Cerrar sesión
            </button>
          </form>
        </nav>
      @endauth

      @guest
        <nav class="flex gap-2 items-center">
          @if(!request()->routeIs('login'))
            <a href="{{ route('login') }}"
               class="font-bold uppercase text-gray-600 text-sm hover:text-sky-600 transition-colors">
              Login
            </a>
          @endif
          @if(!request()->routeIs('register'))
            <a href="{{ route('register') }}"
               class="font-bold uppercase text-gray-600 text-sm bg-sky-600 text-white
                      px-4 py-2 rounded-lg hover:bg-sky-700 transition-colors">
              Crear Cuenta
            </a>
          @endif
        </nav>
      @endguest

    </div>
  </header>

  <main class="container mx-auto mt-10">
    <h2 class="font-black text-center text-3xl mb-10">@yield('titulo')</h2>
    @yield('contenido')
  </main>

  <footer class="mt-10 text-center p-5 text-gray-500 font-bold uppercase">
    DevStagram - Todos los derechos reservados {{ now()->year }}
  </footer>

</body>
</html>