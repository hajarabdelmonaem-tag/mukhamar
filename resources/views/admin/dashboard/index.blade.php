@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb :title="__('admin.dashboard')" :breadcrumbs="[__('admin.dashboard') => null]" />

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 mb-6">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-50 dark:bg-brand-500/20">
                <svg class="fill-brand-500" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zm-8 8a3 3 0 110-6 3 3 0 010 6z" fill="currentColor"/></svg>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('admin.statistics.total_revenue') }}</h4>
                <p class="text-title-md font-semibold text-gray-800 dark:text-white">{{ number_format($totalRevenue, 2) }}</p>
            </div>
        </div>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-success-50 dark:bg-success-500/20">
                <svg class="fill-success-500" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill="currentColor"/></svg>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('admin.statistics.total_orders') }}</h4>
                <p class="text-title-md font-semibold text-gray-800 dark:text-white">{{ $totalOrders }}</p>
            </div>
        </div>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-orange-50 dark:bg-orange-500/20">
                <svg class="fill-orange-500" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 2a5 5 0 015 5v3a5 5 0 01-10 0V7a5 5 0 015-5zm7 9v1a7 7 0 01-14 0v-1h2v1a5 5 0 0010 0v-1h2z" fill="currentColor"/></svg>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('admin.statistics.total_products') }}</h4>
                <p class="text-title-md font-semibold text-gray-800 dark:text-white">{{ $totalProducts }}</p>
            </div>
        </div>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-error-50 dark:bg-error-500/20">
                <svg class="fill-error-500" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M16 11a1 1 0 00-1 1v1a1 1 0 002 0v-1a1 1 0 00-1-1zM8 11a1 1 0 00-1 1v1a1 1 0 002 0v-1a1 1 0 00-1-1zM12 2a10 10 0 100 20 10 10 0 000-20zm0 18a8 8 0 110-16 8 8 0 010 16zm0-13a2 2 0 00-1.7 3.1c.3.5 1 .7 1.7.7s1.4-.2 1.7-.7A2 2 0 0012 7z" fill="currentColor"/></svg>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('admin.statistics.total_users') }}</h4>
                <p class="text-title-md font-semibold text-gray-800 dark:text-white">{{ $totalUsers }}</p>
            </div>
        </div>
    </div>
</div>

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
        <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('admin.latest_orders') }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-start text-sm">
            <thead>
                <tr class="border-b border-gray-200 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                    <th class="px-6 py-3 font-medium">{{ __('admin.table.order_no') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.table.customer') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.table.total') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.table.status') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.table.date') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($latestOrders as $order)
                    <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-6 py-4 font-medium text-brand-500">{{ $order->order_no }}</td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $order->user->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ number_format($order->total, 2) }}</td>
                        <td class="px-6 py-4">
                            @php
                                $colors = [
                                    'pending' => 'bg-warning-50 text-warning-600 dark:bg-warning-500/20 dark:text-warning-400',
                                    'processing' => 'bg-blue-light-50 text-blue-light-600 dark:bg-blue-light-500/20 dark:text-blue-light-400',
                                    'in_transit' => 'bg-brand-50 text-brand-600 dark:bg-brand-500/20 dark:text-brand-400',
                                    'delivered' => 'bg-success-50 text-success-600 dark:bg-success-500/20 dark:text-success-400',
                                    'cancelled' => 'bg-error-50 text-error-600 dark:bg-error-500/20 dark:text-error-400',
                                ];
                            @endphp
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $colors[$order->status] ?? '' }}">{{ __('admin.status.'.$order->status) }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $order->created_at->translatedFormat('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">{{ __('admin.empty.orders') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
