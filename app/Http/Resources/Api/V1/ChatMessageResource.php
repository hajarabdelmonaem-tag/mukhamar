<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * "key" is the name of the tool the reply is based on (for example
     * search_products) and "data" holds the records it found, in the same
     * format as the rest of the API. Both are empty when no tool was used.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'role' => $this->role,
            'text' => $this->text,
            'data' => $this->data ?? [],
            'created_at' => ($this->created_at ?? now())->format('Y-m-d H:i:s'),
        ];
    }
}
