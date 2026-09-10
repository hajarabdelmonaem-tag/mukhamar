@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb :title="__('admin.contact_messages.info')" :breadcrumbs="[__('admin.contact_messages.title') => route('admin.contact-messages.index'), __('admin.contact_messages.info') => null]" />

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
        <div class="flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('admin.contact_messages.info') }}</h3>
            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $contactMessage->is_read ? 'bg-success-50 text-success-600 dark:bg-success-500/20 dark:text-success-400' : 'bg-warning-50 text-warning-600 dark:bg-warning-500/20 dark:text-warning-400' }}">
                {{ $contactMessage->is_read ? __('admin.status.read') : __('admin.status.unread') }}
            </span>
        </div>
    </div>
    <div class="p-6">
        <dl class="space-y-3 text-sm">
            <div class="flex justify-between">
                <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.contact_messages.sender') }}</dt>
                <dd class="font-medium text-gray-800 dark:text-white">{{ $contactMessage->name ?? '—' }}</dd>
            </div>
            @if($contactMessage->email)
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.contact_messages.email') }}</dt>
                    <dd class="font-medium text-gray-800 dark:text-white">{{ $contactMessage->email }}</dd>
                </div>
            @endif
            @if($contactMessage->phone)
                <div class="flex justify-between">
                    <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.contact_messages.phone') }}</dt>
                    <dd class="font-medium text-gray-800 dark:text-white" dir="ltr">{{ $contactMessage->phone }}</dd>
                </div>
            @endif
            <div class="flex justify-between">
                <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.contact_messages.subject') }}</dt>
                <dd class="font-medium text-gray-800 dark:text-white">{{ $contactMessage->subject ?? '—' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.contact_messages.sent_at') }}</dt>
                <dd class="font-medium text-gray-800 dark:text-white">{{ $contactMessage->created_at->format('M d, Y h:i A') }}</dd>
            </div>
        </dl>
        <div class="mt-6 border-t border-gray-200 pt-6 dark:border-gray-800">
            <h4 class="mb-3 text-sm font-semibold text-gray-800 dark:text-white">{{ __('admin.contact_messages.message') }}</h4>
            <p class="whitespace-pre-line rounded-lg bg-gray-50 p-4 text-sm leading-relaxed text-gray-700 dark:bg-gray-800/50 dark:text-gray-300">{{ $contactMessage->message }}</p>
        </div>
    </div>
    <div class="flex items-center gap-3 border-t border-gray-200 px-6 py-4 dark:border-gray-800">
        @unless($contactMessage->is_read)
            <form method="POST" action="{{ route('admin.contact-messages.mark-read', $contactMessage) }}">
                @csrf
                @method('PUT')
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-6 py-3 text-sm font-medium text-white transition hover:bg-brand-600">{{ __('admin.contact_messages.mark_read') }}</button>
            </form>
        @endunless
        <form method="POST" action="{{ route('admin.contact-messages.destroy', $contactMessage) }}" onsubmit="return confirm('{{ __('admin.confirm.are_you_sure') }}')">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-error-200 px-6 py-3 text-sm font-medium text-error-600 transition hover:bg-error-50 dark:border-error-800 dark:text-error-400 dark:hover:bg-error-500/10">{{ __('admin.actions.delete') }}</button>
        </form>
        <a href="{{ route('admin.contact-messages.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-6 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">{{ __('admin.contact_messages.back_to_messages') }}</a>
    </div>
</div>
@endsection
