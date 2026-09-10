<div x-data="{ isOpen: false }" class="relative">
    <button @click="isOpen = !isOpen" class="flex items-center gap-4">
        <span class="hidden text-right lg:block">
            <span class="block text-sm font-medium text-gray-800 dark:text-white">{{ auth()->guard('web')->user()->name ?? __('admin.admins.role') }}</span>
            <span class="block text-xs text-gray-500 dark:text-gray-400">{{ __('admin.admins.role') }}</span>
        </span>
        <span class="h-11 w-11 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center">
            <svg class="fill-gray-500 dark:fill-gray-400" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 3.5C7.30558 3.5 3.5 7.30558 3.5 12C3.5 14.1526 4.3002 16.1184 5.61936 17.616C6.17279 15.3096 8.24852 13.5955 10.7246 13.5955H13.2746C15.7509 13.5955 17.8268 15.31 18.38 17.6167C19.6996 16.119 20.5 14.153 20.5 12C20.5 7.30558 16.6944 3.5 12 3.5ZM17.0246 18.8566V18.8455C17.0246 16.7744 15.3457 15.0955 13.2746 15.0955H10.7246C8.65354 15.0955 6.97461 16.7744 6.97461 18.8455V18.856C8.38223 19.8895 10.1198 20.5 12 20.5C13.8798 20.5 15.6171 19.8898 17.0246 18.8566ZM2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12ZM11.9991 7.25C10.8847 7.25 9.98126 8.15342 9.98126 9.26784C9.98126 10.3823 10.8847 11.2857 11.9991 11.2857C13.1135 11.2857 14.0169 10.3823 14.0169 9.26784C14.0169 8.15342 13.1135 7.25 11.9991 7.25ZM8.48126 9.26784C8.48126 7.32499 10.0563 5.75 11.9991 5.75C13.9419 5.75 15.5169 7.32499 15.5169 9.26784C15.5169 11.2107 13.9419 12.7857 11.9991 12.7857C10.0563 12.7857 8.48126 11.2107 8.48126 9.26784Z" fill="currentColor"/>
            </svg>
        </span>
        <svg class="hidden sm:block" width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M2.5 4.5L6 8L9.5 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
    <div x-show="isOpen" @click.away="isOpen = false" x-cloak x-transition class="absolute right-0 mt-4 w-60 rounded-xl border border-gray-200 bg-white p-2 shadow-theme-md dark:border-gray-800 dark:bg-gray-900">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5 rounded-lg">
            <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 1.5C4.86 1.5 1.5 4.86 1.5 9s3.36 7.5 7.5 7.5 7.5-3.36 7.5-7.5S13.14 1.5 9 1.5zm0 13.5c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6zm.75-9H9v4.5l3.75 2.25.75-1.23-3-1.77V6z"/></svg>
            {{ __('admin.dashboard') }}
        </a>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5 rounded-lg">
                <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.25 15.75H6C3.105 15.75 0.75 13.395 0.75 10.5V7.5C0.75 4.605 3.105 2.25 6 2.25H11.25C11.66 2.25 12 1.91 12 1.5C12 1.09 11.66 0.75 11.25 0.75H6C2.24 0.75 -0.75 3.74 -0.75 7.5V10.5C-0.75 14.26 2.24 17.25 6 17.25H11.25C11.66 17.25 12 16.91 12 16.5C12 16.09 11.66 15.75 11.25 15.75ZM14.25 11.25L16.5 9L14.25 6.75C14.11 6.61 14.005 6.395 14.005 6.15C14.005 5.905 14.11 5.69 14.25 5.55C14.39 5.41 14.605 5.305 14.85 5.305C15.095 5.305 15.31 5.41 15.45 5.55L18.75 8.85C18.89 8.99 18.975 9.185 18.975 9.375C18.975 9.565 18.89 9.76 18.75 9.9L15.45 13.2C15.31 13.34 15.095 13.445 14.85 13.445C14.605 13.445 14.39 13.34 14.25 13.2C14.11 13.06 14.005 12.845 14.005 12.6C14.005 12.355 14.11 12.14 14.25 12V11.25Z" fill="currentColor"/></svg>
                {{ __('admin.actions.logout') }}
            </button>
        </form>
    </div>
</div>
