@extends('admin.layout')
@section('title', __('Products & Addons'))

@section('content')
    <div class="space-y-6" x-data="{
        deleteModalOpen: false,
        deleteUrl: '',
        productName: '',
        openDeleteModal(url, name) {
            this.deleteUrl = url;
            this.productName = name;
            this.deleteModalOpen = true;
        }
    }">
        <!-- Header Summary & Action Bar -->
        <div
            class="flex flex-col md:flex-row justify-between items-stretch md:items-center gap-4 bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm">
            <div class="space-y-1">
                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-stone-100 text-stone-700">
                        {{ $products->total() }} {{ __('Total Items') }}
                    </span>
                    <span id="reorder-status"
                        class="hidden text-xs font-semibold text-emerald-600 transition-opacity duration-300">
                        <i class="fa-solid fa-circle-check"></i> {{ __('Order saved!') }}
                    </span>
                </div>
                <p class="text-xs text-stone-500">
                    {{ __('Manage store products. Drag items to reorder.') }}
                </p>
            </div>

            <!-- Search Form + Add Button -->
            <div class="flex flex-col sm:flex-row items-center gap-3">
                <form method="GET" action="{{ route('admin.products.index') }}" class="relative w-full sm:w-64">

                    @if (request('category_id'))
                        <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                    @endif

                    @if (request('sort_by'))
                        <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                    @endif
                    @if (request('sort_dir'))
                        <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="{{ __('Search product or category...') }}"
                        class="w-full bg-stone-50/70 border border-stone-200 rounded-xl pl-9 pr-8 rtl:pr-9 rtl:pl-8 py-2 text-xs text-stone-800 placeholder-stone-400 focus:outline-none focus:ring-1 focus:ring-[#8F966C] focus:bg-white transition">
                    <span
                        class="absolute inset-y-0 left-3 rtl:left-auto rtl:right-3 flex items-center pointer-events-none text-stone-400">
                        <i class="fa-solid fa-magnifying-glass text-[11px]"></i>
                    </span>
                    @if (request('search'))
                        <a href="{{ route('admin.products.index') }}"
                            class="absolute inset-y-0 right-2.5 rtl:right-auto rtl:left-2.5 flex items-center text-stone-400 hover:text-stone-600 text-xs">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </form>

                <a href="{{ route('admin.products.create') }}"
                    class="w-full sm:w-auto inline-flex justify-center items-center space-x-2 rtl:space-x-reverse bg-[#8F966C] hover:bg-[#7B825B] text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm hover:shadow transition active:scale-95 shrink-0">
                    <i class="fa-solid fa-plus text-[11px]"></i>
                    <span>{{ __('Add New Product') }}</span>
                </a>
            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white border border-stone-200/80 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                @if (isset($selectedCategory))
                    <div
                        class="flex items-center justify-between bg-stone-100 px-4 py-2.5 rounded-xl text-xs text-stone-700">
                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                            <span class="font-bold">{{ __('Filtering by category:') }}</span>
                            <span
                                class="bg-white px-2 py-0.5 rounded-md border border-stone-200 font-extrabold text-[#8F966C]">
                                {{ $selectedCategory->display_name }}
                            </span>
                        </div>
                        <a href="{{ route('admin.products.index') }}"
                            class="text-stone-500 hover:text-stone-800 font-bold">
                            <i class="fa-solid fa-xmark mr-1"></i> {{ __('Clear Filter') }}
                        </a>
                    </div>
                @endif
                <table class="w-full text-left rtl:text-right border-collapse">
                    <thead>
                        <tr
                            class="bg-stone-50/75 border-b border-stone-200 text-stone-500 font-bold text-[11px] uppercase tracking-wider">
                            <!-- Drag & Drop Handle Header -->
                            <th class="py-3.5 px-3 w-10 text-center">
                                <i class="fa-solid fa-arrows-up-down text-stone-400"></i>
                            </th>

                            <!-- SORT BY PRODUCT -->
                            <th class="py-3.5 px-5">
                                <a href="{{ request()->fullUrlWithQuery([
                                    'sort_by' => 'name',
                                    'sort_dir' => request('sort_by') === 'name' && request('sort_dir') === 'asc' ? 'desc' : 'asc',
                                ]) }}"
                                    class="inline-flex items-center space-x-1.5 rtl:space-x-reverse hover:text-[#8F966C] transition">
                                    <span>{{ __('Product') }}</span>
                                    <span class="inline-flex flex-col text-[8px] leading-[6px]">
                                        <i
                                            class="fa-solid fa-caret-up {{ request('sort_by') === 'name' && request('sort_dir') === 'asc' ? 'text-[#8F966C]' : 'text-stone-300' }}"></i>
                                        <i
                                            class="fa-solid fa-caret-down {{ request('sort_by') === 'name' && request('sort_dir') === 'desc' ? 'text-[#8F966C]' : 'text-stone-300' }}"></i>
                                    </span>
                                </a>
                            </th>

                            <th class="py-3.5 px-4">{{ __('Category') }}</th>

                            <!-- SORT BY BASE PRICE -->
                            <th class="py-3.5 px-4">
                                <a href="{{ request()->fullUrlWithQuery([
                                    'sort_by' => 'price',
                                    'sort_dir' => request('sort_by') === 'price' && request('sort_dir') === 'asc' ? 'desc' : 'asc',
                                ]) }}"
                                    class="inline-flex items-center space-x-1.5 rtl:space-x-reverse hover:text-[#8F966C] transition">
                                    <span>{{ __('Base Price') }}</span>
                                    <span class="inline-flex flex-col text-[8px] leading-[6px]">
                                        <i
                                            class="fa-solid fa-caret-up {{ request('sort_by') === 'price' && request('sort_dir') === 'asc' ? 'text-[#8F966C]' : 'text-stone-300' }}"></i>
                                        <i
                                            class="fa-solid fa-caret-down {{ request('sort_by') === 'price' && request('sort_dir') === 'desc' ? 'text-[#8F966C]' : 'text-stone-300' }}"></i>
                                    </span>
                                </a>
                            </th>

                            <th class="py-3.5 px-4">{{ __('Add-on Groups & Modifiers') }}</th>
                            <th class="py-3.5 px-5 text-right rtl:text-left">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-product-list" class="divide-y divide-stone-100 text-xs">
                        @forelse($products as $p)
                            <tr data-id="{{ $p->id }}" class="hover:bg-stone-50/60 transition duration-150 group">
                                <!-- Reorder Handle Column -->
                                <td
                                    class="py-4 px-3 text-center cursor-grab active:cursor-grabbing text-stone-300 hover:text-stone-600 transition drag-handle">
                                    <i class="fa-solid fa-grip-vertical text-xs"></i>
                                </td>

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
                                                class="font-extrabold text-stone-900 group-hover:text-[#8F966C] transition">{{ $p->display_name }}</span>
                                            <p class="text-[11px] text-stone-400 line-clamp-1 max-w-xs mt-0.5">
                                                {{ $p->display_description ?? __('No description provided.') }}
                                            </p>
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
                                            {{ number_format($p->base_price, 3) }}
                                            <span class="text-[10px] text-stone-500 font-bold">{{ __('KD') }}</span>
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
                                                        class="text-[9px] px-1.5 py-0.5 font-bold uppercase rounded mx-1 {{ $grp->is_required ? 'bg-rose-50 text-[#8F966C] border border-rose-200' : 'bg-stone-100 text-stone-500' }}">
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
                                        <a href="{{ route('admin.products.edit', ['product' => $p->id, 'return_url' => request()->fullUrl()]) }}"
                                            title="{{ __('Edit') }}"
                                            class="inline-flex items-center space-x-1 rtl:space-x-reverse px-2.5 py-1.5 rounded-lg text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-100 font-bold transition text-[11px]">
                                            <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                            <span>{{ __('Edit') }}</span>
                                        </a>

                                        <!-- Delete Button with Popup Trigger -->
                                        <button type="button"
                                            @click="openDeleteModal('{{ route('admin.products.delete', $p->id) }}', '{{ addslashes($p->display_name) }}')"
                                            title="{{ __('Delete') }}"
                                            class="inline-flex items-center px-2 py-1.5 rounded-lg text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-100 transition text-[11px]">
                                            <i class="fa-solid fa-trash-can text-[11px]"></i>
                                        </button>

                                        <!-- Clone Button -->
                                        <form action="{{ route('admin.products.clone', $p->id) }}" method="POST"
                                            class="inline m-0">
                                            @csrf
                                            <button type="submit" title="{{ __('Clone Product') }}"
                                                class="inline-flex items-center space-x-1 rtl:space-x-reverse px-2.5 py-1.5 rounded-lg text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-100 font-bold transition text-[11px]">
                                                <i class="fa-solid fa-clone text-[11px]"></i>
                                                <span>{{ __('Clone') }}</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-stone-400">
                                    <div
                                        class="w-12 h-12 rounded-full bg-stone-100 text-stone-400 flex items-center justify-center mx-auto mb-3 text-lg">
                                        <i class="fa-solid fa-cookie-bite"></i>
                                    </div>
                                    <span class="font-bold text-sm text-stone-700">{{ __('No products found') }}</span>
                                    <p class="text-xs text-stone-400 mt-1">
                                        {{ __('Get started by creating your first product item.') }}
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($products->hasPages())
                <div class="p-4 border-t border-stone-200 bg-stone-50/50">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        <!-- Styled Confirmation Modal -->
        <div x-show="deleteModalOpen" x-cloak class="relative z-50">
            <!-- Background Backdrop -->
            <div class="fixed inset-0 bg-stone-900/40 backdrop-blur-xs transition-opacity" x-show="deleteModalOpen"
                x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                @click="deleteModalOpen = false">
            </div>

            <!-- Modal Window -->
            <div class="fixed inset-0 z-10 overflow-y-auto flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl border border-stone-200/80 text-center space-y-4"
                    x-show="deleteModalOpen" x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95">

                    <div
                        class="w-12 h-12 rounded-full bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center mx-auto text-lg">
                        <i class="fa-solid fa-trash-can"></i>
                    </div>

                    <div class="space-y-1">
                        <h3 class="font-extrabold text-stone-900 text-sm">{{ __('Confirm Deletion') }}</h3>
                        <p class="text-xs text-stone-500">
                            {{ __('Are you sure you want to delete') }} <strong class="text-stone-800"
                                x-text="productName"></strong>? {{ __('This action cannot be undone.') }}
                        </p>
                    </div>

                    <div class="flex items-center space-x-2 rtl:space-x-reverse pt-2">
                        <button type="button" @click="deleteModalOpen = false"
                            class="flex-1 py-2.5 px-4 rounded-xl border border-stone-200 text-stone-600 text-xs font-bold hover:bg-stone-50 transition">
                            {{ __('Cancel') }}
                        </button>

                        <form :action="deleteUrl" method="POST" class="flex-1 m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-sm active:scale-95">
                                {{ __('Yes, Delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Drag & Drop Sorting Script -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById('sortable-product-list');
            const statusIndicator = document.getElementById('reorder-status');

            if (!el) return;

            Sortable.create(el, {
                handle: '.drag-handle',
                animation: 200,
                ghostClass: 'bg-stone-100/90',
                chosenClass: 'bg-stone-50',
                onEnd: function() {
                    const orderedIds = Array.from(el.querySelectorAll('tr[data-id]'))
                        .map(row => row.dataset.id);

                    fetch("{{ route('admin.products.reorder') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                order: orderedIds
                            })
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('Network response failed');
                            return response.json();
                        })
                        .then(() => {
                            if (statusIndicator) {
                                statusIndicator.classList.remove('hidden');
                                setTimeout(() => {
                                    statusIndicator.classList.add('hidden');
                                }, 2500);
                            }
                        })
                        .catch(err => {
                            console.error('Failed to update product order:', err);
                            alert(
                                "{{ __('Unable to save product ordering. Please try again.') }}"
                            );
                        });
                }
            });
        });
    </script>
@endsection
