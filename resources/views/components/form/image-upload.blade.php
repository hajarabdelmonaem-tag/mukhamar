@props(['name' => 'image', 'accept' => 'image/*', 'currentImage' => null, 'label' => null])

<div
    x-data="{
        fileName: '',
        preview: {{ $currentImage ? "'" . addslashes($currentImage) . "'" : 'null' }},
        isDragging: false,
        setFile(file) {
            if (!file) return;
            this.fileName = file.name;
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = () => { this.preview = reader.result; };
                reader.readAsDataURL(file);
            }
        },
        onSelect(e) { this.setFile(e.target.files && e.target.files[0]); },
        onDrop(e) {
            const file = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0];
            if (!file) return;
            this.setFile(file);
            if (window.DataTransfer) {
                const dt = new DataTransfer();
                dt.items.add(file);
                this.$refs.input.files = dt.files;
            }
        },
    }"
>
    @if ($label)
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ $label }}</label>
    @endif

    <template x-if="preview">
        <div class="mb-3">
            <img :src="preview" alt="" class="mx-auto max-h-40 rounded-lg border border-gray-200 object-cover dark:border-gray-800">
        </div>
    </template>

    <div
        @click="$refs.input.click()"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="isDragging = false; onDrop($event)"
        :class="isDragging ? 'border-brand-500 bg-brand-50 dark:bg-brand-500/10' : 'border-gray-300 bg-gray-50 hover:border-brand-400 dark:border-gray-700 dark:bg-white/[0.03] dark:hover:border-brand-500'"
        class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed px-6 py-8 text-center transition"
    >
        <svg class="fill-brand-500/80" width="28" height="28" viewBox="0 0 24 24"><path d="M12 16a2 2 0 0 0 2-2v-6h-4v6a2 2 0 0 0 2 2zm6-5v2a6 6 0 1 1-12 0v-2h2v2a4 4 0 1 0 8 0v-2h2z" fill="currentColor"/></svg>
        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('admin.form.click_to_upload') }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="fileName ? fileName : '{{ __('admin.form.drag_drop_hint') }}'"></p>
    </div>

    <input x-ref="input" type="file" name="{{ $name }}" accept="{{ $accept }}" class="sr-only" @change="onSelect($event)">
</div>