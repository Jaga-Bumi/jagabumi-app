<?php

namespace App\Http\Requests\QuestParticipant;

use Illuminate\Foundation\Http\FormRequest;

class ReviewSubmissionRequest extends FormRequest
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
            'status' => ['required', 'in:APPROVED,REJECTED'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Status review wajib dipilih.',
            'status.in' => 'Status harus APPROVED atau REJECTED.',
            'notes.max' => 'Catatan maksimal 500 karakter.',
        ];
    }
}
