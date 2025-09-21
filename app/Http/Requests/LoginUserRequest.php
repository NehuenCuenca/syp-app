<?php

namespace App\Http\Requests;

class LoginUserRequest extends BaseApiRequest
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
            // 'email': requerido, string, y con formato de email válido.
            'email' => ['required', 'string', 'email'],

            // 'password': requerido y string. No necesitamos una longitud mínima aquí,
            // ya que solo estamos verificando si las credenciales son correctas.
            'password' => ['required', 'string'],
        ];
    }
}