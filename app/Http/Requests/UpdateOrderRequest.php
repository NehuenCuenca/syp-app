<?php

namespace App\Http\Requests;

use App\Models\MovementType;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateOrderRequest extends BaseApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->tokenCan('server:update', Order::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_contact' => 'sometimes|required|integer|exists:contacts,id',
            'adjustment_amount' => 'nullable|numeric',
            'notes' => 'nullable|string|max:1000',

            // Validaciones para los detalles del pedido
            'order_details' => 'array|min:1',
            'order_details.*.id_product' => 'integer|exists:products,id',
            'order_details.*.quantity' => 'integer|min:1',
            'order_details.*.unit_price' => 'numeric|min:0|max:9999999',
            'order_details.*.percentage_applied' => 'numeric|min:0',

            'id_movement_type' => 'missing', //el frontend no deberia actualizar el tipo del pedido..
            'sub_total' => 'missing', // el frontend no deberia actualizar el sub_total
            'total_net' => 'missing', // el frontend no deberia actualizar el total_net
        ];
    }

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
            if ($this->order->getIsSaleAttribute() && $this->order_details) {
                foreach ($this->order_details as $index => $new_detail) {
                    $product = Product::find($new_detail['id_product']);
                    $wasOrdered = $this->order->orderDetails->where('id_product', $product->id)->first();
                    // Si el producto ya fue ordenado, sumar al stock actual la cantidad anterior
                    $valid_stock = (isset($wasOrdered)) 
                                    ? ($product->current_stock + $wasOrdered->quantity)
                                    : $product->current_stock;

                    if ($product && $valid_stock < $new_detail['quantity']) {
                        $validator->errors()->add(
                            "order_details.{$index}.quantity",
                            "Stock insuficiente para el producto {$product->name}. Stock disponible: {$valid_stock}"
                        );
                    }
                }
            }
        });
    }
}