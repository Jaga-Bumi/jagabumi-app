<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates Web3Auth authentication requests
 */
class Web3LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules for Web3Auth login
     */
    public function rules(): array
    {
        return [
            'wallet_address' => [
                'required',
                'string',
                'size:42',
                'regex:/^0x[a-fA-F0-9]{40}$/',
            ],
            'user_info' => [
                'required',
                'array',
            ],
            'user_info.email' => [
                'required',
                'email',
                'max:255',
            ],
            'user_info.name' => [
                'nullable',
                'string',
                'max:255',
            ],
            'user_info.profileImage' => [
                'nullable',
                'url',
            ],
            'user_info.verifierId' => [
                'nullable',
                'string',
            ],
            'user_info.typeOfLogin' => [
                'nullable',
                'string',
                'in:google,github,facebook,twitter,email',
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules
     */
    public function messages(): array
    {
        return [
            'wallet_address.required' => 'Wallet address is required.',
            'wallet_address.size' => 'Invalid wallet address format.',
            'wallet_address.regex' => 'Wallet address must be a valid Ethereum address.',
            'user_info.required' => 'User information is required.',
            'user_info.email.required' => 'Email address is required.',
            'user_info.email.email' => 'Please provide a valid email address.',
        ];
    }
}
