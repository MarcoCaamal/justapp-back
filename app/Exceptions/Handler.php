<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function(ValidationException $e, Request $request) {
            if($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'messages' => 'There are validation errors',
                    'errors' => $e->errors()
                ], 400);
            }
        });

        $this->renderable(function(AuthenticationException $ex, Request $request) {
            if($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not Authenticated',
                    'statusCode' => 401
                ], 401);
            }
        });

        $this->renderable(function(AccessDeniedHttpException $e, Request $request) {
            if($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden',
                    'statusCode' => 403
                ], 403);
            }
        });

        $this->renderable(function(NotFoundHttpException $e,Request $request){
            if($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not Found',
                    'statusCode' => 404
                ], 404);
            }
        });

        $this->renderable(function(MethodNotAllowedHttpException $e, Request $request) {
            if($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Method Not Allowed',
                    'statusCode' => 405
                ], 405);
            }
        });

        $this->renderable(function(Throwable $th, Request $request) {
            if($request->is('api/*')) {
                Log::error($th);
                return response()->json([
                    'success' => false,
                    'message' => 'Error interno en el servidor',
                    'statusCode' => 500
                ]);
            }
        });
    }
}
