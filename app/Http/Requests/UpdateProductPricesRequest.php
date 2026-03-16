<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductPricesRequest extends BaseApiRequest
{
    // Constantes para evitar strings hardcodeados
    public const PERCENTAGE_MODE            = 'porcentaje';
    public const ABSOLUTE_PRICE_MODE        = 'precio';
    public const UPGRADE_PRICE_DIRECTION    = 'subir';
    public const DOWNGRADE_PRICE_DIRECTION  = 'bajar';

    public function authorize(): bool
    {
        // Ajusta esto si querés validar permisos/roles
        return auth()->user()->tokenCan('server:update', Product::class);
    }

    public function rules(): array
    {
        return [
            'products_ids'   => ['required', 'array', 'min:1'],
            // incluye soft deletes porque la regla exists va directo a la tabla
            'products_ids.*' => ['integer', 'exists:products,id'],

            'mode' => [
                'required',
                'string',
                Rule::in([
                    self::PERCENTAGE_MODE,
                    self::ABSOLUTE_PRICE_MODE,
                ]),
            ],

            'direction' => [
                'required',
                'string',
                Rule::in([
                    self::UPGRADE_PRICE_DIRECTION,
                    self::DOWNGRADE_PRICE_DIRECTION,
                ]),
            ],

            // El rango máximo depende del modo, así que se refina en withValidator()
            'value' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'products_ids.required'   => 'Debe seleccionar al menos un producto.',
            'products_ids.array'      => 'El campo products_ids debe ser un array.',
            'products_ids.min'        => 'Debe seleccionar al menos un producto.',
            'products_ids.*.exists'   => 'Alguno de los productos seleccionados no existe.',

            'mode.required' => 'El modo de actualización es obligatorio.',
            'mode.in'       => 'El modo debe ser "' . self::PERCENTAGE_MODE . '" o "' . self::ABSOLUTE_PRICE_MODE . '".',

            'direction.required' => 'La dirección es obligatoria.',
            'direction.in'       => 'La dirección debe ser "' . self::UPGRADE_PRICE_DIRECTION . '" o "' . self::DOWNGRADE_PRICE_DIRECTION . '".',

            'value.required' => 'El valor es obligatorio.',
            'value.integer'  => 'El valor debe ser un número entero.',
            'value.min'      => 'El valor mínimo permitido es 1.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $mode = $this->input('mode');

            if ($mode === self::PERCENTAGE_MODE) {
                if ($this->input('value') > 500) {
                    $validator->errors()->add(
                        'value',
                        'Para el modo porcentaje, el valor debe estar entre 1 y 500.'
                    );
                }
            }

            if ($mode === self::ABSOLUTE_PRICE_MODE) {
                if ($this->input('value') > 50_000) {
                    $validator->errors()->add(
                        'value',
                        'Para el modo precio, el valor debe estar entre 1 y 50000.'
                    );
                }
            }
        });
    }
}