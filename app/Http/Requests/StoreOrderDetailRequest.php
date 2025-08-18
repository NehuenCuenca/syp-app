<?php

namespace App\Http\Requests;

use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderDetailRequest extends FormRequest
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
                'max:999999.99'
            ],
            'order_detail.discount_percentage_by_unit' => [
                'required',
                'numeric',
                'min:0',
                'max:1'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Validaciones para id_order
            'id_order.required' => 'El ID del pedido es obligatorio.',
            'id_order.integer' => 'El ID del pedido debe ser un número entero.',
            'id_order.exists' => 'El pedido especificado no existe.',
            
            // Validaciones para order_detail
            'order_detail.required' => 'Los detalles del pedido son obligatorios.',
            'order_detail.array' => 'Los detalles del pedido deben ser un objeto válido.',
            
            // Validaciones para id_product
            'order_detail.id_product.required' => 'El ID del producto es obligatorio.',
            'order_detail.id_product.integer' => 'El ID del producto debe ser un número entero.',
            'order_detail.id_product.exists' => 'El producto especificado no existe.',
            
            // Validaciones para quantity
            'order_detail.quantity.required' => 'La cantidad es obligatoria.',
            'order_detail.quantity.integer' => 'La cantidad debe ser un número entero.',
            'order_detail.quantity.min' => 'La cantidad debe ser al menos 1.',
            'order_detail.quantity.max' => 'La cantidad no puede ser mayor a 999,999.',
            
            // Validaciones para unit_price_at_order
            'order_detail.unit_price_at_order.required' => 'El precio unitario es obligatorio.',
            'order_detail.unit_price_at_order.numeric' => 'El precio unitario debe ser un número válido.',
            'order_detail.unit_price_at_order.min' => 'El precio unitario no puede ser negativo.',
            'order_detail.unit_price_at_order.max' => 'El precio unitario no puede ser mayor a 999,999.99.',

            'order_detail.discount_percentage_by_unit.required' => 'El descuento por unidad es obligatorio.',
            'order_detail.discount_percentage_by_unit.numeric' => 'El descuento por unidad debe ser un número válido.',
            'order_detail.discount_percentage_by_unit.min' => 'El descuento por unidad debe ser al menos 0.',
            'order_detail.discount_percentage_by_unit.max' => 'El descuento por unidad no puede ser mayor a 1.',
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
            'id_order' => 'ID del pedido',
            'order_detail' => 'detalles del pedido',
            'order_detail.id_product' => 'ID del producto',
            'order_detail.quantity' => 'cantidad',
            'order_detail.unit_price_at_order' => 'precio unitario',
            'order_detail.discount_percentage_by_unit' => 'descuento por unidad'
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
            // Validación personalizada: verificar que el pedido esté en estado 'Pendiente'
            if ($this->filled('id_order')) {
                $order = Order::find($this->id_order);
                if ($order && $order->order_status !== 'Pendiente') {
                    $validator->errors()->add('id_order', 'Solo se pueden añadir detalles a pedidos en estado Pendiente.');
                }
            }
            
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