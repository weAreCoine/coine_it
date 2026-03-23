<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HealthCheckQuizRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|indisposable|max:255',
            'phone' => 'required|string|max:50',
            'url' => 'required|url|max:255',
            'marketingConsent' => 'accepted',
            'answers' => 'required|array',
            'answers.*.value' => 'required|string',
            'answers.*.points' => 'required|integer|min:0',
            'score' => 'required|integer|min:0|max:100',
        ];
    }
}
