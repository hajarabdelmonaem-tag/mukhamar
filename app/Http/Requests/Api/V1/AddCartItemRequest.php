<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class AddCartItemRequest extends FormRequest
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
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'product_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
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
            'product_id.required' => __('api.validation.product_required'),
            'product_id.exists' => __('api.validation.product_not_found'),
            'product_variant_id.exists' => __('api.validation.variant_not_found'),
            'quantity.required' => __('api.validation.quantity_required'),
            'quantity.min' => __('api.validation.quantity_min'),
            'quantity.max' => __('api.validation.quantity_max'),
        ];
    }
}
