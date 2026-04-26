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

 use App\Http\Controllers\RutinaPdfController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('principal');
});

 
Route::get('/register', [RegisterController::class, 'index'] )->name('register');
Route::post('/entrenadores/registro', [EntrenadorController::class, 'store'])
    ->name('entrenadores.register');

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('logout', [LogoutController::class, 'store'])->name('logout');

Route::get('/muro', [PostController::class, 'index'])->name('posts.index');

 
/*
Route::post('/guardar-rutina', [recibirguardarController::class, 'guardarRutina'])->name('guardarRutina');
*/
 
 
Route::post('/rutinas/guardar', [recibirguardarController::class, 'guardarRutina'])

    ->name('guardarRutina');
/*
    Route::get('/rutina', [recibirguardarController::class, 'crearRutina'])
    ->name('rutina');
*/

    Route::get('/rutina', [CrearRutinaController::class, 'crearRutina'])->name('crearRutina');


Route::get('entrenador/clientes/{cliente}/rutina/{semana}/{dia}/pdf',
    [RutinaPdfController::class, 'generar']
)->name('entrenador.rutina.pdf');
 

// Dashboard entrenador
Route::get('/entrenador/dashboard', function () {
    return view('entrenador.dashboard');
})->name('entrenador.dashboard');

// Clientes entrenador
Route::get('/entrenador/clientes', function () {
    return view('entrenador.clientes');
})->name('entrenador.clientes');

// Logout entrenador (temporal)
Route::post('/entrenador/logout', function () {
    return redirect('/');
})->name('entrenador.logout');



Route::get('/entrenador/clientes', [EntrenadorClienteController::class, 'index'])
    ->name('entrenador.clientes');




    Route::get('/Sesion', function () {
    return view('rutina.menu');
});



Route::get(
    '/entrenador/rutina/{cliente}',
    [EntrenadorRutinaController::class, 'menu']
)->name('entrenador.rutina.menu');

Route::get(
    '/entrenador/rutina/{cliente}/{semana}/{dia}',
    [EntrenadorRutinaController::class, 'editar']
)->name('entrenador.rutina.editar');


Route::post(
    '/entrenador/rutina/{cliente}/{semana}/{dia}',
    [EntrenadorRutinaController::class, 'guardar']
)->name('entrenador.rutina.guardar');