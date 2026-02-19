<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class FilterProductsRequest extends BaseApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->tokenCan('server:read', Product::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'sort_by' => [
                'nullable',
                'string',
                'in:code,name,category_id,current_stock,created_at,deleted_at'
            ],
            'sort_direction' => 'nullable|string|in:asc,desc',
            'category_id' => 'nullable|integer|exists:categories,id',
            'search' => 'nullable|string|max:255',
            'low_stock' => 'nullable|boolean'
        ];
    }

    /* protected function prepareForValidation()
    {
        if ($this->has('low_stock')) {
            $value = strtolower($this->input('low_stock'));
            $this->merge(['low_stock' => ($value === 'true') ? true : false]);
            dd($value, $this);
        }
    } */

    /**
     * Get sanitized and processed data
     *
     * @return array
     */
    public function getFilters(): array
    {
        return [
            'category_id' => $this->input('category_id'),
            // 'low_stock' => $this->boolean('low_stock'),
            'search' => $this->input('search'),
            'sort_by' => $this->input('sort_by', 'deleted_at'),
            'sort_direction' => $this->input('sort_direction', 'asc'),
            'per_page' => $this->integer('per_page', 9),
            'page' => $this->integer('page', 1)
        ];
    }
}