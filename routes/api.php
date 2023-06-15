<?php

use App\Http\Controllers\API\Auth\APICuentaController;
use App\Http\Controllers\API\GrupoController;
use App\Http\Controllers\API\JustificacionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/cuentas/login', [APICuentaController::class, 'login']);
Route::post('/cuentas/register/profesor', [APICuentaController::class, 'registerProfesor']);
Route::post('/cuentas/register/alumno', [APICuentaController::class, 'registerAlumno']);

Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('abilities:administrador')->group(function () {
        Route::controller(GrupoController::class)->group(function () {
            Route::post('/grupos', 'store');
            Route::put('/grupos/{id}', 'update');
            route::delete('/grupos/{id}', 'destroy');
        });
    });

    Route::middleware('abilities:alumno')->group(function () {
        Route::controller(JustificacionController::class)->group(function () {
            Route::post('/justificaciones', 'store');
            Route::put('/justificaciones/{id}', 'update');
            Route::delete('/justificaciones/{id}', 'destroy');
        });
    });

    Route::controller(GrupoController::class)->group(function () {
        Route::get('/grupos', 'index');
        Route::get('/grupos/{id}', 'show');
    });

    Route::controller(JustificacionController::class)->group(function () {
        Route::get('/justificaciones', 'index');
        Route::get('/justificaciones/{id}', 'show');
    });
});
