<?php

namespace App\Http\Requests\OrganizationRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizationRequestRequest extends FormRequest
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
            'organization_name' => ['required', 'string', 'min:3', 'max:30'],
            'organization_description' => ['required', 'string', 'min:50', 'max:1000'],
            'phone_number' => ['required', 'string', 'min:10', 'max:20', 'regex:/^[\+]?[0-9\s\-\(\)]+$/'],
            'email' => ['required', 'email', 'max:255'],
            'reason' => ['required', 'string', 'min:50', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'organization_name.required' => 'Organization name is required.',
            'organization_name.min' => 'Organization name must be at least 3 characters.',
            'organization_name.max' => 'Organization name cannot exceed 30 characters.',
            'organization_name.regex' => 'Organization name can only contain letters, numbers, spaces, hyphens, and underscores.',
            
            'organization_description.required' => 'Organization description is required.',
            'organization_description.min' => 'Organization description must be at least 50 characters.',
            'organization_description.max' => 'Organization description cannot exceed 1000 characters.',
            
            'phone_number.required' => 'Phone number is required.',
            'phone_number.min' => 'Phone number must be at least 10 characters.',
            'phone_number.max' => 'Phone number cannot exceed 20 characters.',
            'phone_number.regex' => 'Phone number format is invalid. Use only numbers, spaces, hyphens, parentheses, and optional + prefix.',
            
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'Email address cannot exceed 255 characters.',
            
            'reason.required' => 'Reason for creating organization is required.',
            'reason.min' => 'Reason must be at least 50 characters.',
            'reason.max' => 'Reason cannot exceed 500 characters.',
        ];
    }
}
