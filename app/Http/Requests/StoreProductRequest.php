<?php

namespace App\Http\Requests;

use App\Models\Category;

class StoreProductRequest extends BaseApiRequest
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

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // crear categoria si no existe
        $category = Category::firstOrCreate(['name' => $this->input('category', 'Sin categoría')]);
        $this->merge([
            'id_category' => $category->id
        ]);
    }
}