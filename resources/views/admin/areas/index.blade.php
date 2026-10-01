@extends('admin.layout')
@section('title', __('Delivery Areas'))

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm">
            <div>
                <h2 class="text-base font-extrabold text-stone-900">{{ __('Delivery Areas') }}</h2>
                <p class="text-xs text-stone-500">
                    {{ __('Configure regional coverage, custom delivery rates, and area availability.') }}</p>
            </div>
            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                <a href="{{ route('admin.governorates.index') }}"
                    class="px-3.5 py-2.5 rounded-xl border border-stone-200 text-stone-700 hover:bg-stone-50 font-bold text-xs transition">
                    <i class="fa-solid fa-map-location-dot text-[11px] mr-1"></i> {{ __('Governorates') }}
                </a>
                <a href="{{ route('admin.areas.create') }}"
                    class="bg-[#8F966C] hover:bg-[#7B825B] text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition active:scale-95 inline-flex items-center space-x-1.5 rtl:space-x-reverse">
                    <i class="fa-solid fa-plus text-[11px]"></i>
                    <span>{{ __('Add Area') }}</span>
                </a>
            </div>
        </div>

        <!-- Areas Table -->
        <div class="bg-white border border-stone-200/80 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left rtl:text-right border-collapse text-xs">
                    <thead>
                        <tr
                            class="bg-stone-50/75 border-b border-stone-200 text-stone-500 font-bold uppercase text-[11px] tracking-wider">
                            <th class="py-3.5 px-5 w-16">#</th>
                            <th class="py-3.5 px-4">{{ __('Governorate') }}</th>
                            <th class="py-3.5 px-5">{{ __('Area Name (EN)') }}</th>
                            <th class="py-3.5 px-5">{{ __('Area Name (AR)') }}</th>
                            <th class="py-3.5 px-4 text-center">{{ __('Delivery Fee') }}</th>
                            <th class="py-3.5 px-4 text-center">{{ __('Status') }}</th>
                            <th class="py-3.5 px-5 text-right rtl:text-left">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($areas as $area)
                            <tr class="hover:bg-stone-50/60 transition duration-150">
                                <td class="py-4 px-5 font-mono text-stone-400 font-bold">
                                    {{ $area->id }}
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-stone-100 text-stone-700 border border-stone-200">
                                        {{ $area->governorate->name_en ?? ($area->governorate->name ?? 'N/A') }}
                                    </span>
                                </td>
                                <td class="py-4 px-5">
                                    <span class="font-extrabold text-stone-900">{{ $area->name_en ?? $area->name }}</span>
                                </td>
                                <td class="py-4 px-5 text-stone-600 font-semibold" dir="rtl">
                                    {{ $area->name_ar ?? '—' }}
                                </td>
                                <td class="py-4 px-4 text-center whitespace-nowrap font-mono font-bold text-[#8F966C]">
                                    {{ number_format($area->delivery_fee, 3) }} {{ __('KD') }}
                                </td>
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    @if ($area->is_active)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            {{ __('Active') }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            {{ __('Inactive') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-5 text-right rtl:text-left whitespace-nowrap">
                                    <div class="inline-flex items-center space-x-1.5 rtl:space-x-reverse">
                                        <a href="{{ route('admin.areas.edit', $area) }}"
                                            class="inline-flex items-center space-x-1 rtl:space-x-reverse px-2.5 py-1.5 rounded-lg text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-100 font-bold transition text-[11px]">
                                            <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                            <span>{{ __('Edit') }}</span>
                                        </a>

                                        <form action="{{ route('admin.areas.destroy', $area) }}" method="POST"
                                            class="inline m-0"
                                            onsubmit="return confirm('{{ __('Are you sure you want to delete this delivery area?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center space-x-1 rtl:space-x-reverse px-2.5 py-1.5 rounded-lg text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-100 font-bold transition text-[11px]"
                                                title="{{ __('Delete') }}">
                                                <i class="fa-solid fa-trash-can text-[11px]"></i>
                                                <span>{{ __('Delete') }}</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-stone-400">
                                    <i class="fa-solid fa-truck text-2xl mb-2 text-stone-300 block"></i>
                                    {{ __('No delivery areas found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($areas->hasPages())
                <div class="p-4 border-t border-stone-200 bg-stone-50/50">
                    {{ $areas->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
