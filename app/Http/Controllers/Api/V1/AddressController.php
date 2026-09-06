<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreAddressRequest;
use App\Http\Requests\Api\V1\UpdateAddressRequest;
use App\Http\Resources\Api\V1\AddressResource;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * List the authenticated user's saved addresses.
     */
    public function index(Request $request): JsonResponse
    {
        $addresses = $request->user()
            ->addresses()
            ->orderByDesc('is_default')
            ->get();

        return response()->json([
            'data' => AddressResource::collection($addresses),
        ]);
    }

    /**
     * Store a new address for the authenticated user.
     */
    public function store(StoreAddressRequest $request): JsonResponse
    {
        $firstAddress = $request->user()->addresses()->count() === 0;

        $address = $request->user()->addresses()->create([
            'label' => $request->label ?? 'home',
            'recipient_name' => $request->recipient_name,
            'street' => $request->street,
            'district' => $request->district,
            'city' => $request->city ?? __('api.address.default_city'),
            'building' => $request->building,
            'postal_code' => $request->postal_code,
            'phone' => $request->phone,
            'is_default' => $request->boolean('is_default', $firstAddress),
        ]);

        if ($address->is_default) {
            $this->setDefault($address);
        }

        return response()->json([
            'message' => __('api.address.stored'),
            'data' => new AddressResource($address),
        ], 201);
    }

    /**
     * Update an existing address.
     */
    public function update(UpdateAddressRequest $request, Address $address): JsonResponse
    {
        $this->authorizeAddress($request, $address);

        $address->update($request->only([
            'label', 'recipient_name', 'street', 'district', 'city',
            'building', 'postal_code', 'phone',
        ]) + ['is_default' => $request->boolean('is_default', $address->is_default)]);

        if ($address->is_default) {
            $this->setDefault($address);
        }

        return response()->json([
            'message' => __('api.address.updated'),
            'data' => new AddressResource($address->fresh()),
        ]);
    }

    /**
     * Delete an address.
     */
    public function destroy(Request $request, Address $address): JsonResponse
    {
        $this->authorizeAddress($request, $address);

        $address->delete();

        return response()->json([
            'message' => __('api.address.deleted'),
        ]);
    }

    /**
     * Mark an address as the default shipping address.
     */
    public function makeDefault(Request $request, Address $address): JsonResponse
    {
        $this->authorizeAddress($request, $address);

        $this->setDefault($address);

        return response()->json([
            'message' => __('api.address.made_default'),
            'data' => new AddressResource($address->fresh()),
        ]);
    }

    /**
     * Clear other default addresses and set the given one as default.
     */
    private function setDefault(Address $address): void
    {
        $address->user->addresses()
            ->whereKeyNot($address->id)
            ->update(['is_default' => false]);

        $address->forceFill(['is_default' => true])->save();
    }

    /**
     * Ensure the address belongs to the authenticated user.
     */
    private function authorizeAddress(Request $request, Address $address): void
    {
        abort_unless($address->user_id === $request->user()->id, 403);
    }
}
