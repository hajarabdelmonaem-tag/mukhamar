@extends('layouts.app')

@section('content')
<x.common.page-breadcrumb :title="__('admin.products.add')" :breadcrumbs="[__('admin.products.title') => route('admin.products.index'), __('admin.actions.add') => null]" />

@if ($errors->any())
    <div class="mb-4 rounded-lg border border-error-200 bg-error-50 p-4 text-sm text-error-700 dark:border-error-800 dark:bg-error-950 dark:text-error-300">
        <ul class="list-disc space-y-1 ps-4">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
        <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('admin.products.info') }}</h3>
    </div>
    <form method="POST" action="{{ route('admin.products.store') }}" class="p-6" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.table.category') }}<span class="text-error-500">*</span></label>
                <select name="category_id" required class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="">{{ __('admin.products.select_category') }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.products.name_en') }}<span class="text-error-500">*</span></label>
                <input type="text" name="name[en]" value="{{ old('name.en') }}" required class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.products.name_ar') }}<span class="text-error-500">*</span></label>
                <input type="text" name="name[ar]" value="{{ old('name.ar') }}" dir="rtl" required class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.table.slug') }}</label>
                <input type="text" name="slug" value="{{ old('slug') }}" placeholder="{{ __('admin.categories.auto_slug') }}" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.products.description_en') }}</label>
                <textarea name="description[en]" rows="4" class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">{{ old('description.en') }}</textarea>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.products.description_ar') }}</label>
                <textarea name="description[ar]" rows="4" dir="rtl" class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">{{ old('description.ar') }}</textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.products.usage_instructions_en') }}</label>
                <textarea name="usage_instructions[en]" rows="3" class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">{{ old('usage_instructions.en') }}</textarea>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.products.usage_instructions_ar') }}</label>
                <textarea name="usage_instructions[ar]" rows="3" dir="rtl" class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">{{ old('usage_instructions.ar') }}</textarea>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.table.price') }}<span class="text-error-500">*</span></label>
                <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0" required class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.table.old_price') }}</label>
                <input type="number" name="old_price" value="{{ old('old_price') }}" step="0.01" min="0" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.products.top_notes_en') }}</label>
                <input type="text" name="top_notes[en]" value="{{ old('top_notes.en') }}" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.products.top_notes_ar') }}</label>
                <input type="text" name="top_notes[ar]" value="{{ old('top_notes.ar') }}" dir="rtl" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.products.heart_notes_en') }}</label>
                <input type="text" name="heart_notes[en]" value="{{ old('heart_notes.en') }}" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.products.heart_notes_ar') }}</label>
                <input type="text" name="heart_notes[ar]" value="{{ old('heart_notes.ar') }}" dir="rtl" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.products.base_notes_en') }}</label>
                <input type="text" name="base_notes[en]" value="{{ old('base_notes.en') }}" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.products.base_notes_ar') }}</label>
                <input type="text" name="base_notes[ar]" value="{{ old('base_notes.ar') }}" dir="rtl" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
            </div>
            <div class="sm:col-span-2">
                <x-form.image-gallery :label="__('admin.products.images')" :hint="__('admin.products.images_hint')" />
            </div>
            <div class="sm:col-span-2">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('admin.products.badges') }}</label>
                <div class="flex flex-wrap gap-4">
                    @foreach(['new', 'sale', 'bestseller', 'hot'] as $badge)
                        <label class="flex cursor-pointer items-center">
                            <input type="checkbox" name="badges[]" value="{{ $badge }}" {{ in_array($badge, old('badges', [])) ? 'checked' : '' }} class="sr-only" />
                            <div class="mr-2 flex h-5 w-5 items-center justify-center rounded-md border-[1.25px] border-gray-300 dark:border-gray-700"></div>
                            <span class="text-sm font-normal text-gray-700 dark:text-gray-400">{{ __('admin.status.'.$badge) }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="sm:col-span-2 flex flex-wrap items-center gap-6">
                <div x-data="{ isFeatured: false }">
                    <label class="flex cursor-pointer items-center select-none">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} @change="isFeatured = $event.target.checked" class="sr-only" />
                        <div :class="isFeatured ? 'border-brand-500 bg-brand-500' : 'bg-transparent border-gray-300 dark:border-gray-700'"
                            class="mr-3 flex h-5 w-5 items-center justify-center rounded-md border-[1.25px]">
                            <span :class="isFeatured ? '' : 'opacity-0'"><svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M11.6666 3.5L5.24992 9.91667L2.33325 7" stroke="white" stroke-width="1.94437" stroke-linecap="round" stroke-linejoin="round" /></svg></span>
                        </div>
                        <span class="text-sm font-normal text-gray-700 dark:text-gray-400">{{ __('admin.table.featured') }}</span>
                    </label>
                </div>
                <div x-data="{ isActive: true }">
                    <label class="flex cursor-pointer items-center select-none">
                        <input type="checkbox" name="is_active" value="1" checked @change="isActive = $event.target.checked" class="sr-only" />
                        <div :class="isActive ? 'border-brand-500 bg-brand-500' : 'bg-transparent border-gray-300 dark:border-gray-700'"
                            class="mr-3 flex h-5 w-5 items-center justify-center rounded-md border-[1.25px]">
                            <span :class="isActive ? '' : 'opacity-0'"><svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M11.6666 3.5L5.24992 9.91667L2.33325 7" stroke="white" stroke-width="1.94437" stroke-linecap="round" stroke-linejoin="round" /></svg></span>
                        </div>
                        <span class="text-sm font-normal text-gray-700 dark:text-gray-400">{{ __('admin.status.active') }}</span>
                    </label>
                </div>
            </div>
            <div class="sm:col-span-2" x-data="variantManager()">
                <div class="rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white">{{ __('admin.products.variants') }}</h4>
                        <button type="button" @click="add()" class="inline-flex items-center gap-1 rounded-md bg-brand-500 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-brand-600">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M7 2.33331V11.6666M2.33333 7H11.6667" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" /></svg>
                            {{ __('admin.products.add_variant') }}
                        </button>
                    </div>
                    <div class="p-4">
                        <template x-if="rows.length === 0">
                            <p class="py-4 text-center text-sm text-gray-400 dark:text-gray-500">{{ __('admin.products.variants_empty') }}</p>
                        </template>
                        <div class="space-y-4">
                            <template x-for="(row, index) in rows" :key="row.key">
                                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                                    <div class="mb-3 flex items-center justify-between">
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400" x-text="'#' + (index + 1)"></span>
                                        <button type="button" @click="remove(index)" class="text-xs text-error-500 hover:text-error-600">{{ __('admin.products.remove_variant') }}</button>
                                    </div>
                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                        <div>
                                            <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('admin.variants.name_en') }}<span class="text-error-500">*</span></label>
                                            <input type="text" :name="'variants['+index+'][name][en]'" x-model="row.name.en" required class="dark:bg-dark-900 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('admin.variants.name_ar') }}<span class="text-error-500">*</span></label>
                                            <input type="text" :name="'variants['+index+'][name][ar]'" x-model="row.name.ar" dir="rtl" required class="dark:bg-dark-900 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('admin.variants.unit_en') }}</label>
                                            <input type="text" :name="'variants['+index+'][unit][en]'" x-model="row.unit.en" placeholder="{{ __('admin.variants.unit_placeholder') }}" class="dark:bg-dark-900 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('admin.variants.unit_ar') }}</label>
                                            <input type="text" :name="'variants['+index+'][unit][ar]'" x-model="row.unit.ar" dir="rtl" placeholder="{{ __('admin.variants.unit_placeholder') }}" class="dark:bg-dark-900 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('admin.table.price_adjustment') }}</label>
                                            <input type="number" :name="'variants['+index+'][price_adjustment]'" x-model="row.price_adjustment" step="0.01" class="dark:bg-dark-900 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('admin.table.sku') }}</label>
                                            <input type="text" :name="'variants['+index+'][sku]'" x-model="row.sku" class="dark:bg-dark-900 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('admin.table.stock') }}</label>
                                            <input type="number" :name="'variants['+index+'][stock]'" x-model="row.stock" min="0" class="dark:bg-dark-900 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                        </div>
                                        <div class="flex items-center gap-4 sm:col-span-2">
                                            <label class="flex cursor-pointer items-center select-none">
                                                <input type="checkbox" :name="'variants['+index+'][is_default]'" :checked="row.is_default" @change="row.is_default = $event.target.checked" value="1" class="sr-only" />
                                                <div :class="row.is_default ? 'border-brand-500 bg-brand-500' : 'border-gray-300 dark:border-gray-700 bg-transparent'"
                                                    class="mr-2 flex h-4 w-4 items-center justify-center rounded border-[1.25px]">
                                                    <span :class="row.is_default ? '' : 'opacity-0'"><svg width="12" height="12" viewBox="0 0 14 14" fill="none"><path d="M11.6666 3.5L5.24992 9.91667L2.33325 7" stroke="white" stroke-width="1.94437" stroke-linecap="round" stroke-linejoin="round" /></svg></span>
                                                </div>
                                                <span class="text-xs text-gray-600 dark:text-gray-400">{{ __('admin.table.default') }}</span>
                                            </label>
                                            <label class="flex cursor-pointer items-center select-none">
                                                <input type="checkbox" :name="'variants['+index+'][is_active]'" :checked="row.is_active" @change="row.is_active = $event.target.checked" value="1" class="sr-only" />
                                                <div :class="row.is_active ? 'border-brand-500 bg-brand-500' : 'border-gray-300 dark:border-gray-700 bg-transparent'"
                                                    class="mr-2 flex h-4 w-4 items-center justify-center rounded border-[1.25px]">
                                                    <span :class="row.is_active ? '' : 'opacity-0'"><svg width="12" height="12" viewBox="0 0 14 14" fill="none"><path d="M11.6666 3.5L5.24992 9.91667L2.33325 7" stroke="white" stroke-width="1.94437" stroke-linecap="round" stroke-linejoin="round" /></svg></span>
                                                </div>
                                                <span class="text-xs text-gray-600 dark:text-gray-400">{{ __('admin.status.active') }}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3 sm:col-span-2">
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-6 py-3 text-sm font-medium text-white transition hover:bg-brand-600">{{ __('admin.products.save') }}</button>
                <a href="{{ route('admin.products.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-6 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">{{ __('admin.actions.cancel') }}</a>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function variantManager() {
    return {
        rows: [],
        nextKey: 0,
        add() {
            this.rows.push({
                key: this.nextKey++,
                name: { en: '', ar: '' },
                unit: { en: '', ar: '' },
                price_adjustment: 0,
                sku: '',
                stock: 0,
                is_default: false,
                is_active: true,
            });
        },
        remove(index) {
            this.rows.splice(index, 1);
        },
    };
}
</script>
@endpush