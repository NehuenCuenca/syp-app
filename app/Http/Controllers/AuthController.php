<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterUserRequest; 
use App\Http\Requests\LoginUserRequest;
use App\Http\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException; 

class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Registro de un nuevo usuario.
     *
     * @param  \App\Http\Requests\RegisterUserRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterUserRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => $request->role ?? 'Usuario',
            ]);            

            $token = $user->createToken('authToken')->plainTextToken;
            
            DB::commit();

            Log::info('User created', [
                'user_email' => $user->email,
                'ip' => $request->ip(),
            ]);
            
            return $this->createdResponse([
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'token' => $token,
            ], 'Usuario registrado exitosamente');
        } catch (\Exception $e) { 
            DB::rollBack();

            Log::error('User register failed', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
                'data' => $request->all()
            ]);

            return $this->errorResponse(
                'Ocurrió un error inesperado al registrar el usuario.', 
                [],
                [],
                500,
                config('app.debug') ? $e : null
            );
        }
    }

    /**
     * Autenticación de usuario y generación de token.
     *
     * @param  \App\Http\Requests\LoginUserRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginUserRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                Log::error('User has entered incorrect credentials', [
                    'user_email' => $request->email,
                    'ip' => $request->ip(),
                ]);

                // Si las credenciales son incorrectas, lanzamos una ValidationException manualmente
                throw ValidationException::withMessages([
                    'email' => ['Las credenciales proporcionadas son incorrectas.'],
                ]);
            }
            
            $token = $user->createToken(
                'authToken',
                ['server:read', 'server:create', 'server:update', 'server:delete', 'server:restore']
            )->plainTextToken;

            Log::info('User logged in', [
                'user_email' => $request->email,
                'ip' => $request->ip(),
            ]);

            return $this->successResponse([
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role,
                ],
                'token' => $token,
            ], 'Login exitoso.');

        } catch (ValidationException $e) {
            // Capturamos ValidationException específicamente para credenciales incorrectas
            Log::error('User has entered invalid credentials', [
                'email' => $request->email,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
            ]);

            return $this->errorResponse(
                'Error de autenticación',
                $e->errors(),
                [],
                401,
                config('app.debug') ? $e : null
            );
        } catch (\Exception $e) {
            Log::error('Unexpected error while login', [
                'email' => $request->email,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
            ]);

            return $this->errorResponse(
                'Ocurrió un error inesperado durante el login.',
                ['exception' => $e->getMessage()],
                [],
                500,
                config('app.debug') ? $e : null
            );
        }
    }

    /**
     * Cerrar sesión del usuario (revocar token).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            Log::info('User logged out', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
            ]);

            return $this->successResponse(
                null,
                'Sesión cerrada exitosamente.'
            );

        } catch (\Exception $e) {
            Log::info('Unexpected error while user try to logout', [
                'user_email' => $request->user()->email,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
            ]);

            return $this->errorResponse(
                'Ocurrió un error inesperado al cerrar la sesión.',
                ['exception' => $e->getMessage()],
                [],
                500,
                config('app.debug') ? $e : null
            );
        }
    }

    /**
     * Obtener información del usuario autenticado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        Log::info('User information retrieved', [
            'user_email' => $request->user()->email,
            'ip' => $request->ip(),
        ]);

        return $this->successResponse([
            'user' => [
                'id_user' => $request->user()->id,
                'username' => $request->user()->username,
                'email' => $request->user()->email,
                'phone' => $request->user()->phone,
                'role' => $request->user()->role,
            ]
        ], 'Información del usuario obtenida exitosamente');
    }
}