<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'specialization' => ['required', 'string', 'max:120'],
            'polyclinic' => ['nullable', 'string', 'max:120'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'photo_url' => ['nullable', 'url', 'max:500'],
            'is_active' => ['boolean'],

            'leave_start_date' => ['nullable', 'date'],
            'leave_end_date' => ['nullable', 'date', 'after_or_equal:leave_start_date'],
            'leave_reason' => ['nullable', 'string', 'max:255'],

            'schedules' => ['array'],
            'schedules.*.day_of_week' => ['required_with:schedules', 'integer', 'between:0,6'],
            'schedules.*.start_time' => ['required_with:schedules', 'date_format:H:i'],
            'schedules.*.end_time' => ['required_with:schedules', 'date_format:H:i', 'after:schedules.*.start_time'],
            'schedules.*.room' => ['nullable', 'string', 'max:60'],
            'schedules.*.is_active' => ['boolean'],
        ];
    }
}
