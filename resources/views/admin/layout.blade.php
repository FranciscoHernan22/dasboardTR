<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800">

    @auth('admin')
    <nav class="bg-slate-900 text-white">
        <div class="max-w-6xl mx-auto px-4 flex items-center justify-between h-14">
            <div class="flex items-center gap-6">
                <span class="font-semibold">Panel Admin</span>
                <a href="{{ route('admin.dashboard') }}"
                   class="text-sm hover:text-slate-300 {{ request()->routeIs('admin.dashboard') ? 'text-white font-medium' : 'text-slate-300' }}">
                    Entrenadores
                </a>
                <a href="{{ route('admin.plantilla.index') }}"
                   class="text-sm hover:text-slate-300 {{ request()->routeIs('admin.plantilla.*') ? 'text-white font-medium' : 'text-slate-300' }}">
                    Plantilla de ejercicios
                </a>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="text-sm text-slate-300 hover:text-white">Cerrar sesión</button>
            </form>
        </div>
    </nav>
    @endauth

    <main class="max-w-6xl mx-auto px-4 py-8">
        @if (session('status'))
            <div class="mb-4 rounded-md bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

</body>
</html>