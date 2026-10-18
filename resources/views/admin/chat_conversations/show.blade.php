@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb :title="__('admin.chat_conversations.info')" :breadcrumbs="[__('admin.chat_conversations.title') => route('admin.chat-conversations.index'), __('admin.chat_conversations.info') => null]" />

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h3 class="font-semibold text-gray-800 dark:text-white">{{ $conversation->user?->name ?? __('admin.chat_conversations.guest') }}</h3>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('admin.chat_conversations.tokens') }}: <span dir="ltr">{{ number_format($inputTokens) }} / {{ number_format($outputTokens) }}</span>
            </span>
        </div>
    </div>
    <div class="space-y-4 p-6">
        @forelse($messages as $message)
            @php($isCustomer = $message->role === \App\Models\ChatMessage::ROLE_USER)
            <div class="flex {{ $isCustomer ? 'justify-start' : 'justify-end' }}">
                <div class="max-w-[80%] rounded-2xl px-4 py-3 text-sm {{ $isCustomer ? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200' : 'bg-brand-50 text-gray-800 dark:bg-brand-500/15 dark:text-gray-100' }}">
                    <div class="mb-1 text-xs font-medium text-gray-500 dark:text-gray-400">
                        {{ $isCustomer ? __('admin.chat_conversations.customer_label') : __('admin.chat_conversations.assistant_label') }}
                        · {{ $message->created_at->format('M d, h:i A') }}
                    </div>
                    <p class="whitespace-pre-line leading-relaxed">{{ $message->text }}</p>
                    @if(! empty($message->links))
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach($message->links as $link)
                                <a href="{{ $link['url'] }}" title="{{ $link['title'] }}" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-2 text-xs font-medium text-white transition hover:bg-brand-600">{{ $link['label'] ?? $link['title'] }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500 dark:text-gray-400">{{ __('admin.chat_conversations.empty') }}</p>
        @endforelse
    </div>
    <div class="flex items-center gap-3 border-t border-gray-200 px-6 py-4 dark:border-gray-800">
        <form method="POST" action="{{ route('admin.chat-conversations.destroy', $conversation) }}" onsubmit="return confirm('{{ __('admin.confirm.are_you_sure') }}')">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-error-200 px-6 py-3 text-sm font-medium text-error-600 transition hover:bg-error-50 dark:border-error-800 dark:text-error-400 dark:hover:bg-error-500/10">{{ __('admin.actions.delete') }}</button>
        </form>
        <a href="{{ route('admin.chat-conversations.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-6 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">{{ __('admin.chat_conversations.back') }}</a>
    </div>
</div>
@endsection
