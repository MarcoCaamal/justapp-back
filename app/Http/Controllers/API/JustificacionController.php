<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Justificacion;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class JustificacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $token = $request->bearerToken();
		$authUser = PersonalAccessToken::findToken($token)->tokenable;
        $sql = Justificacion::query();

        if (
            $authUser->roles()->where('role_name', 'profesor')->exists()
        ) {
            $sql->where('profesor_id', $authUser->id);
            $sql->with('alumno');
        }

        if (
            $authUser->roles()->where('role_name', 'alumno')->exists()
        ) {
            $sql->where('alumno_id', $authUser->id);
            $sql->with('profesor');
        }

        return $sql->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'identificador' => ['required', 'string'],
            'fecha_inicio' => ['required', 'date', 'date_format:Y-m-d'],
            'fecha_fin' => ['required', 'date', 'date_format:Y-m-d'],
            'motivo' => ['required', 'string'],
            'profesor_id' => ['required', 'numeric'],
        ]);

        $token = $request->bearerToken();
		$authUser = PersonalAccessToken::findToken($token)->tokenable;

        $justificacion = new Justificacion();
        $justificacion->fill($request->only([
            'identificador',
            'fecha_inicio',
            'fecha_fin',
            'motivo',
        ]));

        $justificacion->profesor_id = $request->profesor_id;
        $justificacion->alumno_id = $authUser->id;

        if($justificacion->save()) {
            return response()->json([
                'success' => true,
                'message' => '!Justificacion Creado!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => '!Error al crear!'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $id)
    {
        $token = $request->bearerToken();
		$authUser = PersonalAccessToken::findToken($token)->tokenable;
        $sql = Justificacion::query();

        if (
            $authUser->roles()->where('role_name', 'profesor')->exists()
        ) {
            $sql->where('profesor_id', $authUser->id);
        }

        if (
            $authUser->roles()->where('role_name', 'alumno')->exists()
        ) {
            $sql->where('alumno_id', $authUser->id);
        }

        return $sql->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'identificador' => ['required', 'string'],
            'fecha_inicio' => ['required', 'date', 'date_format:Y-m-d'],
            'fecha_fin' => ['required', 'date', 'date_format:Y-m-d'],
            'motivo' => ['required', 'string'],
        ]);

        $token = $request->bearerToken();
		$authUser = PersonalAccessToken::findToken($token)->tokenable;

        $justificacion = Justificacion::find($id);

        if(!$justificacion) {
            abort(404);
        }

        if($authUser->id !== $justificacion->alumno_id) {
            throw new AccessDeniedHttpException;
        }

        $justificacion->fill($request->only([
            'identificador',
            'fecha_inicio',
            'fecha_fin',
            'motivo',
        ]));

        if($justificacion->save()) {
            return response()->json([
                'success' => true,
                'message' => '!Justificacion Actulizada!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => '!Error al crear!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $id)
    {
        $justificacion = Justificacion::find($id);
        $token = $request->bearerToken();
		$authUser = PersonalAccessToken::findToken($token)->tokenable;

        if(!$justificacion) {
            abort(404);
        }

        if($authUser->id !== $justificacion->alumno_id) {
            throw new AccessDeniedHttpException;
        }

        if($justificacion->delete()) {
            return response()->json([
                'success' => true,
                'message' => '!Justificacion Eliminada!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => '!Error al eliminar!'
        ]);
    }
}