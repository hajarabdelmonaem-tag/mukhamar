<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Display a listing of admin users.
     */
    public function index()
    {
        $admins = User::where('is_admin', true)->latest()->paginate(15);

        return view('admin.admins.index', [
            'title' => __('admin.admins.title'),
            'admins' => $admins,
        ]);
    }

    /**
     * Show the form for creating a new admin account.
     */
    public function create()
    {
        return view('admin.admins.create', [
            'title' => __('admin.admins.add'),
        ]);
    }

    /**
     * Store a newly created admin account.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_admin' => true,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.admins.index')
            ->with('success', __('admin.admins.added'));
    }

    /**
     * Show the form for editing an admin account.
     */
    public function edit(User $user)
    {
        abort_unless($user->is_admin, 404);

        return view('admin.admins.edit', [
            'title' => __('admin.admins.edit'),
            'admin' => $user,
        ]);
    }

    /**
     * Update an admin account.
     */
    public function update(Request $request, User $user)
    {
        abort_unless($user->is_admin, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();

        return redirect()->route('admin.admins.index')
            ->with('success', __('admin.admins.updated'));
    }

    /**
     * Remove the specified admin role.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', __('admin.admins.cannot_remove_self'));
        }

        $user->update(['is_admin' => false]);

        return redirect()->route('admin.admins.index')
            ->with('success', __('admin.admins.removed'));
    }
}
