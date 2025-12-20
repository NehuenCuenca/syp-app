<?php

namespace App\Http\Requests;

use App\Models\Order;
use App\Models\OrderDetail;

class UpdateOrderDetailRequest extends BaseApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->tokenCan('server:update', OrderDetail::class);
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
            'unit_price' => [
                'numeric',
                'min:0',
                'max:999999'
            ],
            'percentage_applied' => [
                'numeric',
                'min:0',
            ],
            'profit_percentage' => [
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
            // Validación personalizada: verificar que profit_percentage no este presente si el pedido es una venta
            $order = $this->order_detail->order;

            if ($this->filled('profit_percentage')) {
                if ($order && $order->getIsSaleAttribute()) {
                    $validator->errors()->add('order_detail.profit_percentage', 'No se puede agregar un porcentaje de ganancia si el pedido es una venta.');
                }
            }

            if ($this->filled('percentage_applied')) {
                 if ($order && $order->getIsPurchaseAttribute()) {
                    $validator->errors()->add('order_detail.percentage_applied', 'No se puede agregar un porcentaje de descuento si el pedido es una compra.');
                }
            }
        });
    }
}