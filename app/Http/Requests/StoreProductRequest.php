<?php

namespace App\Http\Requests;

use App\Models\Product;

class StoreProductRequest extends BaseApiRequest
{
    public function authorize(): bool
    {
        // return true; // Add proper authorization logic later
        return auth()->user()->tokenCan('server:create', Product::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'buy_price' => ['required', 'numeric', 'min:0'],
            'profit_percentage' => ['required', 'numeric', 'min:1'],
            'sale_price' => ['numeric', 'min:0', 'gte:buy_price'],
            'current_stock' => ['required', 'integer', 'min:0'],
            'min_stock_alert' => ['required', 'integer', 'min:0'],
            'category' => ['required', 'string', 'max:100'],
        ];
    }
}