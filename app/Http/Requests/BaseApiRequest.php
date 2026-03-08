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
            'required' => "El campo ':attribute' es obligatorio.",
            'email' => "El campo ':attribute' debe ser una dirección de email válida.",
            'unique' => "El campo ':attribute' ya existe en el sistema.",
            // 'min' => "El campo ':attribute' debe tener al menos :min caracteres.",
            // 'max' => "El campo ':attribute' no debe tener más de :max caracteres.",
            'string' => "El campo ':attribute' debe ser una cadena de texto.",
            'confirmed' => "La confirmación del campo ':attribute' no coincide.",
            'in' => "El campo ':attribute' es inválido. Debe ser uno de los siguientes valores: :values",
            'date' => "El campo ':attribute' debe ser una fecha válida.",
            'numeric' => "El campo ':attribute' debe ser un número.",
            'decimal' => "El campo ':attribute' debe ser un número decimal con :decimal lugares después del punto.",
            'gte' => "El campo ':attribute' debe ser mayor o igual a :value.",
            'integer' => "El campo ':attribute' debe ser un número entero.",
            'missing' => "El campo ':attribute' no puede ser adjuntado o editado (no debe estar presente).",
            'exists' => "El ':attribute' especificado no existe.",
            'boolean' => "El campo ':attribute' debe ser verdadero o falso.",
            'array' => "El campo ':attribute' debe ser un array.",
            'required_without' => "El campo ':attribute' es obligatorio cuando ':other' no está presente.",
            'contact_id.required_without' => "El campo 'contacto' es obligatorio cuando no se proporciona 'nombre de contacto nuevo'.",
            'new_contact_name.required_without' => "El campo 'nombre de contacto nuevo' es obligatorio cuando no se proporciona 'contacto'.",
            'new_contact_name.required_with' => "El campo 'nombre de contacto nuevo' es obligatorio.",
            'prohibited_if' => "El campo ':attribute' no puede ser adjuntado cuando ':other' tiene ese valor especificado.",
            'required_if' => "El campo ':attribute' es requerido junto con el campo ':other'.",

            // PAGINACION
            'per_page.min' => "El campo ':attribute' debe paginar 1 elemento como MINIMO.",
            'per_page.max' => "El campo ':attribute' puede paginar hasta 100 elementos como MAXIMO.",
            'page.min' => "El campo ':attribute' debe empezar desde la 1er pagina.",
            'search.max' => "El campo ':attribute' puede recibir hasta 255 caracteres como MAXIMO.",

            'name.max' => "El campo ':attribute' debe tener hasta 100 caracteres como MAXIMO.",
            'email.max' => "El campo ':attribute' debe tener hasta 100 caracteres como MAXIMO.",
            'phone.max' => "El campo ':attribute' debe tener hasta 50 caracteres como MAXIMO.",
            'address.max' => "El campo ':attribute' debe tener hasta 100 caracteres como MAXIMO.",

            'new_contact_name.max' => "El campo ':attribute' debe tener hasta 100 caracteres como MAXIMO.",
            'notes.max' => "El campo ':attribute' debe tener hasta 255 caracteres como MAXIMO.",
            'order_details.min' => "El campo ':attribute' debe tener :min elementos como MINIMO.",
            'order_details.*.quantity.min' => "El campo ':attribute' debe tener :min unidades como MINIMO.",
            'order_details.*.unit_price.min' => "El campo ':attribute' debe tener $:min (PESOS) como MINIMO.",
            'order_details.*.unit_price.max' => "El campo ':attribute' debe tener hasta $:max como MAXIMO.",
            'order_details.*.percentage_applied.min' => "El campo ':attribute' debe tener :min% (por ciento) como MINIMO.",

            'buy_price.min' => "El campo ':attribute' debe tener $:min (PESOS) como MINIMO.",
            'profit_percentage.min' => "El campo ':attribute' debe tener :min% (por ciento) como MINIMO.",
            'sale_price.min' => "El campo ':attribute' debe tener $:min (PESOS) como MINIMO.",
            'current_stock.min' => "El campo ':attribute' debe tener :min elementos como MINIMO.",
            'min_stock_alert.min' => "El campo ':attribute' debe tener :min elementos como MINIMO.",
            'category.max' => "El campo ':attribute' debe tener hasta 30 caracteres como MAXIMO.",
        ];
    }

    /**
     * Obtener nombres personalizados para los atributos de validación
     */
    public function attributes()
    {
        return [
            // User
            'username' => 'nombre de usuario',
            'email' => 'correo electronico',
            'phone' => 'telefono',
            'password' => 'contraseña',
            'role' => 'rol',
            
            // Contact
            'name' => 'nombre',
            'address' => 'dirección',
            'contact_type' => 'tipo de contacto',
            'registered_at' => 'fecha de registro',

            // Product
            'name' => 'nombre',
            'buy_price' => 'precio de compra',
            'profit_percentage' => 'porcentaje de ganancia',
            'sale_price' => 'precio de venta',
            'current_stock' => 'stock actual',
            'min_stock_alert' => 'alerta de stock',
            'category' => 'categoria',
            
            // Product filters
            'min_sale_price' => 'precio mínimo de venta',
            'max_sale_price' => 'precio máximo de venta',
            'min_stock' => 'stock mínimo',
            'category_id' => 'categoría',
            'low_stock' => 'stock bajo',


            // Pagination and ordering
            'per_page' => 'elementos por página',
            'page' => 'página',
            'sort_by' => 'campo de ordenamiento',
            'sort_direction' => 'orden',
            'search' => 'termino de búsqueda',

            // Order
            'contact_id' => 'contacto',
            'new_contact' => 'nuevo contacto',
            'new_contact_name' => 'nombre de contacto nuevo',
            'notes' => 'notas',
            'total_net' => 'total neto',
            'order_details' => 'detalles del pedido',
            'order_details.*.product_id' => 'producto',
            'order_details.*.quantity' => 'cantidad',
            'order_details.*.unit_price' => 'precio unitario',
            'order_details.*.percentage_applied' => 'porcentaje aplicado',
            'order_details.*.profit_percentage' => 'porcentaje de ganancia',

            // OrderDetail
            'order_id' => 'ID del pedido',
            'order_detail' => 'detalle del pedido',
            'product_id' => 'producto del detalle',
            'quantity' => 'cantidad del detalle',
            'unit_price' => 'precio unitario del detalle',
            'percentage_applied' => 'porcentaje aplicado del detalle',

            // StockMovement
            'quantity_moved' => 'cantidad movida',
            'movement_type' => 'tipo de movimiento',
            'external_reference' => 'referencia externa',
            'order_detail_id' => 'detalle del pedido',
            'movement_type_id' => 'tipo de movimiento',
        ];
    }
}