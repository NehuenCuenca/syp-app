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
            throw new Exception("Error Processing Request", 1);
            
            DB::commit();

            Log::info('User created', [
                'id_user' => $user->id,
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
                // Si las credenciales son incorrectas, lanzamos una ValidationException manualmente
                throw ValidationException::withMessages([
                    'email' => ['Las credenciales proporcionadas son incorrectas.'],
                ]);
            }
            
            $token = $user->createToken('authToken')->plainTextToken;

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
            return $this->errorResponse(
                'Error de autenticación',
                $e->errors(),
                [],
                401,
                config('app.debug') ? $e : null
            );
        } catch (\Exception $e) {
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

            return $this->successResponse(
                null,
                'Sesión cerrada exitosamente.'
            );

        } catch (\Exception $e) {
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