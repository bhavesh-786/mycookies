@extends('admin.layout')
@section('title', isset($area) ? __('Edit Area') : __('Create Delivery Area'))

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm">
            <div>
                <h2 class="text-base font-extrabold text-stone-900">
                    {{ isset($area) ? __('Edit Area') : __('Create Delivery Area') }}
                </h2>
                <p class="text-xs text-stone-500">
                    {{ isset($area) ? __('Update regional area details, delivery fee, and availability.') : __('Add a new delivery area and assign it to a governorate.') }}
                </p>
            </div>
            <a href="{{ route('admin.areas.index') }}"
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
            <form action="{{ isset($area) ? route('admin.areas.update', $area) : route('admin.areas.store') }}"
                method="POST" class="space-y-4 text-xs">
                @csrf
                @if (isset($area))
                    @method('PUT')
                @endif

                <!-- Governorate Selection -->
                <div>
                    <label class="block font-bold text-stone-700 mb-1">
                        {{ __('Governorate') }} <span class="text-rose-500">*</span>
                    </label>
                    <select name="governorate_id" required
                        class="w-full border @error('governorate_id') border-rose-400 bg-rose-50/30 @else border-stone-200 @enderror rounded-xl p-3 outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] bg-white transition">
                        <option value="">{{ __('Select Governorate') }}</option>
                        @foreach ($governorates as $gov)
                            <option value="{{ $gov->id }}"
                                {{ old('governorate_id', $area->governorate_id ?? '') == $gov->id ? 'selected' : '' }}>
                                {{ $gov->name_en ?? $gov->name }} ({{ $gov->name_ar ?? '—' }})
                            </option>
                        @endforeach
                    </select>
                    @error('governorate_id')
                        <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name (English) -->
                <div>
                    <label class="block font-bold text-stone-700 mb-1">
                        {{ __('Name (English)') }} <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name_en"
                        value="{{ old('name_en', $area->name_en ?? ($area->name ?? '')) }}" placeholder="e.g. Dasman"
                        required
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
                    <input type="text" name="name_ar" value="{{ old('name_ar', $area->name_ar ?? '') }}"
                        placeholder="مثال: دسمان" dir="rtl"
                        class="w-full border @error('name_ar') border-rose-400 bg-rose-50/30 @else border-stone-200 @enderror rounded-xl p-3 outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] transition">
                    @error('name_ar')
                        <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Delivery Fee -->
                <div>
                    <label class="block font-bold text-stone-700 mb-1">
                        {{ __('Delivery Fee (KD)') }} <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 pl-3.5 rtl:pl-0 rtl:pr-3.5 flex items-center pointer-events-none text-stone-400 font-bold">
                            KD
                        </div>
                        <input type="number" step="0.001" min="0" name="delivery_fee"
                            value="{{ old('delivery_fee', $area->delivery_fee ?? '0.950') }}" placeholder="0.950" required
                            class="w-full border @error('delivery_fee') border-rose-400 bg-rose-50/30 @else border-stone-200 @enderror rounded-xl py-3 pl-11 pr-3.5 rtl:pr-11 rtl:pl-3.5 outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] font-mono transition">
                    </div>
                    @error('delivery_fee')
                        <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Active Status -->
                <div class="pt-2">
                    <label class="flex items-center space-x-3 rtl:space-x-reverse cursor-pointer select-none">
                        <input type="checkbox" name="is_active" value="1"
                            {{ old('is_active', $area->is_active ?? 1) ? 'checked' : '' }}
                            class="w-4 h-4 text-[#8F966C] rounded border-stone-300 focus:ring-[#8F966C]">
                        <div>
                            <span class="font-bold text-stone-800 text-xs block">{{ __('Available for Delivery') }}</span>
                            <span
                                class="text-stone-400 text-[11px] block">{{ __('Enable or disable delivery orders to this area.') }}</span>
                        </div>
                    </label>
                </div>

                <!-- Footer Buttons -->
                <div class="flex items-center space-x-2 rtl:space-x-reverse pt-4 border-t border-stone-100">
                    <a href="{{ route('admin.areas.index') }}"
                        class="py-3 px-5 border border-stone-200 rounded-xl font-bold text-stone-600 hover:bg-stone-50 transition text-center">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit"
                        class="flex-1 py-3 bg-[#8F966C] hover:bg-[#7B825B] text-white rounded-xl font-bold shadow-sm transition active:scale-95 text-center flex items-center justify-center space-x-1.5 rtl:space-x-reverse">
                        <i class="fa-solid fa-floppy-disk text-[11px]"></i>
                        <span>{{ isset($area) ? __('Update Area') : __('Save Area') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
