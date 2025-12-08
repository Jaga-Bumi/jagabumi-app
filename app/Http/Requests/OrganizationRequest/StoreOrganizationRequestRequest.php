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
            'organization_type' => ['required', 'string', 'in:NGO,Community Group,Educational,Corporate CSR,Environmental,Other'],
            'organization_description' => ['required', 'string', 'min:50', 'max:1000'],
            'planned_activities' => ['required', 'string', 'min:50', 'max:1000'],
            'reason' => ['required', 'string', 'min:50', 'max:500'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'x_url' => ['nullable', 'url', 'max:255'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
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
            
            'organization_type.required' => 'Organization type is required.',
            'organization_type.in' => 'Please select a valid organization type.',
            
            'organization_description.required' => 'Organization description is required.',
            'organization_description.min' => 'Organization description must be at least 50 characters.',
            'organization_description.max' => 'Organization description cannot exceed 1000 characters.',
            
            'planned_activities.required' => 'Planned activities is required.',
            'planned_activities.min' => 'Planned activities must be at least 50 characters.',
            'planned_activities.max' => 'Planned activities cannot exceed 1000 characters.',
            
            'reason.required' => 'Reason for creating organization is required.',
            'reason.min' => 'Reason must be at least 50 characters.',
            'reason.max' => 'Reason cannot exceed 500 characters.',
            
            'website_url.url' => 'Website URL must be a valid URL.',
            'instagram_url.url' => 'Instagram URL must be a valid URL.',
            'x_url.url' => 'X (Twitter) URL must be a valid URL.',
            'facebook_url.url' => 'Facebook URL must be a valid URL.',
        ];
    }
}
