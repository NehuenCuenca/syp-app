<?php
namespace App\Http\Requests;

use App\Http\Traits\ApiResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Clase base para todos los Form Requests de la API
 * Maneja automáticamente las respuestas de validación fallida
 */
class BaseApiRequest extends FormRequest
{
    use ApiResponseTrait;

    /**
     * Maneja una instancia de validación fallida.
     * 
     * Este método se ejecuta automáticamente cuando las validaciones fallan
     * En lugar del comportamiento por defecto de Laravel, usa nuestro formato estándar
     */
    protected function failedValidation(Validator $validator)
    {
        // Lanzar excepción con nuestra respuesta JSON estandarizada
        throw new HttpResponseException(
            $this->validationErrorResponse(
                $validator->errors()->toArray(),
                'Los datos proporcionados no son válidos'
            )
        );
    }

    /**
     * Obtener mensajes de validación personalizados para las reglas definidas
     */
    public function messages()
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'email' => 'El campo :attribute debe ser una dirección de email válida.',
            'unique' => 'El :attribute ya existe en el sistema.',
            'min' => 'El campo :attribute debe tener al menos :min caracteres.',
            'max' => 'El campo :attribute no debe tener más de :max caracteres.',
            'string' => 'El campo :attribute debe ser una cadena de texto.',
            'confirmed' => 'La confirmación de :attribute no coincide.',
            'in' => 'El :attribute es inválido. Debe ser uno de los siguientes valores: :values',
            'date' => 'El :attribute debe ser una fecha válida.',
        ];
    }

    /**
     * Obtener nombres personalizados para los atributos de validación
     */
    public function attributes()
    {
        return [
            'username' => 'nombre de usuario',
            'email' => 'correo electronico',
            'phone' => 'telefono',
            'password' => 'contraseña',
            'role' => 'rol',
            'company_name' => 'nombre de empresa',
            'contact_name' => 'nombre del contacto',
            'address' => 'dirección',
            'contact_type' => 'tipo de contacto',
            'registered_at' => 'fecha de registro',
        ];
    }
}