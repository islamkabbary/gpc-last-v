<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Http\Requests\AbstractFormRequest;

class ServiceUpdateRequest extends AbstractFormRequest
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
            'client_id' => ['required', 'exists:clients,id'],
            'type_of_injury' => ['nullable', Rule::in(['Followup', 'Pending', 'Completed'])],
            'level_injury' => ['nullable', Rule::in(['Low', 'Medium', 'High'])],
            'description' => 'nullable|string',

            'visits' => ['nullable', 'array'],

            'visits.*.unit_id' => [
                Rule::requiredIf(function () {
                    return request()->has('visits');
                }),
                'exists:units,id'
            ],

            'visits.*.team_id' => [
                Rule::requiredIf(function () {
                    return request()->has('visits');
                }),
                'exists:teams,id'
            ],

            'visits.*.tools' => [
                Rule::requiredIf(function () {
                    return request()->has('visits');
                }),
                'array'
            ],

            'visits.*.tools.*.tool_id' => [
                Rule::requiredIf(function () {
                    return request()->has('visits.*.tools');
                }),
                'exists:tools,id'
            ],

            'visits.*.tools.*.quantity' => [
                Rule::requiredIf(function () {
                    return request()->has('visits.*.tools');
                }),
                'integer', 'min:1'
            ],

            'visits.*.tools.*.cost' => [
                Rule::requiredIf(function () {
                    return request()->has('visits.*.tools');
                }),
                'numeric', 'min:0'
            ],

            'visits.*.date' => [
                Rule::requiredIf(function () {
                    return request()->has('visits');
                }),
                'date'
            ],

            'visits.*.time' => [
                Rule::requiredIf(function () {
                    return request()->has('visits');
                }),
                'date_format:H:i'
            ],

            'visits.*.images' => ['nullable', 'array'],
            'visits.*.images.*' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif', 'max:2048'],

            'visits.*.sketch' => ['nullable', 'array'],
            'visits.*.sketch.*' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif', 'max:2048'],

            'visits.*.description' => ['nullable', 'string'],
        ];

    }

}
