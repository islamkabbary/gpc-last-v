<?php

namespace App\Http\Requests;

use App\Models\Contact;
use Illuminate\Foundation\Http\FormRequest;

class ClientUpdateRequest extends AbstractFormRequest
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
        $clientId = $this->route('client')->id;

        $phoneIds = Contact::where('client_id', $clientId)->where('contact_type_id', 'phone')->pluck('id')->toArray();
        $emailIds = Contact::where('client_id', $clientId)->where('contact_type_id', 'email')->pluck('id')->toArray();

        return [
            'name' => 'required|string|max:255',
            'client_id' => 'required|integer|unique:clients,client_id,' . $clientId,
            'status' => 'required|string|in:Followup,Pending,Completed',
            'phone' => 'required|array',
            'phone.*' => [
                'required',
                'string',
                'regex:/^[0-9]{10,15}$/',
                'unique:contacts,value,' . implode(',', $phoneIds)
            ],
            'email' => 'required|array',
            'email.*' => [
                'required',
                'email',
                'max:255',
                'unique:contacts,value,' . implode(',', $emailIds)
            ],
            'description' => 'nullable|string',
            'units' => 'nullable|array',
            'units.*.name' => 'required_with:units|string|max:255',
            'units.*.address' => 'required_with:units|string',
            'units.*.lat' => 'required_with:units|numeric',
            'units.*.lang' => 'required_with:units|numeric',
            'units.*.note_unit' => 'nullable|string',
        ];
    }
}
