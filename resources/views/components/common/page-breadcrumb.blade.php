@props(['title', 'breadcrumbs' => []])

<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <h2 class="text-title-md2 font-semibold text-gray-800 dark:text-white">
        {{ $title }}
    </h2>
    @if(count($breadcrumbs) > 0)
    <nav>
        <ol class="flex items-center gap-1.5">
            <li><a class="text-sm hover:text-brand-500" href="{{ route('admin.dashboard') }}">{{ __('admin.home') }}</a></li>
            @foreach($breadcrumbs as $label => $url)
            <li class="text-sm text-gray-500 dark:text-gray-400">/</li>
            @if(is_string($url))
            <li><a class="text-sm hover:text-brand-500" href="{{ $url }}">{{ $label }}</a></li>
            @else
            <li class="text-sm text-gray-500 dark:text-gray-400">{{ $label }}</li>
            @endif
            @endforeach
        </ol>
    </nav>
    @endif
</div>
