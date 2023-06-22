<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\NuevaJustificacionRecibida;
use App\Models\Grupo;
use App\Models\Justificacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
        $request->validate([
            'param' => ['nullable', 'string'],
            'fecha_inicio' => ['nullable', 'date', 'date_format:Y-m-d'],
            'fecha_fin' => ['nullable', 'date','date_format:Y-m-d'],
        ]);

        $token = $request->bearerToken();
		$authUser = PersonalAccessToken::findToken($token)->tokenable;
        $sql = Justificacion::query();
        $param = $request->query('param');
        $fechaInicio = $request->query('fecha_inicio');
        $fechaFin = $request->query('fecha_fin');

        if($param) {
            $sql->where('identificador', 'like', "%$param%")->orWhere('id', $param);
        }

        if(($fechaInicio && !$fechaFin) || (!$fechaInicio && $fechaFin)) {
            return response()->json([
                'success' => false,
                'message' => 'Tienes erroes de validacion',
                'helper_data' => [
                    'errors' => ['La fecha de inicio o la fecha de fin es requerida.']
                ]
            ], 400);
        }

        $format = Carbon::now();

        if($fechaInicio && $fechaFin) {
            $sql->where([
                ['fecha_inicio', '>=', $fechaInicio],
                ['fecha_fin', '<=', $fechaFin]
            ]);
        }

        if (
            $authUser->roles()->where('role_name', 'profesor')->exists()
        ) {
            $sql->where('profesor_id', $authUser->id);
            $sql->with('alumno');
            return $sql->get();
        }

        if (
            $authUser->roles()->where('role_name', 'alumno')->exists()
        ) {
            $sql->where('alumno_id', $authUser->id);
            $sql->with('profesor');
            return $sql->get();
        }

        return $sql->with(['profesor', 'alumno'])->get();
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
        $profesorId = $request->profesor_id;

        $profesor = User::whereHas('roles', function ($query) use($profesorId) {
            $query->where([
                ['role_name', 'profesor'],
                ['users.id', $profesorId]
            ]);
        })->first();

        if(!$profesor) {
            abort(404);
        }

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
        ]));

        $justificacion->profesor_id = $request->profesor_id;
        $justificacion->alumno_id = $authUser->id;

        if($justificacion->save()) {
            $mailData = [];
            $mailData['profesor'] = $profesor;
            $mailData['alumno'] = $authUser;
            $mailData['justificacion'] = $justificacion;
            $mailData['grupo'] = $grupoAlumnoActual;

            Mail::to($profesor->email)->send(new NuevaJustificacionRecibida($mailData));

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
