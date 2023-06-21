<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\NuevaJustificacionRecibida;
use App\Models\Grupo;
use App\Models\Justificacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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
            'email_docente' => ['required', 'email'],
        ]);

        $token = $request->bearerToken();
		$authUser = PersonalAccessToken::findToken($token)->tokenable;

        $grupoAlumnoActual = Grupo::whereHas('users', function ($query) use($authUser) {
            $query->where([
                ['users.id', $authUser->id],
                ['grupo_user.is_active', true]
            ]);
        })->first();

        $justificacion = new Justificacion();
        $justificacion->fill($request->only([
            'identificador',
            'fecha_inicio',
            'fecha_fin',
            'motivo',
            'email_docente'
        ]));

        $justificacion->profesor_id = $request->profesor_id;
        $justificacion->alumno_id = $authUser->id;

        if($justificacion->save()) {
            $mailData = [];
            $mailData['alumno'] = $authUser;
            $mailData['justificacion'] = $justificacion;
            $mailData['grupo'] = $grupoAlumnoActual;

            Mail::to($request->email_docente)->send(new NuevaJustificacionRecibida($mailData));

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
