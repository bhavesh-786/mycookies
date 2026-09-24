@extends('admin.layout')
@section('title', 'Product: ' . $product->name)

@section('content')
    <div class="max-full -w-5xl space-y-6">
        <!-- Top Action & Navigation Bar -->
        <div class="flex items-center justify-between bg-white p-4 rounded-2xl border border-stone-200/80 shadow-sm">
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.products.index') }}"
                    class="w-9 h-9 rounded-xl border border-stone-200 flex items-center justify-center text-xs text-stone-600 hover:bg-stone-50 transition active:scale-95">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <div class="flex items-center space-x-2">
                        <h2 class="font-extrabold text-base text-stone-900">{{ $product->name }}</h2>
                        @if ($product->name_ar)
                            <span class="text-sm font-bold text-stone-500 font-sans"
                                dir="rtl">({{ $product->name_ar }})</span>
                        @endif
                    </div>
                    <span class="text-xs text-stone-400">Category: <strong
                            class="text-stone-700">{{ $product->category->name ?? 'Uncategorized' }}</strong>
                        @if (!empty($product->category->name_ar))
                            <span dir="rtl">({{ $product->category->name_ar }})</span>
                        @endif
                    </span>
                </div>
            </div>

            <a href="{{ route('admin.products.edit', $product->id) }}"
                class="inline-flex items-center space-x-1.5 bg-[#b5122b] hover:bg-rose-900 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition shadow-sm active:scale-95">
                <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                <span>Edit Product</span>
            </a>
        </div>

        <!-- Product Overview & Dual-Language Details -->
        <div class="bg-white border border-stone-200/80 rounded-2xl p-6 shadow-sm space-y-6">
            <div class="flex flex-col md:flex-row items-start gap-6">
                <!-- Product Image -->
                <div
                    class="w-full md:w-48 h-40 rounded-2xl overflow-hidden bg-stone-100 border border-stone-200/80 shrink-0">
                    <img src="{{ $product->image ?? 'https://placehold.co/400x300' }}" alt="{{ $product->name }}"
                        class="w-full h-full object-cover">
                </div>

                <!-- Price & Attributes -->
                <div class="flex-1 space-y-4 w-full">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pb-4 border-b border-stone-100 text-xs">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-stone-400 tracking-wider">Base Price</span>
                            <div class="text-lg font-black text-[#b5122b] mt-0.5">
                                {{ $product->base_price !== null ? number_format($product->base_price, 3) . ' KD' : 'Price on selection' }}
                            </div>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-stone-400 tracking-wider">Slug</span>
                            <div class="font-mono text-stone-700 mt-1 truncate">{{ $product->slug }}</div>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-stone-400 tracking-wider">Add-on Groups</span>
                            <div class="font-bold text-stone-800 mt-1">{{ $product->addonGroups->count() }} Groups</div>
                        </div>
                    </div>

                    <!-- Side-by-Side English & Arabic Text -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- English Card -->
                        <div class="bg-stone-50 border border-stone-200/70 p-4 rounded-xl space-y-2">
                            <div
                                class="flex items-center space-x-2 text-[11px] font-black uppercase text-stone-500 border-b border-stone-200/60 pb-1.5">
                                <span class="bg-white border px-1.5 py-0.2 rounded text-[10px] text-stone-700">EN</span>
                                <span>English Information</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-stone-400">Name</span>
                                <div class="text-xs font-bold text-stone-900">{{ $product->name }}</div>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-stone-400">Description</span>
                                <p class="text-xs text-stone-600 leading-relaxed">
                                    {{ $product->description ?? 'No English description provided.' }}</p>
                            </div>
                        </div>

                        <!-- Arabic Card -->
                        <div class="bg-stone-50 border border-stone-200/70 p-4 rounded-xl space-y-2 text-right"
                            dir="rtl">
                            <div
                                class="flex items-center justify-between text-[11px] font-black uppercase text-stone-500 border-b border-stone-200/60 pb-1.5">
                                <span>البيانات بالعربية</span>
                                <span class="bg-white border px-1.5 py-0.2 rounded text-[10px] text-stone-700">AR</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-stone-400">الاسم</span>
                                <div class="text-xs font-bold text-stone-900">
                                    {{ $product->name_ar ?: 'لم يتم إدخال اسم بالعربية' }}</div>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-stone-400">الوصف</span>
                                <p class="text-xs text-stone-600 leading-relaxed">
                                    {{ $product->description_ar ?: 'لم يتم إدخال وصف بالعربية.' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bilingual Add-on Groups & Customizations Card -->
        <div class="bg-white border border-stone-200/80 rounded-2xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                <div>
                    <h3 class="font-extrabold text-xs uppercase tracking-wider text-stone-800">Add-on Groups &
                        Customizations</h3>
                    <p class="text-[11px] text-stone-400">English and Arabic options displayed side-by-side</p>
                </div>
                <span class="text-xs font-bold bg-stone-100 px-2.5 py-1 rounded-lg text-stone-600 border border-stone-200">
                    {{ $product->addonGroups->count() }} Groups Configured
                </span>
            </div>

            @forelse($product->addonGroups as $group)
                <div class="border border-stone-200 rounded-2xl p-4 bg-stone-50/60 space-y-3">
                    <!-- Group Header: EN vs AR -->
                    <div
                        class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-stone-200/80 pb-2.5">
                        <div class="flex items-center space-x-3 text-xs">
                            <span class="font-black text-stone-900 uppercase">{{ $group->name }}</span>
                            @if ($group->name_ar)
                                <span class="font-bold text-stone-600" dir="rtl">({{ $group->name_ar }})</span>
                            @endif
                            <span
                                class="px-2 py-0.5 rounded text-[10px] font-bold {{ $group->is_required ? 'bg-rose-50 text-[#b5122b] border border-rose-200' : 'bg-stone-200 text-stone-600' }}">
                                {{ $group->is_required ? 'Required' : 'Optional' }}
                            </span>
                        </div>

                        <div class="text-[11px] text-stone-500 font-semibold">
                            Type: <span class="capitalize text-stone-800 font-bold">{{ $group->type }}</span> (Max:
                            {{ $group->max_selectable }})
                        </div>
                    </div>

                    <!-- Options List (Split EN / AR with Price) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5 pt-1">
                        @foreach ($group->options as $opt)
                            <div
                                class="bg-white border border-stone-200/80 p-3 rounded-xl shadow-xs flex items-center justify-between text-xs">
                                <div class="space-y-0.5">
                                    <div class="font-bold text-stone-800 flex items-center space-x-1.5">
                                        <span
                                            class="text-[10px] bg-stone-100 text-stone-500 px-1 py-0.2 rounded font-mono">EN</span>
                                        <span>{{ $opt->name }}</span>
                                    </div>
                                    <div class="text-stone-500 flex items-center space-x-1.5" dir="rtl">
                                        <span
                                            class="text-[10px] bg-stone-100 text-stone-500 px-1 py-0.2 rounded font-mono mr-1">AR</span>
                                        <span>{{ $opt->name_ar ?: '—' }}</span>
                                    </div>
                                </div>
                                <div
                                    class="font-black text-[#b5122b] text-[13px] whitespace-nowrap pl-3 border-l border-stone-100">
                                    + {{ number_format($opt->price, 3) }} <span
                                        class="text-[10px] text-stone-400 font-bold">KD</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-stone-400 text-xs border border-dashed rounded-xl">
                    No add-ons or modifiers configured for this product.
                </div>
            @endforelse
        </div>
    </div>
@endsection
