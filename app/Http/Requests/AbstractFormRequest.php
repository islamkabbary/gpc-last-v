<?php
namespace App\Http\Requests;

use Illuminate\Support\Facades\App;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator; // إضافة هذه السطر

abstract class AbstractFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Handle failed validation and provide localized error messages.
     */
    protected function failedValidation(ValidatorContract $validator)
    {
        $locales = ['en', 'ar'];
        $validationMessages = [];

        foreach ($locales as $locale) {
            App::setLocale($locale);

            $validatorInstance = Validator::make($this->all(), $this->rules());

            try {
                $validatorInstance->validate();
            } catch (ValidationException $e) {
                $validationMessages[$locale] = collect($e->errors())->first();
            }
        }

        if ($this->wantsJson()) {
            throw new HttpResponseException(
                response()->json(['messages' => $validationMessages], 422)
            );
        }

        // Call the parent method for non-JSON requests
        parent::failedValidation($validator);
    }
}
