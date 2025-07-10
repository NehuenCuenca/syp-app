<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
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
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    protected function unauthenticated( $request, AuthenticationException $exception)
    {
        // Verifica si la solicitud es una API o si espera una respuesta JSON
        // Laravel automáticamente revisa el encabezado 'Accept' o si es una solicitud AJAX
        if ($request->expectsJson()) {
            return response()->json(['message' => 'No autenticado. Por favor, inicie sesión.'], 401);
        }

        // Si no es una API (ej. una solicitud web), redirige a la ruta 'login' (comportamiento por defecto)
        return redirect()->guest($exception->redirectTo() ?? route('login'));
    }
}
