<?php

namespace App\Http\Requests\Organization;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrganizationRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:30'],
            'handle' => ['required', 'string', 'max:30'],
            'org_email' => ['required', 'email', 'max:255'],
            'desc' => ['required', 'string'],
            'motto' => ['nullable', 'string', 'max:255'],
            'banner_img' => ['nullable', 'image'],
            'logo_img' => ['nullable', 'image'],
            'website_url' => ['nullable', 'url'],
            'instagram_url' => ['nullable', 'url'],
            'x_url' => ['nullable', 'url'],
            'facebook_url' => ['nullable', 'url'],
        ];
    }
}
