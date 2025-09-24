<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateStockMovementRequest extends BaseApiRequest
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
                'in:Compra,Venta,Ajuste Positivo,Ajuste Negativo,Devolucion Cliente,Devolucion Proveedor'
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
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->validationErrorResponse(
                $validator->errors()->toArray(),
                'Los datos proporcionados no son válidos'
            )
        );
    }
}