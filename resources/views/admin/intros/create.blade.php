@extends('layouts.app')

@section('content')
<x.common.page-breadcrumb :title="__('admin.intros.add')" :breadcrumbs="[__('admin.intros.title') => route('admin.intros.index'), __('admin.actions.add') => null]" />

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
        <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('admin.intros.info') }}</h3>
    </div>
    <form method="POST" action="{{ route('admin.intros.store') }}" enctype="multipart/form-data" class="p-6">
        @csrf
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.form.title_en') }}<span class="text-error-500">*</span></label>
                <input type="text" name="title[en]" value="{{ old('title.en') }}" required class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.form.title_ar') }}<span class="text-error-500">*</span></label>
                <input type="text" name="title[ar]" value="{{ old('title.ar') }}" required dir="rtl" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            </div>
            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.form.description_en') }}</label>
                <textarea name="description[en]" rows="3" class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">{{ old('description.en') }}</textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.form.description_ar') }}</label>
                <textarea name="description[ar]" rows="3" dir="rtl" class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">{{ old('description.ar') }}</textarea>
            </div>
            <div>
                <x-form.image-upload :label="__('admin.form.upload_image')" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.categories.sort_order') }}</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            </div>
            <div class="flex items-center gap-3 sm:col-span-2">
                <div x-data="{ isActive: true }">
                    <label class="flex cursor-pointer items-center select-none">
                        <input type="checkbox" name="is_active" value="1" checked @change="isActive = $event.target.checked" class="sr-only" />
                        <div :class="isActive ? 'border-brand-500 bg-brand-500' : 'bg-transparent border-gray-300 dark:border-gray-700'"
                            class="mr-3 flex h-5 w-5 items-center justify-center rounded-md border-[1.25px]">
                            <span :class="isActive ? '' : 'opacity-0'"><svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M11.6666 3.5L5.24992 9.91667L2.33325 7" stroke="white" stroke-width="1.94437" stroke-linecap="round" stroke-linejoin="round" /></svg></span>
                        </div>
                        <span class="text-sm font-normal text-gray-700 dark:text-gray-400">{{ __('admin.status.active') }}</span>
                    </label>
                </div>
            </div>
            <div class="flex items-center gap-3 sm:col-span-2">
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-6 py-3 text-sm font-medium text-white transition hover:bg-brand-600">{{ __('admin.intros.save') }}</button>
                <a href="{{ route('admin.intros.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-6 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">{{ __('admin.actions.cancel') }}</a>
            </div>
        </div>
    </form>
</div>
@endsection