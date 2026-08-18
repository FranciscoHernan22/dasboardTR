<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\PostController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\EntrenadorController;
use App\Http\Controllers\CrearRutinaController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\recibirguardarController;
use App\Http\Controllers\EntrenadorRutinaController;
use App\Http\Controllers\EntrenadorClienteController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\RutinaPdfController;
use App\Http\Controllers\PlantillaController;
use App\Http\Controllers\EntrenadorEjercicioController;
use App\Http\Controllers\EntrenadorProgresoController;
use App\Http\Controllers\Api\PlanApiController;
use App\Http\Controllers\Api\RutinaApiController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PlantillaEjercicioWebController;

/*
|--------------------------------------------------------------------------
| Rutas públicas (sin autenticación)
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| Rutas protegidas (requieren sesión iniciada)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // ── Dashboard / logout ──
    Route::get('/entrenador/dashboard', function () {
        return view('entrenador.dashboard');
    })->name('entrenador.dashboard');

    Route::post('/entrenador/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('entrenador.logout');

    // ── Clientes ──
    Route::get('/entrenador/clientes', [EntrenadorClienteController::class, 'index'])
        ->name('entrenador.clientes');

    Route::post('/entrenador/clientes', [EntrenadorClienteController::class, 'store'])
        ->name('entrenador.clientes.store');

    Route::put('/entrenador/clientes/{cliente}', [EntrenadorClienteController::class, 'update'])
        ->name('entrenador.clientes.update');

    Route::patch('/entrenador/clientes/{cliente}/toggle-estado', [EntrenadorClienteController::class, 'toggleEstado'])
        ->name('entrenador.clientes.toggleEstado');

    Route::patch('/entrenador/clientes/bulk-estado', [EntrenadorClienteController::class, 'bulkEstado'])
        ->name('entrenador.clientes.bulkEstado');

    Route::post('/entrenador/plan/{clienteId}', [EntrenadorClienteController::class, 'guardarPlan'])
        ->name('entrenador.plan.guardar');

    // ── Rutinas ──
    // IMPORTANTE: /crear debe ir ANTES de /{cliente}
    Route::get('/rutina', [CrearRutinaController::class, 'crearRutina'])
        ->name('crearRutina');

    Route::get('/entrenador/rutina/{cliente}', [EntrenadorRutinaController::class, 'menu'])
        ->name('entrenador.rutina.menu');

    Route::get('/entrenador/rutina/{cliente}/{semana}/{dia}', [EntrenadorRutinaController::class, 'editar'])
        ->name('entrenador.rutina.editar');

    Route::post('/entrenador/rutina/{cliente}/{semana}/{dia}', [EntrenadorRutinaController::class, 'guardar'])
        ->name('entrenador.rutina.guardar');

    // ── PDF ──
    Route::get('entrenador/clientes/{cliente}/rutina/{semana}/{dia}/pdf', [RutinaPdfController::class, 'generar'])
        ->name('entrenador.rutina.pdf');

    Route::get('/entrenador/plantillas/{plantilla}/pdf', [RutinaPdfController::class, 'plantilla'])
        ->name('entrenador.plantillas.pdf');

    // ── Historial ──
    Route::get('/clientes/{cliente}/historial', [HistorialController::class, 'anio'])
        ->name('entrenador.historial.anio');

    Route::get('/clientes/{cliente}/historial/{anio}/{mes}', [HistorialController::class, 'mes'])
        ->name('entrenador.historial.mes');

    Route::get('/clientes/{cliente}/historial/{anio}/{mes}/{sem}/{dia}', [HistorialController::class, 'dia'])
        ->name('entrenador.historial.dia');

    // ── Plantillas ──
    Route::get('/entrenador/plantillas', [PlantillaController::class, 'index'])
        ->name('entrenador.plantillas.index');

    Route::get('/entrenador/plantillas/crear', [PlantillaController::class, 'crear'])
        ->name('entrenador.plantillas.crear');

    Route::post('/entrenador/plantillas', [PlantillaController::class, 'guardar'])
        ->name('entrenador.plantillas.guardar');

    Route::get('/entrenador/plantillas/{plantilla}/editar', [PlantillaController::class, 'editar'])
        ->name('entrenador.plantillas.editar');

    Route::post('/entrenador/plantillas/{plantilla}', [PlantillaController::class, 'actualizar'])
        ->name('entrenador.plantillas.actualizar');

    Route::delete('/entrenador/plantillas/{plantilla}', [PlantillaController::class, 'eliminar'])
        ->name('entrenador.plantillas.eliminar');

    Route::post('/entrenador/plantillas/{plantilla}/aplicar', [PlantillaController::class, 'aplicar'])
        ->name('entrenador.plantillas.aplicar');

    // ── Perfil ──
    Route::get('/entrenador/perfil/editar', [EntrenadorController::class, 'editarPerfil'])
        ->name('entrenador.perfil.edit');

    Route::post('/entrenador/perfil/editar', [EntrenadorController::class, 'actualizarPerfil'])
        ->name('entrenador.perfil.update');

    // ── Ejercicios ──
    Route::get('/entrenador/ejercicios', [EntrenadorEjercicioController::class, 'index'])
        ->name('entrenador.ejercicios.index');

    Route::post('/entrenador/ejercicios', [EntrenadorEjercicioController::class, 'store'])
        ->name('entrenador.ejercicios.store');

    Route::put('/entrenador/ejercicios/{ejercicio}', [EntrenadorEjercicioController::class, 'update'])
        ->name('entrenador.ejercicios.update');

    Route::delete('/entrenador/ejercicios/{ejercicio}', [EntrenadorEjercicioController::class, 'destroy'])
        ->name('entrenador.ejercicios.destroy');

    // ── Progreso ──
    Route::get('/entrenador/clientes/{cliente}/progreso', [EntrenadorProgresoController::class, 'index'])
        ->name('entrenador.progreso.index');

    Route::post('/entrenador/clientes/{cliente}/progreso/medida', [EntrenadorProgresoController::class, 'storeMedida'])
        ->name('entrenador.progreso.medida.store');

    Route::post('/entrenador/clientes/{cliente}/progreso/foto', [EntrenadorProgresoController::class, 'storeFoto'])
        ->name('entrenador.progreso.foto.store');

    Route::post('/entrenador/clientes/{cliente}/progreso/nota', [EntrenadorProgresoController::class, 'storeNota'])
        ->name('entrenador.progreso.nota.store');





        // ── Ejercicios ──
Route::get('/entrenador/ejercicios/importar', [EntrenadorEjercicioController::class, 'importarForm'])
    ->name('entrenador.ejercicios.importar');

Route::post('/entrenador/ejercicios/subir-video-temporal', [EntrenadorEjercicioController::class, 'subirVideoTemporal'])
    ->name('entrenador.ejercicios.subirVideoTemporal');

Route::post('/entrenador/ejercicios/importar-lote', [EntrenadorEjercicioController::class, 'importarLote'])
    ->name('entrenador.ejercicios.importarLote');
 



    

Route::post('/rutina/{cliente}/copiar-semana', [EntrenadorRutinaController::class, 'copiarSemana'])
    ->name('entrenador.rutina.copiarSemana');

Route::post('/rutina/{cliente}/borrar-historial', [EntrenadorRutinaController::class, 'borrarHistorial'])
    ->name('entrenador.rutina.borrarHistorial');

Route::delete('/rutina/{cliente}/semana/{semana}', [EntrenadorRutinaController::class, 'vaciarSemana'])
    ->name('entrenador.rutina.vaciarSemana');


    Route::delete('/entrenador/clientes/{cliente}/progreso/1rm/{ejercicio}', [EntrenadorProgresoController::class, 'resetear1RM'])
    ->name('entrenador.progreso.resetear1rm');
 
Route::post('/entrenador/rutina/{cliente}/mover-dia', [EntrenadorRutinaController::class, 'moverDia'])
    ->name('entrenador.rutina.moverDia');
 


});



Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('admin.web')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::patch('/entrenadores/{entrenador}/estado', [DashboardController::class, 'actualizarEstado'])
            ->name('entrenadores.estado');

        Route::get('/plantilla', [PlantillaEjercicioWebController::class, 'index'])->name('plantilla.index');
        Route::get('/plantilla/crear', [PlantillaEjercicioWebController::class, 'create'])->name('plantilla.create');
        Route::post('/plantilla', [PlantillaEjercicioWebController::class, 'store'])->name('plantilla.store');
        Route::get('/plantilla/{ejercicio}/editar', [PlantillaEjercicioWebController::class, 'edit'])->name('plantilla.edit');
        Route::put('/plantilla/{ejercicio}', [PlantillaEjercicioWebController::class, 'update'])->name('plantilla.update');
        Route::delete('/plantilla/{ejercicio}', [PlantillaEjercicioWebController::class, 'destroy'])->name('plantilla.destroy');
    });
});
  