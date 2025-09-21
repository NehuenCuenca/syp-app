<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterUserRequest extends BaseApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],

            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:50'],

            // Usamos Password::defaults() para incluir reglas de seguridad por defecto de Laravel (ej. al menos una mayúscula, un número, un símbolo).
            // 'confirmed' asegura que exista un campo 'password_confirmation' y que coincida.
            'password' => ['required', 'string', 'confirmed', Password::defaults()],

            // 'role': opcional (nullable), string, y su valor debe ser 'Admin' o 'Usuario'.
            'role' => ['nullable', 'string', 'in:Admin,Usuario'],
        ];
    }
}