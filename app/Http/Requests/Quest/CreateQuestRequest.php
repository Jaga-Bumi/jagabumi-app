<?php

namespace App\Http\Requests\Quest;

use Illuminate\Foundation\Http\FormRequest;

class CreateQuestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            'title' => ['required', 'string', 'max:255'],
            'desc'  => ['required', 'string'],
            'banner' => ['required', 'image', 'max:2048'],
            'location_name' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius_meter' => ['nullable', 'integer', 'min:10'],
            'liveness_code' => ['nullable', 'string', 'max:255'],
            'registration_start_at' => ['required', 'date'],
            // 'registration_end_at' => ['required', 'date', 'after_or_equal:registration_start_at'],
            'registration_end_at' => ['required', 'date'],
            'quest_start_at' => ['required', 'date'],
            // 'quest_end_at' => ['required', 'date', 'after_or_equal:quest_start_at'],
            'quest_end_at' => ['required', 'date'],
            'judging_start_at' => ['required', 'date'],
            // 'judging_end_at' => ['required', 'date', 'after_or_equal:judging_start_at'],
            'judging_end_at' => ['required', 'date'],
            'prize_distribution_date' => ['required', 'date'],
            'participant_limit' => ['required', 'integer', 'min:1'],
            'winner_limit' => ['required', 'integer', 'min:1', 'lte:participant_limit'],
            'org_id' => ['required', 'exists:organizations,id'],
            'cert_name' => ['required', 'string', 'max:255'],
            'cert_image' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'cert_description' => ['required', 'string', 'max:5000'],
            'coupon_name' => ['nullable', 'string', 'max:255'],
            'coupon_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'coupon_description' => ['nullable', 'string', 'max:5000'],
        ];

    }
}
