<?php
namespace App\Http\Requests;

use App\Http\Traits\ApiResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Clase base para todos los Form Requests de la API
 * Maneja automáticamente las respuestas de validación fallida
 */
class BaseApiRequest extends FormRequest
{
    use ApiResponseTrait;

    /**
     * Maneja una instancia de validación fallida.
     * 
     * Este método se ejecuta automáticamente cuando las validaciones fallan
     * En lugar del comportamiento por defecto de Laravel, usa nuestro formato estándar
     */
    protected function failedValidation(Validator $validator)
    {
        // Lanzar excepción con nuestra respuesta JSON estandarizada
        throw new HttpResponseException(
            $this->validationErrorResponse(
                $validator->errors()->toArray(),
                'Los datos proporcionados no son válidos'
            )
        );
    }

    /**
     * Obtener mensajes de validación personalizados para las reglas definidas
     */
    public function messages()
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'email' => 'El campo :attribute debe ser una dirección de email válida.',
            'unique' => 'El campo :attribute ya existe en el sistema.',
            'min' => 'El campo :attribute debe tener al menos :min caracteres.',
            'max' => 'El campo :attribute no debe tener más de :max caracteres.',
            'string' => 'El campo :attribute debe ser una cadena de texto.',
            'confirmed' => 'La confirmación del campo :attribute no coincide.',
            'in' => 'El campo :attribute es inválido. Debe ser uno de los siguientes valores: :values',
            'date' => 'El campo :attribute debe ser una fecha válida.',
            'numeric' => 'El campo :attribute debe ser un número.',
            'decimal' => 'El campo :attribute debe ser un número decimal con :decimal lugares después del punto.',
            'gte' => 'El campo :attribute debe ser mayor o igual a :value.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'missing' => 'El campo :attribute no puede ser adjuntado o editado (no debe estar presente).',
            'exists' => 'El :attribute especificado no existe.',
            'boolean' => 'El campo :attribute debe ser verdadero o falso.',
            'array' => 'El campo :attribute debe ser un array.',
        ];
    }

    /**
     * Obtener nombres personalizados para los atributos de validación
     */
    public function attributes()
    {
        return [
            'username' => 'nombre de usuario',
            'email' => 'correo electronico',
            'phone' => 'telefono',
            'password' => 'contraseña',
            'role' => 'rol',
            'company_name' => 'nombre de empresa',
            'contact_name' => 'nombre del contacto',
            'address' => 'dirección',
            'contact_type' => 'tipo de contacto',
            'registered_at' => 'fecha de registro',
            'name' => 'nombre',
            'buy_price' => 'precio de compra',
            'profit_percentage' => 'porcentaje de ganancia',
            'sale_price' => 'precio de venta',
            'current_stock' => 'stock actual',
            'min_stock_alert' => 'alerta de stock minimo',
            'category' => 'categoria',

            'min_sale_price' => 'precio mínimo de venta',
            'max_sale_price' => 'precio máximo de venta',
            'min_stock' => 'stock mínimo',
            'per_page' => 'elementos por página',
            'page' => 'página',
            'sort_by' => 'campo de ordenamiento',
            'sort_direction' => 'orden',
            'id_category' => 'categoría',
            'search' => 'búsqueda',
            'low_stock' => 'stock bajo',

            'id_contact' => 'contacto',
            'id_user_creator' => 'creador',
            'order_type' => 'tipo de pedido',
            'order_status' => 'estado de pedido',
            'notes' => 'notas',
            'total_net' => 'total neto',
            'order_details' => 'detalles del pedido',
            'order_details.*.id_product' => 'producto',
            'order_details.*.quantity' => 'cantidad',
            'order_details.*.unit_price_at_order' => 'precio unitario',
            'order_details.*.discount_percentage_by_unit' => 'descuento por unidad',

            'id_order' => 'ID del pedido',
            'order_detail' => 'detalle del pedido',
            'id_product' => 'producto del detalle',
            'quantity' => 'cantidad del detalle',
            'unit_price_at_order' => 'precio unitario del detalle',
            'discount_percentage_by_unit' => 'descuento por unidad del detalle',

            'quantity_moved' => 'cantidad movida',
            'movement_type' => 'tipo de movimiento',
            'external_reference' => 'referencia externa',
        ];
    }
}