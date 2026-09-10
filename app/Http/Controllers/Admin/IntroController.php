<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Intro;
use Illuminate\Http\Request;

class IntroController extends Controller
{
    /**
     * Display a listing of intros.
     */
    public function index()
    {
        $intros = Intro::orderBy('sort_order')->get();

        return view('admin.intros.index', [
            'title' => __('admin.intros.title'),
            'intros' => $intros,
        ]);
    }

    /**
     * Show the form for creating a new intro.
     */
    public function create()
    {
        return view('admin.intros.create', ['title' => __('admin.intros.add')]);
    }

    /**
     * Store a newly created intro.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string', 'max:255'],
            'title.ar' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['boolean'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('intros', 'public');
        }

        $data['is_active'] = $request->boolean('is_active');

        Intro::create($data);

        return redirect()->route('admin.intros.index')
            ->with('success', __('admin.intros.created'));
    }

    /**
     * Show the form for editing an intro.
     */
    public function edit(Intro $intro)
    {
        return view('admin.intros.edit', [
            'title' => __('admin.intros.edit'),
            'intro' => $intro,
        ]);
    }

    /**
     * Update the specified intro.
     */
    public function update(Request $request, Intro $intro)
    {
        $data = $request->validate([
            'title' => ['required', 'array'],
            'title.en' => ['required', 'string', 'max:255'],
            'title.ar' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['boolean'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('intros', 'public');
        }

        $data['is_active'] = $request->boolean('is_active');

        $intro->update($data);

        return redirect()->route('admin.intros.index')
            ->with('success', __('admin.intros.updated'));
    }

    /**
     * Remove the specified intro.
     */
    public function destroy(Intro $intro)
    {
        $intro->delete();

        return redirect()->route('admin.intros.index')
            ->with('success', __('admin.intros.deleted'));
    }
}
