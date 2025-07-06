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
            'avg_purchase_price' => ['required', 'numeric', 'min:0'],
            'suggested_sale_price' => ['required', 'numeric', 'min:0', 'gte:avg_purchase_price'],
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
            'avg_purchase_price.required' => 'El precio promedio de compra es obligatorio',
            'avg_purchase_price.min' => 'El precio promedio de compra debe ser mayor que cero',
            'suggested_sale_price.required' => 'El precio de venta sugerido es obligatorio',
            'suggested_sale_price.min' => 'El precio de venta sugerido debe ser mayor que cero',
            'suggested_sale_price.gte' => 'El precio de venta sugerido debe ser mayor o igual al precio promedio de compra',
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
            'avg_purchase_price' => 'precio de compra promedio',
            'suggested_sale_price' => 'precio de venta sugerido',
            'current_stock' => 'stock actual',
            'min_stock_alert' => 'alerta de stock minimo',
            'category' => 'categoria',
        ];
    }
}