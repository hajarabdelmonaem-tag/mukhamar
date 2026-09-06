<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UpdatePasswordRequest;
use App\Http\Requests\Api\V1\UpdateProfileRequest;
use App\Http\Resources\Api\V1\AddressResource;
use App\Http\Resources\Api\V1\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show the authenticated user's profile with supporting data.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load('addresses');

        return response()->json([
            'user' => new UserResource($user),
            'stats' => [
                'orders_count' => $user->orders()->count(),
                'addresses_count' => $user->addresses()->count(),
                'wishlist_count' => $user->wishlist()->count(),
            ],
            'addresses' => AddressResource::collection($user->addresses),
        ]);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->only(['name', 'email', 'phone']);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return response()->json([
            'message' => __('api.profile.updated'),
            'user' => new UserResource($user->fresh()),
        ]);
    }

    /**
     * Update the authenticated user's password.
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $request->user()->update([
            'password' => $request->password,
        ]);

        return response()->json([
            'message' => __('api.profile.password_updated'),
        ]);
    }

    /**
     * Delete the authenticated user's account.
     */
    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'message' => __('api.profile.deleted'),
        ]);
    }
}
