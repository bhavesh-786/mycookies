@extends('admin.layout')
@section('title', isset($store) ? __('Edit Store Branch') : __('Create Store Branch'))

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm">
            <div>
                <h2 class="text-base font-extrabold text-stone-900">
                    {{ isset($store) ? __('Edit Store Branch') : __('Create Store Branch') }}
                </h2>
                <p class="text-xs text-stone-500">
                    {{ isset($store) ? __('Update pickup branch details, location address, and availability.') : __('Add a new self-pickup store branch location.') }}
                </p>
            </div>
            <a href="{{ route('admin.pickstores.index') }}"
                class="px-3.5 py-2 rounded-xl border border-stone-200 text-stone-600 hover:bg-stone-50 font-bold text-xs transition">
                {{ __('Back to List') }}
            </a>
        </div>

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Card Form -->
        <div class="bg-white border border-stone-200/80 rounded-2xl p-6 shadow-sm">
            <form action="{{ isset($store) ? route('admin.pickstores.update', $store) : route('admin.pickstores.store') }}"
                method="POST" class="space-y-4 text-xs">
                @csrf
                @if (isset($store))
                    @method('PUT')
                @endif

                <!-- Store Branch Name (English) -->
                <div>
                    <label class="block font-bold text-stone-700 mb-1">
                        {{ __('Store Branch Name (English)') }} <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $store->name ?? '') }}"
                        placeholder="e.g. Shuwaikh Branch" required
                        class="w-full border @error('name') border-rose-400 bg-rose-50/30 @else border-stone-200 @enderror rounded-xl p-3 outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] transition">
                    @error('name')
                        <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Store Branch Name (Arabic) -->
                <div>
                    <label class="block font-bold text-stone-700 mb-1">
                        {{ __('Store Branch Name (Arabic)') }}
                    </label>
                    <input type="text" name="name_ar" value="{{ old('name_ar', $store->name_ar ?? '') }}"
                        placeholder="مثال: فرع الشويخ" dir="rtl"
                        class="w-full border @error('name_ar') border-rose-400 bg-rose-50/30 @else border-stone-200 @enderror rounded-xl p-3 outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] transition">
                    @error('name_ar')
                        <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location / Address Description -->
                <div>
                    <label class="block font-bold text-stone-700 mb-1">
                        {{ __('Location / Address Description') }}
                    </label>
                    <textarea name="location_description" rows="3"
                        placeholder="e.g. Industrial Area, Block 1, Street 12, Opposite Al-Rai Market"
                        class="w-full border @error('location_description') border-rose-400 bg-rose-50/30 @else border-stone-200 @enderror rounded-xl p-3 outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] transition">{{ old('location_description', $store->location_description ?? ($store->description ?? '')) }}</textarea>
                    @error('location_description')
                        <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Active Status -->
                <div class="pt-2">
                    <label class="flex items-center space-x-3 rtl:space-x-reverse cursor-pointer select-none">
                        <input type="checkbox" name="is_active" value="1"
                            {{ old('is_active', $store->is_active ?? 1) ? 'checked' : '' }}
                            class="w-4 h-4 text-[#8F966C] rounded border-stone-300 focus:ring-[#8F966C]">
                        <div>
                            <span class="font-bold text-stone-800 text-xs block">{{ __('Available for Pickup') }}</span>
                            <span
                                class="text-stone-400 text-[11px] block">{{ __('Enable or disable customer pickup orders at this branch.') }}</span>
                        </div>
                    </label>
                </div>

                <!-- Footer Buttons -->
                <div class="flex items-center space-x-2 rtl:space-x-reverse pt-4 border-t border-stone-100">
                    <a href="{{ route('admin.pickstores.index') }}"
                        class="py-3 px-5 border border-stone-200 rounded-xl font-bold text-stone-600 hover:bg-stone-50 transition text-center">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit"
                        class="flex-1 py-3 bg-[#8F966C] hover:bg-[#7B825B] text-white rounded-xl font-bold shadow-sm transition active:scale-95 text-center flex items-center justify-center space-x-1.5 rtl:space-x-reverse">
                        <i class="fa-solid fa-floppy-disk text-[11px]"></i>
                        <span>{{ isset($store) ? __('Update Store Branch') : __('Save Store Branch') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
