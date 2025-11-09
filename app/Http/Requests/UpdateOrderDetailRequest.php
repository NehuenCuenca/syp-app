<?php

namespace App\Http\Requests;

use App\Models\OrderDetail;

class UpdateOrderDetailRequest extends BaseApiRequest
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
            'id_product' => ['missing'],
            'quantity' => [
                'integer',
                'min:1',
                'max:999999'
            ],
            'unit_price_at_order' => [
                'numeric',
                'min:0',
                'max:999999.99'
            ],
            'discount_percentage_by_unit' => [
                'numeric',
                'min:0',
                'max:1'
            ]
        ];
    }
}