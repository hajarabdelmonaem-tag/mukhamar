<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'phone' => ['required', 'string', 'max:20'],
            'otp' => ['required', 'string', 'digits:4'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
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
            'phone.required' => __('api.validation.phone_required'),
            'otp.required' => __('api.validation.otp_required'),
            'otp.digits' => __('api.validation.otp_digits'),
            'password.required' => __('api.validation.new_password_required'),
            'password.min' => __('api.validation.password_min'),
            'password.confirmed' => __('api.validation.password_confirmation_mismatch'),
        ];
    }
}
