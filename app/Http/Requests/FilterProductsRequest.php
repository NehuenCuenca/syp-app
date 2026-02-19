<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class FilterProductsRequest extends BaseApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->tokenCan('server:read', Product::class);
    }

    public const ALLOWED_SORT_FIELDS = [
        'code' => 'COD',
        'category_id' => 'Categoria',
        'current_stock' => 'Stock actual',
        'name' => 'Nombre',
        'created_at' => 'Fecha de creacion',
        'deleted_at' => 'Fecha de eliminacion'
    ];

    public const ALLOWED_SORT_DIRECTIONS = [
        'asc' => 'Ascendente',
        'desc' => 'Descendente'
    ];

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
                Rule::in(array_keys(self::ALLOWED_SORT_FIELDS))
            ],
            'sort_direction' => ['nullable', 'string', Rule::in(array_keys(self::ALLOWED_SORT_DIRECTIONS))],
            'category_id' => 'nullable|integer|exists:categories,id',
            'search' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get sanitized and processed data
     *
     * @return array
     */
    public function getFilters(): array
    {
        return [
            'category_id' => $this->input('category_id'),
            'search' => $this->input('search'),
            'sort_by' => $this->input('sort_by', 'deleted_at'),
            'sort_direction' => $this->input('sort_direction', 'asc'),
            'per_page' => $this->integer('per_page', 9),
            'page' => $this->integer('page', 1)
        ];
    }
}
