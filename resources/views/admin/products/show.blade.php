@extends('layouts.app')

@section('content')
<x.common.page-breadcrumb :title="$product->getTranslation('name', app()->getLocale())" :breadcrumbs="[__('admin.products.title') => route('admin.products.index'), $product->getTranslation('name', app()->getLocale()) => null]" />

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('admin.products.info') }}</h3>
            </div>
            <div class="p-6">
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.table.name') }}</dt>
                        <dd class="font-medium text-gray-800 dark:text-white">{{ $product->name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.table.category') }}</dt>
                        <dd class="font-medium text-gray-800 dark:text-white">{{ $product->category->name ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.table.slug') }}</dt>
                        <dd class="font-medium text-gray-800 dark:text-white">{{ $product->slug }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.table.price') }}</dt>
                        <dd class="font-medium text-gray-800 dark:text-white">{{ number_format($product->price, 2) }}</dd>
                    </div>
                    @if($product->old_price)
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.table.old_price') }}</dt>
                        <dd class="font-medium text-gray-800 dark:text-white">{{ number_format($product->old_price, 2) }}</dd>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.table.featured') }}</dt>
                        <dd>
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $product->is_featured ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/20 dark:text-brand-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }}">
                                {{ $product->is_featured ? __('admin.status.yes') : __('admin.status.no') }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.status.active') }}</dt>
                        <dd>
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $product->is_active ? 'bg-success-50 text-success-600 dark:bg-success-500/20 dark:text-success-400' : 'bg-error-50 text-error-600 dark:bg-error-500/20 dark:text-error-400' }}">
                                {{ $product->is_active ? __('admin.status.active') : __('admin.status.inactive') }}
                            </span>
                        </dd>
                    </div>
                    @if(!empty($product->badges))
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.products.badges') }}</dt>
                        <dd class="flex flex-wrap justify-end gap-1.5">
                            @foreach($product->badges as $badge)
                                <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">{{ __('admin.status.'.$badge) }}</span>
                            @endforeach
                        </dd>
                    </div>
                    @endif
                    @if($product->rating)
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.products.rating') }}</dt>
                        <dd class="font-medium text-gray-800 dark:text-white">{{ number_format($product->rating, 1) }} / 5 ({{ $product->reviews_count }})</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        @if($product->getTranslation('description', 'en') || $product->getTranslation('description', 'ar'))
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('admin.table.description') }}</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @if($product->getTranslation('description', 'en'))
                    <div>
                        <p class="mb-1 text-xs font-medium text-gray-500 dark:text-gray-400">English</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $product->getTranslation('description', 'en') }}</p>
                    </div>
                    @endif
                    @if($product->getTranslation('description', 'ar'))
                    <div>
                        <p class="mb-1 text-xs font-medium text-gray-500 dark:text-gray-400">عربي</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300" dir="rtl">{{ $product->getTranslation('description', 'ar') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        @if($product->getTranslation('usage_instructions', 'en') || $product->getTranslation('usage_instructions', 'ar'))
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('admin.products.usage_instructions') }}</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @if($product->getTranslation('usage_instructions', 'en'))
                    <div>
                        <p class="mb-1 text-xs font-medium text-gray-500 dark:text-gray-400">English</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $product->getTranslation('usage_instructions', 'en') }}</p>
                    </div>
                    @endif
                    @if($product->getTranslation('usage_instructions', 'ar'))
                    <div>
                        <p class="mb-1 text-xs font-medium text-gray-500 dark:text-gray-400">عربي</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300" dir="rtl">{{ $product->getTranslation('usage_instructions', 'ar') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        @if($product->getTranslation('top_notes', 'en') || $product->getTranslation('heart_notes', 'en') || $product->getTranslation('base_notes', 'en'))
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('admin.products.notes') }}</h3>
            </div>
            <div class="p-6">
                <dl class="space-y-3 text-sm">
                    @if($product->getTranslation('top_notes', 'en') || $product->getTranslation('top_notes', 'ar'))
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.products.top_notes') }}</dt>
                        <dd class="text-end font-medium text-gray-800 dark:text-white">{{ $product->getTranslation('top_notes', app()->getLocale()) ?: $product->getTranslation('top_notes', 'en') }}</dd>
                    </div>
                    @endif
                    @if($product->getTranslation('heart_notes', 'en') || $product->getTranslation('heart_notes', 'ar'))
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.products.heart_notes') }}</dt>
                        <dd class="text-end font-medium text-gray-800 dark:text-white">{{ $product->getTranslation('heart_notes', app()->getLocale()) ?: $product->getTranslation('heart_notes', 'en') }}</dd>
                    </div>
                    @endif
                    @if($product->getTranslation('base_notes', 'en') || $product->getTranslation('base_notes', 'ar'))
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.products.base_notes') }}</dt>
                        <dd class="text-end font-medium text-gray-800 dark:text-white">{{ $product->getTranslation('base_notes', app()->getLocale()) ?: $product->getTranslation('base_notes', 'en') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>
        @endif

        @if($product->variants->count())
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('admin.products.variants') }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-start text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-gray-500 dark:border-gray-800 dark:text-gray-400">
                            <th class="px-6 py-3 font-medium">{{ __('admin.table.name') }}</th>
                            <th class="px-6 py-3 font-medium">{{ __('admin.table.unit') }}</th>
                            <th class="px-6 py-3 font-medium">{{ __('admin.table.price_adjustment') }}</th>
                            <th class="px-6 py-3 font-medium">{{ __('admin.table.sku') }}</th>
                            <th class="px-6 py-3 font-medium">{{ __('admin.table.stock') }}</th>
                            <th class="px-6 py-3 font-medium">{{ __('admin.table.default') }}</th>
                            <th class="px-6 py-3 font-medium">{{ __('admin.status.active') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($product->variants as $variant)
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-6 py-4 font-medium text-gray-800 dark:text-white">{{ $variant->name }}</td>
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $variant->unit ?: '—' }}</td>
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $variant->price_adjustment ? number_format($variant->price_adjustment, 2) : '—' }}</td>
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $variant->sku ?: '—' }}</td>
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $variant->stock }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $variant->is_default ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/20 dark:text-brand-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }}">
                                    {{ $variant->is_default ? __('admin.status.yes') : __('admin.status.no') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $variant->is_active ? 'bg-success-50 text-success-600 dark:bg-success-500/20 dark:text-success-400' : 'bg-error-50 text-error-600 dark:bg-error-500/20 dark:text-error-400' }}">
                                    {{ $variant->is_active ? __('admin.status.active') : __('admin.status.inactive') }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('admin.products.reviews') }}</h3>
            </div>
            <div class="p-6">
                @forelse($product->reviews as $review)
                <div class="{{ !$loop->last ? 'mb-4 border-b border-gray-100 pb-4 dark:border-gray-800' : '' }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-gray-800 dark:text-white">{{ $review->user->name ?? '—' }}</span>
                            <span class="text-xs text-gray-400">{{ $review->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="h-4 w-4 {{ $i <= $review->rating ? 'text-warning-400' : 'text-gray-200 dark:text-gray-700' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                    </div>
                    @if($review->title)
                    <p class="mt-1 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $review->title }}</p>
                    @endif
                    @if($review->body)
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $review->body }}</p>
                    @endif
                </div>
                @empty
                <p class="py-4 text-center text-sm text-gray-400 dark:text-gray-500">{{ __('admin.products.no_reviews') }}</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="space-y-6">
        @if($product->images->count())
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('admin.products.images') }}</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-3">
                    @foreach($product->images as $image)
                    <div class="relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                        <img src="{{ asset('storage/'.$image->path) }}" alt="{{ $image->alt }}" class="aspect-square w-full object-cover" />
                        @if($image->is_primary)
                        <span class="absolute top-1.5 left-1.5 inline-flex rounded-full bg-brand-500 px-2 py-0.5 text-xs font-medium text-white">{{ __('admin.products.primary') }}</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('admin.products.overview') }}</h3>
            </div>
            <div class="p-6">
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.table.created') }}</dt>
                        <dd class="font-medium text-gray-800 dark:text-white">{{ $product->created_at->format('M d, Y h:i A') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.table.variant') }}</dt>
                        <dd class="font-medium text-gray-800 dark:text-white">{{ $product->variants->count() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.products.images') }}</dt>
                        <dd class="font-medium text-gray-800 dark:text-white">{{ $product->images->count() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500 dark:text-gray-400">{{ __('admin.products.reviews') }}</dt>
                        <dd class="font-medium text-gray-800 dark:text-white">{{ $product->reviews_count }}</dd>
                    </div>
                </dl>
                <div class="mt-4 flex items-center gap-3">
                    <a href="{{ route('admin.products.edit', $product) }}" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-brand-600">{{ __('admin.actions.edit') }}</a>
                    <a href="{{ route('admin.products.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">{{ __('admin.actions.back_to_list') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
