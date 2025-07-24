<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateStockMovementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'quantity_moved' => [
                'required',
                'integer',
                'min:1'
            ],
            'movement_type' => [
                'required',
                'string',
                'in:Compra_Entrante,Venta_Saliente,Ajuste_Positivo,Ajuste_Negativo,Devolucion_Cliente,Devolucion_Proveedor'
            ],
            'external_reference' => [
                'nullable',
                'string',
                'max:100'
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000'
            ]
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
            'quantity_moved' => 'cantidad movida',
            'movement_type' => 'tipo de movimiento',
            'external_reference' => 'referencia externa',
            'notes' => 'notas'
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
            'quantity_moved.required' => 'La :attribute es obligatoria.',
            'quantity_moved.integer' => 'La :attribute debe ser un número entero.',
            'quantity_moved.min' => 'La :attribute debe ser mayor a 0.',
            
            'movement_type.required' => 'El :attribute es obligatorio.',
            'movement_type.string' => 'El :attribute debe ser una cadena de texto.',
            'movement_type.in' => 'El :attribute seleccionado no es válido. Los valores permitidos son: Compra_Entrante, Venta_Saliente, Ajuste_Positivo, Ajuste_Negativo, Devolucion_Cliente, Devolucion_Proveedor.',
            
            'external_reference.string' => 'La :attribute debe ser una cadena de texto.',
            'external_reference.max' => 'La :attribute no puede exceder los :max caracteres.',
            
            'notes.string' => 'Las :attribute deben ser una cadena de texto.',
            'notes.max' => 'Las :attribute no pueden exceder los :max caracteres.'
        ];
    }

    /**
     * Transform the input data before validation.
     */
    protected function prepareForValidation(): void
    {
        // Limpiar campos opcionales vacíos
        if ($this->external_reference === '') {
            $this->merge(['external_reference' => null]);
        }

        if ($this->notes === '') {
            $this->merge(['notes' => null]);
        }
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Los datos enviados no son válidos.',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}