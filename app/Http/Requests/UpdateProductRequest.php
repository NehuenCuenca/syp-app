<?php

namespace App\Http\Requests;

use App\Models\Category;

class UpdateProductRequest extends BaseApiRequest
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
            'current_stock' => ['missing'], //['integer', 'min:0'],
            'min_stock_alert' => ['integer', 'min:0'],
            'category' => ['string', 'max:100'],
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