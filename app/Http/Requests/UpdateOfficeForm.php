<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateOfficeForm extends FormRequest
{
    /**
     * Constants to map the request key => regex.
     */
    public const OFFICE_NAME_KEY       = 'name';
    public const OFFICE_ADDRESS_KEY    = 'address';
    private const OFFICE_NAME_REGEX    = 'max:255';
    private const OFFICE_ADDRESS_REGEX = 'required_if:name,""|max:255';

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
            self::OFFICE_NAME_KEY => self::OFFICE_NAME_REGEX,
            self::OFFICE_ADDRESS_KEY => self::OFFICE_ADDRESS_REGEX,
        ];
    }

    /**
     * Custom message for the address require_if error.
     */
    public function messages(): array
    {
        return [
            'address.required_if' => 'The address field is required when the name is null.'
        ];
    }

    /**
     * Returns a json response with validation request model errors.
     */
    protected function failedValidation(Validator $validator): void
    {
        $validatorException = new ValidationException($validator);

        throw new HttpResponseException(
            response()->json(['errors' => $validatorException->errors()], Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
