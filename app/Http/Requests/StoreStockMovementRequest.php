<?php

namespace App\Http\Requests;

use App\Models\StockMovement;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreStockMovementRequest extends BaseApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->tokenCan('server:create', StockMovement::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_order' => ['exists:orders,id'],
            'id_order_detail' => ['exists:order_details,id'],
            'product_id' => [
                'required',
                'integer',
                'exists:products,id'
            ],
            'quantity_moved' => [
                'required',
                'integer',
                'min:1'
            ],
            'movement_type_id' => [
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
        if ($this->notes === '') {
            $this->merge(['notes' => null]);
        }
    }
}