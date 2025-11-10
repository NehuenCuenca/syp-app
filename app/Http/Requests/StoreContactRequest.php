<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactRequest extends BaseApiRequest
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
            'company_name' => 'required|string|max:255',
            'contact_name' => 'string|max:255',
            'email' => 'nullable|email|unique:contacts,email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'contact_type' => ['required', Rule::in(['Cliente', 'Proveedor'])],
        ];
    }
}