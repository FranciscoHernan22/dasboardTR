<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\EntrenadorController;
use App\Http\Controllers\CrearRutinaController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\recibirguardarController;
use App\Http\Controllers\EntrenadorRutinaController;
use App\Http\Controllers\EntrenadorClienteController;
use App\Http\Controllers\HIstorialController;
use App\Http\Controllers\RutinaPdfController;
use App\Http\Controllers\PlantillaController;
use App\Http\Controllers\EntrenadorEjercicioController;
 


use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('principal');
});

Route::get('/register', [RegisterController::class, 'index'])->name('register');
Route::post('/entrenadores/registro', [EntrenadorController::class, 'store'])->name('entrenadores.register');

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'store']);

Route::post('logout', [LogoutController::class, 'store'])->name('logout');

Route::get('/muro', [PostController::class, 'index'])->name('posts.index');

Route::post('/rutinas/guardar', [recibirguardarController::class, 'guardarRutina'])->name('guardarRutina');

Route::get('/entrenador/dashboard', function () {
    return view('entrenador.dashboard');
})->name('entrenador.dashboard');

Route::post('/entrenador/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('entrenador.logout');

// ── Clientes ──────────────────────────────────────────────────────────────
Route::get('/entrenador/clientes', [EntrenadorClienteController::class, 'index'])
    ->name('entrenador.clientes');

// ── Rutinas ───────────────────────────────────────────────────────────────
// IMPORTANTE: /crear debe ir ANTES de /{cliente}


Route::get('/rutina', [CrearRutinaController::class, 'crearRutina'])
    ->name('crearRutina');

Route::get('/entrenador/rutina/{cliente}', [EntrenadorRutinaController::class, 'menu'])
    ->name('entrenador.rutina.menu');

Route::get('/entrenador/rutina/{cliente}/{semana}/{dia}',
    [EntrenadorRutinaController::class, 'editar'])
    ->name('entrenador.rutina.editar');

Route::post('/entrenador/rutina/{cliente}/{semana}/{dia}',
    [EntrenadorRutinaController::class, 'guardar'])
    ->name('entrenador.rutina.guardar');

// ── PDF ───────────────────────────────────────────────────────────────────
Route::get('entrenador/clientes/{cliente}/rutina/{semana}/{dia}/pdf',
    [RutinaPdfController::class, 'generar'])
    ->name('entrenador.rutina.pdf');

Route::get('/entrenador/plantillas/{plantilla}/pdf',
    [RutinaPdfController::class, 'plantilla'])
    ->name('entrenador.plantillas.pdf');

// ── Historial ─────────────────────────────────────────────────────────────
Route::get('/clientes/{cliente}/historial',
    [HIstorialController::class, 'anio'])
    ->name('entrenador.historial.anio');

Route::get('/clientes/{cliente}/historial/{anio}/{mes}',
    [HIstorialController::class, 'mes'])
    ->name('entrenador.historial.mes');

Route::get('/clientes/{cliente}/historial/{anio}/{mes}/{sem}/{dia}',
    [HIstorialController::class, 'dia'])
    ->name('entrenador.historial.dia');

// ── Plantillas ────────────────────────────────────────────────────────────
Route::get('/entrenador/plantillas',
    [PlantillaController::class, 'index'])
    ->name('entrenador.plantillas.index');

Route::get('/entrenador/plantillas/crear',
    [PlantillaController::class, 'crear'])
    ->name('entrenador.plantillas.crear');

Route::post('/entrenador/plantillas',
    [PlantillaController::class, 'guardar'])
    ->name('entrenador.plantillas.guardar');

Route::get('/entrenador/plantillas/{plantilla}/editar',
    [PlantillaController::class, 'editar'])
    ->name('entrenador.plantillas.editar');

Route::post('/entrenador/plantillas/{plantilla}',
    [PlantillaController::class, 'actualizar'])
    ->name('entrenador.plantillas.actualizar');

Route::delete('/entrenador/plantillas/{plantilla}',
    [PlantillaController::class, 'eliminar'])
    ->name('entrenador.plantillas.eliminar');

Route::post('/entrenador/plan/{clienteId}', [EntrenadorClienteController::class, 'guardarPlan'])
    ->name('entrenador.plan.guardar');


  Route::post('/entrenador/plantillas/{plantilla}/aplicar', [PlantillaController::class, 'aplicar'])
    ->name('entrenador.plantillas.aplicar');


    use App\Http\Controllers\Api\PlanApiController;
use App\Http\Controllers\Api\RutinaApiController;

 

Route::post('/entrenador/clientes', [EntrenadorClienteController::class, 'store'])
     ->name('entrenador.clientes.store');


     


     // ── Perfil ────────────────────────────────────────────────────────────────
Route::get('/entrenador/perfil/editar',
    [EntrenadorController::class, 'editarPerfil'])
    ->name('entrenador.perfil.edit');

Route::post('/entrenador/perfil/editar',
    [EntrenadorController::class, 'actualizarPerfil'])
    ->name('entrenador.perfil.update');







    Route::get('/entrenador/ejercicios', [EntrenadorEjercicioController::class, 'index'])
    ->name('entrenador.ejercicios.index');

Route::post('/entrenador/ejercicios', [EntrenadorEjercicioController::class, 'store'])
    ->name('entrenador.ejercicios.store');

Route::put('/entrenador/ejercicios/{ejercicio}', [EntrenadorEjercicioController::class, 'update'])
    ->name('entrenador.ejercicios.update');

Route::delete('/entrenador/ejercicios/{ejercicio}', [EntrenadorEjercicioController::class, 'destroy'])
    ->name('entrenador.ejercicios.destroy');


Route::patch('/entrenador/clientes/{cliente}/toggle-estado', [EntrenadorClienteController::class, 'toggleEstado'])
    ->name('entrenador.clientes.toggleEstado');
  


    Route::patch('/entrenador/clientes/bulk-estado', [EntrenadorClienteController::class, 'bulkEstado'])
    ->name('entrenador.clientes.bulkEstado');