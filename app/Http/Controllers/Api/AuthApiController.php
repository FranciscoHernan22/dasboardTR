<?php
// app/Http/Controllers/Api/AuthApiController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)
            ->whereNotNull('entrenador_id') // solo clientes
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Credenciales incorrectas'], 401);
        }

        if ($user->status !== 'activo') {
            return response()->json(['error' => 'Cuenta inactiva'], 403);
        }

        $token = $user->createToken('app-movil')->plainTextToken;

        return response()->json([
            'token'      => $token,
            'cliente_id' => $user->id,
            'nombre'     => $user->name,
            'email'      => $user->email,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['ok' => true]);
    }
}