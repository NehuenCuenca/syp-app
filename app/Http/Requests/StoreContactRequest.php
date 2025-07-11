<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactRequest extends FormRequest
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
            'company_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:contacts,email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'contact_type' => ['required', Rule::in(['Cliente', 'Proveedor', 'Empleado', 'Otro'])],
            'registered_at' => 'nullable|date',
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
            'company_name.required' => 'El nombre de la empresa es obligatorio.',
            'company_name.string' => 'El nombre de la empresa debe ser un texto.',
            'company_name.max' => 'El nombre de la empresa no puede exceder los 255 caracteres.',
            
            'contact_name.required' => 'El nombre del contacto es obligatorio.',
            'contact_name.string' => 'El nombre del contacto debe ser un texto.',
            'contact_name.max' => 'El nombre del contacto no puede exceder los 255 caracteres.',
            
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'email.unique' => 'Ya existe un contacto con este correo electrónico.',
            'email.max' => 'El correo electrónico no puede exceder los 255 caracteres.',
            
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.string' => 'El teléfono debe ser un texto.',
            'phone.max' => 'El teléfono no puede exceder los 255 caracteres.',
            
            'address.required' => 'La dirección es obligatoria.',
            'address.string' => 'La dirección debe ser un texto.',
            
            'contact_type.required' => 'El tipo de contacto es obligatorio.',
            'contact_type.in' => 'El tipo de contacto debe ser: Cliente, Proveedor, Empleado u Otro.',
            
            'registered_at.required' => 'La fecha de registro es obligatoria.',
            'registered_at.date' => 'La fecha de registro debe ser una fecha válida.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'company_name' => 'nombre de empresa',
            'contact_name' => 'nombre del contacto',
            'email' => 'correo electrónico',
            'phone' => 'teléfono',
            'address' => 'dirección',
            'contact_type' => 'tipo de contacto',
            'registered_at' => 'fecha de registro',
        ];
    }
}