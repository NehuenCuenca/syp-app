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
            'name' => ['string', 'max:255'],
            'buy_price' => ['numeric', 'min:0'],
            'profit_percentage' => ['numeric', 'min:1'],
            'sale_price' => ['numeric', 'min:0', 'gte:buy_price'],
            'current_stock' => ['integer', 'min:0'], //['missing'],
            'min_stock_alert' => ['integer', 'min:1'],
            'category' => ['string', 'max:100'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // crear categoria si no existe
        $category = Category::firstOrCreate(['name' => $this->input('category', 'Varios')]);
        $this->merge([
            'id_category' => $category->id
        ]);
    }
}