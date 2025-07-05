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
            'sku.required' => 'The SKU is required',
            'sku.unique' => 'This SKU is already in use',
            'name.required' => 'The product name is required',
            'avg_purchase_price.required' => 'The average purchase price is required',
            'avg_purchase_price.min' => 'The average purchase price must be greater than zero',
            'suggested_sale_price.required' => 'The suggested sale price is required',
            'suggested_sale_price.min' => 'The suggested sale price must be greater than zero',
            'suggested_sale_price.gte' => 'The suggested sale price must be greater than or equal to the average purchase price',
            'current_stock.required' => 'The current stock is required',
            'current_stock.min' => 'The current stock cannot be negative',
            'min_stock_alert.required' => 'The minimum stock alert is required',
            'min_stock_alert.min' => 'The minimum stock alert cannot be negative',
            'category.required' => 'The category is required',
        ];
    }
}