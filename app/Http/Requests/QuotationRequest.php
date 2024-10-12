<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\DB;

class QuotationRequest extends AbstractFormRequest
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
            'client_id' => 'required|integer|exists:clients,id',
            'service_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $clientId = $this->input('client_id');
                        $serviceExists = DB::table('services')
                        ->where('id', $value)
                        ->where('client_id', $clientId)
                        ->exists();

                    if (!$serviceExists) {
                        $fail('The selected service does not belong to the specified client.');
                    }
                }
            ],
            'quotation' => 'required',
        ];
    }
}
