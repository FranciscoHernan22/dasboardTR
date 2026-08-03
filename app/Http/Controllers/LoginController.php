<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Clave única por email + IP, para no bloquear a otros usuarios
        $key = strtolower($request->input('email')).'|'.$request->ip();

        // 1. Verificar si ya superó el límite de intentos
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $segundos = RateLimiter::availableIn($key);
            $minutos = ceil($segundos / 60);

            return back()->with(
                'mensaje',
                "Demasiados intentos fallidos. Intenta de nuevo en {$minutos} minuto(s)."
            );
        }

        // 2. Intentar autenticar
        if (! auth()->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            // 3. Registrar el intento fallido (dura 5 minutos = 300 segundos)
            RateLimiter::hit($key, 300);

            return back()->with('mensaje', 'Credenciales incorrectas');
        }

        // 4. Login exitoso -> limpiar el contador de intentos
        RateLimiter::clear($key);

        return redirect()->route('entrenador.dashboard');
    }
}