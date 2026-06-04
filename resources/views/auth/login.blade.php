@extends('layouts.app')

@section('titulo')
  Iniciar sesión
@endsection

@section('contenido')
<div class="min-h-[80vh] flex items-center justify-center px-4">
  <div class="w-full max-w-sm">

    <div class="bg-white rounded-2xl border border-slate-200 p-8">

      {{-- Logo --}}
      <div class="flex justify-center mb-6">
        <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
               stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
          </svg>
        </div>
      </div>

      <h1 class="text-xl font-extrabold text-slate-900 text-center mb-1">
        Bienvenido de nuevo
      </h1>
      <p class="text-sm text-slate-400 text-center mb-7">
        Ingresa tus datos para continuar
      </p>

      @if(session('mensaje'))
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm
                    rounded-xl p-3 mb-5 text-center">
          {{ session('mensaje') }}
        </div>
      @endif

      <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        {{-- Email --}}
        <div class="mb-4">
          <label for="email"
                 class="block text-xs font-bold uppercase tracking-wide
                        text-slate-400 mb-1.5">
            Email
          </label>
          <input id="email" name="email" type="email"
                 placeholder="tucorreo@ejemplo.com"
                 value="{{ old('email') }}"
                 class="w-full border border-slate-200 bg-slate-50 rounded-xl
                        px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400
                        focus:outline-none focus:ring-2 focus:ring-blue-500
                        focus:border-transparent transition
                        @error('email') border-red-400 bg-red-50 @enderror" />
          @error('email')
            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
          @enderror
        </div>

        {{-- Password --}}
        <div class="mb-5">
          <div class="flex items-center justify-between mb-1.5">
            <label for="password"
                   class="text-xs font-bold uppercase tracking-wide text-slate-400">
              Password
            </label>
          </div>
          <input id="password" name="password" type="password"
                 placeholder="Tu contraseña"
                 class="w-full border border-slate-200 bg-slate-50 rounded-xl
                        px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400
                        focus:outline-none focus:ring-2 focus:ring-blue-500
                        focus:border-transparent transition
                        @error('password') border-red-400 bg-red-50 @enderror" />
          @error('password')
            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
          @enderror
        </div>

        {{-- Recuérdame --}}
        <div class="flex items-center gap-2 mb-6">
          <input type="checkbox" name="remember" id="remember"
                 class="w-4 h-4 rounded border-slate-300 text-blue-600
                        focus:ring-blue-500">
          <label for="remember" class="text-sm text-slate-500">
            Mantener sesión abierta
          </label>
        </div>

        <button type="submit"
          class="w-full bg-blue-600 hover:bg-blue-700 active:scale-95
                 transition-all text-white font-bold text-sm uppercase
                 tracking-wide py-3 rounded-xl cursor-pointer">
          Iniciar sesión
        </button>

      </form>

      <p class="text-center text-sm text-slate-400 mt-6">
        ¿No tienes cuenta?
        <a href="{{ route('register') }}"
           class="text-blue-600 font-semibold hover:underline">
          Regístrate gratis
        </a>
      </p>

    </div>

    {{-- Beneficios debajo del card --}}
    <div class="grid grid-cols-3 gap-3 mt-5">
      @foreach([
        ['Rutinas rápidas', 'Crea en minutos'],
        ['App para clientes', 'Todo conectado'],
        ['Pagos automáticos', 'Sin fricción'],
      ] as [$title, $desc])
      <div class="bg-white border border-slate-200 rounded-xl p-3 text-center">
        <p class="text-xs font-bold text-slate-700">{{ $title }}</p>
        <p class="text-xs text-slate-400 mt-0.5">{{ $desc }}</p>
      </div>
      @endforeach
    </div>

  </div>
</div>
@endsection