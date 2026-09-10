@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb :title="__('admin.settings.title')" :breadcrumbs="[__('admin.settings.title') => null]" />

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
        <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('admin.settings.general') }}</h3>
    </div>
    <form method="POST" action="{{ route('admin.settings.update') }}" class="p-6">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.settings.site_name') }}</label>
                <input type="text" name="site_name" value="{{ $settings['site_name'] ?? '' }}" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.settings.site_email') }}</label>
                <input type="email" name="site_email" value="{{ $settings['site_email'] ?? '' }}" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.settings.site_phone') }}</label>
                <input type="text" name="site_phone" value="{{ $settings['site_phone'] ?? '' }}" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.settings.site_logo') }}</label>
                <input type="text" name="site_logo" value="{{ $settings['site_logo'] ?? '' }}" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.settings.currency') }}</label>
                <input type="text" name="currency" value="{{ $settings['currency'] ?? '' }}" placeholder="{{ __('admin.settings.currency_placeholder') }}" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.settings.tax_rate') }}</label>
                <input type="number" name="tax_rate" step="0.01" value="{{ $settings['tax_rate'] ?? '' }}" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.settings.shipping_fee') }}</label>
                <input type="number" name="shipping_fee" step="0.01" value="{{ $settings['shipping_fee'] ?? '' }}" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.settings.free_shipping_threshold') }}</label>
                <input type="number" name="free_shipping_threshold" step="0.01" value="{{ $settings['free_shipping_threshold'] ?? '' }}" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            </div>
            <div class="sm:col-span-2">
                <div x-data="{ checkboxToggle: {{ ($settings['maintenance_mode'] ?? '') === 'true' ? 'true' : 'false' }} }">
                    <label class="flex cursor-pointer items-center select-none">
                        <div class="relative">
                            <input type="hidden" name="maintenance_mode" value="false" />
                            <input type="checkbox" name="maintenance_mode" value="true" x-model="checkboxToggle" class="sr-only" />
                            <div :class="checkboxToggle ? 'border-brand-500 bg-brand-500' : 'bg-transparent border-gray-300 dark:border-gray-700'"
                                class="mr-3 flex h-5 w-5 items-center justify-center rounded-md border-[1.25px]">
                                <span :class="checkboxToggle ? '' : 'opacity-0'">
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M11.6666 3.5L5.24992 9.91667L2.33325 7" stroke="white" stroke-width="1.94437" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                </span>
                            </div>
                        </div>
                        <span class="text-sm font-normal text-gray-700 dark:text-gray-400">{{ __('admin.settings.maintenance_mode') }}</span>
                    </label>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
        <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('admin.settings.legal') }}</h3>
    </div>
    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6 p-6">
        @csrf
        @method('PUT')
        @include('admin.settings._document-editor', [
            'docKey' => 'privacy_policy',
            'docTitle' => __('admin.settings.privacy_policy'),
            'document' => $documents['privacy_policy'],
        ])
        @include('admin.settings._document-editor', [
            'docKey' => 'terms_conditions',
            'docTitle' => __('admin.settings.terms_conditions'),
            'document' => $documents['terms_conditions'],
        ])
        <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-6 py-3 text-sm font-medium text-white transition hover:bg-brand-600">{{ __('admin.settings.save') }}</button>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://cdn.ckeditor.com/4.22.1/full-all/ckeditor.js"></script>
<script>
    function settingsEditor(key, initial) {
        const emptySection = () => ({ heading: { en: '', ar: '' }, body: { en: '', ar: '' } });

        return {
            key: key,
            title: initial.title || { en: '', ar: '' },
            sections: Array.isArray(initial.sections) ? initial.sections : [],
            _syncPending: false,
            _observer: null,

            init() {
                this._observer = new MutationObserver(() => this.scheduleSync());
                this._observer.observe(this.$el, { childList: true, subtree: true });
                this.scheduleSync();
            },

            _nativeOf(editor) {
                return editor.element && (editor.element.$ || editor.element);
            },

            _flush() {
                this.$el.querySelectorAll('textarea[data-ck]').forEach((ta) => {
                    const section = this.sections[ta.dataset.index];
                    if (!section) return;

                    const instance = Object.values(window.CKEDITOR.instances).find((editor) => this._nativeOf(editor) === ta);
                    if (!instance || instance.status !== 'ready') return;

                    try {
                        section[ta.dataset.field][ta.dataset.lang] = instance.getData();
                    } catch (e) {
                        // ignore a still-settling editor
                    }
                });
            },

            _destroyOwn() {
                Object.values(window.CKEDITOR.instances).forEach((instance) => {
                    const el = this._nativeOf(instance);

                    if (!el || !(el instanceof window.Node) || !this.$el.contains(el)) {
                        return;
                    }

                    try {
                        instance.destroy(true);
                    } catch (e) {
                        // ignore a partially loaded instance
                    }
                });
            },

            _rebuild() {
                if (!window.CKEDITOR) return;

                this._flush();
                this._destroyOwn();
            },

            addSection() {
                this._rebuild();
                this.sections.push(emptySection());
            },

            removeSection(index) {
                this._rebuild();
                this.sections.splice(index, 1);
            },

            scheduleSync() {
                if (this._syncPending) return;

                this._syncPending = true;
                setTimeout(() => {
                    this._syncPending = false;
                    this.syncEditors();
                }, 0);
            },

            syncEditors(retries = 5) {
                if (!window.CKEDITOR) return;

                const textareas = Array.from(this.$el.querySelectorAll('textarea[data-ck]'));
                const live = new Set(textareas);

                Object.values(window.CKEDITOR.instances).forEach((instance) => {
                    const el = this._nativeOf(instance);

                    if (!el || !(el instanceof window.Node) || !this.$el.contains(el)) {
                        return;
                    }

                    if (!live.has(el)) {
                        try {
                            instance.destroy(true);
                        } catch (e) {
                            // ignore a partially loaded instance
                        }
                    }
                });

                let uninitialized = 0;

                textareas.forEach((ta) => {
                    const exists = Object.values(window.CKEDITOR.instances).some((editor) => this._nativeOf(editor) === ta);
                    if (exists) return;
                    uninitialized += 1;

                    try {
                        window.CKEDITOR.replace(ta, {
                            height: ta.dataset.field === 'heading' ? 80 : 220,
                            toolbar: [
                                ['Bold', 'Italic', 'Underline', 'Strike'],
                                ['Font', 'FontSize', 'TextColor', 'BGColor'],
                                ['JustifyLeft', 'JustifyCenter', 'JustifyRight'],
                                ['NumberedList', 'BulletedList'],
                                ['Link', 'Unlink'],
                                ['RemoveFormat', 'Undo', 'Redo'],
                            ],
                            language: document.documentElement.lang,
                            removePlugins: 'elementspath',
                        });
                    } catch (e) {
                        // ignore replace failures if the textarea is mid-render
                    }
                });

                if (uninitialized > 0 && retries > 0) {
                    requestAnimationFrame(() => this.syncEditors(retries - 1));
                }
            },
        };
    }
</script>
@endpush
@endsection
