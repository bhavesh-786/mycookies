@extends('admin.layout')
@section('title', __('Create New Category'))

@section('content')
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header with Back Button -->
        <div class="flex items-center justify-between bg-white p-4 rounded-2xl border border-stone-200/80 shadow-sm">
            <div class="flex items-center space-x-3 rtl:space-x-reverse">
                <a href="{{ route('admin.categories.index') }}"
                    class="w-9 h-9 rounded-xl border border-stone-200 flex items-center justify-center text-xs text-stone-600 hover:bg-stone-50 transition active:scale-95">
                    <i class="fa-solid fa-arrow-left rtl:rotate-180"></i>
                </a>
                <div>
                    <h2 class="font-extrabold text-sm text-stone-900">{{ __('Create New Category') }}</h2>
                    <p class="text-[11px] text-stone-400">
                        {{ __('Add a new section to organize your menu items on the storefront.') }}</p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white border border-stone-200/80 rounded-2xl p-6 shadow-sm">
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data"
                class="space-y-4 text-xs">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- English Name -->
                    <div>
                        <label class="block font-bold text-stone-700 mb-1.5">
                            {{ __('Category Name (English)') }} <span class="text-[#8F966C]">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. COOKIES"
                            class="w-full border border-stone-200 p-2.5 rounded-xl outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] transition text-stone-900">
                        @error('name')
                            <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Arabic Name -->
                    <div>
                        <label class="block font-bold text-stone-700 mb-1.5">
                            {{ __('Category Name (Arabic)') }}
                        </label>
                        <input type="text" name="name_ar" dir="rtl" value="{{ old('name_ar') }}"
                            placeholder="مثال: كوكيز"
                            class="w-full border border-stone-200 p-2.5 rounded-xl outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] transition text-stone-900">
                        @error('name_ar')
                            <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Upload Image File -->
                <div>
                    <label class="block font-bold text-stone-700 mb-1.5">{{ __('Upload Image File') }}</label>
                    <input type="file" name="image_file" accept="image/*"
                        class="w-full border border-stone-200 p-2 rounded-xl text-stone-600 file:mr-3 rtl:file:mr-0 rtl:file:ml-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-stone-100 file:text-stone-700 hover:file:bg-stone-200 cursor-pointer">
                    @error('image_file')
                        <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image URL -->
                <div>
                    <label class="block font-bold text-stone-700 mb-1.5">{{ __('Or Image URL') }}</label>
                    <input type="url" name="image_url" value="{{ old('image_url') }}"
                        placeholder="https://images.unsplash.com/..."
                        class="w-full border border-stone-200 p-2.5 rounded-xl outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] transition text-stone-900">
                    @error('image_url')
                        <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center space-x-3 rtl:space-x-reverse pt-4 border-t border-stone-100">
                    <button type="submit"
                        class="bg-[#8F966C] hover:bg-[#8F966C] text-white font-extrabold px-6 py-2.5 rounded-xl shadow-sm transition active:scale-95">
                        {{ __('Save & Publish Category') }}
                    </button>
                    <a href="{{ route('admin.categories.index') }}"
                        class="border border-stone-200 bg-white hover:bg-stone-50 text-stone-700 font-bold px-5 py-2.5 rounded-xl transition">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
