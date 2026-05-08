<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\NotFakeEmail;
use Illuminate\Foundation\Http\FormRequest;

class ContactFormRequest extends FormRequest
{
    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.indisposable' => 'Non sono ammessi indirizzi email temporanei.',
        ];
    }

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
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => ['required', 'email', 'indisposable', new NotFakeEmail],
            'phone' => 'sometimes|nullable',
            'message' => 'required',
            'termsAccepted' => 'accepted',
            'newsletterOptIn' => 'sometimes|boolean',
            'metaEventId' => 'required|uuid',
        ];
    }
}
