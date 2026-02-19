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
            'contact_id' => 'required_without:new_contact_name|integer|exists:contacts,id',
            'new_contact_name' => 'required_without:contact_id|string|max:255',
            'adjustment_amount' => 'nullable|numeric',
            'notes' => 'nullable|string|max:1000',

            // Validaciones para los detalles del pedido
            'order_details' => 'array|min:1',
            'order_details.*.id_product' => 'integer|exists:products,id',
            'order_details.*.quantity' => 'integer|min:1',
            'order_details.*.unit_price' => 'numeric|min:0|max:9999999',
            'order_details.*.percentage_applied' => 'numeric|min:0',

            'movement_type_id' => 'missing', //el frontend no deberia actualizar el tipo del pedido..
            'sub_total' => 'missing', // el frontend no deberia actualizar el sub_total
            'total_net' => 'missing', // el frontend no deberia actualizar el total_net
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->has('order_details')) {
                return; // No need to validate if details aren't being updated
            }

            // Validar duplicados de productos
            $productIds = collect($this->order_details)->pluck('id_product')->toArray();
            $uniqueProductIds = array_unique($productIds);

            if (count($productIds) !== count($uniqueProductIds)) {
                $validator->errors()->add('order_details', 'No se pueden repetir productos en el mismo pedido.');
            }

            // Validar stock usando el servicio
            $orderService = app(\App\Services\OrderService::class);
            $stockErrors = $orderService->validateStockAvailabilityForUpdate(
                $this->order,
                $this->order_details
            );

            foreach ($stockErrors as $field => $message) {
                $validator->errors()->add($field, $message);
            }
        });
    }
}