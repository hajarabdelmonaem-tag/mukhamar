@extends('layouts.app')

@section('content')
<x.common.page-breadcrumb :title="__('admin.socials.edit')" :breadcrumbs="[__('admin.socials.title') => route('admin.socials.index'), __('admin.actions.edit') => null]" />

@if ($errors->any())
    <div class="mb-4 rounded-lg border border-error-200 bg-error-50 p-4 text-sm text-error-700 dark:border-error-800 dark:bg-error-950 dark:text-error-300">
        <ul class="list-disc space-y-1 ps-4">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $socialIcon = !empty($social['icon'])
        ? (str_starts_with($social['icon'], 'http') ? $social['icon'] : \Illuminate\Support\Facades\Storage::url($social['icon']))
        : null;
@endphp

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
        <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('admin.socials.info') }}</h3>
    </div>
    <form method="POST" action="{{ route('admin.socials.update', $social['id']) }}" enctype="multipart/form-data" class="p-6">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.socials.name_en') }}</label>
                <input type="text" name="name[en]" value="{{ old('name.en', $social['name']['en'] ?? '') }}" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.socials.name_ar') }}</label>
                <input type="text" name="name[ar]" value="{{ old('name.ar', $social['name']['ar'] ?? '') }}" dir="rtl" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.socials.link') }}</label>
                <input type="url" name="link" value="{{ old('link', $social['link'] ?? '') }}" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" placeholder="https://..." />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.categories.sort_order') }}</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $social['sort_order'] ?? 0) }}" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            </div>
            <div>
                <x-form.image-upload :label="__('admin.socials.icon')" :current-image="$socialIcon" />
            </div>
            <div class="flex items-center gap-3 sm:col-span-2">
                <label class="flex cursor-pointer items-center gap-3">
                    <input type="checkbox" name="is_active" value="1" {{ ($social['is_active'] ?? true) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-700" />
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.status.active') }}</span>
                </label>
            </div>
            <div class="flex items-center gap-3 sm:col-span-2">
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-6 py-3 text-sm font-medium text-white transition hover:bg-brand-600">{{ __('admin.socials.update') }}</button>
                <a href="{{ route('admin.socials.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-6 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">{{ __('admin.actions.cancel') }}</a>
            </div>
        </div>
    </form>
</div>
@endsection