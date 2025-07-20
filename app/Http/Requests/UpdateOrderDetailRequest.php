<?php

namespace App\Http\Requests;

use App\Models\OrderDetail;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderDetailRequest extends FormRequest
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
            'order_detail' => [
                'required',
                'array'
            ],
            'order_detail.id_product' => [
                // 'required',
                'integer',
                'exists:products,id'
            ],
            'order_detail.quantity' => [
                // 'required',
                'integer',
                'min:1',
                'max:999999'
            ],
            'order_detail.unit_price_at_order' => [
                // 'required',
                'numeric',
                'min:0',
                'max:999999.99'
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
            'order_detail' => 'detalles del pedido',
            'order_detail.id_product' => 'ID del producto',
            'order_detail.quantity' => 'cantidad',
            'order_detail.unit_price_at_order' => 'precio unitario'
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
                    $validator->errors()->add('order_detail', 'Solo se pueden editar detalles de pedidos en estado Pendiente.');
                }
                
                // Validación personalizada: verificar que haya cambios en producto o cantidad
                if ($this->filled('order_detail.id_product') && $this->filled('order_detail.quantity') && $this->filled('order_detail.unit_price_at_order')) {
                    $newProductId = $this->input('order_detail.id_product');
                    $newQuantity = $this->input('order_detail.quantity');
                    $newUnitPrice = $this->input('order_detail.unit_price_at_order');
                    
                    $productChanged = $orderDetail->id_product != $newProductId;
                    $quantityChanged = $orderDetail->quantity != $newQuantity;
                    $unitPriceChanged = $orderDetail->unit_price_at_order != $newUnitPrice;
                    
                    if (!$productChanged && !$quantityChanged && !$unitPriceChanged) {
                        $validator->errors()->add('order_detail', 'Debe cambiar el producto, la cantidad o el precio unitario para actualizar el detalle.');
                    }
                }
                
                // Validación personalizada: verificar que no exista otro detalle con el mismo producto (solo si cambió el producto)
                if ($this->filled('order_detail.id_product')) {
                    $newProductId = $this->input('order_detail.id_product');
                    
                    if ($orderDetail->id_product != $newProductId) {
                        $existingDetail = OrderDetail::where('id_order', $orderDetail->id_order)
                            ->where('id_product', $newProductId)
                            ->where('id', '!=', $orderDetail->id_order_detail)
                            ->first();
                            
                        if ($existingDetail) {
                            $validator->errors()->add('order_detail.id_product', 'Ya existe otro detalle con este producto en el pedido.');
                        }
                    }
                }
            }
        });
    }
}