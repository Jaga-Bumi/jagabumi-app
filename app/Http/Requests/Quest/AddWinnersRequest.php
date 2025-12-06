<?php

namespace App\Http\Requests\Quest;

use Illuminate\Foundation\Http\FormRequest;

class AddWinnersRequest extends FormRequest
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
            'winners' => ['required', 'array', 'min:1'],
            'winners.*.user_id' => ['required', 'exists:users,id'],
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'winners.required' => 'Daftar pemenang harus diisi.',
            'winners.array' => 'Daftar pemenang harus berupa array.',
            'winners.min' => 'Minimal harus ada 1 pemenang.',
            'winners.*.user_id.required' => 'User ID harus diisi untuk setiap pemenang.',
            'winners.*.user_id.exists' => 'User tidak ditemukan.',
        ];
    }
}
