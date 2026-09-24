@extends('admin.layout')
@section('title', __('Categories Management'))

@section('content')
    <div class="space-y-6">
        <!-- Top Action & Overview Bar -->
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm">
            <div class="space-y-1">
                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-stone-100 text-stone-700">
                        {{ $categories->count() }} {{ __('Total Categories') }}
                    </span>
                </div>
                <p class="text-xs text-stone-500">
                    {{ __('Organize your storefront menu sections, banners, and product groupings.') }}</p>
            </div>

            <a href="{{ route('admin.categories.create') }}"
                class="inline-flex items-center space-x-2 rtl:space-x-reverse bg-[#b5122b] hover:bg-[#970e23] text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm hover:shadow transition active:scale-95">
                <i class="fa-solid fa-plus text-[11px]"></i>
                <span>{{ __('Create New Category') }}</span>
            </a>
        </div>

        <!-- Main Grid: Quick-Create Form + Categories Table -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <!-- Quick Add Card -->
            <div class="bg-white border border-stone-200/80 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="flex items-center space-x-2.5 rtl:space-x-reverse pb-3 border-b border-stone-100">
                    <div
                        class="w-8 h-8 rounded-lg bg-rose-50 text-[#b5122b] flex items-center justify-center text-xs shrink-0">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-xs uppercase tracking-wider text-stone-900">
                            {{ __('Quick Add Category') }}</h3>
                        <p class="text-[11px] text-stone-400">{{ __('Instantly add a category to storefront') }}</p>
                    </div>
                </div>

                <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data"
                    class="space-y-3.5 text-xs">
                    @csrf
                    <!-- English Name -->
                    <div>
                        <label class="block font-bold text-stone-700 mb-1">
                            {{ __('Category Name (English)') }} <span class="text-[#b5122b]">*</span>
                        </label>
                        <input type="text" name="name" required placeholder="e.g. COOKIES"
                            class="w-full border border-stone-200 p-2.5 rounded-xl outline-none focus:border-[#b5122b] focus:ring-1 focus:ring-[#b5122b] transition text-stone-900">
                    </div>

                    <!-- Arabic Name -->
                    <div>
                        <label class="block font-bold text-stone-700 mb-1">
                            {{ __('اسم القسم (بالعربية)') }}
                        </label>
                        <input type="text" name="name_ar" dir="rtl" placeholder="مثال: كوكيز"
                            class="w-full border border-stone-200 p-2.5 rounded-xl outline-none focus:border-[#b5122b] focus:ring-1 focus:ring-[#b5122b] transition text-stone-900">
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
                            class="w-full border border-stone-200 p-2.5 rounded-xl outline-none focus:border-[#b5122b] focus:ring-1 focus:ring-[#b5122b] transition text-stone-900">
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
                                <th class="py-3.5 px-5">{{ __('Category') }}</th>
                                <th class="py-3.5 px-4">{{ __('Slug') }}</th>
                                <th class="py-3.5 px-4 text-center">{{ __('Products') }}</th>
                                <th class="py-3.5 px-5 text-right rtl:text-left">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100 text-xs">
                            @forelse($categories as $cat)
                                <tr class="hover:bg-stone-50/60 transition duration-150 group">
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
                                                    class="font-extrabold text-stone-900 group-hover:text-[#b5122b] transition">
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
                                    <td class="py-4 px-4 text-center whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-stone-100 text-stone-700">
                                            {{ $cat->products_count ?? 0 }}
                                        </span>
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
                                            <a href="{{ route('admin.categories.edit', $cat->id) }}"
                                                title="{{ __('Edit') }}"
                                                class="inline-flex items-center space-x-1 rtl:space-x-reverse px-2.5 py-1.5 rounded-lg text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-100 font-bold transition text-[11px]">
                                                <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                                <span>{{ __('Edit') }}</span>
                                            </a>

                                            <!-- Delete Button -->
                                            <form action="{{ route('admin.categories.delete', $cat->id) }}" method="POST"
                                                class="inline-block"
                                                onsubmit="return confirm('{{ __('Are you sure you want to delete') }} {{ $cat->display_name }}?')">
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
                                    <td colspan="4" class="py-12 text-center text-stone-400">
                                        <div
                                            class="w-12 h-12 rounded-full bg-stone-100 text-stone-400 flex items-center justify-center mx-auto mb-3 text-lg">
                                            <i class="fa-solid fa-folder-open"></i>
                                        </div>
                                        <span
                                            class="font-bold text-sm text-stone-700">{{ __('No categories found') }}</span>
                                        <p class="text-xs text-stone-400 mt-1">
                                            {{ __('Add your first category using the quick form on the left.') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
