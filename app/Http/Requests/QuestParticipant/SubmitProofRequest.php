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
            'proof_latitude' => ['required', 'numeric', 'between:-90,90'],
            'proof_longitude' => ['required', 'numeric', 'between:-180,180'],
            'proof_video' => ['nullable', 'file', 'mimetypes:video/mp4,video/mpeg,video/quicktime,video/x-msvideo', 'max:102400'], // 100MB max
            'proof_description' => ['required', 'string', 'min:20', 'max:1000'],
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'proof_latitude.required' => 'Latitude lokasi wajib diisi.',
            'proof_latitude.numeric' => 'Latitude harus berupa angka.',
            'proof_latitude.between' => 'Latitude tidak valid (harus antara -90 hingga 90).',
            'proof_longitude.required' => 'Longitude lokasi wajib diisi.',
            'proof_longitude.numeric' => 'Longitude harus berupa angka.',
            'proof_longitude.between' => 'Longitude tidak valid (harus antara -180 hingga 180).',
            'proof_video.mimetypes' => 'Video harus berformat MP4, MPEG, MOV, atau AVI.',
            'proof_video.max' => 'Ukuran video maksimal 100MB.',
            'proof_description.required' => 'Deskripsi bukti wajib diisi.',
            'proof_description.min' => 'Deskripsi minimal 20 karakter.',
            'proof_description.max' => 'Deskripsi maksimal 1000 karakter.',
        ];
    }
}
