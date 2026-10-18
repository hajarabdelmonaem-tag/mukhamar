@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb :title="__('admin.chat_conversations.title')" :breadcrumbs="[__('admin.chat_conversations.title') => null]" />

@if(session('success'))
    <div class="mb-4 rounded-lg border border-success-200 bg-success-50 p-4 text-sm text-success-700 dark:border-success-800 dark:bg-success-950 dark:text-success-300">
        {{ session('success') }}
    </div>
@endif

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
        <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('admin.chat_conversations.all') }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-start text-sm">
            <thead>
                <tr class="border-b border-gray-200 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                    <th class="px-6 py-3 font-medium">{{ __('admin.chat_conversations.customer') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.chat_conversations.messages') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.chat_conversations.tokens') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('admin.chat_conversations.last_message_at') }}</th>
                    <th class="px-6 py-3 font-medium text-end">{{ __('admin.actions.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($conversations as $conversation)
                    <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.chat-conversations.show', $conversation) }}" class="font-medium text-brand-600 transition hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">{{ $conversation->user?->name ?? __('admin.chat_conversations.guest') }}</a>
                        </td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $conversation->customer_messages_count }}</td>
                        <td class="px-6 py-4 text-gray-700 dark:text-gray-300" dir="ltr">{{ number_format((int) $conversation->messages_sum_input_tokens) }} / {{ number_format((int) $conversation->messages_sum_output_tokens) }}</td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $conversation->last_message_at?->format('M d, Y h:i A') ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.chat-conversations.show', $conversation) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 p-2 text-gray-500 transition hover:bg-brand-50 hover:text-brand-500 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-brand-500/10">
                                    <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24"><path d="M12 5c-5 0-9 6-9 6s4 6 9 6 9-6 9-6-4-6-9-6zm0 10a4 4 0 110-8 4 4 0 010 8zm0-6a2 2 0 100 4 2 2 0 000-4z" fill="currentColor"/></svg>
                                </a>
                                <form method="POST" action="{{ route('admin.chat-conversations.destroy', $conversation) }}" onsubmit="return confirm('{{ __('admin.confirm.are_you_sure') }}')">
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
                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">{{ __('admin.chat_conversations.empty') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($conversations instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="px-6 py-4">{{ $conversations->links() }}</div>
    @endif
</div>
@endsection
