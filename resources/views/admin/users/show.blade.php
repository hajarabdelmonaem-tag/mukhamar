@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb :title="$user->name" :breadcrumbs="[__('admin.users.title') => route('admin.users.index'), $user->name => null]" />

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
            <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('admin.users.info') }}</h3>
        </div>
        <div class="p-6">
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.table.name') }}</dt>
                    <dd class="font-medium text-gray-800 dark:text-white">{{ $user->name }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.table.email') }}</dt>
                    <dd class="font-medium text-gray-800 dark:text-white">{{ $user->email }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.users.phone') }}</dt>
                    <dd class="font-medium text-gray-800 dark:text-white">{{ $user->phone ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.table.orders_count') }}</dt>
                    <dd class="font-medium text-gray-800 dark:text-white">{{ $user->orders_count }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.status.active') }}</dt>
                    <dd>
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $user->is_active ? 'bg-success-50 text-success-600 dark:bg-success-500/20 dark:text-success-400' : 'bg-error-50 text-error-600 dark:bg-error-500/20 dark:text-error-400' }}">
                            {{ $user->is_active ? __('admin.status.active') : __('admin.status.inactive') }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.table.created') }}</dt>
                    <dd class="font-medium text-gray-800 dark:text-white">{{ $user->created_at->format('M d, Y') }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>

<div class="mt-6 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-800">
        <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('admin.users.addresses') }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-start text-sm">
            <thead>
                <tr class="border-b border-gray-200 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                    <th class="px-6 py-3 font-medium">{{ __('admin.addresses.label') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.addresses.recipient') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.addresses.street') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.addresses.district') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.addresses.city') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.table.phone') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.addresses.postal_code') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.addresses.is_default') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($user->addresses as $address)
                    <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-6 py-4 font-medium text-gray-800 dark:text-white">{{ $address->label }}</td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $address->recipient_name }}</td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $address->street }}</td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $address->district }}</td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $address->city }}</td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $address->phone }}</td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $address->postal_code }}</td>
                        <td class="px-6 py-4">
                            @if($address->is_default)
                                <span class="inline-flex rounded-full bg-success-50 px-2.5 py-1 text-xs font-medium text-success-600 dark:bg-success-500/20 dark:text-success-400">{{ __('admin.addresses.is_default') }}</span>
                            @else
                                <span class="text-gray-400 dark:text-gray-600">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">{{ __('admin.empty.addresses') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection