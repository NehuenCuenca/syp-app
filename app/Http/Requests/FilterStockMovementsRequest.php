<?php

namespace App\Http\Requests;

use App\Models\StockMovement;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class FilterStockMovementsRequest extends BaseApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->tokenCan('server:read', StockMovement::class);
    }

    // Constantes para filtros
    public const ALLOWED_SORT_FIELDS = [
        'created_at' => 'Fecha de creacion', 
        'movement_type_id' => 'Tipo de movimiento',
        'quantity_moved' => 'Total neto',
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
            'movement_type_id' => ['nullable', 'integer', 'exists:movement_types,id'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
            'date_from' => ['nullable', 'required_with:date_to', 'date_format:dd-mm-yyyy'],
            'date_to' => ['nullable', 'required_with:date_from', 'date_format:dd-mm-yyyy'],
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
            'movement_type_id' => $this->input('movement_type_id'),
            'product_id' => $this->input('product_id'),
            'order_id' => $this->input('order_id'),
            'date_from' => $this->input('date_from'),
            'date_to' => $this->input('date_to'),
            'search' => $this->input('search'),
            'sort_by' => $this->input('sort_by', 'created_at'),
            'sort_direction' => $this->input('sort_direction', 'desc'),
            'per_page' => $this->integer('per_page', 9),
            'page' => $this->integer('page', 1)
        ];
    }
}
