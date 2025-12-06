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
            'organization_name' => ['required', 'string', 'max:30'],
            'organization_description' => ['required', 'string'],
            'phone_number' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'reason' => ['required', 'string'],
        ];
    }
}
