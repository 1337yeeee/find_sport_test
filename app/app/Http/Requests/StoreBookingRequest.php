<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'slots' => ['required', 'array', 'min:1'],
            'slots.*.start_time' => ['required', 'date'],
            'slots.*.end_time' => ['required', 'date', 'after:slots.*.start_time'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            foreach ($this->input('slots', []) as $index => $slot) {
                $start = $slot['start_time'] ?? null;
                $end = $slot['end_time'] ?? null;

                if ($start && $end && $start >= $end) {
                    $validator->errors()->add("slots.$index", "В слоте $index время начала должно быть меньше времени окончания.");
                }
            }
        });
    }
}
