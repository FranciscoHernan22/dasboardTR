<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RutinaApiController;
use App\Http\Controllers\Api\PlanApiController;
use App\Http\Controllers\Api\AuthApiController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/rutina/{cliente}/{semana}/{dia}', 
    [RutinaApiController::class, 'ver']);

    Route::patch('rutina/{cliente}/{semana}/{dia}/pesos', [RutinaApiController::class, 'guardarPesos']);



 // Plan
Route::get('/cliente/{id}/semana-actual', [PlanApiController::class, 'semanaActual']);
Route::post('/cliente/{id}/semana/{semana}/dia/{dia}/completar', [PlanApiController::class, 'completarDia']);

// Rutina
Route::get('/cliente/{id}/semana/{semana}/dia/{dia}', [RutinaApiController::class, 'ver']);

Route::post('/login', [AuthApiController::class, 'login']);
Route::post('/logout', [AuthApiController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/cliente/{id}/semanas', [PlanApiController::class, 'todasLasSemanas']);