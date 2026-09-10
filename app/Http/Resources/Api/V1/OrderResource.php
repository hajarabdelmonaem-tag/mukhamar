<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'order_no' => $this->order_no,
            'status' => $this->status,
            'status_label' => $this->statusLabel(),
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'payment_status_label' => $this->paymentStatusLabel(),
            'subtotal' => (float) $this->subtotal,
            'shipping' => (float) $this->shipping,
            'discount' => (float) $this->discount,
            'tax' => (float) $this->tax,
            'total' => (float) $this->total,
            'notes' => $this->notes,
            'placed_at' => $this->placed_at?->toISOString(),
            'confirmed_at' => $this->confirmed_at?->toISOString(),
            'shipped_at' => $this->shipped_at?->toISOString(),
            'delivered_at' => $this->delivered_at?->toISOString(),
            'timeline' => $this->timeline(),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'address' => new AddressResource($this->whenLoaded('address')),
        ];
    }

    /**
     * Human-readable localized label for the order status.
     */
    private function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => __('api.order.status.pending'),
            'processing' => __('api.order.status.processing'),
            'in_transit' => __('api.order.status.in_transit'),
            'delivered' => __('api.order.status.delivered'),
            'cancelled' => __('api.order.status.cancelled'),
            default => $this->status,
        };
    }

    /**
     * Human-readable localized label for the payment status.
     */
    private function paymentStatusLabel(): string
    {
        return match ($this->payment_status) {
            'pending' => __('api.order.payment_status.pending'),
            'paid' => __('api.order.payment_status.paid'),
            'failed' => __('api.order.payment_status.failed'),
            'refunded' => __('api.order.payment_status.refunded'),
            default => (string) $this->payment_status,
        };
    }

    /**
     * Build the order tracking timeline matching the mockup.
     *
     * @return array<int, array<string, mixed>>
     */
    private function timeline(): array
    {
        return [
            [
                'key' => 'confirmed',
                'label' => __('api.order.timeline.confirmed'),
                'timestamp' => $this->confirmed_at?->toISOString(),
            ],
            [
                'key' => 'in_transit',
                'label' => __('api.order.timeline.in_transit'),
                'timestamp' => $this->shipped_at?->toISOString(),
            ],
            [
                'key' => 'delivered',
                'label' => __('api.order.timeline.delivered'),
                'timestamp' => $this->delivered_at?->toISOString(),
            ],
        ];
    }
}
