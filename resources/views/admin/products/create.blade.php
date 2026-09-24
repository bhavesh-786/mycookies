@extends('admin.layout')
@section('title', __('Create Product with Custom Addons'))

@section('content')
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data"
        class="max-full -w-5xl space-y-6" x-data="productAddonManager()">
        @csrf

        <!-- General Info Card -->
        <div class="bg-white border border-stone-200/80 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="font-extrabold text-sm text-stone-900">{{ __('General Information') }}</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <!-- Category -->
                <div class="sm:col-span-2">
                    <label class="block font-bold text-stone-700 mb-1.5">{{ __('Category') }} <span
                            class="text-[#b5122b]">*</span></label>
                    <select name="category_id" required
                        class="w-full border border-stone-200 p-2.5 rounded-xl bg-white outline-none focus:border-[#b5122b]">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->display_name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- English Name -->
                <div>
                    <label class="block font-bold text-stone-700 mb-1.5">{{ __('Product Name (English)') }} <span
                            class="text-[#b5122b]">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        placeholder="e.g. Milk Cookies"
                        class="w-full border border-stone-200 p-2.5 rounded-xl outline-none focus:border-[#b5122b]">
                    @error('name')
                        <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Arabic Name -->
                <div>
                    <label class="block font-bold text-stone-700 mb-1.5">{{ __('Product Name (Arabic)') }}</label>
                    <input type="text" name="name_ar" dir="rtl" value="{{ old('name_ar') }}"
                        placeholder="مثال: كوكيز بالحليب"
                        class="w-full border border-stone-200 p-2.5 rounded-xl outline-none focus:border-[#b5122b]">
                    @error('name_ar')
                        <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Base Price -->
                <div>
                    <label
                        class="block font-bold text-stone-700 mb-1.5">{{ __('Base Price (Blank if Price on selection)') }}</label>
                    <div class="relative">
                        <input type="number" step="0.001" name="base_price" value="{{ old('base_price') }}"
                            placeholder="e.g. 3.750"
                            class="w-full border border-stone-200 p-2.5 rounded-xl outline-none focus:border-[#b5122b]">
                        <span
                            class="absolute right-3 rtl:right-auto rtl:left-3 top-2.5 text-stone-400 font-bold">{{ __('KD') }}</span>
                    </div>
                    @error('base_price')
                        <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Upload File -->
                <div>
                    <label class="block font-bold text-stone-700 mb-1.5">{{ __('Upload Image File') }}</label>
                    <input type="file" name="image_file" accept="image/*"
                        class="w-full border border-stone-200 p-2 rounded-xl text-stone-600 file:mr-3 rtl:file:mr-0 rtl:file:ml-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-stone-100 file:text-stone-700 hover:file:bg-stone-200 cursor-pointer">
                    @error('image_file')
                        <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image URL -->
                <div class="sm:col-span-2">
                    <label class="block font-bold text-stone-700 mb-1.5">{{ __('Or Image URL') }}</label>
                    <input type="url" name="image_url" value="{{ old('image_url') }}"
                        placeholder="https://images.unsplash.com/..."
                        class="w-full border border-stone-200 p-2.5 rounded-xl outline-none focus:border-[#b5122b]">
                    @error('image_url')
                        <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- English Description -->
                <div>
                    <label class="block font-bold text-stone-700 mb-1.5">{{ __('Description (English)') }}</label>
                    <textarea name="description" rows="2" placeholder="Freshly baked warm cookies..."
                        class="w-full border border-stone-200 p-2.5 rounded-xl outline-none focus:border-[#b5122b]">{{ old('description') }}</textarea>
                </div>

                <!-- Arabic Description -->
                <div>
                    <label class="block font-bold text-stone-700 mb-1.5">{{ __('Description (Arabic)') }}</label>
                    <textarea name="description_ar" dir="rtl" rows="2" placeholder="كوكيز طازجة ومحضرة يومياً..."
                        class="w-full border border-stone-200 p-2.5 rounded-xl outline-none focus:border-[#b5122b]">{{ old('description_ar') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Dynamic Addons Builder -->
        <div class="bg-white border border-stone-200/80 rounded-2xl p-6 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 border-b border-stone-100 pb-3">
                <div>
                    <h3 class="font-extrabold text-sm text-stone-900">{{ __('Add-on Groups & Customizations') }}</h3>
                    <p class="text-xs text-stone-400">
                        {{ __('Configure single choices, required selections, or optional add-ons with Arabic translations.') }}
                    </p>
                </div>
                <button type="button" @click="addGroup()"
                    class="bg-stone-900 hover:bg-black text-white text-xs font-bold px-3.5 py-2 rounded-xl flex items-center space-x-1.5 rtl:space-x-reverse transition self-start sm:self-auto active:scale-95">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    <span>{{ __('Add Group') }}</span>
                </button>
            </div>

            <div class="space-y-4">
                <template x-for="(group, gIdx) in groups" :key="gIdx">
                    <div class="border border-stone-200 rounded-xl p-4 bg-stone-50/70 space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 text-xs">
                            <!-- Group Name (EN) -->
                            <div>
                                <input type="text" :name="`groups[${gIdx}][name]`" x-model="group.name"
                                    placeholder="{{ __('Group Name (EN)') }}"
                                    class="w-full border border-stone-200 font-bold p-2 rounded-lg bg-white outline-none focus:border-[#b5122b]"
                                    required>
                            </div>
                            <!-- Group Name (AR) -->
                            <div>
                                <input type="text" :name="`groups[${gIdx}][name_ar]`" x-model="group.name_ar"
                                    dir="rtl" placeholder="{{ __('Group Name (AR)') }}"
                                    class="w-full border border-stone-200 font-bold p-2 rounded-lg bg-white outline-none focus:border-[#b5122b]">
                            </div>
                            <!-- Group Type -->
                            <div>
                                <select :name="`groups[${gIdx}][type]`" x-model="group.type"
                                    class="w-full border border-stone-200 p-2 rounded-lg bg-white outline-none focus:border-[#b5122b]">
                                    <option value="radio">{{ __('Single Choice (Radio)') }}</option>
                                    <option value="checkbox">{{ __('Multiple Choice (Checkbox)') }}</option>
                                </select>
                            </div>
                            <!-- Required & Remove -->
                            <div class="flex items-center justify-between">
                                <label
                                    class="flex items-center space-x-1.5 rtl:space-x-reverse font-bold text-stone-700 cursor-pointer select-none">
                                    <input type="checkbox" :name="`groups[${gIdx}][is_required]`" value="1"
                                        x-model="group.is_required" class="text-[#b5122b] rounded focus:ring-0">
                                    <span>{{ __('Required') }}</span>
                                </label>
                                <button type="button" @click="removeGroup(gIdx)"
                                    class="text-stone-400 hover:text-rose-600 p-2 text-sm transition">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Options -->
                        <div class="space-y-2 pt-2 border-t border-stone-200/60">
                            <div class="flex justify-between items-center">
                                <span
                                    class="text-[11px] font-extrabold uppercase text-stone-400">{{ __('Options') }}</span>
                                <button type="button" @click="addOption(gIdx)"
                                    class="text-xs font-bold text-[#b5122b] hover:underline flex items-center space-x-1 rtl:space-x-reverse">
                                    <i class="fa-solid fa-plus text-[10px]"></i>
                                    <span>{{ __('Add Option') }}</span>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                <template x-for="(opt, oIdx) in group.options" :key="oIdx">
                                    <div
                                        class="flex items-center space-x-2 rtl:space-x-reverse bg-white p-2 border border-stone-200 rounded-lg shadow-sm">
                                        <input type="text" :name="`groups[${gIdx}][options][${oIdx}][name]`"
                                            x-model="opt.name" placeholder="{{ __('Option (EN)') }}"
                                            class="flex-1 text-xs p-1 border-b border-stone-200 outline-none focus:border-[#b5122b]"
                                            required>
                                        <input type="text" :name="`groups[${gIdx}][options][${oIdx}][name_ar]`"
                                            x-model="opt.name_ar" dir="rtl" placeholder="{{ __('Option (AR)') }}"
                                            class="flex-1 text-xs p-1 border-b border-stone-200 outline-none focus:border-[#b5122b]">
                                        <div
                                            class="flex items-center border-l rtl:border-l-0 rtl:border-r border-stone-200 px-2">
                                            <span
                                                class="text-[10px] text-stone-400 font-bold mr-1 rtl:mr-0 rtl:ml-1">{{ __('KD') }}</span>
                                            <input type="number" step="0.001"
                                                :name="`groups[${gIdx}][options][${oIdx}][price]`" x-model="opt.price"
                                                placeholder="0.000"
                                                class="w-16 text-xs p-1 border-0 outline-none font-bold text-[#b5122b]"
                                                required>
                                        </div>
                                        <button type="button" @click="removeOption(gIdx, oIdx)"
                                            class="text-stone-300 hover:text-rose-600 p-1 text-xs">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center space-x-3 rtl:space-x-reverse">
            <button type="submit"
                class="bg-[#b5122b] hover:bg-rose-900 text-white font-extrabold px-6 py-3 rounded-xl text-xs shadow-sm transition active:scale-95">
                {{ __('Save & Publish Product') }}
            </button>
            <a href="{{ route('admin.products.index') }}"
                class="border border-stone-200 bg-white hover:bg-stone-50 text-stone-700 font-bold px-5 py-3 rounded-xl text-xs transition">
                {{ __('Cancel') }}
            </a>
        </div>
    </form>

    <script>
        function productAddonManager() {
            return {
                groups: [{
                    name: 'SIZE SELECTION',
                    name_ar: 'اختيار الحجم',
                    type: 'radio',
                    is_required: true,
                    options: [{
                            name: '6 PCS',
                            name_ar: '6 حبات',
                            price: 3.750
                        },
                        {
                            name: '12 PCS',
                            name_ar: '12 حبة',
                            price: 6.250
                        }
                    ]
                }],
                addGroup() {
                    this.groups.push({
                        name: '',
                        name_ar: '',
                        type: 'checkbox',
                        is_required: false,
                        options: [{
                            name: '',
                            name_ar: '',
                            price: 0.000
                        }]
                    });
                },
                removeGroup(index) {
                    this.groups.splice(index, 1);
                },
                addOption(gIdx) {
                    this.groups[gIdx].options.push({
                        name: '',
                        name_ar: '',
                        price: 0.000
                    });
                },
                removeOption(gIdx, oIdx) {
                    this.groups[gIdx].options.splice(oIdx, 1);
                }
            };
        }
    </script>
@endsection
