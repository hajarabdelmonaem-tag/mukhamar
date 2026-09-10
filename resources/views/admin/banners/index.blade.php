@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb :title="__('admin.banners.title')" :breadcrumbs="[__('admin.banners.title') => null]" />

@if(session('success'))
    <div class="mb-4 rounded-lg border border-success-200 bg-success-50 p-4 text-sm text-success-700 dark:border-success-800 dark:bg-success-950 dark:text-success-300">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 rounded-lg border border-error-200 bg-error-50 p-4 text-sm text-error-700 dark:border-error-800 dark:bg-error-950 dark:text-error-300">
        {{ session('error') }}
    </div>
@endif

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-800">
        <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('admin.banners.all') }}</h3>
        <a href="{{ route('admin.banners.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600">
            <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18"><path d="M10 8h6v2h-6v6H8v-6H2V8h6V2h2v6z" fill="currentColor"/></svg>
            {{ __('admin.banners.add') }}
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-start text-sm">
            <thead>
                <tr class="border-b border-gray-200 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                    <th class="px-6 py-3 font-medium">{{ __('admin.table.image') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.table.sort') }}</th>
                    <th class="px-6 py-3 font-medium text-end">{{ __('admin.actions.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($banners as $banner)
                    <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-6 py-4">
                            @if($banner->image)
                                <img src="{{ str_starts_with($banner->image, 'http') ? $banner->image : \Illuminate\Support\Facades\Storage::url($banner->image) }}" class="h-16 w-24 rounded-lg object-cover">
                            @else
                                <span class="text-gray-400 dark:text-gray-600">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $banner->sort_order }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.banners.edit', $banner) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 p-2 text-gray-500 transition hover:bg-brand-50 hover:text-brand-500 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-brand-500/10">
                                    <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 000-1.41l-2.34-2.34a1 1 0 00-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill="currentColor"/></svg>
                                </a>
                                <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" onsubmit="return confirm('{{ __('admin.confirm.are_you_sure') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-gray-200 p-2 text-gray-500 transition hover:bg-error-50 hover:text-error-500 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-error-500/10">
                                        <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24"><path d="M6 19a2 2 0 002 2h8a2 2 0 002-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" fill="currentColor"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">{{ __('admin.empty.banners') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($banners instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="px-6 py-4">{{ $banners->links() }}</div>
    @endif
</div>
@endsection