<div x-data="settingsEditor('{{ $docKey }}', {{ json_encode($document) }})" class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
        <h3 class="font-semibold text-gray-800 dark:text-white">{{ $docTitle }}</h3>
    </div>
    <div class="space-y-5 p-6">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.settings.title_en') }}</label>
                <input type="text" x-model="title.en" :name="`${key}[title][en]`"
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.settings.title_ar') }}</label>
                <input type="text" x-model="title.ar" :name="`${key}[title][ar]`" dir="rtl"
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            </div>
        </div>

        <div>
            <template x-for="(section, index) in sections" :key="index">
                <div class="mb-4 rounded-xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-800 dark:bg-gray-950/50">
                    <div class="mb-4 flex items-center justify-between">
                        <span class="text-sm font-semibold text-gray-800 dark:text-white">{{ __('admin.settings.section') }} <span x-text="index + 1"></span></span>
                        <button type="button" @click="removeSection(index)"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-error-200 text-error-500 transition hover:bg-error-50 dark:border-error-800 dark:hover:bg-error-950"
                            :title="'{{ __('admin.settings.remove_section') }}'">✕</button>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.settings.heading_en') }}</label>
                            <textarea x-model="sections[index].heading.en" :name="`${key}[sections][${index}][heading][en]`"
                                data-ck data-field="heading" data-lang="en" :data-index="index" rows="2"
                                class="ck-editor w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"></textarea>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.settings.heading_ar') }}</label>
                            <textarea x-model="sections[index].heading.ar" :name="`${key}[sections][${index}][heading][ar]`"
                                data-ck data-field="heading" data-lang="ar" :data-index="index" rows="2" dir="rtl"
                                class="ck-editor w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"></textarea>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.settings.body_en') }}</label>
                            <textarea x-model="sections[index].body.en" :name="`${key}[sections][${index}][body][en]`"
                                data-ck data-field="body" data-lang="en" :data-index="index" rows="6"
                                class="ck-editor w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"></textarea>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.settings.body_ar') }}</label>
                            <textarea x-model="sections[index].body.ar" :name="`${key}[sections][${index}][body][ar]`"
                                data-ck data-field="body" data-lang="ar" :data-index="index" rows="6" dir="rtl"
                                class="ck-editor w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"></textarea>
                        </div>
                    </div>
                </div>
            </template>

            <button type="button" @click="addSection()"
                class="inline-flex items-center justify-center rounded-lg border border-dashed border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:border-brand-300 hover:text-brand-500 dark:border-gray-700 dark:text-gray-300 dark:hover:border-brand-500 dark:hover:text-brand-400">
                {{ __('admin.settings.add_section') }}
            </button>
        </div>
    </div>
</div>