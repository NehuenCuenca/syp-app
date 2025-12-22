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
            'id_contact' => 'required_without:new_contact|integer|exists:contacts,id',
            'new_contact' => 'required_without:id_contact|array',
            'new_contact.company_name' => 'required_with:new_contact|string|max:50|min:4',

            'id_movement_type' => 'required|exists:movement_types,id|in:1,2',
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
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if(!$this->has('id_contact') && $this->has('new_contact')) {
            $this->merge([
                'id_contact' => Contact::firstOrCreate([
                    'company_name' => $this->new_contact['company_name'],
                    'contact_type' => 'Cliente',
                ])->id
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
            if ($this->id_movement_type == MovementType::firstWhere('name', Order::ORDER_TYPE_SALE)->id && $this->order_details) {
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
}