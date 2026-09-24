@extends('admin.layout')
@section('title', __('Products & Addons'))

@section('content')
    <div class="space-y-6">
        <!-- Header Summary & Action Bar -->
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm">
            <div class="space-y-1">
                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-stone-100 text-stone-700">
                        {{ $products->total() }} {{ __('Total Items') }}
                    </span>
                </div>
                <p class="text-xs text-stone-500">
                    {{ __('Manage all store products, prices, and customizable add-on groups.') }}</p>
            </div>

            <a href="{{ route('admin.products.create') }}"
                class="inline-flex items-center space-x-2 rtl:space-x-reverse bg-[#b5122b] hover:bg-[#970e23] text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm hover:shadow transition active:scale-95">
                <i class="fa-solid fa-plus text-[11px]"></i>
                <span>{{ __('Add New Product') }}</span>
            </a>
        </div>

        <!-- Products Table -->
        <div class="bg-white border border-stone-200/80 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left rtl:text-right border-collapse">
                    <thead>
                        <tr
                            class="bg-stone-50/75 border-b border-stone-200 text-stone-500 font-bold text-[11px] uppercase tracking-wider">
                            <th class="py-3.5 px-5">{{ __('Product') }}</th>
                            <th class="py-3.5 px-4">{{ __('Category') }}</th>
                            <th class="py-3.5 px-4">{{ __('Base Price') }}</th>
                            <th class="py-3.5 px-4">{{ __('Add-on Groups & Modifiers') }}</th>
                            <th class="py-3.5 px-5 text-right rtl:text-left">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-xs">
                        @forelse($products as $p)
                            <tr class="hover:bg-stone-50/60 transition duration-150 group">
                                <!-- Product thumbnail & localized name -->
                                <td class="py-4 px-5">
                                    <div class="flex items-center space-x-3.5 rtl:space-x-reverse">
                                        <div
                                            class="w-12 h-12 rounded-xl overflow-hidden bg-stone-100 border border-stone-200/70 shrink-0">
                                            <img src="{{ $p->image ?? 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?w=200' }}"
                                                alt="{{ $p->display_name }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                        </div>
                                        <div>
                                            <span
                                                class="font-extrabold text-stone-900 group-hover:text-[#b5122b] transition">{{ $p->display_name }}</span>
                                            <p class="text-[11px] text-stone-400 line-clamp-1 max-w-xs mt-0.5">
                                                {{ $p->display_description ?? __('No description provided.') }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Category badge -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-stone-100 text-stone-700 border border-stone-200">
                                        {{ $p->category->display_name ?? __('Uncategorized') }}
                                    </span>
                                </td>

                                <!-- Price -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    @if ($p->base_price !== null)
                                        <span class="font-black text-stone-900 text-[13px]">
                                            {{ number_format($p->base_price, 3) }} <span
                                                class="text-[10px] text-stone-500 font-bold">{{ __('KD') }}</span>
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            {{ __('Price on selection') }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Add-ons summary -->
                                <td class="py-4 px-4">
                                    @if ($p->addonGroups && $p->addonGroups->count() > 0)
                                        <div class="space-y-1.5 max-w-md">
                                            @foreach ($p->addonGroups as $grp)
                                                <div class="text-[11px] leading-relaxed">
                                                    <span
                                                        class="font-black text-stone-800 uppercase">{{ $grp->display_name }}</span>
                                                    <span
                                                        class="text-[9px] px-1.5 py-0.5 font-bold uppercase rounded mx-1 {{ $grp->is_required ? 'bg-rose-50 text-[#b5122b] border border-rose-200' : 'bg-stone-100 text-stone-500' }}">
                                                        {{ $grp->is_required ? __('Required') : __('Optional') }}
                                                    </span>
                                                    <div class="text-stone-500 text-[11px] mt-0.5">
                                                        {{ $grp->options->map(fn($o) => $o->display_name)->implode(', ') }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="inline-flex items-center text-stone-400 text-[11px]">
                                            <i class="fa-solid fa-minus text-[9px] mx-1 text-stone-300"></i>
                                            {{ __('No add-ons (Fixed)') }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Action Buttons -->
                                <td class="py-4 px-5 text-right rtl:text-left whitespace-nowrap">
                                    <div class="inline-flex items-center space-x-1.5 rtl:space-x-reverse">
                                        <!-- View Button -->
                                        <a href="{{ route('admin.products.show', $p->id) }}" title="{{ __('View') }}"
                                            class="inline-flex items-center space-x-1 rtl:space-x-reverse px-2.5 py-1.5 rounded-lg text-stone-600 bg-stone-100 hover:bg-stone-200/80 font-bold transition text-[11px]">
                                            <i class="fa-solid fa-eye text-[11px]"></i>
                                            <span>{{ __('View') }}</span>
                                        </a>

                                        <!-- Edit Button -->
                                        <a href="{{ route('admin.products.edit', $p->id) }}" title="{{ __('Edit') }}"
                                            class="inline-flex items-center space-x-1 rtl:space-x-reverse px-2.5 py-1.5 rounded-lg text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-100 font-bold transition text-[11px]">
                                            <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                            <span>{{ __('Edit') }}</span>
                                        </a>

                                        <!-- Delete Button -->
                                        <form action="{{ route('admin.products.delete', $p->id) }}" method="POST"
                                            class="inline-block"
                                            onsubmit="return confirm('{{ __('Are you sure you want to delete') }} {{ $p->display_name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="{{ __('Delete') }}"
                                                class="inline-flex items-center px-2 py-1.5 rounded-lg text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-100 transition text-[11px]">
                                                <i class="fa-solid fa-trash-can text-[11px]"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-stone-400">
                                    <div
                                        class="w-12 h-12 rounded-full bg-stone-100 text-stone-400 flex items-center justify-center mx-auto mb-3 text-lg">
                                        <i class="fa-solid fa-cookie-bite"></i>
                                    </div>
                                    <span class="font-bold text-sm text-stone-700">{{ __('No products found') }}</span>
                                    <p class="text-xs text-stone-400 mt-1">
                                        {{ __('Get started by creating your first product item.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($products->hasPages())
                <div class="p-4 border-t border-stone-200 bg-stone-50/50">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
