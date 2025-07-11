<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterUserRequest extends FormRequest
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

            // Usamos Password::defaults() para incluir reglas de seguridad por defecto de Laravel (ej. al menos una mayúscula, un número, un símbolo).
            // 'confirmed' asegura que exista un campo 'password_confirmation' y que coincida.
            'password' => ['required', 'string', 'confirmed', Password::defaults()],

            // 'role': opcional (nullable), string, y su valor debe ser 'Admin' o 'Usuario'.
            'role' => ['nullable', 'string', 'in:Admin,user'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Mensajes para 'username'
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.string' => 'El nombre de usuario debe ser una cadena de texto.',
            'username.max' => 'El nombre de usuario no debe exceder los 50 caracteres.',
            'username.unique' => 'Este nombre de usuario ya está en uso.',

            // Mensajes para 'email'
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.string' => 'El correo electrónico debe ser una cadena de texto.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'email.max' => 'El correo electrónico no debe exceder los 100 caracteres.',
            'email.unique' => 'Este correo electrónico ya está registrado.',

            // Mensajes para 'password'
            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',

            // Mensajes para 'role'
            'role.string' => 'El rol debe ser una cadena de texto.',
            'role.in' => 'El rol seleccionado no es válido. Debe ser "Admin" o "Usuario".',
        ];
    }
}