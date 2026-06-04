@extends('layouts.app')

@section('titulo')
  Crear cuenta
@endsection

@section('contenido')
<div class="flex justify-center px-4 py-10">
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
        Crea tu cuenta
      </h1>
      <p class="text-sm text-slate-400 text-center mb-7">
        Empieza a gestionar tus clientes hoy
      </p>

      <form action="{{ route('entrenadores.register') }}" method="POST" novalidate>
        @csrf

        {{-- Nombre --}}
        <div class="mb-4">
          <label for="name"
                 class="block text-xs font-bold uppercase tracking-wide
                        text-slate-400 mb-1.5">
            Nombre completo
          </label>
          <input id="name" name="name" type="text"
                 placeholder="Tu nombre"
                 value="{{ old('name') }}"
                 class="w-full border border-slate-200 bg-slate-50 rounded-xl
                        px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400
                        focus:outline-none focus:ring-2 focus:ring-blue-500
                        focus:border-transparent transition
                        @error('name') border-red-400 bg-red-50 @enderror" />
          @error('name')
            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
          @enderror
        </div>

        {{-- Username --}}
        <div class="mb-4">
          <label for="username"
                 class="block text-xs font-bold uppercase tracking-wide
                        text-slate-400 mb-1.5">
            Username
          </label>
          <div class="relative">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm
                         text-slate-400 font-medium">@</span>
            <input id="username" name="username" type="text"
                   placeholder="tunombre"
                   value="{{ old('username') }}"
                   class="w-full border border-slate-200 bg-slate-50 rounded-xl
                          pl-8 pr-4 py-2.5 text-sm text-slate-800 placeholder-slate-400
                          focus:outline-none focus:ring-2 focus:ring-blue-500
                          focus:border-transparent transition
                          @error('username') border-red-400 bg-red-50 @enderror" />
          </div>
          @error('username')
            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
          @enderror
        </div>

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
        <div class="mb-4">
          <label for="password"
                 class="block text-xs font-bold uppercase tracking-wide
                        text-slate-400 mb-1.5">
            Password
          </label>
          <input id="password" name="password" type="password"
                 placeholder="Mínimo 8 caracteres"
                 class="w-full border border-slate-200 bg-slate-50 rounded-xl
                        px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400
                        focus:outline-none focus:ring-2 focus:ring-blue-500
                        focus:border-transparent transition
                        @error('password') border-red-400 bg-red-50 @enderror" />
          @error('password')
            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
          @enderror
        </div>

        {{-- Confirmar password --}}
        <div class="mb-7">
          <label for="password_confirmation"
                 class="block text-xs font-bold uppercase tracking-wide
                        text-slate-400 mb-1.5">
            Repetir password
          </label>
          <input id="password_confirmation" name="password_confirmation"
                 type="password" placeholder="Repite tu contraseña"
                 class="w-full border border-slate-200 bg-slate-50 rounded-xl
                        px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400
                        focus:outline-none focus:ring-2 focus:ring-blue-500
                        focus:border-transparent transition
                        @error('password_confirmation') border-red-400 bg-red-50
                        @enderror" />
          @error('password_confirmation')
            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
          @enderror
        </div>

        <button type="submit"
          class="w-full bg-blue-600 hover:bg-blue-700 active:scale-95
                 transition-all text-white font-bold text-sm uppercase
                 tracking-wide py-3 rounded-xl cursor-pointer">
          Crear cuenta gratis
        </button>

      </form>

      <p class="text-center text-xs text-slate-400 mt-4 leading-relaxed">
        Al registrarte aceptas nuestros
        <a href="#" class="text-blue-600 hover:underline">Términos de uso</a>
        y
        <a href="#" class="text-blue-600 hover:underline">Política de privacidad</a>
      </p>

      <p class="text-center text-sm text-slate-400 mt-4">
        ¿Ya tienes cuenta?
        <a href="{{ route('login') }}"
           class="text-blue-600 font-semibold hover:underline">
          Inicia sesión
        </a>
      </p>

    </div>

    {{-- Beneficios --}}
    <div class="grid grid-cols-3 gap-3 mt-5">
      @foreach([
        ['Gratis', 'Sin tarjeta'],
        ['2 minutos', 'Setup rápido'],
        ['Sin límites', 'Todos los clientes'],
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