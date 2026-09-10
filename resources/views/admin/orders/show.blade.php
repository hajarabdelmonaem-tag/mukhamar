@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb :title="__('admin.table.order_no').' '.$order->order_no" :breadcrumbs="[__('admin.orders.title') => route('admin.orders.index'), __('admin.table.order_no').' '.$order->order_no => null]" />

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

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
            <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('admin.orders.info') }}</h3>
        </div>
        <div class="p-6">
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.table.order_number') }}</dt>
                    <dd class="font-medium text-gray-800 dark:text-white">#{{ $order->order_no }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.orders.placed_at') }}</dt>
                    <dd class="font-medium text-gray-800 dark:text-white">{{ $order->placed_at?->format('M d, Y h:i A') ?? $order->created_at->format('M d, Y h:i A') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.table.status') }}</dt>
                    <dd>
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium
                            @if($order->status === 'pending') bg-warning-50 text-warning-600 dark:bg-warning-500/20 dark:text-warning-400
                            @elseif($order->status === 'processing') bg-brand-50 text-brand-600 dark:bg-brand-500/20 dark:text-brand-400
                            @elseif($order->status === 'in_transit') bg-brand-50 text-brand-600 dark:bg-brand-500/20 dark:text-brand-400
                            @elseif($order->status === 'delivered') bg-success-50 text-success-600 dark:bg-success-500/20 dark:text-success-400
                            @else bg-error-50 text-error-600 dark:bg-error-500/20 dark:text-error-400
                            @endif">
                            {{ __('admin.status.'.$order->status) }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.table.payment_status') }}</dt>
                    <dd>
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $order->payment_status === 'paid' ? 'bg-success-50 text-success-600 dark:bg-success-500/20 dark:text-success-400' : ($order->payment_status === 'failed' ? 'bg-error-50 text-error-600 dark:bg-error-500/20 dark:text-error-400' : 'bg-warning-50 text-warning-600 dark:bg-warning-500/20 dark:text-warning-400') }}">
                            {{ __('admin.status.'.($order->payment_status ?? 'pending')) }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.table.payment_method') }}</dt>
                    <dd class="font-medium text-gray-800 dark:text-white">{{ $order->payment_method ?? '—' }}</dd>
                </div>
                @if($order->coupon)
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.table.coupon') }}</dt>
                        <dd class="font-medium text-gray-800 dark:text-white">{{ $order->coupon->code }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
            <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('admin.orders.customer_address') }}</h3>
        </div>
        <div class="p-6">
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.table.customer') }}</dt>
                    <dd class="font-medium text-gray-800 dark:text-white">{{ $order->user->name ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.table.email') }}</dt>
                    <dd class="font-medium text-gray-800 dark:text-white">{{ $order->user->email ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.orders.phone') }}</dt>
                    <dd class="font-medium text-gray-800 dark:text-white">{{ $order->address->phone ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.orders.address') }}</dt>
                    <dd class="text-end font-medium text-gray-800 dark:text-white">{{ $order->address->full_address ?? $order->address->address ?? '—' }}</dd>
                </div>
                @if($order->address && $order->address->notes)
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.orders.notes') }}</dt>
                        <dd class="text-end font-medium text-gray-800 dark:text-white">{{ $order->address->notes }}</dd>
                    </div>
                @endif
                @if($order->notes)
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.orders.order_notes') }}</dt>
                        <dd class="text-end font-medium text-gray-800 dark:text-white">{{ $order->notes }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>
</div>

<div class="mt-6 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
        <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('admin.orders.items') }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-start text-sm">
            <thead>
                <tr class="border-b border-gray-200 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                    <th class="px-6 py-3 font-medium">{{ __('admin.table.product') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.table.variant') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.table.qty') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.table.price') }}</th>
                    <th class="px-6 py-3 font-medium text-end">{{ __('admin.table.total') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($order->items as $item)
                    <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-6 py-4 font-medium text-gray-800 dark:text-white">{{ $item->product_name }}</td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $item->variant_name ?? '—' }}</td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-6 py-4 text-end text-gray-700 dark:text-gray-300">{{ number_format($item->total, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">{{ __('admin.empty.items') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
        <dl class="ms-auto w-full max-w-sm space-y-2 text-sm">
            <div class="flex justify-between">
                <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.table.subtotal') }}</dt>
                <dd class="font-medium text-gray-800 dark:text-white">{{ number_format($order->subtotal, 2) }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.table.shipping') }}</dt>
                <dd class="font-medium text-gray-800 dark:text-white">{{ number_format($order->shipping, 2) }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.table.discount') }}</dt>
                <dd class="font-medium text-gray-800 dark:text-white">-{{ number_format($order->discount, 2) }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.table.tax') }}</dt>
                <dd class="font-medium text-gray-800 dark:text-white">{{ number_format($order->tax, 2) }}</dd>
            </div>
            <div class="flex justify-between border-t border-gray-200 pt-2 dark:border-gray-800">
                <dt class="font-semibold text-gray-800 dark:text-white">{{ __('admin.table.total') }}</dt>
                <dd class="font-semibold text-brand-600 dark:text-brand-400">{{ number_format($order->total, 2) }}</dd>
            </div>
        </dl>
    </div>
</div>

<div class="mt-6 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
        <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('admin.orders.update_status') }}</h3>
    </div>
    <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="p-6">
        @csrf
        @method('PUT')
        <div class="flex items-center gap-3">
            <select name="status" class="dark:bg-dark-900 h-11 w-full max-w-xs rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                @foreach(['pending', 'processing', 'in_transit', 'delivered', 'cancelled'] as $status)
                    <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>{{ __('admin.status.'.$status) }}</option>
                @endforeach
            </select>
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-6 py-3 text-sm font-medium text-white transition hover:bg-brand-600">{{ __('admin.orders.update_status') }}</button>
            <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-6 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">{{ __('admin.orders.back_to_orders') }}</a>
        </div>
    </form>
</div>
@endsection