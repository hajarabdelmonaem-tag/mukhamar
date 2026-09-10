<div x-data="{ isOpen: false }" class="relative">
    <button @click="isOpen = !isOpen" class="relative flex items-center justify-center text-gray-500 transition-colors bg-white border border-gray-200 rounded-full hover:text-dark-900 h-11 w-11 hover:bg-gray-100 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800">
        <span class="flex absolute -top-0.5 -right-0.5">
            <span class="absolute top-3 right-3 size-2 rounded-full bg-success-500"></span>
            <span class="inline-flex size-4 animate-ping rounded-full bg-success-500 opacity-75"></span>
        </span>
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zm0 16a2 2 0 01-2-2h4a2 2 0 01-2 2z" fill="currentColor"/>
        </svg>
    </button>
</div>
