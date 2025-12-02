<?php

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateOrderRequest extends BaseApiRequest
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
            'id_contact' => 'sometimes|required|integer|exists:contacts,id',
            'adjustment_amount' => 'nullable|numeric',
            'notes' => 'nullable|string|max:1000',

            // Validaciones para los detalles del pedido
            'order_details' => 'array|min:1',
            'order_details.*.id_product' => 'integer|exists:products,id',
            'order_details.*.quantity' => 'integer|min:1',
            'order_details.*.unit_price_at_order' => 'numeric|min:0|max:9999999',

            'id_movement_type' => 'missing', //el frontend no deberia actualizar el tipo del pedido..
            'sub_total' => 'missing', // el frontend no deberia actualizar el sub_total
            'total_net' => 'missing', // el frontend no deberia actualizar el total_net
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $order = $this->route('order');

            // Validación personalizada: verificar duplicados de productos
            $productIds = collect($this->order_details)->pluck('id_product')->toArray();
            $uniqueProductIds = array_unique($productIds);
            
            if (count($productIds) !== count($uniqueProductIds)) {
                $validator->errors()->add('order_details', 'No se pueden repetir productos en el mismo pedido.');
            }
            
            // Validación personalizada: verificar que profit_percentage no este presente si el pedido es una venta
            if ($order->getIsSaleAttribute()) {
                foreach ($this->input('order_details') as $pos => $detail) {
                    if (isset($detail['profit_percentage'])) {
                        $validator->errors()->add("order_details.{$pos}.profit_percentage", "No se puede agregar un 'porcentaje de ganancia' si el pedido es una venta.");
                    }
                }
            } else {
                foreach ($this->input('order_details') as $pos => $detail) {
                    if (isset($detail['discount_percentage_by_unit'])) {
                        $validator->errors()->add("order_details.{$pos}.discount_percentage_by_unit", "No se puede agregar un 'porcentaje de descuento' si el pedido es una compra.");
                    }
                }
            }
        });
    }
    
    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->validationErrorResponse(
                $validator->errors()->toArray(),
                'Los datos proporcionados no son válidos'
            )
        );
    }
}