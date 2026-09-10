@props([
    'name' => 'images[]',
    'accept' => 'image/*',
    'label' => null,
    'hint' => null,
    'existing' => [],
])

<div
    x-data="{
        files: [],
        isDragging: false,
        rebuildInput() {
            if (window.DataTransfer) {
                const dt = new DataTransfer();
                this.files.forEach((f) => dt.items.add(f.file));
                this.$refs.input.files = dt.files;
            }
        },
        addFiles(fileList) {
            if (!fileList) return;
            Array.from(fileList).forEach((file) => {
                const preview = this.files.find((f) => f.file === file);
                if (preview || !file.type.startsWith('image/')) return;
                const reader = new FileReader();
                reader.onload = () => {
                    this.files.push({ file, url: reader.result });
                    this.rebuildInput();
                };
                reader.readAsDataURL(file);
            });
        },
        removeAt(index) {
            this.files.splice(index, 1);
            this.rebuildInput();
        },
        onSelect() {
            this.addFiles(this.$refs.input.files);
            this.$refs.input.value = '';
        },
        onDrop(e) {
            this.isDragging = false;
            this.addFiles(e.dataTransfer.files);
        },
    }"
>
    @if ($label)
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ $label }}</label>
    @endif

    @if (count($existing))
        <div class="mb-4 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($existing as $image)
                <div class="group relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                    <img src="{{ $image['src'] }}" alt="{{ $image['alt'] }}" class="h-32 w-full object-cover">
                    <div class="flex items-center justify-between gap-2 bg-white/90 p-1.5 dark:bg-gray-900/90">
                        @if ($image['is_primary'])
                            <span class="inline-flex items-center rounded-full bg-brand-50 px-2 py-0.5 text-xs font-medium text-brand-600 dark:bg-brand-500/20 dark:text-brand-400">{{ __('admin.products.primary') }}</span>
                        @else
                            <form method="POST" action="{{ $image['primary_url'] }}">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="inline-flex items-center rounded-lg px-2 py-1 text-xs font-medium text-gray-500 transition hover:bg-brand-50 hover:text-brand-500 dark:text-gray-400 dark:hover:bg-brand-500/10">{{ __('admin.products.set_primary') }}</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ $image['delete_url'] }}" onsubmit="return confirm('{{ __('admin.confirm.are_you_sure') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center rounded-lg p-1 text-gray-500 transition hover:bg-error-50 hover:text-error-500 dark:text-gray-400 dark:hover:bg-error-500/10">
                                <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24"><path d="M6 19a2 2 0 002 2h8a2 2 0 002-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" fill="currentColor"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div
        @click="$refs.input.click()"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="onDrop($event)"
        :class="isDragging ? 'border-brand-500 bg-brand-50 dark:bg-brand-500/10' : 'border-gray-300 bg-gray-50 hover:border-brand-400 dark:border-gray-700 dark:bg-white/[0.03] dark:hover:border-brand-500'"
        class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed px-6 py-8 text-center transition"
    >
        <svg class="fill-brand-500/80" width="28" height="28" viewBox="0 0 24 24"><path d="M12 16a2 2 0 0 0 2-2v-6h-4v6a2 2 0 0 0 2 2zm6-5v2a6 6 0 1 1-12 0v-2h2v2a4 4 0 1 0 8 0v-2h2z" fill="currentColor"/></svg>
        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('admin.form.click_to_upload') }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('admin.form.drag_drop_hint') }}</p>
        <p x-show="files.length" x-cloak class="text-xs text-gray-500 dark:text-gray-400" x-text="files.length + ' {{ __('admin.actions.files_selected') }}'"></p>
    </div>

    <div x-show="files.length" x-cloak class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
        <template x-for="(file, index) in files" :key="file.file.name + index">
            <div class="group relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                <img :src="file.url" :alt="file.file.name" class="h-32 w-full object-cover">
                <button
                    type="button"
                    @click="removeAt(index)"
                    class="absolute top-2 right-2 inline-flex h-7 w-7 items-center justify-center rounded-full bg-gray-900/60 text-white transition hover:bg-error-500"
                    :title="'{{ __('admin.actions.remove') }}'"
                >
                    <svg class="fill-current" width="14" height="14" viewBox="0 0 24 24"><path d="M18.3 5.7a1 1 0 0 1 0 1.4L13.4 12l4.9 4.9a1 1 0 1 1-1.4 1.4L12 13.4l-4.9 4.9a1 1 0 1 1-1.4-1.4L10.6 12 5.7 7.1a1 1 0 0 1 1.4-1.4L12 10.6l4.9-4.9a1 1 0 0 1 1.4 0z" fill="currentColor"/></svg>
                </button>
            </div>
        </template>
    </div>

    <input x-ref="input" type="file" name="{{ $name }}" accept="{{ $accept }}" multiple class="sr-only" @change="onSelect($event)">

    @if ($hint)
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $hint }}</p>
    @endif
</div>