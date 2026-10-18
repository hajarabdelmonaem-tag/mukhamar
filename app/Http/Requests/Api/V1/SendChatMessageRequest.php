<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class SendChatMessageRequest extends FormRequest
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
            'message' => ['required', 'string', 'max:'.config('services.chatbot.max_message_length')],
            'conversation_id' => ['nullable', 'uuid', 'exists:chat_conversations,uuid'],
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
            'message.required' => __('api.validation.message_required'),
            'message.max' => __('api.validation.chat_message_too_long'),
            'conversation_id.uuid' => __('api.validation.chat_conversation_invalid'),
            'conversation_id.exists' => __('api.validation.chat_conversation_invalid'),
        ];
    }
}
