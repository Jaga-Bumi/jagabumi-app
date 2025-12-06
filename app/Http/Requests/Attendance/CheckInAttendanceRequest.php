<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class CheckInAttendanceRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'proof_photo' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:5120'], // 5MB max
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'latitude' => 'latitude',
            'longitude' => 'longitude',
            'proof_photo' => 'foto bukti',
            'notes' => 'catatan',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'latitude.required' => 'Latitude lokasi wajib diisi.',
            'latitude.numeric' => 'Latitude harus berupa angka.',
            'latitude.between' => 'Latitude harus antara -90 dan 90.',
            'longitude.required' => 'Longitude lokasi wajib diisi.',
            'longitude.numeric' => 'Longitude harus berupa angka.',
            'longitude.between' => 'Longitude harus antara -180 dan 180.',
            'proof_photo.required' => 'Foto bukti check-in wajib diupload.',
            'proof_photo.image' => 'File harus berupa gambar.',
            'proof_photo.mimes' => 'Foto harus berformat jpeg, jpg, atau png.',
            'proof_photo.max' => 'Ukuran foto maksimal 5MB.',
            'notes.max' => 'Catatan maksimal 500 karakter.',
        ];
    }
}
