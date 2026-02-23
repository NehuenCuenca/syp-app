<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class FilterOrdersRequest extends BaseApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->tokenCan('server:read', Order::class);
    }

    // Constantes para filtros
    public const ALLOWED_SORT_FIELDS = [
        'created_at' => 'Fecha de creacion', 
        'movement_type_id' => 'Tipo de pedido',
        'subtotal' => 'Subtotal',
        'total_net' => 'Total neto',
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
            'contact_id' => ['nullable', 'integer', 'exists:contacts,id'],
            'before_equal_date' => ['nullable', 'date', 'before_or_equal:today'],
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
            'contact_id' => $this->input('contact_id'),
            'before_equal_date' => $this->input('before_equal_date'),
            'search' => $this->input('search'),
            'sort_by' => $this->input('sort_by', 'deleted_at'),
            'sort_direction' => $this->input('sort_direction', 'asc'),
            'per_page' => $this->integer('per_page', 9),
            'page' => $this->integer('page', 1)
        ];
    }
}
