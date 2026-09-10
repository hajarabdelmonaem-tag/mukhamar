<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    /**
     * Display a listing of banners.
     */
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->get();

        return view('admin.banners.index', [
            'title' => __('admin.banners.title'),
            'banners' => $banners,
        ]);
    }

    /**
     * Show the form for creating a new banner.
     */
    public function create()
    {
        return view('admin.banners.create', ['title' => __('admin.banners.add')]);
    }

    /**
     * Store a newly created banner.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('banners', 'public');
        }

        Banner::create($data);

        return redirect()->route('admin.banners.index')
            ->with('success', __('admin.banners.created'));
    }

    /**
     * Show the form for editing a banner.
     */
    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', [
            'title' => __('admin.banners.edit'),
            'banner' => $banner,
        ]);
    }

    /**
     * Update the specified banner.
     */
    public function update(Request $request, Banner $banner)
    {
        $data = $request->validate([
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')
            ->with('success', __('admin.banners.updated'));
    }

    /**
     * Remove the specified banner.
     */
    public function destroy(Banner $banner)
    {
        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', __('admin.banners.deleted'));
    }
}
