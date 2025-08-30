<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'id_contact' => 'required|integer|exists:contacts,id',
            'id_user_creator' => 'required|integer|exists:users,id',
            'order_type' => 'required|in:Compra,Venta',
            'order_status' => 'nullable|in:Pendiente',
            'notes' => 'nullable|string|max:1000',
            'total_net' => 'sometimes|numeric|min:0',
            
            // Validaciones para los detalles del pedido
            'order_details' => 'required|array|min:1',
            'order_details.*.id_product' => 'required|integer|exists:products,id',
            'order_details.*.quantity' => 'required|integer|min:1',
            'order_details.*.unit_price_at_order' => 'required|numeric|min:0|max:999999.99',
            'order_details.*.discount_percentage_by_unit' => 'required|numeric|min:0|max:1',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'id_contact.required' => 'El contacto es obligatorio.',
            'id_contact.integer' => 'El contacto debe ser un número entero.',
            'id_contact.exists' => 'El contacto seleccionado no existe.',
            
            'id_user_creator.required' => 'El usuario creador es obligatorio.',
            'id_user_creator.integer' => 'El usuario creador debe ser un número entero.',
            'id_user_creator.exists' => 'El usuario creador seleccionado no existe.',
            
            'order_type.required' => 'El tipo de pedido es obligatorio.',
            'order_type.in' => 'El tipo de pedido debe ser: Compra o Venta.',
            
            'order_status.in' => 'El estado inicial de un pedido debe ser: Pendiente.',
            
            'notes.string' => 'Las notas deben ser texto.',
            'notes.max' => 'Las notas no pueden exceder los 1000 caracteres.',

            'total_net.numeric' => 'El precio total debe ser un número.',
            'total_net.min' => 'El precio total no puede ser negativo.',

            
            // Mensajes para detalles del pedido
            'order_details.required' => 'Los detalles del pedido son obligatorios.',
            'order_details.array' => 'Los detalles del pedido deben ser un array.',
            'order_details.min' => 'Debe incluir al menos un detalle en el pedido.',
            
            'order_details.*.id_product.required' => 'El producto es obligatorio en cada detalle.',
            'order_details.*.id_product.integer' => 'El ID del producto debe ser un número entero.',
            'order_details.*.id_product.exists' => 'El producto seleccionado no existe.',
            
            'order_details.*.quantity.required' => 'La cantidad es obligatoria en cada detalle.',
            'order_details.*.quantity.integer' => 'La cantidad debe ser un número entero.',
            'order_details.*.quantity.min' => 'La cantidad debe ser al menos 1.',
            
            'order_details.*.unit_price_at_order.required' => 'El precio unitario es obligatorio en cada detalle.',
            'order_details.*.unit_price_at_order.numeric' => 'El precio unitario debe ser un número.',
            'order_details.*.unit_price_at_order.min' => 'El precio unitario no puede ser negativo.',
            'order_details.*.unit_price_at_order.max' => 'El precio unitario no puede exceder 999,999.99.',

            'order_details.*.discount_percentage_by_unit.required' => 'El descuento por unidad es obligatorio en cada detalle.',
            'order_details.*.discount_percentage_by_unit.numeric' => 'El descuento por unidad debe ser un número.',
            'order_details.*.discount_percentage_by_unit.min' => 'El descuento por unidad no puede ser menor a 0.',
            'order_details.*.discount_percentage_by_unit.max' => 'El descuento por unidad no puede mayor a 1.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Establecer estado por defecto si no se proporciona
        if (!$this->has('order_status')) {
            $this->merge([
                'order_status' => 'Pendiente'
            ]);
        }
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
            // Validación personalizada: verificar duplicados de productos
            $productIds = collect($this->order_details)->pluck('id_product')->toArray();
            $uniqueProductIds = array_unique($productIds);
            
            if (count($productIds) !== count($uniqueProductIds)) {
                $validator->errors()->add('order_details', 'No se pueden repetir productos en el mismo pedido.');
            }

            // Validación personalizada: verificar stock disponible para pedidos de venta
            if ($this->order_type === 'Venta' && $this->order_details) {
                foreach ($this->order_details as $index => $detail) {
                    $product = Product::find($detail['id_product']);
                    if ($product && $product->current_stock < $detail['quantity']) {
                        $validator->errors()->add(
                            "order_details.{$index}.quantity",
                            "Stock insuficiente para el producto {$product->name}. Stock disponible: {$product->current_stock}"
                        );
                    }
                }
            }
        });
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = response()->json([
            'message' => 'Los datos proporcionados no son válidos.',
            'errors' => $validator->errors()
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}