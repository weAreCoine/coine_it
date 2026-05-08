<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CookieConsentRequest extends FormRequest
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
            'consent_id' => ['required', 'uuid'],
            'marketing' => ['required', 'boolean'],
            'analytics' => ['required', 'boolean'],
            'choice_type' => ['required', Rule::in(['accept_all', 'reject_all', 'custom', 'update'])],
            'path' => ['required', 'string', 'max:2048'],
        ];
    }
}
