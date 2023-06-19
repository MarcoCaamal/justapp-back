<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\Grupo;
use App\Models\Role;
use App\Models\User;
use DateTimeZone;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class APICuentaController extends Controller
{
    public function login(Request $request) {
        $request->validate([
            'email' => ['email', 'required'],
            'password' => ['required', 'string']
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No existe el usuario con ese correo en nuestros registros',
            ], 404);
        }

        if(!$user->roles()) {
            return response()->json([
                'success' => false,
                'message' => '¡Login Incorrecto!'
            ]);
        }

        if(!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Las credenciales ingresadas son incorrectas.'
            ], 400);
        }

        $tokenExpirationDate = Carbon::now()->addMinutes(60);

        if($user->roles()->where('role_name', 'profesor')->exists()) {
            $token = $user->createToken('token', ['profesor'], $tokenExpirationDate);
            return response()->json([
                'token' => $token->plainTextToken,
                'expires_at' => $tokenExpirationDate->toIso8601String(),
                'success' => true,
                'message' => '¡Login Correcto!',
                'helper_data' => [
                    'user_id' => $user->id,
                    'nombre' => $user->nombre,
                    'apellido_paterno' => $user->apellido_paterno,
                    'apellido_materno' => $user->apellido_materno,
                    'roles' => $user->roles
                ]
            ]);
        }

        if($user->roles()->where('role_name', 'alumno')->exists()) {
            $token = $user->createToken('token', ['alumno'], $tokenExpirationDate);
            return response()->json([
                'token' => $token->plainTextToken,
                'expires_at' => $tokenExpirationDate->toIso8601String(),
                'success' => true,
                'message' => '¡Login Correcto!',
                'helper_data' => [
                    'user_id' => $user->id,
                    'nombre' => $user->nombre,
                    'apellido_paterno' => $user->apellido_paterno,
                    'apellido_materno' => $user->apellido_materno,
                    'roles' => $user->roles
                ]
            ]);
        }

        if($user->roles()->where('role_name', 'administrador')->exists()) {
            $token = $user->createToken('token', ['administrador'], $tokenExpirationDate);
            return response()->json([
                'token' => $token->plainTextToken,
                'expires_at' => $tokenExpirationDate->toIso8601String(),
                'success' => true,
                'message' => '¡Login Correcto!',
                'helper_data' => [
                    'user_id' => $user->id,
                    'nombre' => $user->nombre,
                    'apellido_paterno' => $user->apellido_paterno,
                    'apellido_materno' => $user->apellido_materno,
                    'roles' => $user->roles
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => '¡Login Incorrecto!'
        ]);
    }

    public function registerProfesor(Request $request)
    {
        $request->validate([
            'nombre' => ['required', 'string'],
            'apellido_paterno' => ['required', 'string'],
            'apellido_materno' => ['required', 'string'],
            'email' => ['email', 'required'],
            'password' => ['required', 'string'],
            'grupos' => ['required', 'array'],
            'grupos.*' => ['numeric', 'exists:grupos,id']
        ]);

        $profesor = new User();

        $profesor->fill($request->only([
            'nombre',
            'apellido_paterno',
            'apellido_materno',
            'email',
            'password',
        ]));

        if($profesor->save()) {
            $roleProfesor = Role::where('role_name', 'profesor')->first();
            $profesor->roles()->attach($roleProfesor->getKey(), ['is_active' => true]);
            $profesor->grupos()->attach($request->grupos, ['is_active' => true]);

            // TODO:Descomentar el envio de email
            // event(new Registered($profesor));
            return response()->json([
                'success' => true,
                'message' => '¡Profesor registrado!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => '¡Ha ocurrido un error! intentelo más tarde.'
        ]);
    }

    public function registerAlumno(Request $request)
    {
        $request->validate([
            'nombre' => ['required', 'string'],
            'apellido_paterno' => ['required', 'string'],
            'apellido_materno' => ['required', 'string'],
            'numero_control' => ['required', 'numeric', 'digits:14'],
            'email' => ['email', 'required'],
            'password' => ['required', 'string'],
            'grupo_id' => ['required', 'numeric'],
        ]);

        $grupo = Grupo::find($request->grupo_id);

        if(!$grupo) {
            abort(404);
        }

        $alumno = new User();
        $alumno->fill($request->only([
            'nombre',
            'apellido_paterno',
            'apellido_materno',
            'numero_control',
            'email',
            'password',
        ]));

        if($alumno->save()) {
            $roleAlumno = Role::where('role_name', 'alumno')->first();
            $alumno->roles()->attach($roleAlumno->id, ['is_active' => true]);
            $alumno->grupos()->attach($grupo->id, ['is_active' => true]);
            // TODO:Descomentar el envio de email
            // event(new Registered($alumno));
            return response()->json([
                'success' => true,
                'message' => 'Alumno registrado!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => '¡Ha ocurrido un error! intentelo más tarde.'
        ]);
    }
}
