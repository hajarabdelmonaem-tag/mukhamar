<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'accept_terms' => ['required', 'accepted'],
        ];
    }

    /**
     * Get the validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => __('api.validation.full_name_required'),
            'email.required' => __('api.validation.email_required'),
            'email.email' => __('api.validation.email_invalid'),
            'email.unique' => __('api.validation.email_taken'),
            'phone.required' => __('api.validation.phone_required'),
            'phone.unique' => __('api.validation.phone_taken'),
            'password.required' => __('api.validation.password_required'),
            'password.min' => __('api.validation.password_min'),
            'password.confirmed' => __('api.validation.password_confirmation_mismatch'),
            'accept_terms.accepted' => __('api.validation.terms_required'),
        ];
    }
}
