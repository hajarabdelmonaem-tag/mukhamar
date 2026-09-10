@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb :title="__('admin.contact_messages.title')" :breadcrumbs="[__('admin.contact_messages.title') => null]" />

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
    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
        <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('admin.contact_messages.all') }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-start text-sm">
            <thead>
                <tr class="border-b border-gray-200 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                    <th class="px-6 py-3 font-medium">{{ __('admin.contact_messages.sender') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.contact_messages.subject') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.contact_messages.message') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.status.read') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.contact_messages.sent_at') }}</th>
                    <th class="px-6 py-3 font-medium text-end">{{ __('admin.actions.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contactMessages as $contactMessage)
                    <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.contact-messages.show', $contactMessage) }}" class="font-medium text-brand-600 transition hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">{{ $contactMessage->name ?? '—' }}</a>
                        </td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $contactMessage->subject ?? '—' }}</td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ Str::limit($contactMessage->message, 50) }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $contactMessage->is_read ? 'bg-success-50 text-success-600 dark:bg-success-500/20 dark:text-success-400' : 'bg-warning-50 text-warning-600 dark:bg-warning-500/20 dark:text-warning-400' }}">
                                {{ $contactMessage->is_read ? __('admin.status.read') : __('admin.status.unread') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $contactMessage->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.contact-messages.show', $contactMessage) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 p-2 text-gray-500 transition hover:bg-brand-50 hover:text-brand-500 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-brand-500/10">
                                    <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24"><path d="M12 5c-5 0-9 6-9 6s4 6 9 6 9-6 9-6-4-6-9-6zm0 10a4 4 0 110-8 4 4 0 010 8zm0-6a2 2 0 100 4 2 2 0 000-4z" fill="currentColor"/></svg>
                                </a>
                                <form method="POST" action="{{ route('admin.contact-messages.destroy', $contactMessage) }}" onsubmit="return confirm('{{ __('admin.confirm.are_you_sure') }}')">
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
                    <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">{{ __('admin.empty.contact_messages') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($contactMessages instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="px-6 py-4">{{ $contactMessages->links() }}</div>
    @endif
</div>
@endsection
