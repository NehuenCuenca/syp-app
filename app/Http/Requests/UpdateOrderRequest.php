<?php

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateOrderRequest extends BaseApiRequest
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
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->validationErrorResponse(
                $validator->errors()->toArray(),
                'Los datos proporcionados no son válidos'
            )
        );
    }
}