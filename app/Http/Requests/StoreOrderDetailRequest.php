<?php

namespace App\Http\Requests;

use App\Models\Order;
use App\Models\OrderDetail;

class StoreOrderDetailRequest extends BaseApiRequest
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
            'id_order' => [
                'required',
                'integer',
                'exists:orders,id'
            ],
            'order_detail' => [
                'required',
                'array'
            ],
            'order_detail.id_product' => [
                'required',
                'integer',
                'exists:products,id'
            ],
            'order_detail.quantity' => [
                'required',
                'integer',
                'min:1',
                'max:999999'
            ],
            'order_detail.unit_price_at_order' => [
                'required',
                'numeric',
                'min:0',
                'max:999999'
            ],
            'order_detail.discount_percentage_by_unit' => [
                'required',
                'numeric',
                'min:0',
            ]
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
           
            // Validación personalizada: verificar que no exista ya un detalle con el mismo producto
            if ($this->filled('id_order') && $this->filled('order_detail.id_product')) {
                $existingDetail = OrderDetail::where('id_order', $this->id_order)
                    ->where('id_product', $this->input('order_detail.id_product'))
                    ->first();
                    
                if ($existingDetail) {
                    $validator->errors()->add('order_detail.id_product', 'Ya existe un detalle con este producto en el pedido. Debe editarlo en lugar de crear uno nuevo.');
                }
            }
        });
    }
}