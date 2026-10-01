@extends('admin.layout')
@section('title', __('Categories Management'))

@section('content')
    <div class="space-y-6" x-data="{
        deleteModalOpen: false,
        deleteUrl: '',
        categoryName: '',
        openDeleteModal(url, name) {
            this.deleteUrl = url;
            this.categoryName = name;
            this.deleteModalOpen = true;
        }
    }">
        <!-- Top Action & Overview Bar -->
        <!-- Top Action & Overview Bar with Search -->
        <div
            class="flex flex-col md:flex-row justify-between items-stretch md:items-center gap-4 bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm">
            <div class="space-y-1">
                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-stone-100 text-stone-700">
                        {{ $categories instanceof \Illuminate\Pagination\LengthAwarePaginator ? $categories->total() : $categories->count() }}
                        {{ __('Total Categories') }}
                    </span>
                    <span id="reorder-status"
                        class="hidden text-xs font-semibold text-emerald-600 transition-opacity duration-300">
                        <i class="fa-solid fa-circle-check"></i> {{ __('Order saved!') }}
                    </span>
                </div>
                <p class="text-xs text-stone-500">
                    {{ __('Organize your storefront menu sections. Drag rows to reorder.') }}
                </p>
            </div>

            <!-- Search Form + Create Button -->
            <div class="flex flex-col sm:flex-row items-center gap-3">
                <form method="GET" action="{{ route('admin.categories.index') }}" class="relative w-full sm:w-64">
                    @if (request('sort_by'))
                        <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                    @endif
                    @if (request('sort_dir'))
                        <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="{{ __('Search categories...') }}"
                        class="w-full bg-stone-50/70 border border-stone-200 rounded-xl pl-9 pr-8 rtl:pr-9 rtl:pl-8 py-2 text-xs text-stone-800 placeholder-stone-400 focus:outline-none focus:ring-1 focus:ring-[#8F966C] focus:bg-white transition">
                    <span
                        class="absolute inset-y-0 left-3 rtl:left-auto rtl:right-3 flex items-center pointer-events-none text-stone-400">
                        <i class="fa-solid fa-magnifying-glass text-[11px]"></i>
                    </span>
                    @if (request('search'))
                        <a href="{{ route('admin.categories.index') }}"
                            class="absolute inset-y-0 right-2.5 rtl:right-auto rtl:left-2.5 flex items-center text-stone-400 hover:text-stone-600 text-xs">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </form>

                <a href="{{ route('admin.categories.create') }}"
                    class="w-full sm:w-auto inline-flex justify-center items-center space-x-2 rtl:space-x-reverse bg-[#8F966C] hover:bg-[#7B825B] text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm hover:shadow transition active:scale-95 shrink-0">
                    <i class="fa-solid fa-plus text-[11px]"></i>
                    <span>{{ __('Create New Category') }}</span>
                </a>
            </div>
        </div>

        <!-- Main Grid: Quick-Create Form + Categories Table -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <!-- Quick Add Card -->
            <div class="bg-white border border-stone-200/80 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="flex items-center space-x-2.5 rtl:space-x-reverse pb-3 border-b border-stone-100">
                    <div
                        class="w-8 h-8 rounded-lg bg-rose-50 text-[#8F966C] flex items-center justify-center text-xs shrink-0">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-xs uppercase tracking-wider text-stone-900">
                            {{ __('Quick Add Category') }}
                        </h3>
                        <p class="text-[11px] text-stone-400">{{ __('Instantly add a category to storefront') }}</p>
                    </div>
                </div>

                <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data"
                    class="space-y-3.5 text-xs">
                    @csrf
                    <!-- English Name -->
                    <div>
                        <label class="block font-bold text-stone-700 mb-1">
                            {{ __('Category Name (English)') }} <span class="text-[#8F966C]">*</span>
                        </label>
                        <input type="text" name="name" required placeholder="e.g. COOKIES"
                            class="w-full border border-stone-200 p-2.5 rounded-xl outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] transition text-stone-900">
                    </div>

                    <!-- Arabic Name -->
                    <div>
                        <label class="block font-bold text-stone-700 mb-1">
                            {{ __('اسم القسم (بالعربية)') }}
                        </label>
                        <input type="text" name="name_ar" dir="rtl" placeholder="مثال: كوكيز"
                            class="w-full border border-stone-200 p-2.5 rounded-xl outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] transition text-stone-900">
                    </div>

                    <!-- Dual Image Upload: File -->
                    <div>
                        <label class="block font-bold text-stone-700 mb-1">{{ __('Upload Image File') }}</label>
                        <input type="file" name="image_file" accept="image/*"
                            class="w-full border border-stone-200 p-2 rounded-xl text-stone-600 file:mr-3 rtl:file:mr-0 rtl:file:ml-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-stone-100 file:text-stone-700 hover:file:bg-stone-200 cursor-pointer">
                    </div>

                    <!-- Dual Image Upload: URL -->
                    <div>
                        <label class="block font-bold text-stone-700 mb-1">{{ __('Or Image URL') }}</label>
                        <input type="url" name="image_url" placeholder="https://..."
                            class="w-full border border-stone-200 p-2.5 rounded-xl outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] transition text-stone-900">
                    </div>

                    <button type="submit"
                        class="w-full inline-flex items-center justify-center space-x-2 rtl:space-x-reverse bg-stone-900 hover:bg-black text-white font-bold py-2.5 rounded-xl shadow-sm transition active:scale-95">
                        <i class="fa-solid fa-check text-xs"></i>
                        <span>{{ __('Save Category') }}</span>
                    </button>
                </form>
            </div>

            <!-- Categories List Table -->
            <div class="lg:col-span-2 bg-white border border-stone-200/80 rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left rtl:text-right border-collapse">
                        <thead>
                            <tr
                                class="bg-stone-50/75 border-b border-stone-200 text-stone-500 font-bold text-[11px] uppercase tracking-wider">
                                <!-- Drag Handle Header -->
                                <th class="py-3.5 px-3 w-10 text-center">
                                    <i class="fa-solid fa-arrows-up-down text-stone-400"></i>
                                </th>

                                <!-- SORT BY CATEGORY NAME -->
                                <th class="py-3.5 px-5">
                                    <a href="{{ request()->fullUrlWithQuery([
                                        'sort_by' => 'name',
                                        'sort_dir' => request('sort_by') === 'name' && request('sort_dir') === 'asc' ? 'desc' : 'asc',
                                    ]) }}"
                                        class="inline-flex items-center space-x-1.5 rtl:space-x-reverse hover:text-[#8F966C] transition">
                                        <span>{{ __('Category') }}</span>
                                        <span class="inline-flex flex-col text-[8px] leading-[6px]">
                                            <i
                                                class="fa-solid fa-caret-up {{ request('sort_by') === 'name' && request('sort_dir') === 'asc' ? 'text-[#8F966C]' : 'text-stone-300' }}"></i>
                                            <i
                                                class="fa-solid fa-caret-down {{ request('sort_by') === 'name' && request('sort_dir') === 'desc' ? 'text-[#8F966C]' : 'text-stone-300' }}"></i>
                                        </span>
                                    </a>
                                </th>
                                <th class="py-3.5 px-4">{{ __('Slug') }}</th>
                                <th class="py-3.5 px-4 text-center">{{ __('Products') }}</th>
                                <th class="py-3.5 px-5 text-right rtl:text-left">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="sortable-category-list" class="divide-y divide-stone-100 text-xs">
                            @forelse($categories as $cat)
                                <tr data-id="{{ $cat->id }}"
                                    class="hover:bg-stone-50/60 transition duration-150 group">
                                    <!-- Drag Handle -->
                                    <td
                                        class="py-4 px-3 text-center cursor-grab active:cursor-grabbing text-stone-300 hover:text-stone-600 transition drag-handle">
                                        <i class="fa-solid fa-grip-vertical text-xs"></i>
                                    </td>

                                    <!-- Category Banner & Localized Name -->
                                    <td class="py-4 px-5">
                                        <div class="flex items-center space-x-3.5 rtl:space-x-reverse">
                                            <div
                                                class="w-12 h-10 rounded-xl overflow-hidden bg-stone-100 border border-stone-200/70 shrink-0">
                                                <img src="{{ $cat->image ?? 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=200' }}"
                                                    alt="{{ $cat->display_name }}"
                                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                            </div>
                                            <div>
                                                <span
                                                    class="font-extrabold text-stone-900 group-hover:text-[#8F966C] transition">
                                                    {{ $cat->display_name }}
                                                </span>
                                                @if (app()->getLocale() === 'ar' && !empty($cat->name))
                                                    <span class="block text-[10px] text-stone-400 font-normal"
                                                        dir="ltr">({{ $cat->name }})</span>
                                                @elseif(app()->getLocale() === 'en' && !empty($cat->name_ar))
                                                    <span class="block text-[10px] text-stone-400 font-normal"
                                                        dir="rtl">({{ $cat->name_ar }})</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Slug Badge -->
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <span
                                            class="font-mono text-[11px] text-stone-500 bg-stone-100 px-2 py-0.5 rounded border border-stone-200">
                                            {{ $cat->slug }}
                                        </span>
                                    </td>

                                    <!-- Products Count Badge -->
                                    <!-- Products Count Badge / Link -->
                                    <td class="py-4 px-4 text-center whitespace-nowrap">
                                        <a href="{{ route('admin.products.index', ['category_id' => $cat->id]) }}"
                                            title="{{ __('View products in this category') }}"
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-stone-100 text-stone-700 hover:bg-[#8F966C] hover:text-white transition">
                                            {{ $cat->products_count ?? 0 }}
                                        </a>
                                    </td>

                                    <!-- Action Buttons -->
                                    <td class="py-4 px-5 text-right rtl:text-left whitespace-nowrap">
                                        <div class="inline-flex items-center space-x-1.5 rtl:space-x-reverse">
                                            <!-- View Button -->
                                            <a href="{{ route('admin.categories.show', $cat->id) }}"
                                                title="{{ __('View') }}"
                                                class="inline-flex items-center space-x-1 rtl:space-x-reverse px-2.5 py-1.5 rounded-lg text-stone-600 bg-stone-100 hover:bg-stone-200/80 font-bold transition text-[11px]">
                                                <i class="fa-solid fa-eye text-[11px]"></i>
                                                <span>{{ __('View') }}</span>
                                            </a>

                                            <!-- Edit Button -->
                                            <!-- Edit Button with return_url -->
                                            <a href="{{ route('admin.categories.edit', ['category' => $cat->id, 'return_url' => request()->fullUrl()]) }}"
                                                title="{{ __('Edit') }}"
                                                class="inline-flex items-center space-x-1 rtl:space-x-reverse px-2.5 py-1.5 rounded-lg text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-100 font-bold transition text-[11px]">
                                                <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                                <span>{{ __('Edit') }}</span>
                                            </a>

                                            <!-- Delete Button with Popup Trigger -->
                                            <button type="button"
                                                @click="openDeleteModal('{{ route('admin.categories.delete', $cat->id) }}', '{{ addslashes($cat->display_name) }}')"
                                                title="{{ __('Delete') }}"
                                                class="inline-flex items-center px-2 py-1.5 rounded-lg text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-100 transition text-[11px]">
                                                <i class="fa-solid fa-trash-can text-[11px]"></i>
                                            </button>

                                            <!-- Clone Button -->
                                            <form action="{{ route('admin.categories.clone', $cat->id) }}" method="POST"
                                                class="inline m-0">
                                                @csrf
                                                <button type="submit" title="{{ __('Clone Category') }}"
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
                                    <td colspan="5" class="py-12 text-center text-stone-400">
                                        <div
                                            class="w-12 h-12 rounded-full bg-stone-100 text-stone-400 flex items-center justify-center mx-auto mb-3 text-lg">
                                            <i class="fa-solid fa-folder-open"></i>
                                        </div>
                                        <span
                                            class="font-bold text-sm text-stone-700">{{ __('No categories found') }}</span>
                                        <p class="text-xs text-stone-400 mt-1">
                                            {{ __('Add your first category using the quick form on the left.') }}
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (method_exists($categories, 'hasPages') && $categories->hasPages())
                    <div class="p-4 border-t border-stone-200 bg-stone-50/50">
                        {{ $categories->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>

        </div>

        <!-- Styled Confirmation Modal -->
        <div x-show="deleteModalOpen" x-cloak class="relative z-50">
            <!-- Background Backdrop -->
            <div class="fixed inset-0 bg-stone-900/40 backdrop-blur-xs transition-opacity" x-show="deleteModalOpen"
                x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                @click="deleteModalOpen = false"></div>

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
                                x-text="categoryName"></strong>?
                            {{ __('This action cannot be undone and will affect products attached to it.') }}
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
            const el = document.getElementById('sortable-category-list');
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

                    fetch("{{ route('admin.categories.reorder') }}", {
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
                            console.error('Failed to update category order:', err);
                            alert(
                                "{{ __('Unable to save category ordering. Please try again.') }}"
                            );
                        });
                }
            });
        });
    </script>
@endsection
