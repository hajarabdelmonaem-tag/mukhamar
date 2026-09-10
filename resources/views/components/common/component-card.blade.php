@props(['title' => ''])

<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    @if($title)
    <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
        <h3 class="font-medium text-gray-800 dark:text-white">
            {{ $title }}
        </h3>
    </div>
    @endif
    <div class="p-6.5">
        {{ $slot }}
    </div>
</div>
