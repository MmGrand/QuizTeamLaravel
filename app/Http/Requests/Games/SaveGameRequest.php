<?php

namespace App\Http\Requests\Games;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveGameRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'venue_id' => ['required', 'integer', Rule::exists('venues', 'id')],
            'organizer_id' => ['required', 'integer', Rule::exists('organizers', 'id')],
            'title' => ['nullable', 'string', 'max:255'],
            'played_at' => ['required', 'date'],
            'score' => ['required', 'numeric', 'min:0', 'max:9999.99', 'decimal:0,2'],
            'place' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'participants' => ['array'],
            'participants.*' => [
                'integer',
                Rule::exists('team_members', 'user_id')
                    ->where('team_id', $this->user()->current_team_id),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'participants.*.exists' => __('Only team members can be recorded as participants.'),
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'venue_id' => __('venue'),
            'organizer_id' => __('organizer'),
            'played_at' => __('date played'),
        ];
    }
}
