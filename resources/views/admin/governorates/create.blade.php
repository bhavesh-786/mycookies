@extends('admin.layout')
@section('title', isset($governorate) ? __('Edit Governorate') : __('Create Governorate'))

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm">
            <div>
                <h2 class="text-base font-extrabold text-stone-900">
                    {{ isset($governorate) ? __('Edit Governorate') : __('Create Governorate') }}
                </h2>
                <p class="text-xs text-stone-500">
                    {{ isset($governorate) ? __('Update regional governorate details.') : __('Add a new regional governorate to your delivery network.') }}
                </p>
            </div>
            <a href="{{ route('admin.governorates.index') }}"
                class="px-3.5 py-2 rounded-xl border border-stone-200 text-stone-600 hover:bg-stone-50 font-bold text-xs transition">
                {{ __('Back to List') }}
            </a>
        </div>

        <!-- Card Form -->
        <div class="bg-white border border-stone-200/80 rounded-2xl p-6 shadow-sm">
            <form
                action="{{ isset($governorate) ? route('admin.governorates.update', $governorate) : route('admin.governorates.store') }}"
                method="POST" class="space-y-4 text-xs">
                @csrf
                @if (isset($governorate))
                    @method('PUT')
                @endif

                <!-- Name (English) -->
                <div>
                    <label class="block font-bold text-stone-700 mb-1">
                        {{ __('Name (English)') }} <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name_en"
                        value="{{ old('name_en', $governorate->name_en ?? ($governorate->name ?? '')) }}"
                        placeholder="e.g. Al Asimah" required
                        class="w-full border @error('name_en') border-rose-400 bg-rose-50/30 @else border-stone-200 @enderror rounded-xl p-3 outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] transition">
                    @error('name_en')
                        <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name (Arabic) -->
                <div>
                    <label class="block font-bold text-stone-700 mb-1">
                        {{ __('Name (Arabic)') }}
                    </label>
                    <input type="text" name="name_ar" value="{{ old('name_ar', $governorate->name_ar ?? '') }}"
                        placeholder="مثال: العاصمة" dir="rtl"
                        class="w-full border @error('name_ar') border-rose-400 bg-rose-50/30 @else border-stone-200 @enderror rounded-xl p-3 outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] transition">
                    @error('name_ar')
                        <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Footer Buttons -->
                <div class="flex items-center space-x-2 rtl:space-x-reverse pt-4 border-t border-stone-100">
                    <a href="{{ route('admin.governorates.index') }}"
                        class="py-3 px-5 border border-stone-200 rounded-xl font-bold text-stone-600 hover:bg-stone-50 transition text-center">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit"
                        class="flex-1 py-3 bg-[#8F966C] hover:bg-[#7B825B] text-white rounded-xl font-bold shadow-sm transition active:scale-95 text-center flex items-center justify-center space-x-1.5 rtl:space-x-reverse">
                        <i class="fa-solid fa-floppy-disk text-[11px]"></i>
                        <span>{{ isset($governorate) ? __('Update Governorate') : __('Save Governorate') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
