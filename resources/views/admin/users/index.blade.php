@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb title="{{ __('admin.users.title') }}" :breadcrumbs="[__('admin.users.title') => null]" />

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
        <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('admin.users.all') }}</h3>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600">
            <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18"><path d="M10 8h6v2h-6v6H8v-6H2V8h6V2h2v6z" fill="currentColor"/></svg>
            {{ __('admin.users.add') }}
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-start text-sm">
            <thead>
                <tr class="border-b border-gray-200 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                    <th class="px-6 py-3 font-medium">{{ __('admin.table.name') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.table.email_phone') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.table.orders_count') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.status.active') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.table.created') }}</th>
                    <th class="px-6 py-3 font-medium text-end">{{ __('admin.actions.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-50 text-sm font-semibold text-brand-600 dark:bg-brand-500/20 dark:text-brand-400">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <p class="font-medium text-gray-800 dark:text-white">{{ $user->name }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">
                            <p>{{ $user->email }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->phone ?? '—' }}</p>
                        </td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $user->orders_count }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $user->is_active ? 'bg-success-50 text-success-600 dark:bg-success-500/20 dark:text-success-400' : 'bg-error-50 text-error-600 dark:bg-error-500/20 dark:text-error-400' }}">
                                {{ $user->is_active ? __('admin.status.active') : __('admin.status.inactive') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 p-2 text-gray-500 transition hover:bg-brand-50 hover:text-brand-500 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-brand-500/10" title="{{ __('admin.users.view_addresses') }}">
                                    <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17a5 5 0 110-10 5 5 0 010 10zm0-8a3 3 0 100 6 3 3 0 000-6z" fill="currentColor"/></svg>
                                </a>
                                <a href="{{ route('admin.notifications.create', ['user_id' => $user->id]) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 p-2 text-gray-500 transition hover:bg-brand-50 hover:text-brand-500 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-brand-500/10" title="{{ __('admin.users.notify') }}">
                                    <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24"><path d="M12 22a2 2 0 002-2h-4a2 2 0 002 2zm6-6v-5a6 6 0 10-12 0v5l-2 2v1h16v-1l-2-2z" fill="currentColor"/></svg>
                                </a>
                                <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-gray-200 p-2 text-gray-500 transition hover:bg-brand-50 hover:text-brand-500 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-brand-500/10" title="{{ $user->is_active ? __('admin.users.toggle_status') : __('admin.users.toggle_status') }}">
                                        <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm5 11H7v-2h10v2z" fill="currentColor"/></svg>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('{{ __('admin.confirm.are_you_sure') }}')">
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
                    <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">{{ __('admin.empty.users') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="px-6 py-4">{{ $users->links() }}</div>
    @endif
</div>
@endsection
