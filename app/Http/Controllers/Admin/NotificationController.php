<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications.
     */
    public function index()
    {
        $notifications = Notification::with('user')->latest()->get();

        return view('admin.notifications.index', [
            'title' => __('admin.notifications.title'),
            'notifications' => $notifications,
        ]);
    }

    /**
     * Show the form for creating a new notification.
     */
    public function create()
    {
        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('admin.notifications.create', [
            'title' => __('admin.notifications.send'),
            'users' => $users,
            'selectedUserId' => request('user_id'),
        ]);
    }

    /**
     * Store a newly created notification.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        $notification = new GeneralNotification($data['title'], $data['body']);

        if ($data['user_id']) {
            User::findOrFail($data['user_id'])->notify($notification);
        } else {
            NotificationFacade::send(User::where('is_active', true)->get(), $notification);
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', __('admin.notifications.sent'));
    }

    /**
     * Remove the specified notification.
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();

        return redirect()->route('admin.notifications.index')
            ->with('success', __('admin.notifications.deleted'));
    }
}
