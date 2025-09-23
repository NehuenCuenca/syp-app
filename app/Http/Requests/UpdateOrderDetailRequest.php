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
            'id_product' => [
                'integer',
                'exists:products,id'
            ],
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

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Obtener el OrderDetail actual desde la ruta
            $orderDetail = $this->route('order_detail');
            
            if ($orderDetail) {
                // Validación personalizada: verificar que el pedido esté en estado 'Pendiente'
                if ($orderDetail->order->order_status !== 'Pendiente') {
                    $validator->errors()->add('order', 'Solo se pueden editar detalles de pedidos en estado Pendiente.');
                }
                
                // Validación personalizada: verificar que no exista otro detalle con el mismo producto (solo si cambió el producto)
                if ($this->filled('id_product')) {
                    $newProductId = $this->input('id_product');
                    
                    if ($orderDetail->id_product != $newProductId) {
                        $existingDetail = OrderDetail::where('id_order', $orderDetail->id_order)
                            ->where('id_product', $newProductId)
                            ->where('id', '!=', $orderDetail->id_order_detail)
                            ->first();
                            
                        if ($existingDetail) {
                            $validator->errors()->add('id_product', 'Ya existe otro detalle con este producto en el pedido.');
                        }
                    }
                }
            }
        });
    }
}