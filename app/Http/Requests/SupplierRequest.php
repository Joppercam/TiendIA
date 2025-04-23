<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ];
        
        // Para actualización, validamos que el email sea único excepto para el registro actual
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['email'] = 'nullable|email|max:255|unique:suppliers,email,' . $this->route('supplier')->id;
        } else {
            $rules['email'] = 'nullable|email|max:255|unique:suppliers,email';
        }
        
        return $rules;
    }
    
    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del proveedor es obligatorio.',
            'email.unique' => 'Este correo electrónico ya está registrado para otro proveedor.',
        ];
    }
}