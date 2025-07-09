<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterUserRequest; 
use App\Http\Requests\LoginUserRequest;    
use Illuminate\Validation\ValidationException; 

class AuthController extends Controller
{
    /**
     * Registro de un nuevo usuario.
     *
     * @param  \App\Http\Requests\RegisterUserRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterUserRequest $request)
    {
        try {
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role ?? 'user',
            ]);

            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'message' => 'Usuario registrado exitosamente.',
                'user' => [
                    'id_user' => $user->id_user,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'token' => $token,
            ], 201);

        } catch (\Exception $e) { 
            return response()->json([
                'message' => 'Ocurrió un error inesperado al registrar el usuario.',
                'error' => $e->getMessage(),
            ], 500);
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

            return response()->json([
                'message' => 'Login exitoso.',
                'user' => [
                    'id_user' => $user->id_user,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'token' => $token,
            ], 200);

        } catch (ValidationException $e) { // Capturamos ValidationException específicamente para credenciales incorrectas
            return response()->json([
                'message' => 'Error de autenticación',
                'errors' => $e->errors(),
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error inesperado durante el login.',
                'error' => $e->getMessage(),
            ], 500);
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

            return response()->json([
                'message' => 'Sesión cerrada exitosamente.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'No se pudo cerrar la sesión.',
                'error' => $e->getMessage(),
            ], 500);
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
        return response()->json([
            'user' => [
                'id_user' => $request->user()->id_user,
                'username' => $request->user()->username,
                'email' => $request->user()->email,
                'role' => $request->user()->role,
            ]
        ], 200);
    }
}