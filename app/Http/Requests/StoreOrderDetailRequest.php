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
        return auth()->user()->tokenCan('server:create', OrderDetail::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_id' => [
                'required',
                'integer',
                'exists:orders,id'
            ],
            'order_detail' => [
                'required',
                'array'
            ],
            'order_detail.product_id' => [
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
            'order_detail.unit_price' => [
                'required',
                'numeric',
                'min:0',
                'max:999999'
            ],
            'order_detail.percentage_applied' => [
                'numeric',
                'min:0',
            ],
            'order_detail.profit_percentage' => [
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
            if ($this->filled('order_id') && $this->filled('order_detail.product_id')) {
                $existingDetail = OrderDetail::where('order_id', $this->order_id)
                    ->where('product_id', $this->input('order_detail.product_id'))
                    ->first();
                    
                if ($existingDetail) {
                    $validator->errors()->add('order_detail.product_id', 'Ya existe un detalle con este producto en el pedido. Debe editarlo en lugar de crear uno nuevo.');
                }
            }

            // Validación personalizada: verificar que profit_percentage no este presente si el pedido es una venta
            $order = Order::find($this->order_id);

            if ($this->filled('order_id') && $this->filled('order_detail.profit_percentage')) {
                if ($order && $order->getIsSaleAttribute()) {
                    $validator->errors()->add('order_detail.profit_percentage', 'No se puede agregar un porcentaje de ganancia si el pedido es una venta.');
                }
            }

            if ($this->filled('order_id') && $this->filled('order_detail.percentage_applied')) {
                 if ($order && $order->getIsPurchaseAttribute()) {
                    $validator->errors()->add('order_detail.percentage_applied', 'No se puede agregar un porcentaje de descuento si el pedido es una compra.');
                }
            }
        });
    }
}