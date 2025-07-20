<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Add proper authorization logic later
    }

    public function rules(): array
    {
        return [
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku' . ($this->product ? ',' . $this->product->id : '')],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'buy_price' => ['required', 'numeric', 'min:0'],
            'profit_percentage' => ['required', 'decimal:1', 'min:1.1', 'max:1.9'],
            'sale_price' => ['required', 'numeric', 'min:0', 'gte:buy_price'],
            'current_stock' => ['required', 'integer', 'min:0'],
            'min_stock_alert' => ['required', 'integer', 'min:0'],
            'category' => ['required', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'sku.required' => 'El SKU es obligatorio',
            'sku.unique' => 'Este SKU ya está en uso',
            'name.required' => 'El nombre del producto es obligatorio',
            'buy_price.required' => 'El precio promedio de compra es obligatorio',
            'buy_price.min' => 'El precio promedio de compra debe ser mayor que cero',
            'profit_percentage.required' => 'El porcentaje de ganancia es obligatorio',
            'profit_percentage.decimal' => 'El porcentaje de ganancia debe contener al menos un decimal',
            'profit_percentage.min' => 'El porcentaje de ganancia debe ser mayor o igual que 1.1',
            'profit_percentage.max' => 'El porcentaje de ganancia debe ser menor o igual que 1.9',
            'sale_price.required' => 'El precio de venta sugerido es obligatorio',
            'sale_price.min' => 'El precio de venta sugerido debe ser mayor que cero',
            'sale_price.gte' => 'El precio de venta sugerido debe ser mayor o igual al precio promedio de compra',
            'current_stock.required' => 'El stock actual es obligatorio',
            'current_stock.min' => 'El stock actual no puede ser negativo',
            'min_stock_alert.required' => 'La alerta de stock mínimo es obligatoria',
            'min_stock_alert.min' => 'La alerta de stock mínimo no puede ser negativa',
            'category.required' => 'La categoría es obligatoria',
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
            'name' => 'nombre',
            'buy_price' => 'precio de compra promedio',
            'profit_percentage' => 'porcentaje de ganancia',
            'sale_price' => 'precio de venta sugerido',
            'current_stock' => 'stock actual',
            'min_stock_alert' => 'alerta de stock minimo',
            'category' => 'categoria',
        ];
    }
}