@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb title="{{ __('admin.orders.title') }}" :breadcrumbs="[__('admin.orders.title') => null]" />

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

<div class="mb-4 flex items-center gap-3 rounded-2xl border border-gray-200 bg-white px-6 py-4 dark:border-gray-800 dark:bg-gray-900">
    <form method="GET" action="{{ route('admin.orders.index') }}" class="flex items-center gap-3">
        <label class="text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.table.status') }}</label>
        <select name="status" onchange="this.form.submit()" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option value="">{{ __('admin.status.all_statuses') }}</option>
            @foreach(['pending', 'processing', 'in_transit', 'delivered', 'cancelled'] as $status)
                <option value="{{ $status }}" {{ $currentStatus === $status ? 'selected' : '' }}>{{ __('admin.status.'.$status) }}</option>
            @endforeach
        </select>
        @if($currentStatus)
            <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">{{ __('admin.actions.clear') }}</a>
        @endif
    </form>
</div>

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
        <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('admin.orders.all') }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-start text-sm">
            <thead>
                <tr class="border-b border-gray-200 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                    <th class="px-6 py-3 font-medium">{{ __('admin.table.order') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.table.customer') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.table.payment_method') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.table.payment_status') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.table.total') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.table.status') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.table.date') }}</th>
                    <th class="px-6 py-3 font-medium text-end">{{ __('admin.actions.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.orders.show', $order) }}" class="font-medium text-brand-600 transition hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">#{{ $order->order_no }}</a>
                        </td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $order->user->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $order->payment_method ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $order->payment_status === 'paid' ? 'bg-success-50 text-success-600 dark:bg-success-500/20 dark:text-success-400' : ($order->payment_status === 'failed' ? 'bg-error-50 text-error-600 dark:bg-error-500/20 dark:text-error-400' : 'bg-warning-50 text-warning-600 dark:bg-warning-500/20 dark:text-warning-400') }}">
                                {{ __('admin.status.'.($order->payment_status ?? 'pending')) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ number_format($order->total, 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium
                                @if($order->status === 'pending') bg-warning-50 text-warning-600 dark:bg-warning-500/20 dark:text-warning-400
                                @elseif($order->status === 'processing') bg-brand-50 text-brand-600 dark:bg-brand-500/20 dark:text-brand-400
                                @elseif($order->status === 'in_transit') bg-brand-50 text-brand-600 dark:bg-brand-500/20 dark:text-brand-400
                                @elseif($order->status === 'delivered') bg-success-50 text-success-600 dark:bg-success-500/20 dark:text-success-400
                                @else bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400
                                @endif">
                                {{ __('admin.status.'.$order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $order->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end">
                                <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 p-2 text-gray-500 transition hover:bg-brand-50 hover:text-brand-500 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-brand-500/10">
                                    <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24"><path d="M12 5c-5 0-9 6-9 6s4 6 9 6 9-6 9-6-4-6-9-6zm0 10a4 4 0 110-8 4 4 0 010 8zm0-6a2 2 0 100 4 2 2 0 000-4z" fill="currentColor"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">{{ __('admin.empty.orders') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="px-6 py-4">{{ $orders->links() }}</div>
    @endif
</div>
@endsection