<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Http\Requests\AbstractFormRequest;

class ServiceRequest extends AbstractFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow all users to make this request (modify if needed)
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'services' => 'required|array',
            'services.*.client_id' => ['required', 'exists:clients,id'],
            'services.*.type_of_injury' => ['nullable', Rule::in(['Callout', 'Followup', 'Pending', 'Completed'])],
            'services.*.level_injury' => ['nullable', Rule::in(['Low', 'Medium', 'High'])],
            'services.*.description' => 'nullable|string',

            'services.*.visits' => 'nullable|array',
            'services.*.visits.*.unit_id' => [
                Rule::requiredIf(fn($input) => !empty($input->visits)),
                'exists:units,id'
            ],
            'services.*.visits.*.team_id' => [
                Rule::requiredIf(fn($input) => !empty($input->visits)),
                'exists:teams,id'
            ],
            'services.*.visits.*.tools' => [
                Rule::requiredIf(fn($input) => !empty($input->visits)),
                'array'
            ],
            'services.*.visits.*.tools.*.tool_id' => [
                Rule::requiredIf(fn($input) => !empty($input->visits)),
                'exists:tools,id'
            ],
            'services.*.visits.*.tools.*.quantity' => [
                Rule::requiredIf(fn($input) => !empty($input->visits)),
                'integer',
                'min:1'
            ],
            'services.*.visits.*.tools.*.cost' => [
                Rule::requiredIf(fn($input) => !empty($input->visits)),
                'numeric',
                'min:0'
            ],
            'services.*.visits.*.date' => [
                Rule::requiredIf(fn($input) => !empty($input->visits)),
                'date'
            ],
            'services.*.visits.*.time' => [
                Rule::requiredIf(fn($input) => !empty($input->visits)),
                'date_format:H:i'
            ],

            'services.*.visits.*.images' => ['nullable', 'array'],
            'services.*.visits.*.sketch' => ['nullable', 'array'],
            'services.*.visits.*.description' => ['nullable', 'string'],
        ];
    }
}
