<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdminWeb
{
    public function handle(Request $request, Closure $next): Response
    {
        $entrenador = Auth::guard('admin')->user();

        if (! $entrenador || $entrenador->role !== 'admin') {
            return redirect()->route('admin.login')
                ->withErrors(['login' => 'Necesitas iniciar sesión como administrador.']);
        }

        return $next($request);
    }
}