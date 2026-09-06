<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'address_id' => ['required', 'integer', 'exists:addresses,id'],
            'payment_method' => ['required', 'string', 'in:VISA,MADA,Apple Pay,card'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:2000'],
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
            'address_id.required' => __('api.validation.address_required'),
            'address_id.exists' => __('api.validation.address_not_found'),
            'payment_method.required' => __('api.validation.payment_method_required'),
            'payment_method.in' => __('api.validation.payment_method_unsupported'),
            'coupon_code.max' => __('api.validation.coupon_code_too_long'),
            'notes.max' => __('api.validation.notes_too_long'),
        ];
    }
}
