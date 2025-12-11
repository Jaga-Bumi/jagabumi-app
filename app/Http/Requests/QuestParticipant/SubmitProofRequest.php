<?php

namespace App\Http\Requests\QuestParticipant;

use Illuminate\Foundation\Http\FormRequest;

class SubmitProofRequest extends FormRequest
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
            'video' => ['required', 'file', 'mimes:mp4,mov,avi,wmv,flv,mkv', 'max:3145728'], // 3GB = 3145728 KB
            'description' => ['required', 'string', 'min:50', 'max:10000'],
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'video.required' => 'Video proof wajib diupload',
            'video.file' => 'File harus berupa video',
            'video.mimes' => 'Video harus format: mp4, mov, avi, wmv, flv, atau mkv',
            'video.max' => 'Ukuran video maksimal 3GB',
            'description.required' => 'Deskripsi wajib diisi',
            'description.min' => 'Deskripsi minimal 50 karakter',
            'description.max' => 'Deskripsi maksimal 10000 karakter',
        ];
    }
}
