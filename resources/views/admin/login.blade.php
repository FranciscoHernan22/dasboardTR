@extends('admin.layout')

@section('title', 'Iniciar sesión')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center -mt-8">
    <div class="w-full max-w-sm bg-white rounded-xl shadow-sm border border-slate-200 p-8">
        <h1 class="text-xl font-semibold mb-1">Panel Admin</h1>
        <p class="text-sm text-slate-500 mb-6">Inicia sesión con tu cuenta de administrador.</p>

        <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-800">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Contraseña</label>
                <input type="password" name="password" required
                       class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-800">
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" class="rounded border-slate-300">
                Recordarme
            </label>

            <button type="submit"
                    class="w-full bg-slate-900 text-white rounded-md py-2 text-sm font-medium hover:bg-slate-800">
                Entrar
            </button>
        </form>
    </div>
</div>
@endsection