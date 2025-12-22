<?php

namespace App\Http\Requests;

use App\Models\StockMovement;
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
        return auth()->user()->tokenCan('server:update', StockMovement::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_order' => ['missing'],
            'id_order_detail' => ['missing'],
            'quantity_moved' => [
                'required',
                'integer',
                'min:1'
            ],
            'id_movement_type' => [
                'required',
                'exists:movement_types,id'
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
}