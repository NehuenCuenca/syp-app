<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_contact' => 'sometimes|required|integer|exists:contacts,id',
            'actual_delivery_date' => 'nullable|date',
            'order_type' => 'missing|in:Compra,Venta',
            'order_status' => 'sometimes|required|in:Pendiente,Completado,Cancelado,Devuelto',
            'notes' => 'nullable|string|max:1000',
            'total_net' => 'sometimes|numeric|min:0',
            
            // Validaciones para los detalles del pedido (opcionales en actualización)
            'order_details' => 'missing|array|min:1',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'id_contact.required' => 'El contacto es obligatorio.',
            'id_contact.integer' => 'El contacto debe ser un número entero.',
            'id_contact.exists' => 'El contacto seleccionado no existe.',
            
            'actual_delivery_date.date' => 'La fecha real de entrega debe ser una fecha válida.',
            
            'order_type.missing' => 'El tipo de pedido no puede ser editado.',
            
            'order_status.required' => 'El estado del pedido es obligatorio.',
            'order_status.in' => 'El estado del pedido debe ser: Pendiente, Completado, Cancelado o Devuelto.',
            
            'notes.string' => 'Las notas deben ser texto.',
            'notes.max' => 'Las notas no pueden exceder los 1000 caracteres.',

            'total_net.numeric' => 'El precio total debe ser un número.',
            'total_net.min' => 'El precio total no puede ser negativo.',
            
            // Mensajes para detalles del pedido
            'order_details.missing' => 'Los detalles del pedido no pueden ser editados desde esta ruta.',
            'order_details.array' => 'Los detalles del pedido deben ser un array.',
            'order_details.min' => 'Debe incluir al menos un detalle en el pedido.',
            
            'order_details.*.id_product.required_with' => 'El producto es obligatorio en cada detalle.',
            'order_details.*.id_product.integer' => 'El ID del producto debe ser un número entero.',
            'order_details.*.id_product.exists' => 'El producto seleccionado no existe.',
            
            'order_details.*.quantity.required_with' => 'La cantidad es obligatoria en cada detalle.',
            'order_details.*.quantity.integer' => 'La cantidad debe ser un número entero.',
            'order_details.*.quantity.min' => 'La cantidad debe ser al menos 1.',
            
            'order_details.*.unit_price_at_order.required_with' => 'El precio unitario es obligatorio en cada detalle.',
            'order_details.*.unit_price_at_order.numeric' => 'El precio unitario debe ser un número.',
            'order_details.*.unit_price_at_order.min' => 'El precio unitario no puede ser negativo.',
            'order_details.*.unit_price_at_order.max' => 'El precio unitario no puede exceder 999,999.99.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validación personalizada para cambios de estado
            if ($this->has('order_status')) {
                $this->validateStatusChange($validator);
            }
        });
    }

    /**
     * Validar cambios de estado del pedido
     */
    private function validateStatusChange($validator)
    {
        $order = $this->route('order');
        $newStatus = $this->input('order_status');
        $currentStatus = $order->order_status;

        // Permitir todos los cambios de estado con advertencias, pero no bloquear
        // La lógica de negocio se maneja en el controlador
        
        // Validar que si se marca como completado, debe tener fecha de entrega
        if ($newStatus === 'Completado' && !$this->has('actual_delivery_date') && !$order->actual_delivery_date) {
            $validator->errors()->add(
                'actual_delivery_date',
                'La fecha real de entrega es obligatoria cuando el pedido se marca como completado.'
            );
        }

        // Validar que el nuevo estado corresponda a las transiciones válidas
        if( !in_array($newStatus, $order->getShowValidTransitionsAttribute()) ){
            $validator->errors()->add(
                'order_status',
                'El estado del pedido no puede ser cambiado a '.$newStatus.'. Solo se pueden cambiar a: '.implode(', ', $order->getShowValidTransitionsAttribute())
            );
        }
    }


    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = response()->json([
            'message' => 'Los datos proporcionados no son válidos.',
            'errors' => $validator->errors()
        ], 422);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}