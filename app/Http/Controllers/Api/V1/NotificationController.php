<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * List the authenticated user's notifications and mark the returned page as read.
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = Notification::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate($request->integer('per_page', 15));

        $readAt = now();

        Notification::query()
            ->whereKey($notifications->getCollection()->modelKeys())
            ->whereNull('read_at')
            ->update(['read_at' => $readAt]);

        $notifications->getCollection()->each(function (Notification $notification) use ($readAt): void {
            $notification->forceFill(['read_at' => $readAt]);
        });

        return response()->json([
            'data' => NotificationResource::collection($notifications),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
            'unread_count' => Notification::where('user_id', $request->user()->id)
                ->where('read_at', null)
                ->count(),
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(Request $request, Notification $notification): JsonResponse
    {
        $this->authorizeNotification($request, $notification);

        if (! $notification->isRead()) {
            $notification->update(['read_at' => now()]);
        }

        return response()->json([
            'message' => __('api.notification.updated'),
            'data' => new NotificationResource($notification),
        ]);
    }

    /**
     * Mark all of the user's notifications as read.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        Notification::query()
            ->where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'message' => __('api.notification.all_read'),
        ]);
    }

    /**
     * Ensure the notification belongs to the authenticated user.
     */
    private function authorizeNotification(Request $request, Notification $notification): void
    {
        abort_unless($notification->user_id === $request->user()->id, 403);
    }
}
