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
            'buy_price' => ['numeric', 'min:0'],
            'profit_percentage' => ['decimal:1', 'min:1.1', 'max:1.9'],
            'sale_price' => ['numeric', 'min:0', 'gte:buy_price'],
            'current_stock' => ['integer', 'min:0'],
            'min_stock_alert' => ['integer', 'min:0'],
            'category' => ['string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'sku.unique' => 'Este SKU ya está en uso.',
            'profit_percentage.decimal' => 'El porcentaje de ganancia debe contener al menos un decimal',
            'profit_percentage.min' => 'El porcentaje de ganancia debe ser mayor o igual que 1.1',
            'profit_percentage.max' => 'El porcentaje de ganancia debe ser menor o igual que 1.9',
            'sale_price.gte' => 'El precio de venta sugerido debe ser mayor o igual al precio promedio de compra.',
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
            'profit_percentage' => 'porcentaje de ganancia',
            'sale_price' => 'precio de venta sugerido',
            'current_stock' => 'stock actual',
            'min_stock_alert' => 'alerta de stock minimo',
        ];
    }
}