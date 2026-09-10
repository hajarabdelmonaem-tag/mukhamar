<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;

class ContactMessageController extends Controller
{
    /**
     * Display a listing of contact messages.
     */
    public function index()
    {
        $contactMessages = ContactMessage::latest()->paginate(15);

        return view('admin.contact_messages.index', [
            'title' => __('admin.contact_messages.title'),
            'contactMessages' => $contactMessages,
        ]);
    }

    /**
     * Display the specified contact message.
     */
    public function show(ContactMessage $contactMessage)
    {
        if (! $contactMessage->is_read) {
            $contactMessage->update(['is_read' => true]);
        }

        return view('admin.contact_messages.show', [
            'title' => __('admin.contact_messages.title'),
            'contactMessage' => $contactMessage,
        ]);
    }

    /**
     * Mark the specified contact message as read.
     */
    public function markAsRead(ContactMessage $contactMessage)
    {
        $contactMessage->update(['is_read' => true]);

        return back()->with('success', __('admin.contact_messages.marked_read'));
    }

    /**
     * Remove the specified contact message.
     */
    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return redirect()->route('admin.contact-messages.index')
            ->with('success', __('admin.contact_messages.deleted'));
    }
}
