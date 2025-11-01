<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateContactRequest extends BaseApiRequest
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
        $contactId = $this->route('contact')->id;

        return [
            'company_name' => 'string|max:255',
            'contact_name' => 'string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('contacts', 'email')->ignore($contactId)
            ],
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'contact_type' => [Rule::in(['Cliente', 'Proveedor', 'Empleado', 'Otro'])],
            'registered_at' => 'date',
        ];
    }
}