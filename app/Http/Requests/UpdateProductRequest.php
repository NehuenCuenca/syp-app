<?php

namespace App\Http\Requests;

use App\Models\Product;

class UpdateProductRequest extends BaseApiRequest
{
    public function authorize(): bool
    {
        return auth()->user()->tokenCan('server:update', Product::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['string', 'max:100'],
            'buy_price' => ['numeric', 'min:0'],
            'profit_percentage' => ['numeric', 'min:1'],
            'sale_price' => ['numeric', 'min:0', 'gte:buy_price'],
            'current_stock' => ['integer', 'min:0'],
            'min_stock_alert' => ['integer', 'min:1'],
            'category' => ['string', 'max:30'],
        ];
    }
}