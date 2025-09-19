<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class FilterProductsRequest extends FormRequest
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
                'in:name,sku,id_category,current_stock,min_stock_alert,sale_price,buy_price,created_at,updated_at'
            ],
            'sort_direction' => 'nullable|string|in:asc,desc',
            'id_category' => 'nullable|integer|exists:categories,id',
            'search' => 'nullable|string|max:255',
            'low_stock' => 'nullable|boolean'
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'min_sale_price.numeric' => 'El precio mínimo de venta debe ser un número válido.',
            'min_sale_price.min' => 'El precio mínimo de venta no puede ser negativo.',
            'max_sale_price.numeric' => 'El precio máximo de venta debe ser un número válido.',
            'max_sale_price.min' => 'El precio máximo de venta no puede ser negativo.',
            'max_sale_price.gte' => 'El precio máximo de venta debe ser mayor o igual al precio mínimo de venta.',
            'min_stock.integer' => 'El stock mínimo debe ser un número entero.',
            'min_stock.min' => 'El stock mínimo no puede ser negativo.',
            'per_page.integer' => 'La cantidad por página debe ser un número entero.',
            'per_page.min' => 'La cantidad por página debe ser al menos 1.',
            'per_page.max' => 'La cantidad por página no puede ser mayor a 100.',
            'page.integer' => 'El número de página debe ser un número entero.',
            'page.min' => 'El número de página debe ser al menos 1.',
            'sort_by.in' => 'El campo de ordenamiento no es válido.',
            'sort_direction.in' => 'El orden debe ser "asc" o "desc".',
            'id_category.exists' => 'La categoría especificada no existe.',
            'search.max' => 'El término de búsqueda no puede tener más de 255 caracteres.',
            'low_stock.boolean' => 'El filtro de stock bajo debe ser verdadero o falso.'
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
            'min_sale_price' => 'precio mínimo de venta',
            'max_sale_price' => 'precio máximo de venta',
            'min_stock' => 'stock mínimo',
            'per_page' => 'elementos por página',
            'page' => 'página',
            'sort_by' => 'campo de ordenamiento',
            'sort_direction' => 'orden',
            'id_category' => 'categoría',
            'search' => 'búsqueda',
            'low_stock' => 'stock bajo'
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
            response()->json([
                'success' => false,
                'message' => 'Los parámetros proporcionados no son válidos.',
                'errors' => $validator->errors()
            ], 422)
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