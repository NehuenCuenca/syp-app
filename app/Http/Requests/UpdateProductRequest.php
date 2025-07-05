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
            'sku.unique' => 'This SKU is already in use.',
            'suggested_sale_price.gte' => 'The suggested sale price must be greater than or equal to the average purchase price.',
            'current_stock.min' => 'The current stock cannot be negative.',
            'min_stock_alert.min' => 'The minimum stock alert cannot be negative.',
        ];
    }
}