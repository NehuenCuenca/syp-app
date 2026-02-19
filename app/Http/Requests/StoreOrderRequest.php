<?php

namespace App\Http\Requests;

use App\Models\Contact;
use App\Models\MovementType;
use App\Models\Order;
use Illuminate\Contracts\Validation\Validator;
use App\Models\Product;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreOrderRequest extends BaseApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->tokenCan('server:create', Order::class);
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

            'id_movement_type' => 'required|exists:movement_types,id',
            'notes' => 'nullable|string|max:1000',
            'adjustment_amount' => 'nullable|numeric',
            'sub_total' => 'missing',
            'total_net' => 'missing',
            
            // Validaciones para los detalles del pedido
            'order_details' => 'required|array|min:1',
            'order_details.*.id_product' => 'required|integer|exists:products,id',
            'order_details.*.quantity' => 'required|integer|min:1',
            'order_details.*.unit_price' => 'required|numeric|min:0|max:9999999',
            'order_details.*.percentage_applied' => 'required|numeric|min:0', 
            // 'order_details.*.percentage_applied' => 'prohibited_if:id_movement_type,1|required_if:id_movement_type,2|numeric|min:0', 
            // 'order_details.*.profit_percentage' => 'prohibited_if:id_movement_type,2|required_if:id_movement_type,1|numeric|min:1',
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
            // Validar tipo de movimiento
            $validMovementTypes = [
                MovementType::firstWhere('name', Order::ORDER_TYPE_SALE)->id,
                MovementType::firstWhere('name', Order::ORDER_TYPE_PURCHASE)->id,
            ];
            
            if (!in_array($this->id_movement_type, $validMovementTypes)) {
                $validator->errors()->add('id_movement_type', 'Tipo de movimiento inválido (solo se permiten compras y ventas).');
            }

            // Validar duplicados de productos
            $productIds = collect($this->order_details)->pluck('id_product')->toArray();
            $uniqueProductIds = array_unique($productIds);
            
            if (count($productIds) !== count($uniqueProductIds)) {
                $validator->errors()->add('order_details', 'No se pueden repetir productos en el mismo pedido.');
            }

            // Validar stock usando el servicio
            $orderService = app(\App\Services\OrderService::class);
            $stockErrors = $orderService->validateStockAvailability(
                $this->order_details,
                $this->id_movement_type
            );

            foreach ($stockErrors as $field => $message) {
                $validator->errors()->add($field, $message);
            }
        });
    }
}