<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Add proper authorization logic later
    }

    public function rules(): array
    {
        return [
            'sku' => ['string', 'max:50', 'unique:products,sku,' . $this->product->id],
            'name' => ['string', 'max:255'],
            'description' => ['nullable', 'string'],
            'avg_purchase_price' => ['numeric', 'min:0'],
            'suggested_sale_price' => ['numeric', 'min:0', 'gte:avg_purchase_price'],
            'current_stock' => ['integer', 'min:0'],
            'min_stock_alert' => ['integer', 'min:0'],
            'category' => ['string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'sku.unique' => 'Este SKU ya está en uso.',
            'suggested_sale_price.gte' => 'El precio de venta sugerido debe ser mayor o igual al precio promedio de compra.',
            'current_stock.min' => 'El stock actual no puede ser negativo.',
            'min_stock_alert.min' => 'La alerta de stock mínimo no puede ser negativa.',
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
            'sku' => 'SKU',
            'suggested_sale_price' => 'precio de venta sugerido',
            'current_stock' => 'stock actual',
            'min_stock_alert' => 'alerta de stock minimo',
        ];
    }
}