<?php

use App\Http\Controllers\API\Auth\APICuentaController;
use App\Http\Controllers\API\GrupoController;
use App\Http\Controllers\API\JustificacionController;
use App\Models\Grupo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\PersonalAccessToken;

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


Route::get('/grupos', [GrupoController::class, 'indexWithoutAuth']);

Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('abilities:orientador')->group(function () {
        Route::post('/cuentas/register/alumno', [APICuentaController::class, 'registerAlumno']);

        Route::controller(GrupoController::class)->group(function () {
            Route::post('/grupos', 'store');
            Route::put('/grupos/{id}', 'update');
            route::delete('/grupos/{id}', 'destroy');
        });

        Route::get('/usuarios', function (Request $request) {
            $request->validate([
                'param' => ['nullable', 'string']
            ]);
            $sql = User::query();
            $param = $request->query('param');

            if($param) {
                $sql->where('nombre', 'like', "%$param%")
                    ->orWhere('apellido_materno', 'like', "%$param%")
                    ->orWhere('apellido_paterno', 'like', "%$param%")
                    ->orWhere('numero_control', 'like', "%$param%")
                    ->orWhere('email', 'like', "%$param%");
            }

            $sql->whereHas('roles', function ($query) {
                $query->where('role_name', '<>', 'administrador');
            });

            return $sql->with(['roles', 'grupos'])->get();
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
        Route::get('/grupos/with-users', 'indexWithUsers');
        Route::get('/grupos/{id}', 'show');
    });

    Route::controller(JustificacionController::class)->group(function () {
        Route::get('/justificaciones', 'index');
        Route::get('/justificaciones/{id}', 'show');
    });
});
