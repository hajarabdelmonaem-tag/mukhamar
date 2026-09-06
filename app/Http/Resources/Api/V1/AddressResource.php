<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'recipient_name' => $this->recipient_name,
            'street' => $this->street,
            'district' => $this->district,
            'city' => $this->city,
            'building' => $this->building,
            'postal_code' => $this->postal_code,
            'phone' => $this->phone,
            'is_default' => $this->is_default,
        ];
    }
}
