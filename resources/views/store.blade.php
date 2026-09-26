<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title x-text="pageTitle">{{ __('otherwise') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: {{ app()->getLocale() === 'ar' ? "'Cairo', sans-serif" : "'Plus Jakarta Sans', sans-serif" }};
        }

        .custom-scroll::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #e5e7eb;
            border-radius: 9999px;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-stone-100 text-stone-800 antialiased h-screen overflow-hidden" x-data="storeApp()"
    x-init="initRouter()">

    <div class="flex h-screen w-full">
        <!-- ================= LEFT INTERACTIVE PANEL (50%) ================= -->
        <main
            class="w-full lg:w-1/2 flex flex-col h-full bg-white border-r rtl:border-r-0 rtl:border-l border-stone-200 relative z-10">

            <!-- Sticky Header -->
            <header
                class="px-5 py-3 border-b border-stone-100 flex items-center justify-between bg-white sticky top-0 z-20">
                <div class="flex items-center space-x-3 rtl:space-x-reverse cursor-pointer" @click="navigate('/')">
                    <div
                        class="w-9 h-9 bg-[#8F966C] text-white flex items-center justify-center font-black rounded-lg text-xs tracking-wider uppercase">
                        OW
                    </div>
                    <div>
                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                            <h1 class="font-extrabold text-sm text-stone-900 leading-none tracking-tight">
                                {{ __('otherwise') }}</h1>
                            <i class="fa-solid fa-circle-info text-stone-400 text-xs"></i>
                        </div>
                        <p class="text-[11px] text-stone-400 font-medium mt-0.5">{{ __('Choose well.') }}</p>
                        <div
                            class="flex items-center space-x-1.5 rtl:space-x-reverse mt-0.5 text-[11px] text-emerald-600 font-semibold">
                            <i class="fa-solid fa-credit-card text-[10px]"></i>
                            <i class="fa-solid fa-money-bill-wave text-[10px]"></i>
                            <span class="text-stone-500 font-normal">{{ __('Min. order: 3.75 KD') }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-2.5 rtl:space-x-reverse text-stone-600">
                    <button @click="navigate('/profile')" class="p-2 hover:bg-stone-50 rounded-full"
                        title="Menu & Profile">
                        <i class="fa-solid fa-bars text-sm"></i>
                    </button>

                    <button @click="navigate('/cart')" class="relative p-2 hover:bg-stone-50 rounded-full">
                        <i class="fa-solid fa-bag-shopping text-sm"></i>
                        <span x-show="cart.length > 0" x-text="cartCount"
                            class="absolute top-0 right-0 rtl:right-auto rtl:left-0 bg-[#8F966C] text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center">
                        </span>
                    </button>

                    <button @click="toggleSearch()" class="p-2 hover:bg-stone-50 rounded-full"
                        :class="searchOpen ? 'text-[#8F966C] bg-stone-50' : ''">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </button>

                    <a href="{{ route('lang.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}"
                        class="inline-flex items-center space-x-1.5 rtl:space-x-reverse px-2.5 py-1.5 rounded-xl border border-stone-200 text-xs font-bold text-stone-700 hover:bg-stone-50 transition active:scale-95">
                        <i class="fa-solid fa-globe text-stone-400"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}</span>
                    </a>
                </div>
            </header>

            <!-- Search Bar -->
            <div x-show="searchOpen" x-cloak class="px-5 py-2.5 bg-stone-50 border-b border-stone-200">
                <div class="relative">
                    <i
                        class="fa-solid fa-magnifying-glass absolute left-3 rtl:left-auto rtl:right-3 top-2.5 text-stone-400 text-xs"></i>
                    <input type="text" x-model="productSearch" x-ref="searchInput"
                        placeholder="{{ __('Search products or categories...') }}"
                        class="w-full bg-white border border-stone-200 rounded-xl py-2 pl-9 pr-8 rtl:pr-9 rtl:pl-8 text-xs outline-none focus:border-[#8F966C]">
                    <button x-show="productSearch.length > 0" @click="productSearch = ''"
                        class="absolute right-2.5 rtl:right-auto rtl:left-2.5 top-2 text-stone-400 hover:text-stone-600 text-xs">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            <!-- Delivery / Pickup Context Switcher Bar -->
            <section class="p-4 bg-white border-b border-stone-100"
                x-show="['categories-grid', 'category-products'].includes(view)">
                <div class="flex max-w-[280px] mx-auto border border-stone-200 rounded-lg overflow-hidden p-0.5 mb-3">
                    <button @click="setMethod('delivery')"
                        :class="method === 'delivery' ? 'bg-[#8F966C] text-white font-bold shadow-xs' : 'text-stone-600'"
                        class="flex-1 py-1.5 text-xs text-center transition rounded-md">
                        {{ __('Delivery') }}
                    </button>
                    <button @click="setMethod('pickup')"
                        :class="method === 'pickup' ? 'bg-[#8F966C] text-white font-bold shadow-xs' : 'text-stone-600'"
                        class="flex-1 py-1.5 text-xs text-center transition rounded-md">
                        {{ __('Pickup') }}
                    </button>
                </div>

                <div class="space-y-1.5 text-xs">
                    <div class="flex justify-between items-center text-stone-600">
                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                            <i class="fa-solid text-stone-400"
                                :class="method === 'delivery' ? 'fa-bicycle' : 'fa-store'"></i>
                            <span class="text-stone-400"
                                x-text="method === 'delivery' ? '{{ __('Deliver to') }}' : '{{ __('Store') }}'"></span>
                        </div>
                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                            <strong class="text-stone-800" x-text="currentLocationName"></strong>
                            <button @click="navigate('/select-location')"
                                class="text-[#8F966C] font-semibold hover:underline">{{ __('Edit') }}</button>
                        </div>
                    </div>
                    <div class="flex justify-between items-center text-stone-600">
                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                            <i class="fa-regular fa-clock text-stone-400"></i>
                            <span class="text-stone-400">{{ __('Earliest arrival') }}</span>
                        </div>
                        <span class="font-medium text-stone-800"
                            x-text="method === 'delivery' ? '{{ __('1 h') }}' : '{{ __('Ready in 30 mins') }}'"></span>
                    </div>
                </div>
            </section>

            <!-- Filter & Sort Tag Button & Active Status Bar -->
            <div class="px-5 pt-3 pb-1 flex items-center justify-between"
                x-show="view === 'categories-grid' || view === 'category-products'">
                <button @click="showFilterModal = true"
                    class="border border-stone-200 bg-white text-stone-700 px-3.5 py-1.5 rounded-lg text-xs font-bold shadow-sm hover:border-[#8F966C] transition flex items-center space-x-1.5 rtl:space-x-reverse"
                    :class="(selectedSort !== 'default' || maxPriceFilter < 25.000) ?
                    'border-[#8F966C] text-[#8F966C] bg-[#8F966C]/5' : ''">
                    <i class="fa-solid fa-sliders text-[10px]"></i>
                    <span>{{ __('Filter & Sort') }}</span>
                    <span x-show="selectedSort !== 'default' || maxPriceFilter < 25.000"
                        class="w-1.5 h-1.5 rounded-full bg-[#8F966C]"></span>
                </button>

                <button x-show="selectedSort !== 'default' || productSearch !== '' || maxPriceFilter < 25.000"
                    @click="resetFilters()" class="text-[11px] text-stone-400 hover:text-stone-600 underline">
                    {{ __('Reset Filters') }}
                </button>
            </div>

            <!-- ================= SIDEBAR DRAWER (MATCHES 2ND PIC) ================= -->
            <div x-show="showFilterModal" x-cloak class="relative z-50">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-black/50 backdrop-blur-xs transition-opacity" x-show="showFilterModal"
                    @click="showFilterModal = false"></div>

                <!-- Slide-over Drawer Panel -->
                <div class="fixed inset-y-0 left-0 rtl:left-auto rtl:right-0 w-full max-w-sm sm:max-w-md bg-white shadow-2xl z-50 flex flex-col h-full"
                    x-show="showFilterModal" x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="-translate-x-full rtl:translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in duration-200 transform"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="-translate-x-full rtl:translate-x-full">

                    <!-- Header -->
                    <div class="px-5 py-4 border-b border-stone-200 flex items-center justify-between">
                        <h3 class="font-extrabold text-sm text-stone-900">{{ __('Filter & Sort') }}</h3>
                        <button @click="showFilterModal = false"
                            class="w-8 h-8 rounded-full hover:bg-stone-100 flex items-center justify-center text-stone-400 hover:text-stone-700">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="flex-1 overflow-y-auto custom-scroll p-5 space-y-6 text-xs">

                        <!-- 1. SORT BY GRID -->
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="font-bold text-stone-900 text-xs">{{ __('Sort by') }}</span>
                                <button type="button" @click="selectedSort = 'default'"
                                    class="text-[11px] text-stone-400 hover:text-stone-700 font-semibold underline">{{ __('Reset') }}</button>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" @click="selectedSort = 'price_asc'"
                                    :class="selectedSort === 'price_asc' ?
                                        'border-[#8F966C] bg-[#8F966C]/10 text-[#394326] font-bold ring-1 ring-[#8F966C]' :
                                        'border-stone-200 text-stone-700 hover:bg-stone-50'"
                                    class="p-3 border rounded-xl text-left rtl:text-right transition">
                                    <div class="text-[10px] text-stone-400 uppercase font-bold">{{ __('Price') }}
                                    </div>
                                    <div class="text-xs mt-0.5">{{ __('Low to High') }}</div>
                                </button>

                                <button type="button" @click="selectedSort = 'price_desc'"
                                    :class="selectedSort === 'price_desc' ?
                                        'border-[#8F966C] bg-[#8F966C]/10 text-[#394326] font-bold ring-1 ring-[#8F966C]' :
                                        'border-stone-200 text-stone-700 hover:bg-stone-50'"
                                    class="p-3 border rounded-xl text-left rtl:text-right transition">
                                    <div class="text-[10px] text-stone-400 uppercase font-bold">{{ __('Price') }}
                                    </div>
                                    <div class="text-xs mt-0.5">{{ __('High to Low') }}</div>
                                </button>

                                <button type="button" @click="selectedSort = 'name_asc'"
                                    :class="selectedSort === 'name_asc' ?
                                        'border-[#8F966C] bg-[#8F966C]/10 text-[#394326] font-bold ring-1 ring-[#8F966C]' :
                                        'border-stone-200 text-stone-700 hover:bg-stone-50'"
                                    class="p-3 border rounded-xl text-left rtl:text-right transition">
                                    <div class="text-[10px] text-stone-400 uppercase font-bold">{{ __('Name') }}
                                    </div>
                                    <div class="text-xs mt-0.5">{{ __('A to Z') }}</div>
                                </button>

                                <button type="button" @click="selectedSort = 'name_desc'"
                                    :class="selectedSort === 'name_desc' ?
                                        'border-[#8F966C] bg-[#8F966C]/10 text-[#394326] font-bold ring-1 ring-[#8F966C]' :
                                        'border-stone-200 text-stone-700 hover:bg-stone-50'"
                                    class="p-3 border rounded-xl text-left rtl:text-right transition">
                                    <div class="text-[10px] text-stone-400 uppercase font-bold">{{ __('Name') }}
                                    </div>
                                    <div class="text-xs mt-0.5">{{ __('Z to A') }}</div>
                                </button>
                            </div>
                        </div>

                        <hr class="border-stone-100">

                        <!-- 2. CATEGORIES CHECKLIST -->
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="font-bold text-stone-900 text-xs">{{ __('Categories') }}</span>
                                <button type="button" @click="applyCategoryFilter(null)"
                                    class="text-[11px] text-stone-400 hover:text-stone-700 font-semibold underline">{{ __('Clear all') }}</button>
                            </div>
                            <div class="space-y-1.5 max-h-56 overflow-y-auto custom-scroll pr-1">
                                <template x-for="cat in categoriesList" :key="cat.id">
                                    <label
                                        class="flex items-center justify-between p-2.5 rounded-xl hover:bg-stone-50 cursor-pointer transition select-none"
                                        :class="Number(selectedCategoryFilter) === Number(cat.id) ?
                                            'bg-[#8F966C]/10 text-[#394326] font-bold border border-[#8F966C]/30' :
                                            'text-stone-700 border border-transparent'">
                                        <span class="text-xs tracking-wide" x-text="cat.name"></span>
                                        <input type="radio" name="cat_filter" :value="cat.id"
                                            :checked="Number(selectedCategoryFilter) === Number(cat.id)"
                                            @change="applyCategoryFilter(cat.id)"
                                            class="w-4 h-4 text-[#8F966C] focus:ring-[#8F966C] border-stone-300">
                                    </label>
                                </template>
                            </div>
                        </div>

                        <hr class="border-stone-100">

                        <!-- 3. MAX PRICE SLIDER -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-stone-900 text-xs">{{ __('Price') }}</span>
                                <span class="font-extrabold text-xs text-[#8F966C]"
                                    x-text="`${parseFloat(maxPriceFilter).toFixed(3)} {{ __('KD') }}`"></span>
                            </div>
                            <input type="range" min="0" max="25" step="0.250"
                                :value="maxPriceFilter" @input="maxPriceFilter = parseFloat($event.target.value)"
                                class="w-full accent-[#8F966C] cursor-pointer h-1.5 bg-stone-200 rounded-lg">
                            <div class="flex justify-between text-[10px] text-stone-400 mt-1.5">
                                <span>0.000 KD</span>
                                <button type="button" @click="maxPriceFilter = 25.000"
                                    class="hover:text-stone-700 underline font-semibold">
                                    {{ __('Reset Price') }}
                                </button>
                                <span>25.000 KD</span>
                            </div>
                        </div>

                    </div>

                    <!-- Sticky Apply Footer -->
                    <div class="p-4 border-t border-stone-200 bg-white">
                        <button type="button" @click="showFilterModal = false"
                            class="w-full bg-[#8F966C] hover:bg-[#7B825B] text-white font-extrabold py-3.5 rounded-xl text-xs transition active:scale-95 shadow-md flex items-center justify-center space-x-2 rtl:space-x-reverse">
                            <span>{{ __('Show results') }}</span>
                            <span x-text="`(${totalFilteredResultsCount})`" class="opacity-80"></span>
                        </button>
                    </div>

                </div>
            </div>

            <!-- SCROLLABLE BODY AREA -->
            <div class="flex-1 overflow-y-auto custom-scroll p-5 space-y-5">

                <!-- SCREEN 1: CATEGORY TILES GRID (Uses displayedCategories) -->
                <div x-show="view === 'categories-grid'" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <template x-for="category in displayedCategories" :key="category.id">
                            <div @click="openCategory(category)"
                                class="group cursor-pointer rounded-2xl overflow-hidden border border-stone-200 hover:shadow-md transition">
                                <div class="relative aspect-[4/3] bg-stone-50 overflow-hidden">
                                    <img :src="category.image || 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=500'"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </div>
                                <div class="p-3 bg-white">
                                    <h3 class="font-extrabold text-xs uppercase tracking-wide text-stone-900 group-hover:text-[#8F966C] transition"
                                        x-text="category.name">
                                    </h3>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div x-show="displayedCategories.length === 0" class="text-center py-12 text-stone-400">
                        <i class="fa-solid fa-magnifying-glass text-3xl mb-2"></i>
                        <p class="text-xs">{{ __('No categories found matching your price or search filter.') }}</p>
                    </div>
                </div>

                <!-- SCREEN 2: PRODUCTS UNDER SELECTED CATEGORY (Uses displayedProducts) -->
                <div x-show="view === 'category-products'" x-cloak class="space-y-4">
                    <div class="flex items-center space-x-2 rtl:space-x-reverse pb-2 border-b border-stone-100">
                        <button @click="navigate('/')"
                            class="w-7 h-7 rounded-full hover:bg-stone-100 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-arrow-left rtl:rotate-180"></i>
                        </button>
                        <h2 class="font-extrabold text-sm uppercase tracking-wide text-stone-900"
                            x-text="activeCategory?.name"></h2>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <template x-for="product in displayedProducts" :key="product.id">
                            <div
                                class="border border-stone-200 rounded-2xl p-3 bg-white flex flex-col justify-between hover:shadow-sm">
                                <div>
                                    <div class="aspect-[4/3] rounded-xl overflow-hidden bg-stone-50 mb-2.5">
                                        <img :src="product.image ||
                                            'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?w=500'"
                                            class="w-full h-full object-cover">
                                    </div>
                                    <h3 class="font-bold text-xs text-stone-900" x-text="product.name"></h3>
                                    <p class="text-[11px] text-stone-400 mt-0.5 line-clamp-2"
                                        x-text="product.description"></p>
                                </div>
                                <div class="mt-3 pt-2">
                                    <div class="text-xs font-bold text-[#8F966C] mb-1.5"
                                        x-text="product.base_price ? `${parseFloat(product.base_price).toFixed(3)} {{ __('KD') }}` : '{{ __('Price on selection') }}'">
                                    </div>
                                    <button @click="openCustomizer(product)"
                                        class="w-full border border-[#8F966C] text-[#8F966C] hover:bg-[#8F966C] hover:text-white text-xs font-bold py-1.5 rounded-lg transition active:scale-95">
                                        {{ __('+ Add') }}
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div x-show="displayedProducts.length === 0" class="text-center py-12 text-stone-400">
                        <i class="fa-solid fa-magnifying-glass text-3xl mb-2"></i>
                        <p class="text-xs">{{ __('No products found within this price range.') }}</p>
                    </div>
                </div>

                <!-- SCREEN 3: PRODUCT ADDON CUSTOMIZER -->
                <div x-show="view === 'customizer'" x-cloak class="space-y-5">
                    <div class="flex items-center space-x-3 rtl:space-x-reverse">
                        <button @click="navigate('/category/' + (activeCategory?.slug || ''))"
                            class="w-8 h-8 rounded-full border border-stone-200 hover:bg-stone-50 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-arrow-left rtl:rotate-180"></i>
                        </button>
                        <h2 class="font-extrabold text-sm text-stone-900" x-text="activeProduct?.name"></h2>
                    </div>

                    <div class="aspect-video w-full rounded-2xl overflow-hidden bg-stone-50">
                        <img :src="activeProduct?.image || 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?w=600'"
                            class="w-full h-full object-cover">
                    </div>

                    <div
                        class="flex items-center justify-between bg-stone-50 p-3 rounded-xl border border-stone-200/70">
                        <span class="font-bold text-xs text-stone-700">{{ __('Quantity') }}</span>
                        <div class="flex items-center space-x-3 rtl:space-x-reverse">
                            <button @click="itemQuantity > 1 ? itemQuantity-- : null; recalcCustomizerPrice()"
                                class="w-7 h-7 rounded-lg bg-white border border-stone-200 font-bold text-xs hover:border-[#8F966C]">-</button>
                            <span class="text-xs font-extrabold w-4 text-center" x-text="itemQuantity"></span>
                            <button @click="itemQuantity++; recalcCustomizerPrice()"
                                class="w-7 h-7 rounded-lg bg-white border border-stone-200 font-bold text-xs hover:border-[#8F966C]">+</button>
                        </div>
                    </div>

                    <template x-for="group in activeProduct?.addon_groups" :key="group.id">
                        <div class="border border-stone-200 rounded-2xl p-4 bg-white space-y-3">
                            <div class="flex items-center justify-between border-b border-stone-100 pb-2.5">
                                <div>
                                    <h4 class="font-extrabold text-xs text-stone-900 uppercase" x-text="group.name">
                                    </h4>
                                    <div class="flex items-center space-x-2 rtl:space-x-reverse mt-1">
                                        <span
                                            :class="group.is_required ? 'bg-[#8F966C] text-white' :
                                                'bg-stone-200 text-stone-600'"
                                            class="px-2 py-0.5 rounded text-[10px] font-bold"
                                            x-text="group.is_required ? '{{ __('Required') }}' : '{{ __('Optional') }}'">
                                        </span>
                                        <span class="text-[11px] text-stone-400 font-medium"
                                            x-text="group.type === 'radio' ? '{{ __('Single Choice') }}' : `{{ __('Max') }}: ${group.max_selectable}`">
                                        </span>
                                    </div>
                                </div>
                                <template x-if="group.is_required && !isGroupSatisfied(group)">
                                    <span
                                        class="text-[11px] font-bold text-rose-600">{{ __('(This field is required)') }}</span>
                                </template>
                            </div>

                            <div class="space-y-2 pt-1">
                                <template x-for="opt in group.options" :key="opt.id">
                                    <label
                                        :class="{
                                            'opacity-40 cursor-not-allowed bg-stone-50': isOptionDisabled(group, opt
                                                .id),
                                            'cursor-pointer hover:border-stone-300 bg-white': !isOptionDisabled(group,
                                                opt.id),
                                            'border-[#8F966C] bg-[#8F966C]/10': isOptionSelected(group.id, opt.id)
                                        }"
                                        class="flex items-center justify-between p-3 rounded-xl border border-stone-200 transition select-none">

                                        <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                            <input :type="group.type === 'radio' ? 'radio' : 'checkbox'"
                                                :name="'grp_' + group.id"
                                                :checked="isOptionSelected(group.id, opt.id)"
                                                :disabled="isOptionDisabled(group, opt.id)"
                                                @change="toggleOption(group, opt)"
                                                class="w-4 h-4 text-[#8F966C] focus:ring-[#8F966C]"
                                                :class="group.type === 'radio' ? '' : 'rounded'">
                                            <span class="text-xs font-semibold text-stone-800"
                                                x-text="opt.name"></span>
                                        </div>

                                        <span class="text-xs font-bold text-[#8F966C]"
                                            x-text="parseFloat(opt.price) > 0 ? `+ ${parseFloat(opt.price).toFixed(3)} {{ __('KD') }}` : `0.000 {{ __('KD') }}`">
                                        </span>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- SCREEN 4: SHOPPING CART -->
                <div x-show="view === 'cart'" x-cloak class="space-y-4">
                    <div class="flex items-center space-x-3 rtl:space-x-reverse">
                        <button @click="navigate('/')"
                            class="w-8 h-8 rounded-full border border-stone-200 hover:bg-stone-50 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-arrow-left rtl:rotate-180"></i>
                        </button>
                        <h2 class="font-extrabold text-base text-stone-900">{{ __('Shopping Cart') }}</h2>
                    </div>

                    <template x-if="cart.length === 0">
                        <div class="text-center py-12 text-stone-400">
                            <i class="fa-solid fa-bag-shopping text-3xl mb-2"></i>
                            <p class="text-xs">{{ __('Your cart is empty.') }}</p>
                        </div>
                    </template>

                    <div class="space-y-3">
                        <template x-for="(item, idx) in cart" :key="idx">
                            <div
                                class="p-3.5 rounded-2xl border border-stone-200 bg-white flex justify-between items-start">
                                <div>
                                    <h4 class="font-bold text-xs text-stone-900" x-text="item.name"></h4>
                                    <div class="mt-1 space-y-0.5">
                                        <template x-for="add in item.addons">
                                            <p class="text-[11px] text-stone-400"
                                                x-text="`${add.name} (+ ${add.price.toFixed(3)} {{ __('KD') }})`">
                                            </p>
                                        </template>
                                    </div>
                                    <span class="inline-block mt-2 font-extrabold text-xs text-[#8F966C]"
                                        x-text="`${item.total_price.toFixed(3)} {{ __('KD') }}`"></span>
                                </div>
                                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                    <div class="flex items-center space-x-1.5 rtl:space-x-reverse">
                                        <button @click="decreaseQty(idx)"
                                            class="w-6 h-6 rounded-lg bg-stone-100 border flex items-center justify-center font-bold text-xs hover:border-[#8F966C]">-</button>
                                        <span class="text-xs font-bold text-stone-700 w-4 text-center"
                                            x-text="item.quantity"></span>
                                        <button @click="increaseQty(idx)"
                                            class="w-6 h-6 rounded-lg bg-stone-100 border flex items-center justify-center font-bold text-xs hover:border-[#8F966C]">+</button>
                                    </div>
                                    <button @click="removeItem(idx)"
                                        class="text-stone-300 hover:text-rose-600 transition p-1">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- SCREEN: LOCATION SELECTOR (Delivery vs Pickup) -->
                <div x-show="view === 'select-location'" x-cloak class="space-y-4">
                    <div class="flex items-center space-x-3 rtl:space-x-reverse pb-2 border-b border-stone-100">
                        <button @click="navigate('/')"
                            class="w-8 h-8 rounded-full border border-stone-200 hover:bg-stone-50 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-arrow-left rtl:rotate-180"></i>
                        </button>
                        <h2 class="font-extrabold text-sm text-stone-900"
                            x-text="method === 'delivery' ? '{{ __('Choose Delivery Area') }}' : '{{ __('Choose Store Branch') }}'">
                        </h2>
                    </div>

                    <div class="relative">
                        <i
                            class="fa-solid fa-magnifying-glass absolute left-3 rtl:left-auto rtl:right-3 top-3 text-stone-400 text-xs"></i>
                        <input type="text" x-model="areaSearch"
                            :placeholder="method === 'delivery' ? '{{ __('Search area...') }}' :
                                '{{ __('Search store branch...') }}'"
                            class="w-full border border-stone-200 rounded-xl py-2 pl-9 pr-3 rtl:pr-9 rtl:pl-3 text-xs outline-none focus:border-[#8F966C]">
                    </div>

                    <!-- 1. DELIVERY MODE -->
                    <div x-show="method === 'delivery'" class="space-y-3">
                        <template x-for="gov in filteredGovernorates" :key="gov.id">
                            <div class="border border-stone-200 rounded-xl overflow-hidden bg-white shadow-xs">
                                <button type="button" @click="toggleGov(gov.id)"
                                    class="w-full px-4 py-3.5 bg-stone-50/80 hover:bg-stone-100 flex justify-between items-center text-xs font-bold text-stone-800 transition">
                                    <span x-text="gov.name"></span>
                                    <i class="fa-solid fa-chevron-down text-[10px] text-stone-400 transition-transform duration-200"
                                        :class="isGovOpen(gov.id) ? 'rotate-180 text-stone-700' : ''"></i>
                                </button>

                                <div x-show="isGovOpen(gov.id)"
                                    class="p-2 space-y-1 divide-y divide-stone-100 border-t border-stone-100">
                                    <template x-for="area in gov.filteredAreas" :key="area.id">
                                        <button type="button" @click="setDeliveryArea(area.name, area.delivery_fee)"
                                            class="w-full text-left rtl:text-right py-2.5 px-3 rounded-lg text-xs hover:bg-[#8F966C]/10 hover:text-[#394326] flex items-center transition"
                                            :class="selectedDeliveryArea?.name === area.name ?
                                                'bg-[#8F966C]/15 text-[#394326] font-bold' : 'text-stone-700'">
                                            <span x-text="area.name"></span>
                                        </button>
                                    </template>
                                    <div x-show="gov.filteredAreas.length === 0"
                                        class="text-xs text-stone-400 p-2 text-center">
                                        {{ __('No areas found') }}
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- 2. PICKUP MODE -->
                    <div x-show="method === 'pickup'" class="space-y-2.5">
                        <template x-for="st in filteredStores" :key="st.id">
                            <button type="button" @click="setStorePickup(st.name)"
                                class="w-full text-left rtl:text-right p-4 rounded-xl border border-stone-200 hover:border-[#8F966C] hover:bg-[#8F966C]/5 transition bg-white flex justify-between items-center"
                                :class="selectedPickupStore?.name === st.name ? 'border-[#8F966C] bg-[#8F966C]/10' : ''">
                                <div>
                                    <h4 class="font-bold text-xs text-stone-900" x-text="st.name"></h4>
                                    <p class="text-[11px] text-stone-400 mt-1" x-text="st.description"></p>
                                </div>
                                <i class="fa-solid fa-store text-[#8F966C] text-sm"></i>
                            </button>
                        </template>
                        <div x-show="filteredStores.length === 0"
                            class="text-xs text-stone-400 p-8 text-center bg-white rounded-xl border border-stone-200">
                            {{ __('No store branches available') }}
                        </div>
                    </div>
                </div>

                <!-- SCREEN: MY ORDERS & TRACKING DETAILS WITH 5-MIN CANCELLATION -->
                <div x-show="view === 'my-orders'" x-cloak class="space-y-4">
                    <div class="flex items-center space-x-3 rtl:space-x-reverse pb-2 border-b border-stone-100">
                        <button @click="navigate('/profile')"
                            class="w-8 h-8 rounded-full border border-stone-200 hover:bg-stone-50 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-arrow-left rtl:rotate-180"></i>
                        </button>
                        <h2 class="font-extrabold text-sm text-stone-900">{{ __('My Orders & Tracking') }}</h2>
                    </div>

                    <div x-show="ordersLoading" class="text-center py-10 text-stone-400 text-xs">
                        <i class="fa-solid fa-circle-notch fa-spin text-xl mb-2 text-[#8F966C]"></i>
                        <p>{{ __('Loading orders...') }}</p>
                    </div>

                    <div x-show="!ordersLoading && customerOrdersList.length === 0"
                        class="text-center py-12 text-stone-400">
                        <i class="fa-regular fa-clock text-4xl mb-2 text-stone-300"></i>
                        <p class="text-xs font-bold">{{ __('You don\'t have any past orders.') }}</p>
                    </div>

                    <div x-show="!ordersLoading && customerOrdersList.length > 0" class="space-y-3.5">
                        <template x-for="ord in customerOrdersList" :key="ord.id">
                            <div class="border border-stone-200 rounded-2xl p-4 bg-white shadow-xs space-y-3">
                                <!-- Order Header -->
                                <div class="flex items-start justify-between border-b border-stone-100 pb-2.5">
                                    <div>
                                        <div class="font-extrabold text-xs text-stone-900 tracking-wide"
                                            x-text="ord.order_number"></div>
                                        <div class="text-[11px] text-stone-400 mt-0.5" x-text="ord.created_at"></div>
                                    </div>
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider"
                                        :class="{
                                            'bg-emerald-50 text-emerald-700': ord.status === 'delivered',
                                            'bg-amber-50 text-amber-700': ['pending', 'placed', 'preparing'].includes(
                                                ord.status),
                                            'bg-blue-50 text-blue-700': ord.status === 'shipped',
                                            'bg-rose-50 text-rose-700': ord.status === 'cancelled'
                                        }"
                                        x-text="ord.status">
                                    </span>
                                </div>

                                <!-- Progress Step Bar -->
                                <div class="py-1">
                                    <div
                                        class="flex items-center justify-between text-[10px] font-bold text-stone-500 mb-1">
                                        <span
                                            :class="ord.status !== 'cancelled' ? 'text-[#8F966C]' : ''">{{ __('Placed') }}</span>
                                        <span
                                            :class="['preparing', 'shipped', 'delivered'].includes(ord.status) ?
                                                'text-[#8F966C]' : ''">{{ __('Preparing') }}</span>
                                        <span
                                            :class="['shipped', 'delivered'].includes(ord.status) ? 'text-[#8F966C]' : ''">{{ __('Out for delivery') }}</span>
                                        <span
                                            :class="ord.status === 'delivered' ? 'text-emerald-600' : ''">{{ __('Delivered') }}</span>
                                    </div>
                                    <div class="w-full bg-stone-100 h-1.5 rounded-full overflow-hidden">
                                        <div class="h-full bg-[#8F966C] transition-all duration-500"
                                            :style="{
                                                width: ord.status === 'cancelled' ? '0%' : (ord
                                                    .status === 'delivered' ? '100%' : (ord
                                                        .status === 'shipped' ? '70%' : (ord
                                                            .status === 'preparing' ? '40%' : '15%')))
                                            }">
                                        </div>
                                    </div>
                                </div>

                                <!-- Items -->
                                <div class="divide-y divide-stone-50 text-xs">
                                    <template x-for="item in ord.items" :key="item.id">
                                        <div class="py-1.5 flex justify-between items-start">
                                            <div>
                                                <span class="font-bold text-stone-800"
                                                    x-text="`${item.name} x${item.quantity}`"></span>
                                                <template x-if="item.addons && item.addons.length > 0">
                                                    <div class="text-[10px] text-stone-400">
                                                        <template x-for="ad in item.addons">
                                                            <span x-text="`${ad.name}, `"></span>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                            <span class="font-bold text-stone-700"
                                                x-text="`${parseFloat(item.total_price).toFixed(3)} {{ __('KD') }}`"></span>
                                        </div>
                                    </template>
                                </div>

                                <!-- Summary & 5-Minute Cancel Button -->
                                <div class="border-t border-stone-100 pt-2.5 flex items-center justify-between">
                                    <div>
                                        <span class="text-[11px] text-stone-400 block">{{ __('Total Amount') }}</span>
                                        <strong class="text-xs font-extrabold text-[#8F966C]"
                                            x-text="`${parseFloat(ord.total).toFixed(3)} {{ __('KD') }}`"></strong>
                                    </div>

                                    <template x-if="ord.can_cancel">
                                        <button type="button" @click="cancelCustomerOrder(ord.id)"
                                            class="bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 px-3 py-1.5 rounded-xl font-bold text-xs transition active:scale-95 flex items-center space-x-1.5 rtl:space-x-reverse">
                                            <i class="fa-solid fa-xmark"></i>
                                            <span>{{ __('Cancel Order') }}</span>
                                            <span class="font-normal text-[10px]"
                                                x-text="`(${formatCountdown(ord.remaining_cancel_seconds)})`"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- SCREEN: CHECKOUT DETAILS -->
                <div x-show="view === 'checkout-details'" x-cloak class="space-y-5">
                    <div class="flex items-center space-x-3 rtl:space-x-reverse pb-2 border-b border-stone-100">
                        <button @click="navigate('/cart')"
                            class="w-8 h-8 rounded-full border border-stone-200 hover:bg-stone-50 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-arrow-left rtl:rotate-180"></i>
                        </button>
                        <h2 class="font-extrabold text-sm text-stone-900">{{ __('Contact Information') }}</h2>
                    </div>

                    <div class="text-center py-2 space-y-3">
                        <div
                            class="w-16 h-16 mx-auto bg-[#8F966C]/15 text-[#394326] rounded-2xl flex items-center justify-center text-2xl shadow-xs">
                            <i class="fa-regular fa-address-card"></i>
                        </div>
                        <h3 class="font-extrabold text-sm text-stone-900">{{ __('Contact Information') }}</h3>

                        <div class="max-w-xs mx-auto space-y-2 text-left rtl:text-right text-xs text-stone-600 pt-1">
                            <div class="flex items-center space-x-2.5 rtl:space-x-reverse">
                                <i class="fa-solid fa-check text-[#8F966C] text-[10px]"></i>
                                <span>{{ __('Save your addresses') }}</span>
                            </div>
                            <div class="flex items-center space-x-2.5 rtl:space-x-reverse">
                                <i class="fa-solid fa-check text-[#8F966C] text-[10px]"></i>
                                <span>{{ __('Save your contact information') }}</span>
                            </div>
                            <div class="flex items-center space-x-2.5 rtl:space-x-reverse">
                                <i class="fa-solid fa-check text-[#8F966C] text-[10px]"></i>
                                <span>{{ __('One-tap re-ordering') }}</span>
                            </div>
                        </div>

                        <div class="pt-2">
                            <button @click="navigate('/profile/email-signin')"
                                class="w-full max-w-xs mx-auto bg-[#8F966C] hover:bg-[#7B825B] text-white font-extrabold py-3 rounded-xl text-xs shadow transition active:scale-95 block">
                                {{ __('SIGN UP') }}
                            </button>
                        </div>

                        <div class="pt-3">
                            <button @click="guestExpanded = !guestExpanded"
                                class="text-xs text-stone-500 font-bold hover:text-stone-800 underline transition">
                                {{ __('Or continue as Guest') }}
                            </button>
                        </div>
                    </div>

                    <!-- Collapsible Guest Form -->
                    <div x-show="guestExpanded"
                        class="space-y-3 pt-3 border-t border-stone-100 max-w-sm mx-auto text-xs">
                        <div>
                            <label class="block font-bold text-stone-700 mb-1">{{ __('Name *') }}</label>
                            <input type="text" x-model="customer.name" placeholder="John Doe"
                                class="w-full border border-stone-200 rounded-xl px-3.5 py-2.5 text-xs outline-none focus:ring-1 focus:ring-[#8F966C]">
                        </div>
                        <div>
                            <label
                                class="block font-bold text-stone-700 mb-1">{{ __('Email (for invoice) *') }}</label>
                            <input type="email" x-model="customer.email" placeholder="example@email.com"
                                class="w-full border border-stone-200 rounded-xl px-3.5 py-2.5 text-xs outline-none focus:ring-1 focus:ring-[#8F966C]">
                        </div>
                        <div>
                            <label class="block font-bold text-stone-700 mb-1">{{ __('Phone (+965) *') }}</label>
                            <div class="flex">
                                <span
                                    class="inline-flex items-center px-3 rounded-l-xl rtl:rounded-l-none rtl:rounded-r-xl border border-r-0 rtl:border-r rtl:border-l-0 border-stone-200 bg-stone-50 text-stone-500 text-xs font-bold">+965</span>
                                <input type="tel" x-model="customer.phone" placeholder="965..."
                                    class="w-full border border-stone-200 rounded-r-xl rtl:rounded-r-none rtl:rounded-l-xl px-3.5 py-2.5 text-xs outline-none focus:ring-1 focus:ring-[#8F966C]">
                            </div>
                        </div>
                        <button @click="proceedGuestToAddress()"
                            class="w-full bg-stone-900 hover:bg-black text-white font-extrabold py-3 rounded-xl text-xs transition active:scale-95 mt-2">
                            {{ __('Next') }}
                        </button>
                    </div>
                </div>

                <!-- SCREEN: PROFILE & MENU DRAWER -->
                <div x-show="view === 'profile-menu'" x-cloak class="space-y-6">
                    <div class="flex items-center space-x-3 rtl:space-x-reverse pb-2 border-b border-stone-100">
                        <button @click="navigate('/')"
                            class="w-8 h-8 rounded-full border border-stone-200 hover:bg-stone-50 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-arrow-left rtl:rotate-180"></i>
                        </button>
                        <h2 class="font-extrabold text-sm text-stone-900">{{ __('Profile') }}</h2>
                    </div>

                    <template x-if="isAuthenticated">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center space-x-3.5 rtl:space-x-reverse">
                                <div
                                    class="w-14 h-14 rounded-full bg-stone-200 flex items-center justify-center text-stone-400 text-2xl">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div class="space-y-0.5 text-xs">
                                    <h3 class="font-bold text-stone-900" x-text="currentUser.name"></h3>
                                    <p class="text-stone-500 text-[11px]" x-text="currentUser.email"></p>
                                    <p class="text-stone-500 text-[11px]" x-text="currentUser.phone || '+965...'"></p>
                                </div>
                            </div>
                            <button @click="signOut()" class="text-[#8F966C] text-xs font-semibold hover:underline">
                                {{ __('Sign out') }}
                            </button>
                        </div>
                    </template>

                    <!-- Menu list with restored "My orders" -->
                    <div class="space-y-1">
                        <div class="text-[11px] font-extrabold text-stone-400 uppercase tracking-wider mb-2">
                            {{ __('Menu') }}
                        </div>
                        <div class="divide-y divide-stone-100 border-t border-b border-stone-100 text-xs">
                            <button @click="navigate('/cart')"
                                class="w-full flex items-center justify-between py-3.5 text-stone-700 hover:text-[#8F966C] transition">
                                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                    <i class="fa-solid fa-cart-shopping text-stone-400 w-4 text-center"></i>
                                    <span class="font-medium">{{ __('My cart') }}</span>
                                </div>
                                <span x-show="cart.length > 0" class="w-2 h-2 rounded-full bg-[#8F966C]"></span>
                            </button>

                            <button @click="navigate('/')"
                                class="w-full flex items-center space-x-3 rtl:space-x-reverse py-3.5 text-stone-700 hover:text-[#8F966C] transition">
                                <i class="fa-solid fa-mug-hot text-stone-400 w-4 text-center"></i>
                                <span class="font-medium">{{ __('Menu') }}</span>
                            </button>

                            <button @click="navigate('/profile/orders')"
                                class="w-full flex items-center space-x-3 rtl:space-x-reverse py-3.5 text-stone-700 hover:text-[#8F966C] transition">
                                <i class="fa-regular fa-clock text-stone-400 w-4 text-center"></i>
                                <span class="font-medium">{{ __('My orders') }}</span>
                            </button>

                            <button @click="view = 'address'"
                                class="w-full flex items-center space-x-3 rtl:space-x-reverse py-3.5 text-stone-700 hover:text-[#8F966C] transition">
                                <i class="fa-solid fa-map-location-dot text-stone-400 w-4 text-center"></i>
                                <span class="font-medium">{{ __('Delivery addresses') }}</span>
                            </button>

                            <template x-if="isAuthenticated">
                                <button @click="deleteAccount()"
                                    class="w-full flex items-center space-x-3 rtl:space-x-reverse py-3.5 text-rose-600 hover:opacity-80 transition font-medium">
                                    <i class="fa-solid fa-trash-can w-4 text-center"></i>
                                    <span>{{ __('Delete account') }}</span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <template x-if="!isAuthenticated">
                        <div class="space-y-2 pt-2">
                            <div class="text-[11px] font-extrabold text-stone-400 uppercase tracking-wider">
                                {{ __('Sign in with') }}
                            </div>
                            <div class="divide-y divide-stone-100 border-t border-b border-stone-100 text-xs">
                                <button @click="navigate('/profile/email-signin')"
                                    class="w-full flex items-center space-x-3 rtl:space-x-reverse py-3.5 text-stone-700 hover:text-stone-900 transition">
                                    <i class="fa-solid fa-envelope text-stone-400 w-4 text-center"></i>
                                    <span>{{ __('Email') }}</span>
                                </button>
                                <button
                                    class="w-full flex items-center space-x-3 rtl:space-x-reverse py-3.5 text-stone-700 hover:text-stone-900 transition">
                                    <i class="fa-brands fa-apple text-stone-800 text-base w-4 text-center"></i>
                                    <span>Apple</span>
                                </button>
                                <button
                                    class="w-full flex items-center space-x-3 rtl:space-x-reverse py-3.5 text-stone-700 hover:text-stone-900 transition">
                                    <i class="fa-brands fa-google text-rose-500 w-4 text-center"></i>
                                    <span>Google</span>
                                </button>
                            </div>
                        </div>
                    </template>

                    <div class="flex justify-center pt-6">
                        <div class="p-2 border border-stone-200 rounded-2xl bg-white shadow-xs">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode(url('/')) }}"
                                alt="Store QR Code" class="w-24 h-24 object-contain">
                        </div>
                    </div>
                </div>

                <!-- SCREEN: TABBED EMAIL SIGNIN / REGISTER -->
                <div x-show="view === 'email-signin'" x-cloak class="space-y-5">
                    <div class="flex items-center space-x-3 rtl:space-x-reverse pb-2 border-b border-stone-100">
                        <button @click="navigate('/profile')"
                            class="w-8 h-8 rounded-full border border-stone-200 hover:bg-stone-50 flex items-center justify-center text-xs">
                            <i class="fa-solid fa-arrow-left rtl:rotate-180"></i>
                        </button>
                        <h2 class="font-extrabold text-sm text-stone-900"
                            x-text="authTab === 'login' ? '{{ __('Login') }}' : '{{ __('Register') }}'"></h2>
                    </div>

                    <div class="flex border border-stone-200 rounded-xl overflow-hidden p-0.5">
                        <button @click="authTab = 'login'"
                            :class="authTab === 'login' ? 'bg-[#8F966C] text-white font-bold' : 'text-stone-600'"
                            class="flex-1 py-2 text-xs transition rounded-lg">
                            {{ __('Login') }}
                        </button>
                        <button @click="authTab = 'register'"
                            :class="authTab === 'register' ? 'bg-[#8F966C] text-white font-bold' : 'text-stone-600'"
                            class="flex-1 py-2 text-xs transition rounded-lg">
                            {{ __('Register') }}
                        </button>
                    </div>

                    <template x-if="authError">
                        <div class="p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold"
                            x-text="authError"></div>
                    </template>

                    <!-- Login Tab View -->
                    <div x-show="authTab === 'login'" class="space-y-3 pt-2 text-xs">
                        <div>
                            <label class="block font-bold text-stone-700 mb-1">{{ __('Email *') }}</label>
                            <input type="email" x-model="authForm.email" placeholder="example@email.com"
                                class="w-full border border-stone-200 rounded-xl px-3.5 py-2.5 text-xs outline-none focus:ring-1 focus:ring-[#8F966C]">
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="font-bold text-stone-700">{{ __('Password *') }}</label>
                                <a href="#"
                                    class="text-[10px] text-stone-400 hover:text-stone-600 uppercase font-bold">{{ __('Forgot Password?') }}</a>
                            </div>
                            <input type="password" x-model="authForm.password" placeholder="••••••••"
                                class="w-full border border-stone-200 rounded-xl px-3.5 py-2.5 text-xs outline-none focus:ring-1 focus:ring-[#8F966C]">
                        </div>

                        <button @click="submitSignIn()"
                            class="w-full bg-[#8F966C] hover:bg-[#7B825B] text-white font-extrabold py-3 rounded-xl text-xs transition active:scale-95 shadow">
                            {{ __('Login') }}
                        </button>
                    </div>

                    <!-- Register Tab View -->
                    <div x-show="authTab === 'register'" class="space-y-3 pt-2 text-xs">
                        <div>
                            <label class="block font-bold text-stone-700 mb-1">{{ __('Full Name *') }}</label>
                            <input type="text" x-model="regForm.name" placeholder="John Doe"
                                class="w-full border border-stone-200 rounded-xl px-3.5 py-2.5 text-xs outline-none focus:ring-1 focus:ring-[#8F966C]">
                        </div>
                        <div>
                            <label class="block font-bold text-stone-700 mb-1">{{ __('Email *') }}</label>
                            <input type="email" x-model="regForm.email" placeholder="example@email.com"
                                class="w-full border border-stone-200 rounded-xl px-3.5 py-2.5 text-xs outline-none focus:ring-1 focus:ring-[#8F966C]">
                        </div>
                        <div>
                            <label class="block font-bold text-stone-700 mb-1">{{ __('Phone (+965) *') }}</label>
                            <input type="tel" x-model="regForm.phone" placeholder="965..."
                                class="w-full border border-stone-200 rounded-xl px-3.5 py-2.5 text-xs outline-none focus:ring-1 focus:ring-[#8F966C]">
                        </div>
                        <div>
                            <label class="block font-bold text-stone-700 mb-1">{{ __('Password *') }}</label>
                            <input type="password" x-model="regForm.password" placeholder="••••••••"
                                class="w-full border border-stone-200 rounded-xl px-3.5 py-2.5 text-xs outline-none focus:ring-1 focus:ring-[#8F966C]">
                        </div>

                        <button @click="submitSignUp()"
                            class="w-full bg-[#8F966C] hover:bg-[#7B825B] text-white font-extrabold py-3 rounded-xl text-xs transition active:scale-95 shadow">
                            {{ __('Register') }}
                        </button>
                    </div>
                </div>

                <!-- SCREEN: KUWAIT ADDRESS DETAILS -->
                <div x-show="view === 'address'" x-cloak class="space-y-4">
                    <h3 class="font-extrabold text-sm text-stone-900">{{ __('Delivery Address Details') }}</h3>

                    <!-- Address Type Selection -->
                    <div>
                        <label
                            class="block font-bold text-stone-700 text-xs mb-1.5">{{ __('Address Type *') }}</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button" @click="address.type = 'Home'"
                                :class="address.type === 'Home' ? 'bg-[#8F966C] text-white' :
                                    'border border-stone-200 text-stone-600 hover:bg-stone-50'"
                                class="py-2 rounded-xl text-xs font-bold transition">{{ __('Home') }}</button>
                            <button type="button" @click="address.type = 'Apartment'"
                                :class="address.type === 'Apartment' ? 'bg-[#8F966C] text-white' :
                                    'border border-stone-200 text-stone-600 hover:bg-stone-50'"
                                class="py-2 rounded-xl text-xs font-bold transition">{{ __('Apartment') }}</button>
                            <button type="button" @click="address.type = 'Office'"
                                :class="address.type === 'Office' ? 'bg-[#8F966C] text-white' :
                                    'border border-stone-200 text-stone-600 hover:bg-stone-50'"
                                class="py-2 rounded-xl text-xs font-bold transition">{{ __('Office') }}</button>
                        </div>
                    </div>

                    <!-- Block & Street Fields with Labels -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-stone-700 text-xs mb-1">{{ __('Block *') }}</label>
                            <input type="text" x-model="address.block" placeholder="{{ __('e.g. 1') }}"
                                class="w-full border border-stone-200 rounded-xl p-2.5 text-xs outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] transition">
                        </div>
                        <div>
                            <label class="block font-bold text-stone-700 text-xs mb-1">{{ __('Street *') }}</label>
                            <input type="text" x-model="address.street" placeholder="{{ __('e.g. Street 10') }}"
                                class="w-full border border-stone-200 rounded-xl p-2.5 text-xs outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] transition">
                        </div>
                    </div>

                    <!-- Building & PACI Fields with Labels -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label
                                class="block font-bold text-stone-700 text-xs mb-1">{{ __('Building / House *') }}</label>
                            <input type="text" x-model="address.building"
                                placeholder="{{ __('e.g. Building 12') }}"
                                class="w-full border border-stone-200 rounded-xl p-2.5 text-xs outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] transition">
                        </div>
                        <div>
                            <label class="block font-bold text-stone-700 text-xs mb-1">{{ __('PACI') }} <span
                                    class="text-stone-400 font-normal text-[11px]">({{ __('Optional') }})</span></label>
                            <input type="text" x-model="address.paci" placeholder="{{ __('8-digit number') }}"
                                class="w-full border border-stone-200 rounded-xl p-2.5 text-xs outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] transition">
                        </div>
                    </div>
                </div>

                <!-- SCREEN: PAYMENT & TOTAL REVIEW -->
                <div x-show="view === 'checkout'" x-cloak class="space-y-4">
                    <h3 class="font-extrabold text-sm text-stone-900">{{ __('Payment Option') }}</h3>
                    <div class="space-y-2">
                        <label
                            class="flex items-center justify-between p-3.5 border rounded-xl cursor-pointer hover:border-stone-300">
                            <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                <input type="radio" value="knet" x-model="paymentMethod"
                                    class="text-[#8F966C] focus:ring-[#8F966C]">
                                <span class="text-xs font-bold">{{ __('Debit Card (KNET)') }}</span>
                            </div>
                            <i class="fa-regular fa-credit-card text-stone-400"></i>
                        </label>
                        <label
                            class="flex items-center justify-between p-3.5 border rounded-xl cursor-pointer hover:border-stone-300">
                            <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                <input type="radio" value="cash" x-model="paymentMethod"
                                    class="text-[#8F966C] focus:ring-[#8F966C]">
                                <span class="text-xs font-bold">{{ __('Cash on Delivery') }}</span>
                            </div>
                            <i class="fa-solid fa-money-bill-wave text-stone-400"></i>
                        </label>
                    </div>

                    <div class="border-t border-stone-100 pt-4 space-y-2 text-xs">
                        <div class="flex justify-between text-stone-500">
                            <span>{{ __('Subtotal') }}</span>
                            <span class="font-bold text-stone-800"
                                x-text="`${calculateSubtotal().toFixed(3)} {{ __('KD') }}`"></span>
                        </div>
                        <div class="flex justify-between text-stone-500">
                            <span>{{ __('Delivery Fee') }}</span>
                            <span class="font-bold text-stone-800"
                                x-text="`${deliveryFee.toFixed(3)} {{ __('KD') }}`"></span>
                        </div>
                        <div class="flex justify-between text-sm font-extrabold text-stone-900 border-t pt-2">
                            <span>{{ __('Total') }}</span>
                            <span class="text-[#8F966C]"
                                x-text="`${calculateGrandTotal().toFixed(3)} {{ __('KD') }}`"></span>
                        </div>
                    </div>
                </div>

                <!-- SCREEN: CONFIRMATION -->
                <div x-show="view === 'success'" x-cloak class="text-center py-16 space-y-3">
                    <div
                        class="w-16 h-16 bg-[#8F966C]/20 text-[#394326] rounded-full flex items-center justify-center mx-auto text-2xl mb-2">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <h2 class="font-extrabold text-base text-stone-900">{{ __('Order Confirmed!') }}</h2>
                    <p class="text-xs text-stone-400">{{ __('Reference:') }} <strong class="text-stone-800"
                            x-text="placedOrderNo"></strong></p>
                    <button @click="navigate('/')"
                        class="mt-4 bg-[#8F966C] hover:bg-[#7B825B] text-white px-6 py-2.5 rounded-xl font-bold text-xs">{{ __('Back to Menu') }}</button>
                </div>

            </div>

            <!-- Sticky Bottom Review Bar -->
            <footer class="p-4 bg-white border-t border-stone-200 sticky bottom-0 z-20"
                x-show="view !== 'success' && view !== 'profile-menu' && view !== 'email-signin' && view !== 'my-orders'">
                <template x-if="(view === 'categories-grid' || view === 'category-products') && cart.length > 0">
                    <button @click="navigate('/cart')"
                        class="w-full bg-[#8F966C] hover:bg-[#7B825B] text-white font-extrabold py-3.5 px-4 rounded-xl flex items-center justify-between text-xs transition shadow-md active:scale-95">
                        <span class="bg-black/20 px-2 py-0.5 rounded-md" x-text="cartCount"></span>
                        <span>{{ __('Review Order') }}</span>
                        <span x-text="`${calculateSubtotal().toFixed(3)} {{ __('KD') }}`"></span>
                    </button>
                </template>

                <template x-if="view === 'customizer'">
                    <button @click="commitAddonToCart()" :disabled="!canAddToCart()"
                        :class="canAddToCart() ? 'bg-[#8F966C] hover:bg-[#7B825B] text-white active:scale-95' :
                            'bg-stone-300 text-stone-500 cursor-not-allowed'"
                        class="w-full py-3.5 px-4 rounded-xl font-extrabold text-xs flex items-center justify-between transition">
                        <span
                            x-text="canAddToCart() ? '{{ __('Add to Cart') }}' : '{{ __('Select Required Options') }}'"></span>
                        <span x-text="`${(customizerPrice * itemQuantity).toFixed(3)} {{ __('KD') }}`"></span>
                    </button>
                </template>

                <template x-if="view === 'cart' && cart.length > 0">
                    <button @click="navigate(isAuthenticated ? '/checkout/address' : '/checkout/details')"
                        class="w-full bg-[#8F966C] hover:bg-[#7B825B] text-white font-extrabold py-3.5 rounded-xl text-xs transition active:scale-95">
                        {{ __('Go to checkout') }} (<span
                            x-text="`${calculateSubtotal().toFixed(3)} {{ __('KD') }}`"></span>)
                    </button>
                </template>

                <template x-if="view === 'address'">
                    <button @click="view = 'checkout'"
                        class="w-full bg-[#8F966C] hover:bg-[#7B825B] text-white font-extrabold py-3.5 rounded-xl text-xs transition active:scale-95">
                        {{ __('Next') }}
                    </button>
                </template>

                <template x-if="view === 'checkout'">
                    <button @click="placeOrderNow()"
                        class="w-full bg-[#8F966C] hover:bg-[#7B825B] text-white font-extrabold py-3.5 rounded-xl text-xs transition active:scale-95">
                        {{ __('Place Order') }} (<span
                            x-text="`${calculateGrandTotal().toFixed(3)} {{ __('KD') }}`"></span>)
                    </button>
                </template>
            </footer>
        </main>

        <!-- ================= RIGHT STATIC BRAND BANNER (50%) ================= -->
        <aside class="hidden lg:block lg:w-1/2 h-full relative overflow-hidden bg-[#DDD5C9]">
            <img src="{{ asset('images/otherwise-banner-new.jpeg') }}" alt="otherwise - Choose well. Drink well."
                class="w-full h-full object-cover object-[50%_40%]">

            <a href="https://www.instagram.com/otherwisekw/" target="_blank"
                class="absolute bottom-8 right-8 rtl:right-auto rtl:left-8 bg-[#394326]/90 hover:bg-[#394326] text-white p-3.5 rounded-2xl shadow-xl hover:scale-110 transition">
                <i class="fa-brands fa-instagram text-2xl"></i>
            </a>
        </aside>
    </div>

    <!-- Alpine.js Store Engine -->
    <script>
        function storeApp() {
            return {
                view: 'categories-grid',
                method: 'delivery',

                selectedDeliveryArea: {
                    name: '{{ app()->getLocale() === 'ar' ? 'أبو حليفة' : 'Abu Halifa' }}',
                    fee: 0.950
                },
                selectedPickupStore: null,
                deliveryFee: 0.950,

                // Search & Filter State
                searchOpen: false,
                productSearch: '',
                showFilterModal: false,
                selectedSort: 'default',
                selectedCategoryFilter: null,
                maxPriceFilter: 25.000,

                toggleSearch() {
                    this.searchOpen = !this.searchOpen;
                    if (this.searchOpen) {
                        this.$nextTick(() => {
                            if (this.$refs.searchInput) this.$refs.searchInput.focus();
                        });
                    }
                },

                resetFilters() {
                    this.selectedSort = 'default';
                    this.selectedCategoryFilter = null;
                    this.maxPriceFilter = 25.000;
                    this.productSearch = '';
                },

                applyCategoryFilter(catId) {
                    if (!catId) {
                        this.selectedCategoryFilter = null;
                        this.navigate('/');
                        return;
                    }
                    this.selectedCategoryFilter = Number(catId);
                    const targetCat = this.categoriesList.find(c => Number(c.id) === Number(catId));
                    if (targetCat) {
                        this.openCategory(targetCat);
                    }
                },

                get totalFilteredResultsCount() {
                    if (this.view === 'category-products') {
                        return this.displayedProducts.length;
                    }
                    return this.displayedCategories.length;
                },

                get currentLocationName() {
                    if (this.method === 'delivery') {
                        return this.selectedDeliveryArea ? this.selectedDeliveryArea.name :
                            '{{ __('Choose location') }}';
                    } else {
                        return this.selectedPickupStore ? this.selectedPickupStore.name :
                            '{{ __('Choose a Store') }}';
                    }
                },

                // Cart local persistence
                cart: JSON.parse(localStorage.getItem('otherwise_cart') || '[]'),
                saveCart() {
                    localStorage.setItem('otherwise_cart', JSON.stringify(this.cart));
                },

                get cartCount() {
                    return this.cart.reduce((total, item) => total + (item.quantity || 1), 0);
                },

                categoriesList: @json($categories),
                governoratesList: @json($governoratesList),
                storesList: @json($storesList),

                // Dynamic Categories Filter
                get displayedCategories() {
                    let list = [...this.categoriesList];
                    const query = this.productSearch.toLowerCase().trim();
                    const max = Number(this.maxPriceFilter) || 25.000;

                    // Price Filter on Categories Grid
                    if (max < 25.000) {
                        list = list.filter(cat => {
                            if (!cat.products || cat.products.length === 0) return false;
                            return cat.products.some(p => {
                                const price = parseFloat(p.base_price);
                                return !isNaN(price) && price <= max;
                            });
                        });
                    }

                    // Text Search
                    if (query) {
                        list = list.filter(cat => {
                            const matchCat = cat.name.toLowerCase().includes(query);
                            const matchProd = (cat.products || []).some(p => p.name.toLowerCase().includes(
                                query));
                            return matchCat || matchProd;
                        });
                    }

                    // Sort
                    if (this.selectedSort === 'name_asc') {
                        list.sort((a, b) => a.name.localeCompare(b.name));
                    } else if (this.selectedSort === 'name_desc') {
                        list.sort((a, b) => b.name.localeCompare(a.name));
                    }

                    return list;
                },

                // Dynamic Products Filter (CORRECTED)
                get displayedProducts() {
                    if (!this.activeCategory || !this.activeCategory.products) return [];
                    let list = [...this.activeCategory.products];
                    const query = this.productSearch.toLowerCase().trim();
                    const max = Number(this.maxPriceFilter) || 25.000;

                    // 1. Max Price Filter
                    if (max < 25.000) {
                        list = list.filter(p => {
                            const price = parseFloat(p.base_price);
                            return !isNaN(price) && price <= max;
                        });
                    }

                    // 2. Text Search Filter
                    if (query) {
                        list = list.filter(p =>
                            p.name.toLowerCase().includes(query) ||
                            (p.description && p.description.toLowerCase().includes(query))
                        );
                    }

                    // 3. Sorting
                    if (this.selectedSort === 'price_asc') {
                        list.sort((a, b) => (parseFloat(a.base_price) || 0) - (parseFloat(b.base_price) || 0));
                    } else if (this.selectedSort === 'price_desc') {
                        list.sort((a, b) => (parseFloat(b.base_price) || 0) - (parseFloat(a.base_price) || 0));
                    } else if (this.selectedSort === 'name_asc') {
                        list.sort((a, b) => a.name.localeCompare(b.name));
                    } else if (this.selectedSort === 'name_desc') {
                        list.sort((a, b) => b.name.localeCompare(a.name));
                    }

                    return list;
                },

                areaSearch: '',
                activeGovId: null,

                customerOrdersList: [],
                ordersLoading: false,
                cancelInterval: null,

                get filteredGovernorates() {
                    const search = this.areaSearch.toLowerCase().trim();
                    return this.governoratesList.map(gov => {
                        const filteredAreas = (gov.areas || []).filter(a => !search || a.name.toLowerCase()
                            .includes(search));
                        return {
                            id: gov.id,
                            name: gov.name,
                            filteredAreas: filteredAreas
                        };
                    }).filter(gov => !search || gov.filteredAreas.length > 0);
                },

                get filteredStores() {
                    const search = this.areaSearch.toLowerCase().trim();
                    return this.storesList.filter(s => {
                        if (!search) return true;
                        return s.name.toLowerCase().includes(search) || (s.description && s.description
                            .toLowerCase().includes(search));
                    });
                },

                setMethod(m) {
                    this.method = m;
                    if (m === 'pickup') {
                        this.deliveryFee = 0.000;
                        if (!this.selectedPickupStore && this.storesList.length > 0) {
                            this.selectedPickupStore = {
                                name: this.storesList[0].name,
                                description: this.storesList[0].description
                            };
                        }
                    } else {
                        this.deliveryFee = this.selectedDeliveryArea ? this.selectedDeliveryArea.fee : 0.950;
                    }
                },

                toggleGov(govId) {
                    this.activeGovId = (this.activeGovId === govId) ? null : govId;
                },

                isGovOpen(govId) {
                    if (this.areaSearch.trim().length > 0) return true;
                    return this.activeGovId === govId;
                },

                activeCategory: null,
                activeProduct: null,
                itemQuantity: 1,
                userSelections: {},
                customizerPrice: 0.000,
                pageTitle: '{{ __('otherwise') }}',

                isAuthenticated: {{ $currentUser ? 'true' : 'false' }},
                currentUser: @json($currentUser ?? ['name' => '', 'email' => '', 'phone' => '']),
                authError: '',
                authTab: 'login',
                guestExpanded: false,

                authForm: {
                    email: '',
                    password: ''
                },
                regForm: {
                    name: '',
                    email: '',
                    phone: '',
                    password: ''
                },

                customer: {
                    name: '{{ $currentUser['name'] ?? '' }}',
                    email: '{{ $currentUser['email'] ?? '' }}',
                    phone: '{{ $currentUser['phone'] ?? '' }}'
                },
                address: {
                    type: 'Home',
                    block: '',
                    street: '',
                    building: '',
                    paci: ''
                },
                paymentMethod: 'knet',
                placedOrderNo: '',

                initRouter() {
                    if (this.storesList && this.storesList.length > 0 && !this.selectedPickupStore) {
                        this.selectedPickupStore = {
                            name: this.storesList[0].name,
                            description: this.storesList[0].description
                        };
                    }

                    window.addEventListener('popstate', () => {
                        this.handlePath(window.location.pathname);
                    });
                    this.handlePath(window.location.pathname);
                },

                navigate(path, title = null) {
                    if (window.location.pathname !== path) {
                        window.history.pushState({}, '', path);
                    }
                    this.handlePath(path, title);
                },

                handlePath(path, customTitle = null) {
                    const clean = path.replace(/^\/|\/$/g, '');
                    const parts = clean.split('/');

                    if (!clean || clean === '') {
                        this.view = 'categories-grid';
                        this.selectedCategoryFilter = null;
                        this.setPageTitle('{{ __('otherwise') }}');
                    } else if (parts[0] === 'category' && parts[1]) {
                        const foundCat = this.categoriesList.find(c => c.slug === parts[1]);
                        if (foundCat) {
                            this.activeCategory = foundCat;
                            this.selectedCategoryFilter = Number(foundCat.id);
                            this.view = 'category-products';
                            this.setPageTitle(foundCat.name);
                        } else {
                            this.view = 'categories-grid';
                        }
                    } else if (parts[0] === 'product' && parts[1]) {
                        let foundProd = null;
                        for (let cat of this.categoriesList) {
                            const p = cat.products.find(item => item.slug === parts[1]);
                            if (p) {
                                foundProd = p;
                                this.activeCategory = cat;
                                break;
                            }
                        }
                        if (foundProd) {
                            this.setupCustomizer(foundProd);
                            this.setPageTitle(foundProd.name);
                        } else {
                            this.view = 'categories-grid';
                        }
                    } else if (parts[0] === 'cart') {
                        this.view = 'cart';
                        this.setPageTitle('{{ __('Shopping Cart') }}');
                    } else if (parts[0] === 'select-location') {
                        this.view = 'select-location';
                        this.setPageTitle(this.method === 'delivery' ? '{{ __('Choose Delivery Area') }}' :
                            '{{ __('Choose Store Branch') }}');
                    } else if (parts[0] === 'checkout') {
                        if (parts[1] === 'details') {
                            this.view = 'checkout-details';
                            this.setPageTitle('{{ __('Contact Information') }}');
                        } else if (parts[1] === 'address') {
                            this.view = 'address';
                            this.setPageTitle('{{ __('Delivery Address Details') }}');
                        }
                    } else if (parts[0] === 'profile') {
                        if (parts[1] === 'orders') {
                            this.view = 'my-orders';
                            this.setPageTitle('{{ __('My Orders') }}');
                            this.fetchCustomerOrders();
                        } else if (parts[1] === 'email-signin') {
                            this.view = 'email-signin';
                            this.setPageTitle('{{ __('Login') }}');
                        } else {
                            this.view = 'profile-menu';
                            this.setPageTitle('{{ __('Profile') }}');
                        }
                    }
                },

                setPageTitle(title) {
                    this.pageTitle = title;
                    document.title = title;
                },

                openCategory(cat) {
                    this.activeCategory = cat;
                    this.selectedCategoryFilter = Number(cat.id);
                    this.navigate('/category/' + cat.slug, cat.name);
                },

                openCustomizer(product) {
                    this.navigate('/product/' + product.slug, product.name);
                },

                setupCustomizer(product) {
                    this.activeProduct = product;
                    this.itemQuantity = 1;
                    this.userSelections = {};

                    if (product.addon_groups) {
                        product.addon_groups.forEach(grp => {
                            if (grp.type === 'radio' && grp.is_required && grp.options && grp.options.length > 0) {
                                this.userSelections[grp.id] = [{
                                    id: grp.options[0].id,
                                    name: grp.options[0].name,
                                    price: parseFloat(grp.options[0].price || 0)
                                }];
                            }
                        });
                    }

                    this.recalcCustomizerPrice();
                    this.view = 'customizer';
                },

                setDeliveryArea(name, fee) {
                    this.selectedDeliveryArea = {
                        name: name,
                        fee: parseFloat(fee || 0.950)
                    };
                    this.deliveryFee = parseFloat(fee || 0.950);
                    this.navigate('/');
                },

                setStorePickup(name) {
                    const st = this.storesList.find(s => s.name === name);
                    this.selectedPickupStore = {
                        name: name,
                        description: st ? st.description : ''
                    };
                    this.deliveryFee = 0.000;
                    this.navigate('/');
                },

                proceedGuestToAddress() {
                    if (!this.customer.name || !this.customer.email || !this.customer.phone) {
                        alert('{{ __('Please fill all required fields') }}');
                        return;
                    }
                    this.navigate('/checkout/address', '{{ __('Delivery Address Details') }}');
                },

                // ------------------ ORDERS HISTORY & 5-MIN CANCEL ------------------
                fetchCustomerOrders() {
                    this.ordersLoading = true;
                    const email = this.currentUser?.email || this.customer?.email || '';
                    const phone = this.currentUser?.phone || this.customer?.phone || '';

                    const params = new URLSearchParams();
                    if (email) params.append('email', email);
                    if (phone) params.append('phone', phone);

                    fetch(`/api/customer/orders?${params.toString()}`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            this.customerOrdersList = data.orders || [];
                            this.ordersLoading = false;
                            this.startCancelCountdown();
                        })
                        .catch(() => {
                            this.ordersLoading = false;
                        });
                },

                startCancelCountdown() {
                    if (this.cancelInterval) clearInterval(this.cancelInterval);
                    this.cancelInterval = setInterval(() => {
                        let activeCountdown = false;
                        this.customerOrdersList.forEach(ord => {
                            if (ord.remaining_cancel_seconds > 0) {
                                ord.remaining_cancel_seconds--;
                                activeCountdown = true;
                            } else {
                                ord.can_cancel = false;
                            }
                        });
                        if (!activeCountdown) clearInterval(this.cancelInterval);
                    }, 1000);
                },

                formatCountdown(seconds) {
                    const mins = Math.floor(seconds / 60);
                    const secs = seconds % 60;
                    return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
                },

                cancelCustomerOrder(orderId) {
                    if (!confirm('{{ __('Are you sure you want to cancel this order?') }}')) return;

                    fetch(`/api/customer/orders/${orderId}/cancel`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        })
                        .then(async res => {
                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || 'Could not cancel order');
                            return data;
                        })
                        .then(data => {
                            alert(data.message);
                            this.fetchCustomerOrders();
                        })
                        .catch(err => {
                            alert(err.message);
                        });
                },

                // ------------------ AUTH LOGIC ------------------
                submitSignIn() {
                    this.authError = '';
                    if (!this.authForm.email || !this.authForm.password) {
                        this.authError = '{{ __('Please fill all required fields') }}';
                        return;
                    }

                    fetch('{{ route('customer.login') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.authForm)
                        })
                        .then(async res => {
                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || 'Login failed');
                            return data;
                        })
                        .then(data => {
                            if (data.success) {
                                this.currentUser = data.user;
                                this.customer.name = data.user.name;
                                this.customer.email = data.user.email;
                                this.customer.phone = data.user.phone;
                                this.isAuthenticated = true;
                                this.authForm = {
                                    email: '',
                                    password: ''
                                };
                                this.navigate('/profile', '{{ __('Profile') }}');
                            }
                        })
                        .catch(err => {
                            this.authError = err.message;
                        });
                },

                // submitSignUp() {
                //     this.authError = '';
                //     if (!this.regForm.name || !this.regForm.email || !this.regForm.password) {
                //         this.authError = '{{ __('Please fill all required fields') }}';
                //         return;
                //     }

                //     fetch('{{ route('customer.register') }}', {
                //             method: 'POST',
                //             headers: {
                //                 'Content-Type': 'application/json',
                //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                //                 'Accept': 'application/json'
                //             },
                //             body: JSON.stringify(this.regForm)
                //         })
                //         .then(async res => {
                //             const data = await res.json();
                //             if (!res.ok) throw new Error(data.message || 'Registration failed');
                //             return data;
                //         })
                //         .then(data => {
                //             if (data.success) {
                //                 this.currentUser = data.user;
                //                 this.customer.name = data.user.name;
                //                 this.customer.email = data.user.email;
                //                 this.customer.phone = data.user.phone;
                //                 this.isAuthenticated = true;
                //                 this.regForm = {
                //                     name: '',
                //                     email: '',
                //                     phone: '',
                //                     password: ''
                //                 };
                //                 this.navigate('/profile', '{{ __('Profile') }}');
                //             }
                //         })
                //         .catch(err => {
                //             this.authError = err.message;
                //         });
                // },

                submitSignUp() {
                    this.authError = '';
                    if (!this.regForm.name || !this.regForm.email || !this.regForm.password) {
                        this.authError = '{{ __('Please fill all required fields') }}';
                        return;
                    }

                    fetch('{{ route('customer.register') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.regForm)
                        })
                        .then(async res => {
                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || 'Registration failed');
                            return data;
                        })
                        .then(data => {
                            if (data.requires_verify) {
                                // Reset the registration form
                                this.regForm = {
                                    name: '',
                                    email: '',
                                    phone: '',
                                    password: ''
                                };
                                // Switch to the login tab
                                this.authTab = 'login';
                                // Show the verification notice to the user
                                alert(data.message);
                            }
                        })
                        .catch(err => {
                            this.authError = err.message;
                        });
                },

                signOut() {
                    fetch('{{ route('customer.logout') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        })
                        .then(() => {
                            this.isAuthenticated = false;
                            this.currentUser = {
                                name: '',
                                email: '',
                                phone: ''
                            };
                            this.navigate('/', '{{ __('otherwise') }}');
                        });
                },

                deleteAccount() {
                    if (confirm('{{ __('Are you sure you want to delete your account?') }}')) {
                        fetch('{{ route('customer.deleteAccount') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                }
                            })
                            .then(() => {
                                this.isAuthenticated = false;
                                this.currentUser = {
                                    name: '',
                                    email: '',
                                    phone: ''
                                };
                                this.navigate('/', '{{ __('otherwise') }}');
                            });
                    }
                },

                // ------------------ ADDONS & PRICING ------------------
                toggleOption(group, option) {
                    if (!this.userSelections[group.id]) {
                        this.userSelections[group.id] = [];
                    }

                    if (group.type === 'radio') {
                        this.userSelections[group.id] = [{
                            id: option.id,
                            name: option.name,
                            price: parseFloat(option.price || 0)
                        }];
                    } else {
                        const idx = this.userSelections[group.id].findIndex(o => o.id === option.id);
                        if (idx > -1) {
                            this.userSelections[group.id].splice(idx, 1);
                        } else {
                            const maxLimit = parseInt(group.max_selectable) || 999;
                            if (this.userSelections[group.id].length < maxLimit) {
                                this.userSelections[group.id].push({
                                    id: option.id,
                                    name: option.name,
                                    price: parseFloat(option.price || 0)
                                });
                            }
                        }
                    }

                    this.recalcCustomizerPrice();
                },

                isOptionSelected(groupId, optionId) {
                    return this.userSelections[groupId]?.some(o => o.id === optionId) || false;
                },

                isOptionDisabled(group, optionId) {
                    if (group.type === 'radio') return false;
                    const maxLimit = parseInt(group.max_selectable) || 999;
                    const isSelected = this.isOptionSelected(group.id, optionId);
                    const count = this.userSelections[group.id]?.length || 0;
                    return !isSelected && count >= maxLimit;
                },

                isGroupSatisfied(group) {
                    if (!group.is_required) return true;
                    const count = this.userSelections[group.id]?.length || 0;
                    return count >= 1;
                },

                canAddToCart() {
                    if (!this.activeProduct || !this.activeProduct.addon_groups) return true;
                    return this.activeProduct.addon_groups.every(grp => this.isGroupSatisfied(grp));
                },

                recalcCustomizerPrice() {
                    let total = parseFloat(this.activeProduct?.base_price || 0);
                    Object.values(this.userSelections).forEach(selectedOpts => {
                        selectedOpts.forEach(opt => {
                            total += parseFloat(opt.price || 0);
                        });
                    });
                    this.customizerPrice = total;
                },

                commitAddonToCart() {
                    if (!this.canAddToCart()) return;

                    const flattenedAddons = [];
                    Object.values(this.userSelections).forEach(opts => {
                        opts.forEach(o => flattenedAddons.push(o));
                    });

                    this.cart.push({
                        product_id: this.activeProduct.id,
                        name: this.activeProduct.name,
                        quantity: this.itemQuantity,
                        unit_price: this.customizerPrice,
                        total_price: this.customizerPrice * this.itemQuantity,
                        addons: flattenedAddons
                    });

                    this.saveCart();
                    this.navigate('/category/' + (this.activeCategory?.slug || ''), this.activeCategory?.name);
                },

                increaseQty(idx) {
                    this.cart[idx].quantity++;
                    this.saveCart();
                },

                decreaseQty(idx) {
                    if (this.cart[idx].quantity > 1) {
                        this.cart[idx].quantity--;
                    } else {
                        this.cart.splice(idx, 1);
                    }
                    this.saveCart();
                },

                removeItem(idx) {
                    this.cart.splice(idx, 1);
                    this.saveCart();
                },

                calculateSubtotal() {
                    return this.cart.reduce((acc, it) => acc + it.total_price, 0);
                },

                calculateGrandTotal() {
                    return this.calculateSubtotal() + (this.method === 'delivery' ? this.deliveryFee : 0);
                },

                placeOrderNow() {
                    const chosenLocation = this.method === 'delivery' ?
                        (this.selectedDeliveryArea ? this.selectedDeliveryArea.name : '') :
                        (this.selectedPickupStore ? this.selectedPickupStore.name : '');

                    const payload = {
                        order_type: this.method,
                        customer_name: this.customer.name,
                        customer_email: this.customer.email,
                        customer_phone: this.customer.phone,
                        area_name: chosenLocation,
                        address_type: this.address.type,
                        block: this.address.block,
                        street: this.address.street,
                        building: this.address.building,
                        paci: this.address.paci,
                        subtotal: this.calculateSubtotal(),
                        delivery_fee: this.method === 'delivery' ? this.deliveryFee : 0,
                        total: this.calculateGrandTotal(),
                        payment_method: this.paymentMethod,
                        items: this.cart
                    };

                    fetch('/api/orders/place', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        })
                        .then(async res => {
                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || 'Server error');
                            return data;
                        })
                        .then(res => {
                            if (res.success) {
                                this.placedOrderNo = res.order_number;
                                this.cart = [];
                                localStorage.removeItem('otherwise_cart');
                                this.view = 'success';
                                this.setPageTitle('{{ __('Order Confirmed!') }}');
                                window.history.pushState({}, '', '/');
                            }
                        })
                        .catch(err => {
                            alert(err.message);
                        });
                }
            };
        }
    </script>
</body>

</html>
