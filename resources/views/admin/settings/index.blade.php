@extends('admin.layout')
@section('title', __('Store Settings'))

@section('content')
    <div class="max-w-3xls mx-auto space-y-6">
        <!-- Header -->
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm">
            <div>
                <h2 class="text-base font-extrabold text-stone-900">{{ __('Store Settings') }}</h2>
                <p class="text-xs text-stone-500">
                    {{ __('Configure store operation details, minimum orders, and social links.') }}</p>
            </div>
            <a href="{{ route('admin.dashboard') }}"
                class="px-3.5 py-2 rounded-xl border border-stone-200 text-stone-600 hover:bg-stone-50 font-bold text-xs transition">
                {{ __('Dashboard') }}
            </a>
        </div>

        <!-- Settings Form Card -->
        <div class="bg-white border border-stone-200/80 rounded-2xl p-6 shadow-sm">
            <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-5 text-xs">
                @csrf

                <!-- Store Status -->
                <div>
                    <label class="block font-bold text-stone-700 mb-1">
                        {{ __('Store Status Text') }} <span
                            class="text-stone-400 font-normal">({{ __('e.g. Open in Kuwait') }})</span>
                    </label>
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 pl-3.5 rtl:pl-0 rtl:pr-3.5 flex items-center pointer-events-none text-stone-400">
                            <i class="fa-solid fa-store text-xs"></i>
                        </div>
                        <input type="text" name="store_status"
                            value="{{ old('store_status', $settings['store_status'] ?? 'Open in Kuwait') }}"
                            placeholder="Open in Kuwait" required
                            class="w-full border border-stone-200 rounded-xl py-2.5 pl-9 pr-3.5 rtl:pr-9 rtl:pl-3.5 outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Minimum Order Amount -->
                    <div>
                        <label class="block font-bold text-stone-700 mb-1">
                            {{ __('Minimum Order Amount (KD)') }} <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div
                                class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 pl-3.5 rtl:pl-0 rtl:pr-3.5 flex items-center pointer-events-none text-stone-400">
                                <i class="fa-solid fa-coins text-xs"></i>
                            </div>
                            <input type="number" step="0.001" min="0" name="min_order"
                                value="{{ old('min_order', $settings['min_order'] ?? '3.750') }}" placeholder="3.750"
                                required
                                class="w-full border border-stone-200 rounded-xl py-2.5 pl-9 pr-3.5 rtl:pr-9 rtl:pl-3.5 outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] transition">
                        </div>
                    </div>

                    <!-- Default Delivery Fee -->
                    <div>
                        <label class="block font-bold text-stone-700 mb-1">
                            {{ __('Default Delivery Fee (KD)') }} <span
                                class="text-stone-400 font-normal">({{ __('Optional') }})</span>
                        </label>
                        <div class="relative">
                            <div
                                class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 pl-3.5 rtl:pl-0 rtl:pr-3.5 flex items-center pointer-events-none text-stone-400">
                                <i class="fa-solid fa-truck text-xs"></i>
                            </div>
                            <input type="number" step="0.001" min="0" name="default_delivery_fee"
                                value="{{ old('default_delivery_fee', $settings['default_delivery_fee'] ?? '0.950') }}"
                                placeholder="0.950"
                                class="w-full border border-stone-200 rounded-xl py-2.5 pl-9 pr-3.5 rtl:pr-9 rtl:pl-3.5 outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] transition">
                        </div>
                    </div>
                </div>

                <!-- Operating Hours -->
                <div>
                    <label class="block font-bold text-stone-700 mb-1">
                        {{ __('Operating Hours') }} <span
                            class="text-stone-400 font-normal">({{ __('e.g. 7:00 AM - 11:30 PM') }})</span>
                    </label>
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 pl-3.5 rtl:pl-0 rtl:pr-3.5 flex items-center pointer-events-none text-stone-400">
                            <i class="fa-regular fa-clock text-xs"></i>
                        </div>
                        <input type="text" name="operating_hours"
                            value="{{ old('operating_hours', $settings['operating_hours'] ?? '7:00 AM - 11:30 PM') }}"
                            placeholder="7:00 AM - 11:30 PM" required
                            class="w-full border border-stone-200 rounded-xl py-2.5 pl-9 pr-3.5 rtl:pr-9 rtl:pl-3.5 outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] transition">
                    </div>
                </div>

                <!-- Instagram Handle -->
                <div>
                    <label class="block font-bold text-stone-700 mb-1">
                        {{ __('Instagram Handle') }} <span
                            class="text-stone-400 font-normal">({{ __('e.g. @otherwisekw') }})</span>
                    </label>
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 rtl:left-auto rtl:right-0 pl-3.5 rtl:pl-0 rtl:pr-3.5 flex items-center pointer-events-none text-stone-400">
                            <i class="fa-brands fa-instagram text-xs"></i>
                        </div>
                        <input type="text" name="instagram_handle"
                            value="{{ old('instagram_handle', $settings['instagram_handle'] ?? '@otherwisekw') }}"
                            placeholder="@otherwisekw"
                            class="w-full border border-stone-200 rounded-xl py-2.5 pl-9 pr-3.5 rtl:pr-9 rtl:pl-3.5 outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] transition">
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="flex items-center space-x-2 rtl:space-x-reverse pt-4 border-t border-stone-100">
                    <a href="{{ route('admin.dashboard') }}"
                        class="py-3 px-5 border border-stone-200 rounded-xl font-bold text-stone-600 hover:bg-stone-50 transition text-center">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit"
                        class="flex-1 py-3 bg-[#8F966C] hover:bg-[#7B825B] text-white rounded-xl font-bold shadow-sm transition active:scale-95 text-center flex items-center justify-center space-x-1.5 rtl:space-x-reverse">
                        <i class="fa-solid fa-floppy-disk text-[11px]"></i>
                        <span>{{ __('Save Settings') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
