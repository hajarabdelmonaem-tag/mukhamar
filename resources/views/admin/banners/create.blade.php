@extends('layouts.app')

@section('content')
<x.common.page-breadcrumb :title="__('admin.banners.add')" :breadcrumbs="[__('admin.banners.title') => route('admin.banners.index'), __('admin.actions.add') => null]" />

@if ($errors->any())
    <div class="mb-4 rounded-lg border border-error-200 bg-error-50 p-4 text-sm text-error-700 dark:border-error-800 dark:bg-error-950 dark:text-error-300">
        <ul class="list-disc space-y-1 ps-4">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
        <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('admin.banners.info') }}</h3>
    </div>
    <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data" class="p-6">
        @csrf
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
<div>
                    <x-form.image-upload :label="__('admin.form.upload_image')" />
                </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.categories.sort_order') }}</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            </div>
            <div class="flex items-center gap-3 sm:col-span-2">
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-6 py-3 text-sm font-medium text-white transition hover:bg-brand-600">{{ __('admin.banners.save') }}</button>
                <a href="{{ route('admin.banners.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-6 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">{{ __('admin.actions.cancel') }}</a>
            </div>
        </div>
    </form>
</div>
@endsection