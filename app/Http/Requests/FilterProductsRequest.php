<?php

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class FilterProductsRequest extends BaseApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Ajusta según tu lógica de autorización
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'min_sale_price' => 'nullable|numeric|min:0',
            'max_sale_price' => 'nullable|numeric|min:0|gte:min_sale_price',
            'min_stock' => 'nullable|integer|min:0',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
            'sort_by' => [
                'nullable',
                'string',
                'in:name,id_category,current_stock,min_stock_alert,sale_price,buy_price,created_at,updated_at'
            ],
            'sort_direction' => 'nullable|string|in:asc,desc',
            'id_category' => 'nullable|integer|exists:categories,id',
            'search' => 'nullable|string|max:255',
            'low_stock' => 'nullable|boolean'
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->validationErrorResponse(
                $validator->errors()->toArray(),
                'Los datos proporcionados no son válidos'
            )
        );
    }

    /**
     * Get sanitized and processed data
     *
     * @return array
     */
    public function getFilters(): array
    {
        return [
            'id_category' => $this->input('id_category'),
            'low_stock' => $this->boolean('low_stock'),
            'search' => $this->input('search'),
            'min_sale_price' => $this->integer('min_sale_price'),
            'max_sale_price' => $this->integer('max_sale_price'),
            'min_stock' => $this->integer('min_stock'),
            'sort_by' => $this->input('sort_by', 'created_at'),
            'sort_direction' => $this->input('sort_direction', 'desc'),
            'per_page' => $this->integer('per_page', 9),
            'page' => $this->integer('page', 1)
        ];
    }
}