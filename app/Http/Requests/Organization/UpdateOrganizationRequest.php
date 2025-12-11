<?php

namespace App\Http\Requests\Organization;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Convert empty strings to null for URL fields
        $urlFields = ['website_url', 'instagram_url', 'x_url', 'facebook_url'];
        
        foreach ($urlFields as $field) {
            if ($this->has($field) && trim($this->input($field)) === '') {
                $this->merge([$field => null]);
            }
        }
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    }

    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:30',
            'org_email' => 'nullable|email|max:255',
            'desc' => 'nullable|string',
            'motto' => 'nullable|string|max:255',
            'website_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'x_url' => 'nullable|url|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'banner_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'logo_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'Organization name cannot exceed 30 characters.',
            'org_email.email' => 'Please provide a valid email address.',
            'motto.max' => 'Motto cannot exceed 255 characters.',
            'website_url.url' => 'Website URL must be a valid URL.',
            'instagram_url.url' => 'Instagram URL must be a valid URL.',
            'x_url.url' => 'X URL must be a valid URL.',
            'facebook_url.url' => 'Facebook URL must be a valid URL.',
            'banner_img.image' => 'Banner must be an image file.',
            'banner_img.mimes' => 'Banner must be: jpeg, png, jpg, gif, or webp.',
            'banner_img.max' => 'Banner cannot exceed 5MB.',
            'logo_img.image' => 'Logo must be an image file.',
            'logo_img.mimes' => 'Logo must be: jpeg, png, jpg, gif, or webp.',
            'logo_img.max' => 'Logo cannot exceed 2MB.',
        ];
    }
}
