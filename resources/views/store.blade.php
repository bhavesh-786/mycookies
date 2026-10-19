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
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Leaflet CSS & JS for Interactive Map Selection -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        body {
            font-family: {{ app()->getLocale() === 'ar' ? "'Cairo', sans-serif" : "'Plus Jakarta Sans', sans-serif" }};
            -webkit-tap-highlight-color: transparent;
        }

        .custom-scroll::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #d6d3d1;
            border-radius: 9999px;
        }

        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: #a8a29e;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none !important;
            height: 0 !important;
            width: 0 !important;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        [x-cloak] {
            display: none !important;
        }

        @keyframes slowZoom {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.03);
            }

            100% {
                transform: scale(1);
            }
        }

        .animate-subtle-zoom {
            animation: slowZoom 24s ease-in-out infinite;
        }

        .pb-safe {
            padding-bottom: env(safe-area-inset-bottom, 16px);
        }
    </style>
</head>

<body
    class="bg-[#F8F7F4] text-[#24261F] antialiased h-[100dvh] overflow-hidden selection:bg-[#747D52]/20 selection:text-[#272E1B]"
    x-data="storeApp()" x-init="initRouter()">

    <div class="flex h-[100dvh] w-full overflow-hidden">
        <!-- ================= LEFT INTERACTIVE PANEL (50%) ================= -->
        <main
            class="w-full lg:w-1/2 flex flex-col h-full bg-[#FCFBF9] border-r rtl:border-r-0 rtl:border-l border-stone-200/80 relative z-10 shadow-sm min-w-0 overflow-hidden">

            <!-- Sticky Header -->
            <header
                class="px-4 sm:px-6 py-3 border-b border-stone-200/70 flex items-center justify-between bg-white/95 backdrop-blur-md sticky top-0 z-30 transition-all shrink-0">
                <div class="flex items-center space-x-3 rtl:space-x-reverse cursor-pointer group min-w-0"
                    @click="navigate('/')">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-[#747D52] to-[#5A623E] text-white flex items-center justify-center font-black rounded-xl text-xs tracking-widest uppercase shadow-md shadow-[#747D52]/25 group-hover:scale-105 transition-all shrink-0">
                        OW
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                            <h1
                                class="font-black text-sm sm:text-base tracking-tight text-[#24261F] group-hover:text-[#747D52] transition truncate">
                                {{ __('otherwise') }}
                            </h1>
                            <span
                                class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-[#747D52]/10 text-[#747D52] border border-[#747D52]/20 shrink-0">
                                Specialty
                            </span>
                        </div>
                        <div
                            class="flex items-center space-x-2 rtl:space-x-reverse mt-0.5 text-[11px] text-stone-500 font-medium">
                            <span class="flex items-center space-x-1.5 rtl:space-x-reverse text-emerald-700 shrink-0">
                                <span class="relative flex h-2 w-2">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                <span>{{ $settings['store_status'] ?? __('Open') }}</span>
                            </span>
                            <span>&bull;</span>
                            <span class="truncate">{{ __('Min.') }} {{ $settings['min_order'] ?? '3.75' }}
                                {{ __('KD') }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-1 sm:space-x-2 rtl:space-x-reverse shrink-0">
                    <button @click="toggleSearch()"
                        class="w-9 h-9 flex items-center justify-center hover:bg-stone-100 rounded-xl transition text-stone-600 active:scale-95"
                        :class="searchOpen ? 'text-[#747D52] bg-[#747D52]/10' : ''" title="Search">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </button>

                    <button @click="navigate('/cart')"
                        class="relative w-9 h-9 flex items-center justify-center hover:bg-stone-100 rounded-xl transition text-stone-600 active:scale-95"
                        title="Cart">
                        <i class="fa-solid fa-bag-shopping text-xs"></i>
                        <span x-show="cart.length > 0" x-text="cartCount"
                            class="absolute top-1 right-1 rtl:right-auto rtl:left-1 bg-[#747D52] text-white text-[9px] font-black w-4 h-4 rounded-full flex items-center justify-center ring-2 ring-white shadow-xs">
                        </span>
                    </button>

                    <button @click="navigate('/profile')"
                        class="w-9 h-9 flex items-center justify-center hover:bg-stone-100 rounded-xl transition text-stone-600 active:scale-95"
                        title="Menu & Profile">
                        <i class="fa-solid fa-bars text-xs"></i>
                    </button>

                    <a href="{{ route('lang.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}"
                        class="inline-flex items-center space-x-1 rtl:space-x-reverse px-2.5 py-1.5 rounded-xl border border-stone-200 text-[11px] font-bold text-stone-700 hover:bg-stone-50 transition active:scale-95 shadow-2xs">
                        <i class="fa-solid fa-globe text-stone-400 text-[10px]"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'EN' : 'عربي' }}</span>
                    </a>
                </div>
            </header>

            <!-- Search Bar -->
            <div x-show="searchOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                class="px-4 sm:px-6 py-2.5 bg-stone-50/90 border-b border-stone-200/70 shrink-0">
                <div class="relative">
                    <i
                        class="fa-solid fa-magnifying-glass absolute left-3.5 rtl:left-auto rtl:right-3.5 top-3 text-stone-400 text-xs"></i>
                    <input type="text" x-model="productSearch" x-ref="searchInput"
                        placeholder="{{ __('Search products or categories...') }}"
                        class="w-full bg-white border border-stone-200 rounded-xl py-2 pl-9 pr-8 rtl:pr-9 rtl:pl-8 text-xs text-stone-900 placeholder-stone-400 outline-none focus:border-[#747D52] focus:ring-2 focus:ring-[#747D52]/20 transition shadow-inner">
                    <button x-show="productSearch.length > 0" @click="productSearch = ''"
                        class="absolute right-2.5 rtl:right-auto rtl:left-2.5 top-2.5 text-stone-400 hover:text-stone-700 text-xs">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </button>
                </div>
            </div>

            <!-- ================= SIDEBAR DRAWER (FILTER & SORT) ================= -->
            <div x-show="showFilterModal" x-cloak class="relative z-50">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-xs transition-opacity" x-show="showFilterModal"
                    @click="showFilterModal = false"></div>

                <div class="fixed inset-y-0 left-0 rtl:left-auto rtl:right-0 w-full max-w-sm sm:max-w-md bg-white shadow-2xl z-50 flex flex-col h-full"
                    x-show="showFilterModal" x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="-translate-x-full rtl:translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in duration-200 transform"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="-translate-x-full rtl:translate-x-full">

                    <div class="px-5 py-4 border-b border-stone-200 flex items-center justify-between">
                        <h3 class="font-extrabold text-sm text-stone-900">{{ __('Filter & Sort') }}</h3>
                        <button @click="showFilterModal = false"
                            class="w-8 h-8 rounded-full hover:bg-stone-100 flex items-center justify-center text-stone-400 hover:text-stone-700">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

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
                                        'border-[#747D52] bg-[#747D52]/10 text-[#272E1B] font-bold ring-1 ring-[#747D52]' :
                                        'border-stone-200 text-stone-700 hover:bg-stone-50'"
                                    class="p-3 border rounded-xl text-left rtl:text-right transition">
                                    <div class="text-[10px] text-stone-400 uppercase font-bold">{{ __('Price') }}
                                    </div>
                                    <div class="text-xs mt-0.5">{{ __('Low to High') }}</div>
                                </button>

                                <button type="button" @click="selectedSort = 'price_desc'"
                                    :class="selectedSort === 'price_desc' ?
                                        'border-[#747D52] bg-[#747D52]/10 text-[#272E1B] font-bold ring-1 ring-[#747D52]' :
                                        'border-stone-200 text-stone-700 hover:bg-stone-50'"
                                    class="p-3 border rounded-xl text-left rtl:text-right transition">
                                    <div class="text-[10px] text-stone-400 uppercase font-bold">{{ __('Price') }}
                                    </div>
                                    <div class="text-xs mt-0.5">{{ __('High to Low') }}</div>
                                </button>

                                <button type="button" @click="selectedSort = 'name_asc'"
                                    :class="selectedSort === 'name_asc' ?
                                        'border-[#747D52] bg-[#747D52]/10 text-[#272E1B] font-bold ring-1 ring-[#747D52]' :
                                        'border-stone-200 text-stone-700 hover:bg-stone-50'"
                                    class="p-3 border rounded-xl text-left rtl:text-right transition">
                                    <div class="text-[10px] text-stone-400 uppercase font-bold">{{ __('Name') }}
                                    </div>
                                    <div class="text-xs mt-0.5">{{ __('A to Z') }}</div>
                                </button>

                                <button type="button" @click="selectedSort = 'name_desc'"
                                    :class="selectedSort === 'name_desc' ?
                                        'border-[#747D52] bg-[#747D52]/10 text-[#272E1B] font-bold ring-1 ring-[#747D52]' :
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
                                            'bg-[#747D52]/10 text-[#272E1B] font-bold border border-[#747D52]/30' :
                                            'text-stone-700 border border-transparent'">
                                        <span class="text-xs tracking-wide" x-text="cat.name"></span>
                                        <input type="radio" name="cat_filter" :value="cat.id"
                                            :checked="Number(selectedCategoryFilter) === Number(cat.id)"
                                            @change="applyCategoryFilter(cat.id)"
                                            class="w-4 h-4 text-[#747D52] focus:ring-[#747D52] border-stone-300">
                                    </label>
                                </template>
                            </div>
                        </div>

                        <hr class="border-stone-100">

                        <!-- 3. MAX PRICE SLIDER -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-stone-900 text-xs">{{ __('Price') }}</span>
                                <span class="font-extrabold text-xs text-[#747D52]"
                                    x-text="`${parseFloat(maxPriceFilter).toFixed(3)} {{ __('KD') }}`"></span>
                            </div>
                            <input type="range" min="0" max="25" step="0.250"
                                :value="maxPriceFilter" @input="maxPriceFilter = parseFloat($event.target.value)"
                                class="w-full accent-[#747D52] cursor-pointer h-1.5 bg-stone-200 rounded-lg">
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

                    <div class="p-4 border-t border-stone-200 bg-white">
                        <button type="button" @click="showFilterModal = false"
                            class="w-full bg-[#747D52] hover:bg-[#636C44] text-white font-extrabold py-3.5 rounded-xl text-xs transition active:scale-[0.98] shadow-md shadow-[#747D52]/20 flex items-center justify-center space-x-2 rtl:space-x-reverse">
                            <span>{{ __('Show results') }}</span>
                            <span x-text="`(${totalFilteredResultsCount})`" class="opacity-80"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- SCROLLABLE BODY AREA -->
            <div class="flex-1 min-h-0 overflow-y-auto custom-scroll p-4 sm:p-6 space-y-5 pb-32 lg:pb-12">

                <!-- Delivery / Pickup Switcher & Quick Location Info -->
                <section class="p-3.5 sm:p-4 bg-white border border-stone-200/60 rounded-2xl shadow-xs shrink-0"
                    x-show="['categories-grid', 'category-products'].includes(view)">
                    <div
                        class="grid grid-cols-2 max-w-[280px] mx-auto bg-stone-100 p-1 rounded-2xl shadow-inner mb-3 border border-stone-200/50">
                        <button @click="setMethod('delivery')"
                            :class="method === 'delivery' ? 'bg-white text-[#24261F] font-black shadow-xs' :
                                'text-stone-500 hover:text-stone-800 font-semibold'"
                            class="py-1.5 text-xs text-center transition-all duration-200 rounded-xl flex items-center justify-center space-x-1.5 rtl:space-x-reverse">
                            <i class="fa-solid fa-bicycle text-[11px]"
                                :class="method === 'delivery' ? 'text-[#747D52]' : 'opacity-60'"></i>
                            <span>{{ __('Delivery') }}</span>
                        </button>
                        <button @click="setMethod('pickup')"
                            :class="method === 'pickup' ? 'bg-[#747D52] text-white font-black shadow-xs' :
                                'text-stone-500 hover:text-stone-800 font-semibold'"
                            class="py-1.5 text-xs text-center transition-all duration-200 rounded-xl flex items-center justify-center space-x-1.5 rtl:space-x-reverse">
                            <i class="fa-solid fa-store text-[11px]"
                                :class="method === 'pickup' ? 'text-white' : 'opacity-60'"></i>
                            <span>{{ __('Pickup') }}</span>
                        </button>
                    </div>

                    <div
                        class="flex items-center justify-between text-xs px-3 py-2 bg-[#FAF9F5] border border-stone-200/70 rounded-xl shadow-2xs">
                        <div class="flex items-center space-x-2 rtl:space-x-reverse truncate min-w-0">
                            <span
                                class="w-7 h-7 rounded-lg bg-white flex items-center justify-center text-[#747D52] shadow-xs shrink-0 border border-stone-100">
                                <i class="fa-solid text-[11px]"
                                    :class="method === 'delivery' ? 'fa-location-dot' : 'fa-store'"></i>
                            </span>
                            <div class="truncate min-w-0">
                                <span class="text-[10px] text-stone-400 block leading-tight font-medium"
                                    x-text="method === 'delivery' ? '{{ __('Deliver to') }}' : '{{ __('Store Branch') }}'"></span>
                                <span class="font-bold text-[#24261F] truncate text-xs block"
                                    x-text="currentLocationName"></span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 rtl:space-x-reverse shrink-0 ml-2 rtl:ml-0 rtl:mr-2">
                            <span
                                class="text-[11px] font-semibold text-stone-600 bg-white px-2 py-0.5 rounded-lg border border-stone-200/60 shadow-2xs"
                                x-text="method === 'delivery' ? '{{ __('~1 h') }}' : '{{ __('~30 min') }}'"></span>
                            <button
                                @click="navigate(method === 'delivery' && savedAddresses.length > 0 ? '/checkout/address' : '/select-location')"
                                class="text-[#747D52] font-black text-[11px] hover:underline">{{ __('Change') }}</button>
                        </div>
                    </div>
                </section>

                <!-- ================= HORIZONTAL QUICK-CATEGORY BAR ================= -->
                <div class="relative w-full rounded-2xl border border-stone-200/60 bg-white p-2 shrink-0 overflow-hidden shadow-xs"
                    x-show="view === 'categories-grid' || view === 'category-products'" x-data="{
                        scrollLeft() { $refs.pillsContainer.scrollBy({ left: -160, behavior: 'smooth' }); },
                            scrollRight() { $refs.pillsContainer.scrollBy({ left: 160, behavior: 'smooth' }); }
                    }">

                    <button type="button" @click="scrollLeft()"
                        class="hidden sm:flex absolute left-0 top-0 bottom-0 z-10 w-7 items-center justify-center bg-gradient-to-r from-white via-white/90 to-transparent text-stone-500 hover:text-stone-900 transition">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    </button>

                    <div x-ref="pillsContainer"
                        class="px-2 py-1 flex items-center space-x-2 rtl:space-x-reverse overflow-x-auto no-scrollbar scroll-smooth w-full">
                        <button type="button" @click="applyCategoryFilter(null)"
                            class="px-3.5 py-1.5 rounded-full text-xs font-bold whitespace-nowrap transition active:scale-95 shrink-0"
                            :class="!selectedCategoryFilter ? 'bg-[#747D52] text-white shadow-xs' :
                                'bg-stone-50 border border-stone-200 text-stone-600 hover:border-stone-400'">
                            {{ __('All Categories') }}
                        </button>
                        <template x-for="cat in categoriesList" :key="cat.id">
                            <button type="button" @click="openCategory(cat)"
                                class="px-3.5 py-1.5 rounded-full text-xs font-bold whitespace-nowrap transition active:scale-95 shrink-0"
                                :class="Number(selectedCategoryFilter) === Number(cat.id) ?
                                    'bg-[#747D52] text-white shadow-xs' :
                                    'bg-stone-50 border border-stone-200 text-stone-600 hover:border-stone-400'"
                                x-text="cat.name">
                            </button>
                        </template>
                        <div class="w-8 shrink-0"></div>
                    </div>

                    <button type="button" @click="scrollRight()"
                        class="hidden sm:flex absolute right-0 top-0 bottom-0 z-10 w-7 items-center justify-center bg-gradient-to-l from-white via-white/90 to-transparent text-stone-500 hover:text-stone-900 transition">
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </button>
                </div>

                <!-- Filter & Sort Tag Button & Active Status Bar -->
                <div class="flex items-center justify-between shrink-0"
                    x-show="view === 'categories-grid' || view === 'category-products'">
                    <button @click="showFilterModal = true"
                        class="border border-stone-200 bg-white text-stone-700 px-3.5 py-1.5 rounded-xl text-xs font-bold shadow-2xs hover:border-[#747D52] hover:bg-stone-50 transition flex items-center space-x-1.5 rtl:space-x-reverse"
                        :class="(selectedSort !== 'default' || maxPriceFilter < 25.000) ?
                        'border-[#747D52] text-[#747D52] bg-[#747D52]/10' : ''">
                        <i class="fa-solid fa-sliders text-[10px]"></i>
                        <span>{{ __('Filter & Sort') }}</span>
                        <span x-show="selectedSort !== 'default' || maxPriceFilter < 25.000"
                            class="w-1.5 h-1.5 rounded-full bg-[#747D52]"></span>
                    </button>

                    <button x-show="selectedSort !== 'default' || productSearch !== '' || maxPriceFilter < 25.000"
                        @click="resetFilters()"
                        class="text-[11px] text-stone-400 hover:text-stone-800 underline font-semibold transition">
                        {{ __('Reset Filters') }}
                    </button>
                </div>

                <!-- MOBILE HERO SLIDER FROM PRODUCTS (lg:hidden) -->
                <div class="block lg:hidden shrink-0" x-show="view === 'categories-grid'" x-data="{
                    activeSlide: 0,
                    timer: null,
                    get featuredSlides() {
                        const items = [];
                        this.categoriesList.forEach(cat => {
                            (cat.products || []).forEach(prod => {
                                if (prod.image && items.length < 5) {
                                    items.push({
                                        id: prod.id,
                                        name: prod.name,
                                        slug: prod.slug,
                                        image: prod.image,
                                        base_price: prod.base_price,
                                        product: prod
                                    });
                                }
                            });
                        });
                        return items;
                    },
                    startAutoSlide() {
                        if (this.timer) clearInterval(this.timer);
                        this.timer = setInterval(() => {
                            if (this.featuredSlides.length > 1) {
                                this.activeSlide = (this.activeSlide + 1) % this.featuredSlides.length;
                            }
                        }, 4000);
                    },
                    init() {
                        this.startAutoSlide();
                    }
                }">

                    <template x-if="featuredSlides.length > 0">
                        <div
                            class="relative w-full aspect-[21/9] sm:aspect-[2.4/1] rounded-2xl overflow-hidden shadow-sm bg-stone-100 group select-none ring-1 ring-black/5">
                            <template x-for="(item, i) in featuredSlides" :key="item.id">
                                <div x-show="activeSlide === i" @click="openCustomizer(item.product)"
                                    x-transition:enter="transition ease-out duration-500"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-300"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute inset-0 cursor-pointer">

                                    <img :src="item.image" :alt="item.name"
                                        class="w-full h-full object-cover">

                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent flex flex-col justify-end p-3.5">
                                        <div class="flex items-center justify-between text-white">
                                            <div>
                                                <h4 class="font-extrabold text-xs tracking-wide leading-tight drop-shadow-sm"
                                                    x-text="item.name"></h4>
                                                <span
                                                    class="text-[11px] font-extrabold text-[#272E1B] bg-white/95 px-2.5 py-0.5 rounded-lg mt-1 inline-block shadow-xs"
                                                    x-text="item.base_price ? `${parseFloat(item.base_price).toFixed(3)} {{ __('KD') }}` : '{{ __('Customizable') }}'"></span>
                                            </div>
                                            <span
                                                class="w-7 h-7 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-xs">
                                                <i class="fa-solid fa-arrow-right rtl:rotate-180"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <div class="absolute bottom-2.5 inset-x-0 flex justify-center space-x-1.5 rtl:space-x-reverse z-10"
                                x-show="featuredSlides.length > 1">
                                <template x-for="(item, i) in featuredSlides" :key="i">
                                    <button type="button" @click.stop="activeSlide = i; startAutoSlide()"
                                        class="h-1.5 rounded-full transition-all duration-300"
                                        :class="activeSlide === i ? 'w-5 bg-white' : 'w-1.5 bg-white/50'"></button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- SCREEN 1: CATEGORY TILES GRID -->
                <div x-show="view === 'categories-grid'" class="space-y-4">
                    <div class="grid grid-cols-2 sm:grid-cols-2 xl:grid-cols-3 gap-3.5 sm:gap-4">
                        <template x-for="category in displayedCategories" :key="category.id">
                            <div @click="openCategory(category)"
                                class="group cursor-pointer rounded-2xl overflow-hidden border border-stone-200/80 bg-white hover:border-[#747D52]/50 hover:shadow-lg transition-all duration-300 flex flex-col justify-between">
                                <div class="relative aspect-square sm:aspect-[4/3] bg-stone-100 overflow-hidden">
                                    <img :src="category.image || 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=500'"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors">
                                    </div>
                                </div>
                                <div class="p-3 sm:p-3.5 bg-white flex items-center justify-between">
                                    <h3 class="font-extrabold text-xs uppercase tracking-wider text-stone-900 group-hover:text-[#747D52] transition truncate"
                                        x-text="category.name">
                                    </h3>
                                    <i
                                        class="fa-solid fa-chevron-right rtl:fa-chevron-left text-[10px] text-stone-400 group-hover:text-[#747D52] group-hover:translate-x-0.5 rtl:group-hover:-translate-x-0.5 transition-transform shrink-0 ml-1"></i>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div x-show="displayedCategories.length === 0" class="text-center py-16 text-stone-400">
                        <i class="fa-solid fa-magnifying-glass text-3xl mb-2 text-stone-300"></i>
                        <p class="text-xs font-medium">
                            {{ __('No categories found matching your price or search filter.') }}</p>
                    </div>
                </div>

                <!-- SCREEN 2: PRODUCTS UNDER SELECTED CATEGORY -->
                <div x-show="view === 'category-products'" x-cloak class="space-y-4">
                    <div class="flex items-center space-x-2.5 rtl:space-x-reverse pb-2.5 border-b border-stone-100">
                        <button @click="navigate('/')"
                            class="w-8 h-8 rounded-xl border border-stone-200 hover:bg-stone-100 flex items-center justify-center text-xs text-stone-600 transition active:scale-95">
                            <i class="fa-solid fa-arrow-left rtl:rotate-180"></i>
                        </button>
                        <h2 class="font-extrabold text-sm uppercase tracking-wide text-stone-900"
                            x-text="activeCategory?.name"></h2>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-2 xl:grid-cols-3 gap-3.5 sm:gap-4">
                        <template x-for="product in displayedProducts" :key="product.id">
                            <div
                                class="group border border-stone-200/80 rounded-2xl p-3 bg-white flex flex-col justify-between hover:shadow-md hover:border-[#747D52]/40 transition-all duration-300">
                                <div>
                                    <div class="aspect-square sm:aspect-[4/3] rounded-xl overflow-hidden bg-stone-100 mb-2.5 relative cursor-pointer"
                                        @click="openCustomizer(product)">
                                        <img :src="product.image ||
                                            'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?w=500'"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    </div>
                                    <h3 class="font-bold text-xs text-stone-900 line-clamp-1 group-hover:text-[#747D52] transition cursor-pointer"
                                        @click="openCustomizer(product)" x-text="product.name"></h3>
                                    <p class="text-[11px] text-stone-400 mt-1 line-clamp-2 leading-relaxed"
                                        x-text="product.description"></p>
                                </div>
                                <div class="mt-3 pt-2 border-t border-stone-50">
                                    <div class="text-xs font-extrabold text-[#747D52] mb-2"
                                        x-text="product.base_price ? `${parseFloat(product.base_price).toFixed(3)} {{ __('KD') }}` : '{{ __('Price on selection') }}'">
                                    </div>
                                    <button @click="openCustomizer(product)"
                                        class="w-full border border-[#747D52]/80 text-[#747D52] hover:bg-[#747D52] hover:text-white text-xs font-bold py-2 rounded-xl transition active:scale-[0.97] shadow-2xs">
                                        {{ __('+ Add') }}
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div x-show="displayedProducts.length === 0" class="text-center py-16 text-stone-400">
                        <i class="fa-solid fa-mug-hot text-3xl mb-2 text-stone-300"></i>
                        <p class="text-xs font-medium">{{ __('No products found within this price range.') }}</p>
                    </div>
                </div>

                <!-- SCREEN 3: PRODUCT ADDON CUSTOMIZER WITH PRODUCT REMARKS -->
                <div x-show="view === 'customizer'" x-cloak class="space-y-5 max-w-xl mx-auto pb-6">
                    <div class="flex items-center space-x-3 rtl:space-x-reverse">
                        <button @click="navigate('/category/' + (activeCategory?.slug || ''))"
                            class="w-8 h-8 rounded-xl border border-stone-200 hover:bg-stone-50 flex items-center justify-center text-xs text-stone-600 transition active:scale-95 shadow-2xs">
                            <i class="fa-solid fa-arrow-left rtl:rotate-180"></i>
                        </button>
                        <h2 class="font-extrabold text-sm text-stone-900 tracking-tight" x-text="activeProduct?.name">
                        </h2>
                    </div>

                    <!-- Product Showcase Card -->
                    <div class="bg-white rounded-3xl overflow-hidden border border-stone-200/80 shadow-md">
                        <div
                            class="relative w-full aspect-[16/10] sm:aspect-[16/9] bg-stone-900 overflow-hidden group">
                            <img :src="activeProduct?.image || 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?w=800'"
                                class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out">
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent">
                            </div>
                            <div class="absolute bottom-3.5 left-4 right-4 flex items-end justify-between text-white">
                                <div>
                                    <span
                                        class="text-[10px] uppercase tracking-widest font-extrabold bg-[#747D52] px-2.5 py-1 rounded-lg text-white inline-block mb-1 shadow-sm">{{ __('Signature Item') }}</span>
                                    <h3 class="font-black text-base sm:text-lg drop-shadow-sm"
                                        x-text="activeProduct?.name"></h3>
                                </div>
                                <span
                                    class="text-sm sm:text-base font-black bg-white/95 text-[#272E1B] px-3 py-1 rounded-xl shadow-md"
                                    x-text="activeProduct?.base_price ? `${parseFloat(activeProduct.base_price).toFixed(3)} {{ __('KD') }}` : '{{ __('Customizable') }}'"></span>
                            </div>
                        </div>

                        <div class="p-4 sm:p-5 space-y-2 border-b border-stone-100"
                            x-show="activeProduct?.description">
                            <p class="text-xs text-stone-500 leading-relaxed font-medium"
                                x-text="activeProduct?.description"></p>
                        </div>
                    </div>

                    <!-- Quantity Selection Card -->
                    <div
                        class="flex items-center justify-between bg-white p-4 rounded-2xl border border-stone-200/80 shadow-xs">
                        <div>
                            <span class="font-extrabold text-xs text-stone-900 block">{{ __('Quantity') }}</span>
                            <span
                                class="text-[11px] text-stone-400 font-medium">{{ __('Select desired amount') }}</span>
                        </div>
                        <div
                            class="flex items-center space-x-3 rtl:space-x-reverse bg-stone-100 p-1.5 rounded-xl border border-stone-200/60">
                            <button @click="itemQuantity > 1 ? itemQuantity-- : null; recalcCustomizerPrice()"
                                class="w-8 h-8 rounded-lg bg-white border border-stone-200 font-black text-xs hover:border-[#747D52] hover:text-[#747D52] shadow-xs active:scale-90 transition flex items-center justify-center">-</button>
                            <span class="text-xs font-black w-6 text-center text-stone-900"
                                x-text="itemQuantity"></span>
                            <button @click="itemQuantity++; recalcCustomizerPrice()"
                                class="w-8 h-8 rounded-lg bg-white border border-stone-200 font-black text-xs hover:border-[#747D52] hover:text-[#747D52] shadow-xs active:scale-90 transition flex items-center justify-center">+</button>
                        </div>
                    </div>

                    <!-- Addon Groups -->
                    <template x-for="group in activeProduct?.addon_groups" :key="group.id">
                        <div class="border border-stone-200/80 rounded-2xl p-4 sm:p-5 bg-white space-y-4 shadow-xs">
                            <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                                <div>
                                    <h4 class="font-extrabold text-xs text-stone-900 uppercase tracking-wider"
                                        x-text="group.name">
                                    </h4>
                                    <div class="flex items-center space-x-2 rtl:space-x-reverse mt-1">
                                        <span
                                            :class="group.is_required ? 'bg-[#747D52] text-white' :
                                                'bg-stone-100 text-stone-600'"
                                            class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-wider"
                                            x-text="group.is_required ? '{{ __('Required') }}' : '{{ __('Optional') }}'">
                                        </span>
                                        <span class="text-[11px] text-stone-400 font-medium"
                                            x-text="group.type === 'radio' ? '{{ __('Single Choice') }}' : `{{ __('Max') }}: ${group.max_selectable}`">
                                        </span>
                                    </div>
                                </div>
                                <template x-if="group.is_required && !isGroupSatisfied(group)">
                                    <span
                                        class="text-[11px] font-bold text-rose-600 bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-200/50">{{ __('Required') }}</span>
                                </template>
                            </div>

                            <div class="space-y-2 pt-1">
                                <template x-for="opt in group.options" :key="opt.id">
                                    <label
                                        :class="{
                                            'opacity-40 cursor-not-allowed bg-stone-50 border-stone-200': isOptionDisabled(
                                                group, opt.id),
                                            'cursor-pointer hover:border-[#747D52]/60 hover:bg-[#747D52]/5 bg-white': !
                                                isOptionDisabled(
                                                    group, opt.id),
                                            'border-[#747D52] bg-[#747D52]/10 text-[#272E1B] font-bold ring-1 ring-[#747D52]/40 shadow-xs': isOptionSelected(
                                                group.id, opt.id)
                                        }"
                                        class="flex items-center justify-between p-3.5 rounded-xl border border-stone-200 transition select-none">

                                        <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                            <input :type="group.type === 'radio' ? 'radio' : 'checkbox'"
                                                :name="'grp_' + group.id"
                                                :checked="isOptionSelected(group.id, opt.id)"
                                                :disabled="isOptionDisabled(group, opt.id)"
                                                @change="toggleOption(group, opt)"
                                                class="w-4 h-4 text-[#747D52] focus:ring-[#747D52] accent-[#747D52]"
                                                :class="group.type === 'radio' ? '' : 'rounded'">
                                            <span class="text-xs font-semibold text-stone-800"
                                                x-text="opt.name"></span>
                                        </div>

                                        <span class="text-xs font-extrabold text-[#747D52]"
                                            x-text="parseFloat(opt.price) > 0 ? `+ ${parseFloat(opt.price).toFixed(3)} {{ __('KD') }}` : `0.000 {{ __('KD') }}`">
                                        </span>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- PRODUCT SPECIAL REMARKS/INSTRUCTIONS CARD[cite: 1] -->
                    <div class="border border-stone-200/80 rounded-2xl p-4 bg-white space-y-2 shadow-xs">
                        <label
                            class="block font-extrabold text-xs text-stone-900 flex items-center space-x-1.5 rtl:space-x-reverse">
                            <i class="fa-regular fa-comment-dots text-[#747D52]"></i>
                            <span>{{ __('Special Remarks / Instructions') }}</span>
                            <span class="text-stone-400 font-normal text-[10px]">({{ __('Optional') }})</span>
                        </label>
                        <textarea x-model="itemRemarks" rows="2"
                            placeholder="{{ __('e.g., Less sugar, extra sauce on the side, allergies...') }}"
                            class="w-full border border-stone-200 rounded-xl p-3 text-xs outline-none focus:border-[#747D52] focus:ring-2 focus:ring-[#747D52]/20 transition shadow-inner resize-none"></textarea>
                    </div>
                </div>

                <!-- SCREEN 4: SHOPPING CART -->
                <div x-show="view === 'cart'" x-cloak class="space-y-4">
                    <div class="flex items-center space-x-3 rtl:space-x-reverse pb-2.5 border-b border-stone-100">
                        <button @click="navigate('/')"
                            class="w-8 h-8 rounded-xl border border-stone-200 hover:bg-stone-50 flex items-center justify-center text-xs text-stone-600 transition active:scale-95">
                            <i class="fa-solid fa-arrow-left rtl:rotate-180"></i>
                        </button>
                        <h2 class="font-extrabold text-base text-stone-900 tracking-tight">{{ __('Shopping Cart') }}
                        </h2>
                    </div>

                    <template x-if="cart.length === 0">
                        <div class="text-center py-16 text-stone-400">
                            <div
                                class="w-14 h-14 rounded-2xl bg-stone-100 flex items-center justify-center mx-auto text-stone-400 text-2xl mb-3">
                                <i class="fa-solid fa-bag-shopping"></i>
                            </div>
                            <p class="text-xs font-bold text-stone-600">{{ __('Your cart is empty.') }}</p>
                            <button @click="navigate('/')"
                                class="mt-3 text-xs text-[#747D52] font-bold underline hover:text-[#636C44]">
                                {{ __('Explore Menu') }}
                            </button>
                        </div>
                    </template>

                    <div class="space-y-3">
                        <template x-for="(item, idx) in cart" :key="idx">
                            <div
                                class="p-3.5 rounded-2xl border border-stone-200/80 bg-white flex justify-between items-start shadow-xs hover:border-stone-300 transition">
                                <div class="min-w-0 pr-2">
                                    <h4 class="font-bold text-xs text-stone-900" x-text="item.name"></h4>
                                    <div class="mt-1 space-y-0.5">
                                        <template x-for="add in item.addons">
                                            <p class="text-[11px] text-stone-400 font-medium"
                                                x-text="`${add.name} (+ ${add.price.toFixed(3)} {{ __('KD') }})`">
                                            </p>
                                        </template>
                                        <template x-if="item.notes">
                                            <p
                                                class="text-[11px] text-[#747D52] font-semibold italic flex items-center space-x-1 rtl:space-x-reverse pt-0.5">
                                                <i class="fa-solid fa-pen-nib text-[9px]"></i>
                                                <span x-text="item.notes"></span>
                                            </p>
                                        </template>
                                    </div>
                                    <span class="inline-block mt-2 font-extrabold text-xs text-[#747D52]"
                                        x-text="`${item.total_price.toFixed(3)} {{ __('KD') }}`"></span>
                                </div>
                                <div class="flex items-center space-x-3 rtl:space-x-reverse shrink-0">
                                    <div
                                        class="flex items-center space-x-1.5 rtl:space-x-reverse bg-stone-100 p-1 rounded-xl">
                                        <button @click="decreaseQty(idx)"
                                            class="w-6 h-6 rounded-lg bg-white border border-stone-200 flex items-center justify-center font-bold text-xs text-stone-700 hover:border-[#747D52] transition shadow-xs">-</button>
                                        <span class="text-xs font-bold text-stone-800 w-5 text-center"
                                            x-text="item.quantity"></span>
                                        <button @click="increaseQty(idx)"
                                            class="w-6 h-6 rounded-lg bg-white border border-stone-200 flex items-center justify-center font-bold text-xs text-stone-700 hover:border-[#747D52] transition shadow-xs">+</button>
                                    </div>
                                    <button @click="removeItem(idx)"
                                        class="w-8 h-8 rounded-xl hover:bg-rose-50 text-stone-400 hover:text-rose-600 transition flex items-center justify-center">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- SCREEN: LOCATION SELECTOR -->
                <div x-show="view === 'select-location'" x-cloak class="space-y-4">
                    <div class="flex items-center space-x-3 rtl:space-x-reverse pb-2.5 border-b border-stone-100">
                        <button @click="navigate('/')"
                            class="w-8 h-8 rounded-xl border border-stone-200 hover:bg-stone-50 flex items-center justify-center text-xs text-stone-600 transition active:scale-95">
                            <i class="fa-solid fa-arrow-left rtl:rotate-180"></i>
                        </button>
                        <h2 class="font-extrabold text-sm text-stone-900"
                            x-text="method === 'delivery' ? '{{ __('Choose Delivery Area') }}' : '{{ __('Choose Store Branch') }}'">
                        </h2>
                    </div>

                    <div class="relative">
                        <i
                            class="fa-solid fa-magnifying-glass absolute left-3.5 rtl:left-auto rtl:right-3.5 top-3.5 text-stone-400 text-xs"></i>
                        <input type="text" x-model="areaSearch"
                            :placeholder="method === 'delivery' ? '{{ __('Search area...') }}' :
                                '{{ __('Search store branch...') }}'"
                            class="w-full border border-stone-200 rounded-xl py-2.5 pl-9 pr-3.5 rtl:pr-9 rtl:pl-3.5 text-xs outline-none focus:border-[#747D52] focus:ring-2 focus:ring-[#747D52]/20 shadow-xs transition">
                    </div>

                    <div x-show="method === 'delivery'" class="space-y-2.5">
                        <template x-for="gov in filteredGovernorates" :key="gov.id">
                            <div class="border border-stone-200/80 rounded-2xl overflow-hidden bg-white shadow-xs">
                                <button type="button" @click="toggleGov(gov.id)"
                                    class="w-full px-4 py-3.5 bg-stone-50/70 hover:bg-stone-100/80 flex justify-between items-center text-xs font-bold text-stone-800 transition">
                                    <span x-text="gov.name"></span>
                                    <i class="fa-solid fa-chevron-down text-[10px] text-stone-400 transition-transform duration-200"
                                        :class="isGovOpen(gov.id) ? 'rotate-180 text-stone-700' : ''"></i>
                                </button>

                                <div x-show="isGovOpen(gov.id)"
                                    class="p-2 space-y-1 divide-y divide-stone-100 border-t border-stone-100">
                                    <template x-for="area in gov.filteredAreas" :key="area.id">
                                        <button type="button"
                                            @click="setDeliveryArea(area.id, area.name, area.delivery_fee)"
                                            class="w-full text-left rtl:text-right py-2.5 px-3 rounded-xl text-xs hover:bg-[#747D52]/10 hover:text-[#272E1B] flex items-center transition"
                                            :class="selectedDeliveryArea?.name === area.name ?
                                                'bg-[#747D52]/15 text-[#272E1B] font-bold' : 'text-stone-700'">
                                            <span x-text="area.name"></span>
                                        </button>
                                    </template>
                                    <div x-show="gov.filteredAreas.length === 0"
                                        class="text-xs text-stone-400 p-2.5 text-center">
                                        {{ __('No areas found') }}
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div x-show="method === 'pickup'" class="space-y-2.5">
                        <template x-for="st in filteredStores" :key="st.id">
                            <button type="button" @click="setStorePickup(st)"
                                class="w-full text-left rtl:text-right p-4 rounded-2xl border border-stone-200/80 hover:border-[#747D52] hover:bg-[#747D52]/5 transition bg-white flex justify-between items-center shadow-xs"
                                :class="selectedPickupStore?.id === st.id ?
                                    'border-[#747D52] bg-[#747D52]/10 ring-1 ring-[#747D52]' : ''">
                                <div>
                                    <h4 class="font-bold text-xs text-stone-900" x-text="st.name"></h4>
                                    <p class="text-[11px] text-stone-400 mt-1" x-text="st.description"></p>
                                </div>
                                <span
                                    class="w-8 h-8 rounded-xl bg-stone-100 flex items-center justify-center text-[#747D52]">
                                    <i class="fa-solid fa-store text-xs"></i>
                                </span>
                            </button>
                        </template>
                        <div x-show="filteredStores.length === 0"
                            class="text-xs text-stone-400 p-8 text-center bg-white rounded-2xl border border-stone-200">
                            {{ __('No store branches available') }}
                        </div>
                    </div>
                </div>

                <!-- SCREEN: MY ORDERS & TRACKING DETAILS -->
                <div x-show="view === 'my-orders'" x-cloak class="space-y-4">
                    <div class="flex items-center space-x-3 rtl:space-x-reverse pb-2.5 border-b border-stone-100">
                        <button @click="navigate('/profile')"
                            class="w-8 h-8 rounded-xl border border-stone-200 hover:bg-stone-50 flex items-center justify-center text-xs text-stone-600 transition active:scale-95">
                            <i class="fa-solid fa-arrow-left rtl:rotate-180"></i>
                        </button>
                        <h2 class="font-extrabold text-sm text-stone-900">{{ __('My Orders & Tracking') }}</h2>
                    </div>

                    <div x-show="ordersLoading" class="text-center py-10 text-stone-400 text-xs">
                        <i class="fa-solid fa-circle-notch fa-spin text-xl mb-2 text-[#747D52]"></i>
                        <p>{{ __('Loading orders...') }}</p>
                    </div>

                    <div x-show="!ordersLoading && customerOrdersList.length === 0"
                        class="text-center py-16 text-stone-400">
                        <i class="fa-regular fa-clock text-4xl mb-2 text-stone-300"></i>
                        <p class="text-xs font-bold text-stone-600">{{ __('You don\'t have any past orders.') }}</p>
                    </div>

                    <div x-show="!ordersLoading && customerOrdersList.length > 0" class="space-y-3.5">
                        <template x-for="ord in customerOrdersList" :key="ord.id">
                            <div class="border border-stone-200/80 rounded-2xl p-4 bg-white shadow-xs space-y-3">
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
                                            'bg-amber-50 text-amber-700': ['pending', 'placed', 'preparing',
                                                'awaiting_payment'
                                            ].includes(
                                                ord.status),
                                            'bg-blue-50 text-blue-700': ord.status === 'shipped',
                                            'bg-rose-50 text-rose-700': ord.status === 'cancelled'
                                        }"
                                        x-text="ord.status">
                                    </span>
                                </div>

                                <div class="py-1">
                                    <div
                                        class="flex items-center justify-between text-[10px] font-bold text-stone-500 mb-1.5">
                                        <span
                                            :class="ord.status !== 'cancelled' ? 'text-[#747D52]' : ''">{{ __('Placed') }}</span>
                                        <span
                                            :class="['preparing', 'shipped', 'delivered'].includes(ord.status) ?
                                                'text-[#747D52]' : ''">{{ __('Preparing') }}</span>
                                        <span
                                            :class="['shipped', 'delivered'].includes(ord.status) ? 'text-[#747D52]' : ''">{{ __('Out for delivery') }}</span>
                                        <span
                                            :class="ord.status === 'delivered' ? 'text-emerald-600' : ''">{{ __('Delivered') }}</span>
                                    </div>
                                    <div class="w-full bg-stone-100 h-2 rounded-full overflow-hidden p-0.5">
                                        <div class="h-full bg-gradient-to-r from-[#747D52] to-[#636C44] rounded-full transition-all duration-500"
                                            :style="{
                                                width: ord.status === 'cancelled' ? '0%' : (ord
                                                    .status === 'delivered' ? '100%' : (ord
                                                        .status === 'shipped' ? '70%' : (ord
                                                            .status === 'preparing' ? '40%' : '15%')))
                                            }">
                                        </div>
                                    </div>
                                </div>

                                <div class="divide-y divide-stone-50 text-xs">
                                    <template x-for="item in ord.items" :key="item.id">
                                        <div class="py-1.5 flex justify-between items-start">
                                            <div>
                                                <span class="font-bold text-stone-800"
                                                    x-text="`${item.name || item.product_name} x${item.quantity}`"></span>
                                                <template x-if="item.addons && item.addons.length > 0">
                                                    <div class="text-[10px] text-stone-400">
                                                        <template x-for="ad in item.addons">
                                                            <span x-text="`${ad.name}, `"></span>
                                                        </template>
                                                    </div>
                                                </template>
                                                <template x-if="item.notes">
                                                    <div class="text-[10px] text-[#747D52] italic"
                                                        x-text="`Note: ${item.notes}`"></div>
                                                </template>
                                            </div>
                                            <span class="font-bold text-stone-700"
                                                x-text="`${parseFloat(item.total_price).toFixed(3)} {{ __('KD') }}`"></span>
                                        </div>
                                    </template>
                                </div>

                                <div class="border-t border-stone-100 pt-2.5 flex items-center justify-between">
                                    <div>
                                        <span class="text-[11px] text-stone-400 block">{{ __('Total Amount') }}</span>
                                        <strong class="text-xs font-extrabold text-[#747D52]"
                                            x-text="`${parseFloat(ord.total).toFixed(3)} {{ __('KD') }}`"></strong>
                                    </div>

                                    <template x-if="ord.can_cancel">
                                        <button type="button" @click="cancelCustomerOrder(ord.id)"
                                            class="bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 px-3 py-1.5 rounded-xl font-bold text-xs transition active:scale-95 flex items-center space-x-1.5 rtl:space-x-reverse shadow-xs">
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

                <!-- SCREEN: CHECKOUT DETAILS (GUEST / REGISTER) -->
                <div x-show="view === 'checkout-details'" x-cloak class="space-y-5">
                    <div class="flex items-center space-x-3 rtl:space-x-reverse pb-2.5 border-b border-stone-100">
                        <button @click="navigate('/cart')"
                            class="w-8 h-8 rounded-xl border border-stone-200 hover:bg-stone-50 flex items-center justify-center text-xs text-stone-600 transition active:scale-95">
                            <i class="fa-solid fa-arrow-left rtl:rotate-180"></i>
                        </button>
                        <h2 class="font-extrabold text-sm text-stone-900">{{ __('Contact Information') }}</h2>
                    </div>

                    <div class="text-center py-2 space-y-3.5">
                        <div
                            class="w-16 h-16 mx-auto bg-gradient-to-br from-[#747D52]/20 to-[#747D52]/5 text-[#272E1B] rounded-2xl flex items-center justify-center text-2xl shadow-sm border border-[#747D52]/20">
                            <i class="fa-regular fa-address-card"></i>
                        </div>
                        <h3 class="font-extrabold text-sm text-stone-900">{{ __('Fast Checkout') }}</h3>

                        <div class="max-w-xs mx-auto space-y-2 text-left rtl:text-right text-xs text-stone-600 pt-1">
                            <div class="flex items-center space-x-2.5 rtl:space-x-reverse">
                                <span
                                    class="w-4 h-4 rounded-full bg-[#747D52]/20 text-[#272E1B] flex items-center justify-center text-[9px] shrink-0">
                                    <i class="fa-solid fa-check"></i>
                                </span>
                                <span>{{ __('Save your addresses') }}</span>
                            </div>
                            <div class="flex items-center space-x-2.5 rtl:space-x-reverse">
                                <span
                                    class="w-4 h-4 rounded-full bg-[#747D52]/20 text-[#272E1B] flex items-center justify-center text-[9px] shrink-0">
                                    <i class="fa-solid fa-check"></i>
                                </span>
                                <span>{{ __('Save your contact information') }}</span>
                            </div>
                            <div class="flex items-center space-x-2.5 rtl:space-x-reverse">
                                <span
                                    class="w-4 h-4 rounded-full bg-[#747D52]/20 text-[#272E1B] flex items-center justify-center text-[9px] shrink-0">
                                    <i class="fa-solid fa-check"></i>
                                </span>
                                <span>{{ __('One-tap re-ordering') }}</span>
                            </div>
                        </div>

                        <div class="pt-2">
                            <button @click="navigate('/profile/email-signin')"
                                class="w-full max-w-xs mx-auto bg-[#747D52] hover:bg-[#636C44] text-white font-extrabold py-3.5 rounded-xl text-xs shadow-md shadow-[#747D52]/20 transition active:scale-[0.98] block">
                                {{ __('SIGN UP') }}
                            </button>
                        </div>

                        <div class="pt-2">
                            <button @click="guestExpanded = !guestExpanded"
                                class="text-xs text-stone-500 font-bold hover:text-stone-900 underline transition">
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
                                class="w-full border border-stone-200 rounded-xl px-3.5 py-2.5 text-xs outline-none focus:border-[#747D52] focus:ring-2 focus:ring-[#747D52]/20 transition">
                        </div>
                        <div>
                            <label
                                class="block font-bold text-stone-700 mb-1">{{ __('Email (for invoice) *') }}</label>
                            <input type="email" x-model="customer.email" placeholder="example@email.com"
                                class="w-full border border-stone-200 rounded-xl px-3.5 py-2.5 text-xs outline-none focus:border-[#747D52] focus:ring-2 focus:ring-[#747D52]/20 transition">
                        </div>
                        <div>
                            <label class="block font-bold text-stone-700 mb-1">{{ __('Phone (+965) *') }}</label>
                            <div class="flex">
                                <span
                                    class="inline-flex items-center px-3.5 rounded-l-xl rtl:rounded-l-none rtl:rounded-r-xl border border-r-0 rtl:border-r rtl:border-l-0 border-stone-200 bg-stone-50 text-stone-600 text-xs font-bold">+965</span>
                                <input type="tel" x-model="customer.phone" placeholder="965..."
                                    class="w-full border border-stone-200 rounded-r-xl rtl:rounded-r-none rtl:rounded-l-xl px-3.5 py-2.5 text-xs outline-none focus:border-[#747D52] focus:ring-2 focus:ring-[#747D52]/20 transition">
                            </div>
                        </div>
                        <button @click="proceedGuestToAddress()" :disabled="guestVerificationLoading"
                            class="w-full bg-stone-900 hover:bg-black text-white font-extrabold py-3.5 rounded-xl text-xs transition active:scale-[0.98] mt-2 shadow-sm flex items-center justify-center space-x-2">
                            <span x-show="!guestVerificationLoading">{{ __('Next (Continue to Address)') }}</span>
                            <span x-show="guestVerificationLoading"><i class="fa-solid fa-circle-notch fa-spin"></i>
                                Processing...</span>
                        </button>
                    </div>
                </div>

                <!-- SCREEN: PROFILE & MENU DRAWER -->
                <div x-show="view === 'profile-menu'" x-cloak class="space-y-6">
                    <div class="flex items-center space-x-3 rtl:space-x-reverse pb-2.5 border-b border-stone-100">
                        <button @click="navigate('/')"
                            class="w-8 h-8 rounded-xl border border-stone-200 hover:bg-stone-50 flex items-center justify-center text-xs text-stone-600 transition active:scale-95">
                            <i class="fa-solid fa-arrow-left rtl:rotate-180"></i>
                        </button>
                        <h2 class="font-extrabold text-sm text-stone-900">{{ __('Profile') }}</h2>
                    </div>

                    <template x-if="isAuthenticated">
                        <div
                            class="flex items-start justify-between bg-stone-50/70 p-4 rounded-2xl border border-stone-200/60 shadow-2xs">
                            <div class="flex items-center space-x-3.5 rtl:space-x-reverse">
                                <div
                                    class="w-12 h-12 rounded-xl bg-[#747D52]/15 text-[#272E1B] flex items-center justify-center text-xl font-bold">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div class="space-y-0.5 text-xs">
                                    <h3 class="font-extrabold text-stone-900 text-sm" x-text="currentUser.name"></h3>
                                    <p class="text-stone-500 text-[11px]" x-text="currentUser.email"></p>
                                    <p class="text-stone-500 text-[11px]" x-text="currentUser.phone || '+965...'"></p>
                                </div>
                            </div>
                            <button @click="signOut()" class="text-[#747D52] text-xs font-bold hover:underline">
                                {{ __('Sign out') }}
                            </button>
                        </div>
                    </template>

                    <div class="space-y-1">
                        <div class="text-[11px] font-extrabold text-stone-400 uppercase tracking-wider mb-2">
                            {{ __('Menu') }}
                        </div>
                        <div class="divide-y divide-stone-100 border-t border-b border-stone-100 text-xs">
                            <button @click="navigate('/cart')"
                                class="w-full flex items-center justify-between py-3.5 text-stone-700 hover:text-[#747D52] transition group">
                                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                    <span
                                        class="w-7 h-7 rounded-lg bg-stone-100 flex items-center justify-center text-stone-500 group-hover:text-[#747D52] group-hover:bg-[#747D52]/10 transition">
                                        <i class="fa-solid fa-cart-shopping text-xs"></i>
                                    </span>
                                    <span class="font-semibold">{{ __('My cart') }}</span>
                                </div>
                                <span x-show="cart.length > 0" class="w-2 h-2 rounded-full bg-[#747D52]"></span>
                            </button>

                            <button @click="navigate('/')"
                                class="w-full flex items-center space-x-3 rtl:space-x-reverse py-3.5 text-stone-700 hover:text-[#747D52] transition group">
                                <span
                                    class="w-7 h-7 rounded-lg bg-stone-100 flex items-center justify-center text-stone-500 group-hover:text-[#747D52] group-hover:bg-[#747D52]/10 transition">
                                    <i class="fa-solid fa-mug-hot text-xs"></i>
                                </span>
                                <span class="font-semibold">{{ __('Menu') }}</span>
                            </button>

                            <button @click="navigate('/profile/orders')"
                                class="w-full flex items-center space-x-3 rtl:space-x-reverse py-3.5 text-stone-700 hover:text-[#747D52] transition group">
                                <span
                                    class="w-7 h-7 rounded-lg bg-stone-100 flex items-center justify-center text-stone-500 group-hover:text-[#747D52] group-hover:bg-[#747D52]/10 transition">
                                    <i class="fa-regular fa-clock text-xs"></i>
                                </span>
                                <span class="font-semibold">{{ __('My orders') }}</span>
                            </button>

                            <button @click="manageAddressesFromProfile()"
                                class="w-full flex items-center space-x-3 rtl:space-x-reverse py-3.5 text-stone-700 hover:text-[#747D52] transition group">
                                <span
                                    class="w-7 h-7 rounded-lg bg-stone-100 flex items-center justify-center text-stone-500 group-hover:text-[#747D52] group-hover:bg-[#747D52]/10 transition">
                                    <i class="fa-solid fa-map-location-dot text-xs"></i>
                                </span>
                                <span class="font-semibold">{{ __('Delivery addresses') }}</span>
                            </button>

                            <template x-if="isAuthenticated">
                                <button @click="deleteAccount()"
                                    class="w-full flex items-center space-x-3 rtl:space-x-reverse py-3.5 text-rose-600 hover:opacity-80 transition font-semibold group">
                                    <span
                                        class="w-7 h-7 rounded-lg bg-rose-50 flex items-center justify-center text-rose-500">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </span>
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
                                    <span
                                        class="w-7 h-7 rounded-lg bg-stone-100 flex items-center justify-center text-stone-500">
                                        <i class="fa-solid fa-envelope text-xs"></i>
                                    </span>
                                    <span class="font-semibold">{{ __('Email') }}</span>
                                </button>
                            </div>
                        </div>
                    </template>

                    <div class="flex justify-center pt-6">
                        <div class="p-3 border border-stone-200/80 rounded-2xl bg-white shadow-xs">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode(url('/')) }}"
                                alt="Store QR Code" class="w-24 h-24 object-contain">
                        </div>
                    </div>
                </div>

                <!-- SCREEN: TABBED EMAIL SIGNIN / REGISTER -->
                <div x-show="view === 'email-signin'" x-cloak class="space-y-5">
                    <div class="flex items-center space-x-3 rtl:space-x-reverse pb-2.5 border-b border-stone-100">
                        <button @click="navigate('/profile')"
                            class="w-8 h-8 rounded-xl border border-stone-200 hover:bg-stone-50 flex items-center justify-center text-xs text-stone-600 transition active:scale-95">
                            <i class="fa-solid fa-arrow-left rtl:rotate-180"></i>
                        </button>
                        <h2 class="font-extrabold text-sm text-stone-900"
                            x-text="authTab === 'reset-password' ? '{{ __('Reset Password') }}' : (authTab === 'forgot' ? '{{ __('Forgot Password') }}' : (authTab === 'login' ? '{{ __('Login') }}' : '{{ __('Register') }}'))">
                        </h2>
                    </div>

                    <div class="flex bg-stone-100 p-1 rounded-2xl border border-stone-200/60"
                        x-show="authTab !== 'forgot' && authTab !== 'reset-password'">
                        <button @click="authTab = 'login'"
                            :class="authTab === 'login' ? 'bg-white text-stone-900 font-extrabold shadow-sm' :
                                'text-stone-600 font-medium'"
                            class="flex-1 py-2 text-xs transition rounded-xl">
                            {{ __('Login') }}
                        </button>
                        <button @click="authTab = 'register'"
                            :class="authTab === 'register' ? 'bg-[#747D52] text-white font-extrabold shadow-sm' :
                                'text-stone-600 font-medium'"
                            class="flex-1 py-2 text-xs transition rounded-xl">
                            {{ __('Register') }}
                        </button>
                    </div>

                    <template x-if="authError">
                        <div
                            class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold flex items-center space-x-2 rtl:space-x-reverse shadow-2xs">
                            <i class="fa-solid fa-circle-exclamation shrink-0"></i>
                            <span x-text="authError"></span>
                        </div>
                    </template>

                    <template x-if="authSuccess">
                        <div
                            class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold flex items-center space-x-2 rtl:space-x-reverse shadow-2xs">
                            <i class="fa-solid fa-circle-check shrink-0"></i>
                            <span x-text="authSuccess"></span>
                        </div>
                    </template>

                    <div x-show="authTab === 'login'" class="space-y-3 pt-2 text-xs">
                        <div>
                            <label class="block font-bold text-stone-700 mb-1">{{ __('Email *') }}</label>
                            <input type="email" x-model="authForm.email" placeholder="example@email.com"
                                class="w-full border border-stone-200 rounded-xl px-3.5 py-2.5 text-xs outline-none focus:border-[#747D52] focus:ring-2 focus:ring-[#747D52]/20 transition">
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="font-bold text-stone-700">{{ __('Password *') }}</label>
                                <button type="button" @click="authTab = 'forgot'"
                                    class="text-[11px] text-stone-400 hover:text-[#747D52] font-semibold transition">
                                    {{ __('Forgot Password?') }}
                                </button>
                            </div>
                            <input type="password" x-model="authForm.password" placeholder="••••••••"
                                class="w-full border border-stone-200 rounded-xl px-3.5 py-2.5 text-xs outline-none focus:border-[#747D52] focus:ring-2 focus:ring-[#747D52]/20 transition">
                        </div>

                        <button @click="submitSignIn()" :disabled="authLoading"
                            class="w-full bg-[#747D52] hover:bg-[#636C44] text-white font-extrabold py-3.5 rounded-xl text-xs transition active:scale-[0.98] shadow-md shadow-[#747D52]/20 flex items-center justify-center space-x-2 rtl:space-x-reverse disabled:opacity-75 disabled:cursor-not-allowed">
                            <template x-if="!authLoading">
                                <span>{{ __('Login') }}</span>
                            </template>
                            <template x-if="authLoading">
                                <span class="flex items-center space-x-2 rtl:space-x-reverse">
                                    <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                                    <span>otherwise choose well...</span>
                                </span>
                            </template>
                        </button>
                    </div>

                    <div x-show="authTab === 'register'" class="space-y-3 pt-2 text-xs">
                        <div>
                            <label class="block font-bold text-stone-700 mb-1">{{ __('Full Name *') }}</label>
                            <input type="text" x-model="regForm.name" placeholder="John Doe"
                                class="w-full border border-stone-200 rounded-xl px-3.5 py-2.5 text-xs outline-none focus:border-[#747D52] focus:ring-2 focus:ring-[#747D52]/20 transition">
                        </div>
                        <div>
                            <label class="block font-bold text-stone-700 mb-1">{{ __('Email *') }}</label>
                            <input type="email" x-model="regForm.email" placeholder="example@email.com"
                                class="w-full border border-stone-200 rounded-xl px-3.5 py-2.5 text-xs outline-none focus:border-[#747D52] focus:ring-2 focus:ring-[#747D52]/20 transition">
                        </div>
                        <div>
                            <label class="block font-bold text-stone-700 mb-1">{{ __('Phone (+965) *') }}</label>
                            <input type="tel" x-model="regForm.phone" placeholder="965..."
                                class="w-full border border-stone-200 rounded-xl px-3.5 py-2.5 text-xs outline-none focus:border-[#747D52] focus:ring-2 focus:ring-[#747D52]/20 transition">
                        </div>
                        <div>
                            <label class="block font-bold text-stone-700 mb-1">{{ __('Password *') }}</label>
                            <input type="password" x-model="regForm.password" placeholder="••••••••"
                                class="w-full border border-stone-200 rounded-xl px-3.5 py-2.5 text-xs outline-none focus:border-[#747D52] focus:ring-2 focus:ring-[#747D52]/20 transition">
                        </div>

                        <button @click="submitSignUp()" :disabled="authLoading"
                            class="w-full bg-[#747D52] hover:bg-[#636C44] text-white font-extrabold py-3.5 rounded-xl text-xs transition active:scale-[0.98] shadow-md shadow-[#747D52]/20 flex items-center justify-center space-x-2 rtl:space-x-reverse disabled:opacity-75 disabled:cursor-not-allowed">
                            <template x-if="!authLoading">
                                <span>{{ __('Register') }}</span>
                            </template>
                            <template x-if="authLoading">
                                <span class="flex items-center space-x-2 rtl:space-x-reverse">
                                    <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                                    <span>otherwise choose well...</span>
                                </span>
                            </template>
                        </button>
                    </div>

                    <div x-show="authTab === 'forgot'" class="space-y-3 pt-2 text-xs">
                        <p class="text-stone-500 text-[11px] leading-relaxed">
                            {{ __('Enter your registered email address and we will send you a link to reset your password.') }}
                        </p>
                        <div>
                            <label class="block font-bold text-stone-700 mb-1">{{ __('Email *') }}</label>
                            <input type="email" x-model="forgotEmail" placeholder="example@email.com"
                                class="w-full border border-stone-200 rounded-xl px-3.5 py-2.5 text-xs outline-none focus:border-[#747D52] focus:ring-2 focus:ring-[#747D52]/20 transition">
                        </div>

                        <button @click="submitForgotPassword()" :disabled="authLoading"
                            class="w-full bg-[#747D52] hover:bg-[#636C44] text-white font-extrabold py-3.5 rounded-xl text-xs transition active:scale-[0.98] shadow-md shadow-[#747D52]/20 flex items-center justify-center space-x-2 rtl:space-x-reverse disabled:opacity-75 disabled:cursor-not-allowed">
                            <template x-if="!authLoading">
                                <span>{{ __('Send Reset Link') }}</span>
                            </template>
                            <template x-if="authLoading">
                                <span class="flex items-center space-x-2 rtl:space-x-reverse">
                                    <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                                    <span>otherwise choose well...</span>
                                </span>
                            </template>
                        </button>

                        <div class="text-center pt-2">
                            <button type="button" @click="authTab = 'login'"
                                class="text-xs text-stone-500 hover:text-stone-900 underline font-semibold">
                                {{ __('Back to Login') }}
                            </button>
                        </div>
                    </div>

                    <div x-show="authTab === 'reset-password'" class="space-y-3 pt-2 text-xs">
                        <p class="text-stone-500 text-[11px] leading-relaxed">
                            {{ __('Enter your new password below.') }}
                        </p>
                        <div>
                            <label class="block font-bold text-stone-700 mb-1">{{ __('Email') }}</label>
                            <input type="email" x-model="resetForm.email" readonly disabled
                                class="w-full border border-stone-200 bg-stone-50 rounded-xl px-3.5 py-2.5 text-xs text-stone-500 outline-none">
                        </div>
                        <div>
                            <label class="block font-bold text-stone-700 mb-1">{{ __('New Password *') }}</label>
                            <input type="password" x-model="resetForm.password" placeholder="••••••••"
                                class="w-full border border-stone-200 rounded-xl px-3.5 py-2.5 text-xs outline-none focus:border-[#747D52] focus:ring-2 focus:ring-[#747D52]/20 transition">
                        </div>
                        <div>
                            <label
                                class="block font-bold text-stone-700 mb-1">{{ __('Confirm New Password *') }}</label>
                            <input type="password" x-model="resetForm.password_confirmation" placeholder="••••••••"
                                class="w-full border border-stone-200 rounded-xl px-3.5 py-2.5 text-xs outline-none focus:border-[#747D52] focus:ring-2 focus:ring-[#747D52]/20 transition">
                        </div>

                        <button @click="submitPasswordReset()" :disabled="authLoading"
                            class="w-full bg-[#747D52] hover:bg-[#636C44] text-white font-extrabold py-3.5 rounded-xl text-xs transition active:scale-[0.98] shadow-md shadow-[#747D52]/20 flex items-center justify-center space-x-2 rtl:space-x-reverse disabled:opacity-75 disabled:cursor-not-allowed">
                            <template x-if="!authLoading">
                                <span>{{ __('Update Password') }}</span>
                            </template>
                            <template x-if="authLoading">
                                <span class="flex items-center space-x-2 rtl:space-x-reverse">
                                    <i class="fa-solid fa-circle-notch fa-spin text-xs"></i>
                                    <span>otherwise choose well...</span>
                                </span>
                            </template>
                        </button>
                    </div>
                </div>

                <!-- ================= SCREEN: DEDICATED SAVED ADDRESSES MANAGER ================= -->
                <div x-show="view === 'select-saved-address'" x-cloak class="space-y-4">
                    <div class="flex items-center justify-between pb-2.5 border-b border-stone-100">
                        <div class="flex items-center space-x-3 rtl:space-x-reverse">
                            <button type="button" @click="navigate(addressBackRoute)"
                                class="w-8 h-8 rounded-xl border border-stone-200 hover:bg-stone-50 flex items-center justify-center text-xs text-stone-600 transition active:scale-95 shadow-2xs">
                                <i class="fa-solid fa-arrow-left rtl:rotate-180"></i>
                            </button>
                            <h2 class="font-extrabold text-sm text-stone-900">{{ __('My Saved Addresses') }}</h2>
                        </div>
                        <button type="button" @click="createNewAddress()"
                            class="text-xs text-[#747D52] hover:text-[#5A623E] font-black flex items-center space-x-1 rtl:space-x-reverse">
                            <i class="fa-solid fa-plus text-[10px]"></i>
                            <span>{{ __('Add New') }}</span>
                        </button>
                    </div>

                    <!-- Loading State -->
                    <div x-show="addressLoading" class="text-center py-10 text-stone-400 text-xs">
                        <i class="fa-solid fa-circle-notch fa-spin text-xl mb-2 text-[#747D52]"></i>
                        <p>{{ __('Loading addresses...') }}</p>
                    </div>

                    <!-- Empty State -->
                    <div x-show="!addressLoading && savedAddresses.length === 0"
                        class="text-center py-16 text-stone-400">
                        <div
                            class="w-14 h-14 rounded-2xl bg-stone-100 flex items-center justify-center mx-auto text-stone-400 text-2xl mb-3">
                            <i class="fa-solid fa-map-location-dot"></i>
                        </div>
                        <p class="text-xs font-bold text-stone-600">{{ __('No saved addresses found.') }}</p>
                        <button type="button" @click="createNewAddress()"
                            class="mt-3 text-xs text-[#747D52] font-bold underline hover:text-[#636C44]">
                            {{ __('Add your first address') }}
                        </button>
                    </div>

                    <!-- Address Cards List -->
                    <div class="space-y-3" x-show="!addressLoading && savedAddresses.length > 0">
                        <template x-for="addr in savedAddresses" :key="addr.id">
                            <div class="border rounded-2xl p-4 bg-white shadow-xs transition hover:border-[#747D52]/40"
                                :class="selectedAddressId === addr.id ?
                                    'border-[#747D52] ring-2 ring-[#747D52]/20 bg-[#747D52]/5' : 'border-stone-200/80'">

                                <!-- Top Row: Label, Badges & Action Buttons -->
                                <div class="flex items-center justify-between border-b border-stone-100 pb-2.5 mb-2.5">
                                    <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                        <span
                                            class="w-7 h-7 rounded-lg bg-stone-100 flex items-center justify-center text-xs text-[#747D52]">
                                            <i class="fa-solid"
                                                :class="addr.type === 'Office' ? 'fa-briefcase' : (addr
                                                    .type === 'Friend' ? 'fa-user-group' : 'fa-house')"></i>
                                        </span>
                                        <h4 class="font-extrabold text-xs text-stone-900" x-text="addr.type"></h4>
                                        <template x-if="addr.is_default">
                                            <span
                                                class="text-[9px] font-black uppercase tracking-wider bg-[#747D52] text-white px-2 py-0.5 rounded-full shadow-2xs">
                                                {{ __('Default') }}
                                            </span>
                                        </template>
                                    </div>

                                    <div class="flex items-center space-x-1.5 rtl:space-x-reverse">
                                        <button type="button" @click="editSavedAddress(addr)"
                                            class="w-7 h-7 rounded-lg hover:bg-stone-100 text-stone-400 hover:text-stone-700 flex items-center justify-center text-xs transition"
                                            title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button type="button" @click="deleteSavedAddress(addr.id)"
                                            class="w-7 h-7 rounded-lg hover:bg-rose-50 text-stone-400 hover:text-rose-600 flex items-center justify-center text-xs transition"
                                            title="Delete">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Middle Row: Formatted Address Details -->
                                <div class="text-xs text-stone-600 leading-relaxed space-y-0.5">
                                    <p class="font-bold text-stone-800"
                                        x-text="`Block ${addr.block}, Street ${addr.street}${addr.avenue ? ', Ave ' + addr.avenue : ''}, Building ${addr.building}`">
                                    </p>
                                    <p class="text-[11px] text-stone-400" x-show="addr.floor || addr.flat_no"
                                        x-text="`${addr.floor ? 'Floor: ' + addr.floor : ''}${addr.floor && addr.flat_no ? ' | ' : ''}${addr.flat_no ? 'Flat/Office: ' + addr.flat_no : ''}`">
                                    </p>
                                    <p class="text-[11px] text-stone-400" x-show="addr.additional_directions"
                                        x-text="addr.additional_directions"></p>
                                </div>

                                <!-- Bottom Row: Controls -->
                                <div class="flex items-center justify-between pt-3 mt-3 border-t border-stone-100">
                                    <button type="button" @click="markAsDefaultAddress(addr.id)"
                                        class="text-[11px] font-bold transition flex items-center space-x-1.5 rtl:space-x-reverse"
                                        :class="addr.is_default ? 'text-[#747D52] pointer-events-none' :
                                            'text-stone-400 hover:text-stone-700'">
                                        <i class="fa-regular"
                                            :class="addr.is_default ? 'fa-circle-check text-[#747D52]' : 'fa-circle'"></i>
                                        <span
                                            x-text="addr.is_default ? '{{ __('Default Address') }}' : '{{ __('Set as Default') }}'"></span>
                                    </button>

                                    <button type="button" @click="applyAddressAndProceed(addr)"
                                        class="bg-[#747D52] hover:bg-[#636C44] text-white text-xs font-bold px-4 py-2 rounded-xl transition active:scale-95 shadow-xs flex items-center space-x-1 rtl:space-x-reverse">
                                        <span>{{ __('Deliver Here') }}</span>
                                        <i class="fa-solid fa-arrow-right text-[10px] rtl:rotate-180"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- ================= SCREEN: KUWAIT ADDRESS FORM (CREATE & EDIT) ================= -->
                <div x-show="view === 'address'" x-cloak class="space-y-4">

                    <!-- 1. HEADER WITH EXPLICIT "SAVED ADDRESSES" BUTTON -->
                    <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                        <div class="flex items-center space-x-3 rtl:space-x-reverse">
                            <button type="button" @click="navigate(addressBackRoute)"
                                class="w-8 h-8 rounded-xl border border-stone-200 hover:bg-stone-50 flex items-center justify-center text-xs text-stone-600 transition active:scale-95">
                                <i class="fa-solid fa-arrow-left rtl:rotate-180"></i>
                            </button>
                            <h3 class="font-extrabold text-sm text-stone-900"
                                x-text="selectedAddressId ? '{{ __('Edit Delivery Address') }}' : '{{ __('Delivery Address Details') }}'">
                            </h3>
                        </div>

                        <!-- ALWAYS VISIBLE SELECTION BUTTON -->
                        <button type="button"
                            @click="view = 'select-saved-address'; setPageTitle('{{ __('Select Delivery Address') }}')"
                            class="inline-flex items-center space-x-1.5 rtl:space-x-reverse px-3 py-1.5 rounded-xl border border-[#747D52]/40 bg-[#747D52]/10 hover:bg-[#747D52]/20 text-[#272E1B] text-xs font-extrabold transition shadow-2xs active:scale-95">
                            <i class="fa-solid fa-map-location-dot text-[#747D52] text-xs"></i>
                            <span>{{ __('Choose Saved Address') }}</span>
                            <span
                                class="bg-[#747D52] text-white text-[10px] w-4 h-4 rounded-full inline-flex items-center justify-center font-bold"
                                x-text="savedAddresses.length"></span>
                        </button>
                    </div>

                    <!-- 2. QUICK ADDRESS SELECTOR PILLS (SHOWS SAVED ADDRESSES DIRECTLY HERE) -->
                    <div x-show="savedAddresses.length > 0"
                        class="bg-stone-50/90 p-3 rounded-2xl border border-stone-200/70 space-y-2">
                        <div class="flex items-center justify-between">
                            <span
                                class="text-xs font-bold text-stone-700 flex items-center space-x-1 rtl:space-x-reverse">
                                <i class="fa-solid fa-clock-rotate-left text-[#747D52] text-xs"></i>
                                <span>{{ __('Select Existing Address:') }}</span>
                            </span>
                            <button type="button" @click="resetAddressForm()"
                                class="text-[11px] text-[#747D52] hover:underline font-bold">
                                {{ __('+ Enter New Address') }}
                            </button>
                        </div>

                        <!-- Horizontal scrollable address cards -->
                        <div class="flex space-x-2 rtl:space-x-reverse overflow-x-auto no-scrollbar py-1">
                            <template x-for="saved in savedAddresses" :key="saved.id">
                                <button type="button" @click="selectSavedAddress(saved)"
                                    class="p-2.5 rounded-xl border text-left rtl:text-right shrink-0 min-w-[170px] max-w-[210px] transition active:scale-95"
                                    :class="selectedAddressId === saved.id ?
                                        'border-[#747D52] bg-[#747D52]/15 ring-1 ring-[#747D52]' :
                                        'border-stone-200 bg-white hover:border-stone-400'">

                                    <div class="flex items-center justify-between mb-1">
                                        <span
                                            class="text-xs font-extrabold text-stone-900 flex items-center space-x-1 rtl:space-x-reverse">
                                            <i class="fa-solid text-[10px] text-[#747D52]"
                                                :class="saved.type === 'Office' ? 'fa-briefcase' : (saved
                                                    .type === 'Friend' ? 'fa-user-group' : 'fa-house')"></i>
                                            <span x-text="saved.type"></span>
                                        </span>
                                        <template x-if="saved.is_default">
                                            <span
                                                class="text-[9px] font-black uppercase tracking-wider bg-[#747D52] text-white px-1.5 py-0.2 rounded">
                                                {{ __('Default') }}
                                            </span>
                                        </template>
                                    </div>

                                    <p class="text-[10px] text-stone-500 truncate"
                                        x-text="`Blk ${saved.block}, St ${saved.street}, Bld ${saved.building}`"></p>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- 3. Address Type Switcher -->
                    <div>
                        <label
                            class="block font-bold text-stone-700 text-xs mb-1.5">{{ __('Address Label / Type *') }}</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button" @click="address.type = 'Home'"
                                :class="address.type === 'Home' ? 'bg-[#747D52] text-white shadow-xs' :
                                    'border border-stone-200 text-stone-600 hover:bg-stone-50'"
                                class="py-2.5 rounded-xl text-xs font-bold transition flex items-center justify-center space-x-1.5 rtl:space-x-reverse">
                                <i class="fa-solid fa-house text-[11px]"></i>
                                <span>{{ __('Home') }}</span>
                            </button>
                            <button type="button" @click="address.type = 'Office'"
                                :class="address.type === 'Office' ? 'bg-[#747D52] text-white shadow-xs' :
                                    'border border-stone-200 text-stone-600 hover:bg-stone-50'"
                                class="py-2.5 rounded-xl text-xs font-bold transition flex items-center justify-center space-x-1.5 rtl:space-x-reverse">
                                <i class="fa-solid fa-briefcase text-[11px]"></i>
                                <span>{{ __('Office') }}</span>
                            </button>
                            <button type="button" @click="address.type = 'Friend'"
                                :class="address.type === 'Friend' ? 'bg-[#747D52] text-white shadow-xs' :
                                    'border border-stone-200 text-stone-600 hover:bg-stone-50'"
                                class="py-2.5 rounded-xl text-xs font-bold transition flex items-center justify-center space-x-1.5 rtl:space-x-reverse">
                                <i class="fa-solid fa-user-group text-[11px]"></i>
                                <span>{{ __('Friend') }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- 4. Paste Google Maps Link or Coordinates Input -->
                    <div class="bg-stone-50/80 p-3 rounded-2xl border border-stone-200/70 space-y-1.5">
                        <label
                            class="block font-bold text-stone-700 text-xs flex items-center space-x-1 rtl:space-x-reverse">
                            <i class="fa-solid fa-paste text-[#747D52]"></i>
                            <span>{{ __('Paste Google Maps Link or Coordinates') }}</span>
                        </label>
                        <div class="flex space-x-2 rtl:space-x-reverse">
                            <input type="text" x-model="pastedMapUrl"
                                placeholder="{{ __('Paste link or lat, lng') }}"
                                class="flex-1 bg-white border border-stone-200 rounded-xl p-2.5 text-xs outline-none focus:border-[#747D52] focus:ring-2 focus:ring-[#747D52]/20 transition shadow-inner">
                            <button type="button" @click="applyPastedLocation()"
                                class="bg-[#747D52] hover:bg-[#636C44] text-white px-3.5 rounded-xl text-xs font-bold transition active:scale-95 shadow-xs shrink-0">
                                {{ __('Locate') }}
                            </button>
                        </div>
                    </div>

                    <!-- 5. Map Pinpoint Picker -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="block font-bold text-stone-700 text-xs">
                                <i class="fa-solid fa-location-crosshairs text-[#747D52] mr-1"></i>
                                {{ __('Pin Exact Location on Map') }}
                            </label>
                            <button type="button" @click="detectCurrentLocation()"
                                class="text-[11px] text-[#747D52] hover:text-[#5A623E] font-bold flex items-center space-x-1 rtl:space-x-reverse">
                                <i class="fa-solid fa-crosshairs"></i>
                                <span>{{ __('Use My GPS') }}</span>
                            </button>
                        </div>
                        <div class="rounded-2xl overflow-hidden border border-stone-200/90 shadow-inner relative">
                            <div id="addressPickerMap" class="w-full h-44 z-0"></div>
                            <div
                                class="absolute bottom-2 left-2 right-2 bg-white/90 backdrop-blur-xs px-2.5 py-1 rounded-lg text-[10px] text-stone-500 font-semibold shadow-xs pointer-events-none flex items-center justify-between">
                                <span>{{ __('Drag marker to adjust') }}</span>
                                <span
                                    x-text="address.latitude ? `${parseFloat(address.latitude).toFixed(4)}, ${parseFloat(address.longitude).toFixed(4)}` : 'Kuwait'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- 6. Block & Street -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-stone-700 text-xs mb-1">{{ __('Block *') }}</label>
                            <input type="text" x-model="address.block" placeholder="{{ __('e.g. 1') }}"
                                class="w-full border border-stone-200 rounded-xl p-2.5 text-xs outline-none focus:border-[#747D52] focus:ring-2 focus:ring-[#747D52]/20 transition shadow-xs">
                        </div>
                        <div>
                            <label class="block font-bold text-stone-700 text-xs mb-1">{{ __('Street *') }}</label>
                            <input type="text" x-model="address.street" placeholder="{{ __('e.g. Street 10') }}"
                                class="w-full border border-stone-200 rounded-xl p-2.5 text-xs outline-none focus:border-[#747D52] focus:ring-2 focus:ring-[#747D52]/20 transition shadow-xs">
                        </div>
                    </div>

                    <!-- 7. Avenue & Building/House -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-stone-700 text-xs mb-1">{{ __('Avenue') }} <span
                                    class="text-stone-400 font-normal text-[11px]">({{ __('Optional') }})</span></label>
                            <input type="text" x-model="address.avenue" placeholder="{{ __('e.g. Avenue 2') }}"
                                class="w-full border border-stone-200 rounded-xl p-2.5 text-xs outline-none focus:border-[#747D52] focus:ring-2 focus:ring-[#747D52]/20 transition shadow-xs">
                        </div>
                        <div>
                            <label
                                class="block font-bold text-stone-700 text-xs mb-1">{{ __('Building / House *') }}</label>
                            <input type="text" x-model="address.building"
                                placeholder="{{ __('e.g. Building 12') }}"
                                class="w-full border border-stone-200 rounded-xl p-2.5 text-xs outline-none focus:border-[#747D52] focus:ring-2 focus:ring-[#747D52]/20 transition shadow-xs">
                        </div>
                    </div>

                    <!-- 8. Floor & Flat / Office No. -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-stone-700 text-xs mb-1">{{ __('Floor') }} <span
                                    class="text-stone-400 font-normal text-[11px]">({{ __('Optional') }})</span></label>
                            <input type="text" x-model="address.floor" placeholder="{{ __('e.g. Floor 3') }}"
                                class="w-full border border-stone-200 rounded-xl p-2.5 text-xs outline-none focus:border-[#747D52] focus:ring-2 focus:ring-[#747D52]/20 transition shadow-xs">
                        </div>
                        <div>
                            <label class="block font-bold text-stone-700 text-xs mb-1">{{ __('Flat / Office No.') }}
                                <span
                                    class="text-stone-400 font-normal text-[11px]">({{ __('Optional') }})</span></label>
                            <input type="text" x-model="address.flat_no" placeholder="{{ __('e.g. Flat 12') }}"
                                class="w-full border border-stone-200 rounded-xl p-2.5 text-xs outline-none focus:border-[#747D52] focus:ring-2 focus:ring-[#747D52]/20 transition shadow-xs">
                        </div>
                    </div>

                    <!-- 9. Additional Directions & PACI -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label
                                class="block font-bold text-stone-700 text-xs mb-1">{{ __('Additional Directions') }}
                                <span
                                    class="text-stone-400 font-normal text-[11px]">({{ __('Optional') }})</span></label>
                            <input type="text" x-model="address.additional_directions"
                                placeholder="{{ __('e.g. Near park / Basement parking') }}"
                                class="w-full border border-stone-200 rounded-xl p-2.5 text-xs outline-none focus:border-[#747D52] focus:ring-2 focus:ring-[#747D52]/20 transition shadow-xs">
                        </div>
                        <div>
                            <label class="block font-bold text-stone-700 text-xs mb-1">{{ __('PACI') }} <span
                                    class="text-stone-400 font-normal text-[11px]">({{ __('Optional') }})</span></label>
                            <input type="text" x-model="address.paci" placeholder="{{ __('8-digit number') }}"
                                class="w-full border border-stone-200 rounded-xl p-2.5 text-xs outline-none focus:border-[#747D52] focus:ring-2 focus:ring-[#747D52]/20 transition shadow-xs">
                        </div>
                    </div>

                    <!-- 10. Save Address Checkbox Options -->
                    <div class="p-3.5 bg-stone-50/70 border border-stone-200/80 rounded-2xl space-y-2">
                        <label class="flex items-center space-x-2 rtl:space-x-reverse cursor-pointer">
                            <input type="checkbox" x-model="saveAddressToBook"
                                class="w-4 h-4 text-[#747D52] rounded border-stone-300 focus:ring-[#747D52]">
                            <span
                                class="text-xs font-bold text-stone-800">{{ __('Save this address to my profile') }}</span>
                        </label>
                        <div x-show="saveAddressToBook" class="pl-6 rtl:pl-0 rtl:pr-6">
                            <label class="flex items-center space-x-2 rtl:space-x-reverse cursor-pointer">
                                <input type="checkbox" x-model="setAsDefaultAddress"
                                    class="w-3.5 h-3.5 text-[#747D52] rounded border-stone-300 focus:ring-[#747D52]">
                                <span
                                    class="text-[11px] font-semibold text-stone-600">{{ __('Set as default delivery address') }}</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- SCREEN: PAYMENT & TOTAL REVIEW -->
                <div x-show="view === 'checkout'" x-cloak class="space-y-4">
                    <div class="flex items-center space-x-3 rtl:space-x-reverse pb-2.5 border-b border-stone-100">
                        <button @click="view = 'address'"
                            class="w-8 h-8 rounded-xl border border-stone-200 hover:bg-stone-50 flex items-center justify-center text-xs text-stone-600 transition active:scale-95">
                            <i class="fa-solid fa-arrow-left rtl:rotate-180"></i>
                        </button>
                        <h3 class="font-extrabold text-sm text-stone-900">{{ __('Payment Option') }}</h3>
                    </div>
                    <div class="space-y-2">
                        <label
                            class="flex items-center justify-between p-3.5 border rounded-2xl cursor-pointer hover:border-stone-300 transition bg-white shadow-xs"
                            :class="paymentMethod === 'knet' ? 'border-[#747D52] bg-[#747D52]/5 ring-1 ring-[#747D52]' :
                                'border-stone-200'">
                            <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                <input type="radio" value="knet" x-model="paymentMethod"
                                    class="text-[#747D52] focus:ring-[#747D52]">
                                <span class="text-xs font-bold text-stone-800">{{ __('Debit Card (KNET)') }}</span>
                            </div>
                            <i class="fa-regular fa-credit-card text-stone-400 text-sm"></i>
                        </label>
                        <label
                            class="flex items-center justify-between p-3.5 border rounded-2xl cursor-pointer hover:border-stone-300 transition bg-white shadow-xs"
                            :class="paymentMethod === 'cash' ? 'border-[#747D52] bg-[#747D52]/5 ring-1 ring-[#747D52]' :
                                'border-stone-200'">
                            <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                <input type="radio" value="cash" x-model="paymentMethod"
                                    class="text-[#747D52] focus:ring-[#747D52]">
                                <span class="text-xs font-bold text-stone-800">{{ __('Cash on Delivery') }}</span>
                            </div>
                            <i class="fa-solid fa-money-bill-wave text-stone-400 text-sm"></i>
                        </label>
                    </div>

                    <!-- General Order Special Remarks / Delivery Notes -->
                    <div class="space-y-1.5 pt-1">
                        <label
                            class="block font-bold text-xs text-stone-800 flex items-center space-x-1.5 rtl:space-x-reverse">
                            <i class="fa-regular fa-clipboard text-[#747D52]"></i>
                            <span>{{ __('General Order / Driver Note') }}</span>
                            <span class="text-stone-400 font-normal text-[10px]">({{ __('Optional') }})</span>
                        </label>
                        <input type="text" x-model="orderSpecialRemarks"
                            placeholder="{{ __('e.g., Please leave outside door, do not ring bell...') }}"
                            class="w-full border border-stone-200 rounded-xl p-2.5 text-xs outline-none focus:border-[#747D52] focus:ring-2 focus:ring-[#747D52]/20 transition shadow-inner">
                    </div>

                    <div
                        class="border-t border-stone-100 pt-4 space-y-2.5 text-xs bg-stone-50/70 p-4 rounded-2xl border border-stone-200/60">
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
                        <div
                            class="flex justify-between text-sm font-extrabold text-stone-900 border-t border-stone-200/60 pt-2.5">
                            <span>{{ __('Total') }}</span>
                            <span class="text-[#747D52]"
                                x-text="`${calculateGrandTotal().toFixed(3)} {{ __('KD') }}`"></span>
                        </div>
                    </div>
                </div>

                <!-- SCREEN: CONFIRMATION -->
                <div x-show="view === 'success'" x-cloak class="text-center py-16 space-y-3.5">
                    <div
                        class="w-16 h-16 bg-[#747D52]/20 text-[#272E1B] rounded-2xl flex items-center justify-center mx-auto text-2xl mb-2 shadow-sm">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <h2 class="font-extrabold text-lg text-stone-900 tracking-tight">{{ __('Order Confirmed!') }}
                    </h2>
                    <p class="text-xs text-stone-500">{{ __('Reference:') }} <strong
                            class="text-stone-900 font-extrabold" x-text="placedOrderNo"></strong></p>
                    <button @click="view = 'categories-grid'; navigate('/')"
                        class="mt-4 bg-[#747D52] hover:bg-[#636C44] text-white px-7 py-3 rounded-xl font-extrabold text-xs shadow-md shadow-[#747D52]/20 active:scale-95 transition">
                        {{ __('Back to Menu') }}
                    </button>
                </div>
            </div>

            <!-- Sticky Bottom Review Bar -->
            <footer
                class="fixed lg:sticky bottom-0 left-0 right-0 lg:left-auto lg:right-auto w-full p-4 pb-safe bg-white/95 backdrop-blur-md border-t border-stone-200 z-40 shadow-lg shrink-0"
                x-show="view !== 'success' && view !== 'profile-menu' && view !== 'email-signin' && view !== 'my-orders' && view !== 'select-saved-address'">
                <template x-if="(view === 'categories-grid' || view === 'category-products') && cart.length > 0">
                    <button @click="navigate('/cart')"
                        class="w-full bg-[#747D52] hover:bg-[#636C44] text-white font-extrabold py-3.5 px-4 rounded-xl flex items-center justify-between text-xs transition shadow-md shadow-[#747D52]/25 active:scale-[0.98]">
                        <span class="bg-black/20 px-2.5 py-0.5 rounded-lg font-bold" x-text="cartCount"></span>
                        <span class="tracking-wide">{{ __('Review Order') }}</span>
                        <span class="font-black"
                            x-text="`${calculateSubtotal().toFixed(3)} {{ __('KD') }}`"></span>
                    </button>
                </template>

                <template x-if="view === 'customizer'">
                    <button @click="commitAddonToCart()" :disabled="!canAddToCart()"
                        :class="canAddToCart() ?
                            'bg-[#747D52] hover:bg-[#636C44] text-white active:scale-[0.98] shadow-md shadow-[#747D52]/25' :
                            'bg-stone-200 text-stone-400 cursor-not-allowed'"
                        class="w-full py-3.5 px-4 rounded-xl font-extrabold text-xs flex items-center justify-between transition">
                        <span
                            x-text="canAddToCart() ? '{{ __('Add to Cart') }}' : '{{ __('Select Required Options') }}'"></span>
                        <span class="font-black"
                            x-text="`${(customizerPrice * itemQuantity).toFixed(3)} {{ __('KD') }}`"></span>
                    </button>
                </template>

                <template x-if="view === 'cart' && cart.length > 0">
                    <button @click="navigate(isAuthenticated ? '/checkout/address' : '/checkout/details')"
                        class="w-full bg-[#747D52] hover:bg-[#636C44] text-white font-extrabold py-3.5 rounded-xl text-xs transition active:scale-[0.98] shadow-md shadow-[#747D52]/25">
                        {{ __('Go to checkout') }} (<span
                            x-text="`${calculateSubtotal().toFixed(3)} {{ __('KD') }}`"></span>)
                    </button>
                </template>

                <template x-if="view === 'address'">
                    <button @click="validateAndProceedAddress()"
                        class="w-full bg-[#747D52] hover:bg-[#636C44] text-white font-extrabold py-3.5 rounded-xl text-xs transition active:scale-[0.98] shadow-md shadow-[#747D52]/25">
                        {{ __('Save & Proceed') }}
                    </button>
                </template>

                <template x-if="view === 'checkout'">
                    <button @click="placeOrderNow()"
                        class="w-full bg-[#747D52] hover:bg-[#636C44] text-white font-extrabold py-3.5 rounded-xl text-xs transition active:scale-[0.98] shadow-md shadow-[#747D52]/25">
                        {{ __('Place Order') }} (<span
                            x-text="`${calculateGrandTotal().toFixed(3)} {{ __('KD') }}`"></span>)
                    </button>
                </template>
            </footer>
        </main>

        <!-- ================= RIGHT STATIC BRAND BANNER (50%) ================= -->
        <aside
            class="hidden lg:flex lg:w-1/2 h-full relative overflow-hidden bg-[#24261F] select-none min-w-0 flex-col justify-between p-8 xl:p-10 group">

            <!-- Core Photographic Asset -->
            <div class="absolute inset-0 z-0 overflow-hidden">
                <img src="{{ asset('images/otherwise-banner-new.jpeg') }}"
                    alt="otherwise - Choose well. Drink well."
                    class="w-full h-full object-cover object-[50%_40%] group-hover:scale-105 transition-transform duration-700 ease-out brightness-[0.98]">
            </div>

            <!-- Gradients -->
            <div
                class="pointer-events-none absolute inset-x-0 top-0 h-32 bg-gradient-to-b from-black/50 to-transparent z-10">
            </div>
            <div
                class="pointer-events-none absolute inset-x-0 bottom-0 h-44 bg-gradient-to-t from-black/60 via-black/20 to-transparent z-10">
            </div>

            <!-- Top Row -->
            <div class="relative z-20 flex items-center justify-between w-full">
                <div
                    class="inline-flex items-center space-x-2.5 rtl:space-x-reverse bg-black/40 backdrop-blur-xl border border-white/20 px-4 py-2 rounded-2xl shadow-xl">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-[11px] font-black uppercase tracking-widest text-white">
                        {{ $settings['store_status'] ?? __('Open in Kuwait') }}
                    </span>
                </div>

                <div
                    class="inline-flex items-start space-x-2 rtl:space-x-reverse bg-black/40 backdrop-blur-xl border border-white/15 px-3.5 py-2.5 rounded-2xl text-stone-200 text-[11px] font-bold shadow-lg">
                    <i class="fa-regular fa-clock text-[#B5BF8A] mt-0.5"></i>
                    <div class="space-y-0.5">
                        @php
                            $hours = $settings['operating_hours'] ?? null;
                            if (is_string($hours)) {
                                $hours = json_decode($hours, true);
                            }
                        @endphp

                        @if (is_array($hours) && count($hours) > 0)
                            @foreach ($hours as $schedule)
                                <div>
                                    <span class="text-stone-300">{{ $schedule['days'] }}:</span>
                                    @if (!empty($schedule['closed']))
                                        <span class="text-rose-400 font-extrabold">{{ __('Closed') }}</span>
                                    @else
                                        <span class="text-white">
                                            {{ !empty($schedule['open']) ? date('g:i A', strtotime($schedule['open'])) : '' }}
                                            –
                                            {{ !empty($schedule['close']) ? date('g:i A', strtotime($schedule['close'])) : '' }}
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <span>{{ __('7:00 AM – 11:30 PM') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="relative z-20 my-auto pointer-events-none"></div>

            <!-- Bottom Dock -->
            <div
                class="relative z-20 flex items-center justify-between bg-white/90 backdrop-blur-2xl border border-white/50 px-5 py-3.5 rounded-2xl shadow-2xl w-full">
                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <div
                        class="w-8 h-8 rounded-xl bg-[#747D52] text-white flex items-center justify-center text-xs font-black shadow-sm">
                        <i class="fa-solid" :class="method === 'delivery' ? 'fa-bicycle' : 'fa-store'"></i>
                    </div>
                    <div class="text-left rtl:text-right">
                        <span class="block text-xs font-black text-stone-900 leading-tight"
                            x-text="method === 'delivery' ? `{{ __('Delivery:') }} ${deliveryFee.toFixed(3)} {{ __('KD') }}` : '{{ __('Store Pickup (Free)') }}'"></span>
                        <span
                            class="block text-[10px] font-bold text-stone-500 leading-none mt-0.5">{{ __('Freshly prepared & dispatched') }}</span>
                    </div>
                </div>

                <a href="https://www.instagram.com/{{ ltrim($settings['instagram_handle'] ?? 'otherwisekw', '@') }}/"
                    target="_blank"
                    class="group/ig inline-flex items-center space-x-2 text-stone-900 hover:text-[#747D52] transition-colors font-black text-xs">
                    <div
                        class="w-7 h-7 rounded-xl bg-gradient-to-tr from-amber-500 via-rose-500 to-purple-600 text-white flex items-center justify-center text-xs shadow-sm group-hover/ig:rotate-12 transition-transform">
                        <i class="fa-brands fa-instagram"></i>
                    </div>
                    <span class="hidden sm:inline" x-text="settings.instagram_handle || '@otherwisekw'"></span>
                </a>
            </div>

        </aside>
    </div>

    <!-- ================= PRETTY GLOBAL MODAL DIALOG ================= -->
    <div x-show="modal.open" x-cloak class="relative z-50">
        <div class="fixed inset-0 bg-stone-900/50 backdrop-blur-xs transition-opacity" x-show="modal.open"
            x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-stone-200/80 text-center space-y-4"
                x-show="modal.open" x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95">

                <div class="w-12 h-12 rounded-2xl flex items-center justify-center mx-auto text-lg shadow-sm"
                    :class="modal.type === 'confirm' ? 'bg-amber-50 text-amber-600 border border-amber-100' :
                        'bg-[#747D52]/15 text-[#747D52] border border-[#747D52]/20'">
                    <i class="fa-solid"
                        :class="modal.type === 'confirm' ? 'fa-triangle-exclamation' : 'fa-bell'"></i>
                </div>

                <div class="space-y-1">
                    <h3 class="font-extrabold text-stone-900 text-sm" x-text="modal.title"></h3>
                    <p class="text-xs text-stone-500 leading-relaxed" x-text="modal.message"></p>
                </div>

                <div class="flex items-center space-x-2 rtl:space-x-reverse pt-2">
                    <template x-if="modal.type === 'confirm'">
                        <button type="button" @click="modal.open = false"
                            class="flex-1 py-3 px-4 rounded-xl border border-stone-200 text-stone-600 text-xs font-bold hover:bg-stone-50 transition">
                            {{ __('Cancel') }}
                        </button>
                    </template>
                    <button type="button" @click="modal.open = false; if (modal.callback) modal.callback();"
                        class="w-full flex-1 py-3 px-4 rounded-xl bg-[#747D52] hover:bg-[#636C44] text-white text-xs font-bold transition shadow-md shadow-[#747D52]/20 active:scale-95">
                        <span
                            x-text="modal.type === 'confirm' ? '{{ __('Confirm') }}' : '{{ __('OK') }}'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js Store Engine -->
    <script>
        function storeApp() {
            return {
                view: 'categories-grid',
                method: 'delivery',

                settings: @json($settings ?? []),

                selectedDeliveryArea: {
                    id: {{ isset($defaultArea) && $defaultArea ? $defaultArea->id : $governoratesList[0]['areas'][0]['id'] ?? 'null' }},
                    name: '{{ isset($defaultArea) && $defaultArea ? (app()->getLocale() === 'ar' ? $defaultArea->name_ar : $defaultArea->name_en) : (app()->getLocale() === 'ar' ? 'أبو حليفة' : 'Abu Halifa') }}',
                    fee: parseFloat('{{ $settings['default_delivery_fee'] ?? 0.95 }}')
                },
                selectedPickupStore: null,
                deliveryFee: parseFloat('{{ $settings['default_delivery_fee'] ?? 0.95 }}'),

                // Search & Filter State
                searchOpen: false,
                productSearch: '',
                showFilterModal: false,
                selectedSort: 'default',
                selectedCategoryFilter: null,
                maxPriceFilter: 25.000,
                guestVerificationLoading: false,

                // Remarks & Address Management
                itemRemarks: '',
                orderSpecialRemarks: '',
                pastedMapUrl: '',
                selectedAddressId: null,
                saveAddressToBook: true,
                setAsDefaultAddress: false,
                savedAddresses: [],
                addressLoading: false,
                addressBackRoute: '/checkout/details',

                // Pretty Modal State
                modal: {
                    open: false,
                    title: '{{ __('Notice') }}',
                    message: '',
                    type: 'alert',
                    callback: null
                },

                showAlert(message, title = '{{ __('Notice') }}') {
                    this.modal.title = title;
                    this.modal.message = message;
                    this.modal.type = 'alert';
                    this.modal.callback = null;
                    this.modal.open = true;
                },

                showConfirm(message, callback, title = '{{ __('Confirmation') }}') {
                    this.modal.title = title;
                    this.modal.message = message;
                    this.modal.type = 'confirm';
                    this.modal.callback = callback;
                    this.modal.open = true;
                },

                getCsrfToken() {
                    const meta = document.querySelector('meta[name="csrf-token"]');
                    return meta ? meta.getAttribute('content') : '{{ csrf_token() }}';
                },

                updateCsrfToken(newToken) {
                    if (!newToken) return;
                    const meta = document.querySelector('meta[name="csrf-token"]');
                    if (meta) {
                        meta.setAttribute('content', newToken);
                    }
                },

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

                get displayedCategories() {
                    let list = [...this.categoriesList];
                    const query = this.productSearch.toLowerCase().trim();
                    const max = Number(this.maxPriceFilter) || 25.000;

                    if (max < 25.000) {
                        list = list.filter(cat => {
                            if (!cat.products || cat.products.length === 0) return false;
                            return cat.products.some(p => {
                                const price = parseFloat(p.base_price);
                                return !isNaN(price) && price <= max;
                            });
                        });
                    }

                    if (query) {
                        list = list.filter(cat => {
                            const matchCat = cat.name.toLowerCase().includes(query);
                            const matchProd = (cat.products || []).some(p => p.name.toLowerCase().includes(
                                query));
                            return matchCat || matchProd;
                        });
                    }

                    if (this.selectedSort === 'name_asc') {
                        list.sort((a, b) => a.name.localeCompare(b.name));
                    } else if (this.selectedSort === 'name_desc') {
                        list.sort((a, b) => b.name.localeCompare(a.name));
                    }

                    return list;
                },

                get displayedProducts() {
                    if (!this.activeCategory || !this.activeCategory.products) return [];
                    let list = [...this.activeCategory.products];
                    const query = this.productSearch.toLowerCase().trim();
                    const max = Number(this.maxPriceFilter) || 25.000;

                    if (max < 25.000) {
                        list = list.filter(p => {
                            const price = parseFloat(p.base_price);
                            return !isNaN(price) && price <= max;
                        });
                    }

                    if (query) {
                        list = list.filter(p =>
                            p.name.toLowerCase().includes(query) ||
                            (p.description && p.description.toLowerCase().includes(query))
                        );
                    }

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
                                id: this.storesList[0].id,
                                name: this.storesList[0].name,
                                description: this.storesList[0].description
                            };
                        }
                    } else {
                        this.deliveryFee = this.selectedDeliveryArea ? this.selectedDeliveryArea.fee : parseFloat(
                            '{{ $settings['default_delivery_fee'] ?? 0.95 }}');
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
                authSuccess: '',
                forgotEmail: '',
                authTab: 'login',
                guestExpanded: false,
                authLoading: false,

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
                resetForm: {
                    token: '',
                    email: '',
                    password: '',
                    password_confirmation: ''
                },
                customer: JSON.parse(localStorage.getItem('otherwise_customer') || JSON.stringify({
                    name: '{{ $currentUser['name'] ?? '' }}',
                    email: '{{ $currentUser['email'] ?? '' }}',
                    phone: '{{ $currentUser['phone'] ?? '' }}'
                })),
                saveCustomer() {
                    localStorage.setItem('otherwise_customer', JSON.stringify(this.customer));
                },

                address: {
                    type: 'Home',
                    block: '',
                    street: '',
                    avenue: '',
                    building: '',
                    floor: '',
                    flat_no: '',
                    additional_directions: '',
                    paci: '',
                    latitude: 29.3759,
                    longitude: 47.9774
                },
                leafletMap: null,
                leafletMarker: null,
                paymentMethod: 'knet',
                placedOrderNo: '',

                initRouter() {
                    const urlParams = new URLSearchParams(window.location.search);

                    if (urlParams.get('payment') === 'success') {
                        const orderNo = urlParams.get('order') || '';
                        this.placedOrderNo = orderNo;
                        this.cart = [];
                        localStorage.removeItem('otherwise_cart');
                        this.view = 'success';
                        this.setPageTitle('{{ __('Order Confirmed!') }}');
                        window.history.replaceState({}, document.title, window.location.pathname);
                        return;
                    }

                    if (urlParams.get('payment') === 'failed') {
                        const errorMsg = urlParams.get('msg') || '{{ __('Payment was cancelled or failed.') }}';
                        this.showAlert(errorMsg, '{{ __('Payment Failed') }}');
                        this.view = 'checkout';
                        window.history.replaceState({}, document.title, window.location.pathname);
                        return;
                    }

                    if (urlParams.get('verified') === '1') {
                        this.isAuthenticated = true;
                        this.view = 'address';
                        this.showAlert('{{ __('Email verified successfully! Please enter your delivery address.') }}',
                            '{{ __('Verified') }}');
                        window.history.replaceState({}, document.title, window.location.pathname);
                    }

                    if (urlParams.get('token') && urlParams.get('email')) {
                        this.view = 'email-signin';
                        this.authTab = 'reset-password';
                        this.resetForm.token = urlParams.get('token');
                        this.resetForm.email = decodeURIComponent(urlParams.get('email'));
                    }

                    if (this.storesList && this.storesList.length > 0 && !this.selectedPickupStore) {
                        this.selectedPickupStore = {
                            id: this.storesList[0].id,
                            name: this.storesList[0].name,
                            description: this.storesList[0].description
                        };
                    }

                    // Fetch saved addresses from DB
                    this.fetchAddressesFromDb();

                    window.addEventListener('popstate', () => {
                        this.handlePath(window.location.pathname);
                    });
                    this.handlePath(window.location.pathname);
                },

                navigate(path, title = null) {
                    if (this.view === 'success') {
                        this.view = 'categories-grid';
                    }
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
                            this.addressBackRoute = this.isAuthenticated ? '/cart' : '/checkout/details';
                            if (this.savedAddresses.length > 0 && !this.selectedAddressId) {
                                this.view = 'select-saved-address';
                                this.setPageTitle('{{ __('Select Delivery Address') }}');
                            } else {
                                this.view = 'address';
                                this.setPageTitle('{{ __('Delivery Address Details') }}');
                                this.initAddressMap();
                            }
                        }
                    } else if (parts[0] === 'profile') {
                        if (parts[1] === 'orders') {
                            this.view = 'my-orders';
                            this.setPageTitle('{{ __('My Orders') }}');
                            this.fetchCustomerOrders();
                        } else if (parts[1] === 'email-signin') {
                            this.view = 'email-signin';
                            this.setPageTitle('{{ __('Login') }}');
                        } else if (parts[1] === 'reset-password') {
                            this.view = 'email-signin';
                            this.authTab = 'reset-password';
                            this.setPageTitle('{{ __('Reset Password') }}');
                            const urlParams = new URLSearchParams(window.location.search);
                            if (urlParams.get('token')) this.resetForm.token = urlParams.get('token');
                            if (urlParams.get('email')) this.resetForm.email = decodeURIComponent(urlParams.get('email'));
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
                    this.itemRemarks = '';
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

                setDeliveryArea(id, name, fee) {
                    this.selectedDeliveryArea = {
                        id: id,
                        name: name,
                        fee: parseFloat(fee || 0.950)
                    };
                    this.deliveryFee = parseFloat(fee || 0.950);
                    this.navigate('/');
                },

                setStorePickup(store) {
                    this.selectedPickupStore = {
                        id: store.id,
                        name: store.name,
                        description: store.description || ''
                    };
                    this.deliveryFee = 0.000;
                    this.navigate('/');
                },

                proceedGuestToAddress() {
                    if (!this.customer.name || !this.customer.email || !this.customer.phone) {
                        this.showAlert('{{ __('Please fill all required fields') }}');
                        return;
                    }
                    this.saveCustomer();
                    this.addressBackRoute = '/checkout/details';
                    this.view = 'address';
                    this.initAddressMap();
                },

                // ------------------ DB & LOCAL ADDRESS MANAGEMENT ------------------
                fetchAddressesFromDb() {
                    this.addressLoading = true;
                    const email = this.currentUser?.email || this.customer?.email || '';
                    const phone = this.currentUser?.phone || this.customer?.phone || '';

                    const params = new URLSearchParams();
                    if (email) params.append('email', email);
                    if (phone) params.append('phone', phone);

                    fetch(`/customer/addresses?${params.toString()}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.getCsrfToken()
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.addresses && data.addresses.length > 0) {
                                this.savedAddresses = data.addresses;
                                const defaultAddr = this.savedAddresses.find(a => a.is_default) || this.savedAddresses[
                                    0];
                                if (defaultAddr && !this.selectedAddressId) {
                                    this.selectSavedAddress(defaultAddr, false);
                                }
                            } else {
                                this.savedAddresses = JSON.parse(localStorage.getItem('otherwise_saved_addresses') ||
                                    '[]');
                            }
                        })
                        .catch(() => {
                            this.savedAddresses = JSON.parse(localStorage.getItem('otherwise_saved_addresses') || '[]');
                        })
                        .finally(() => {
                            this.addressLoading = false;
                        });
                },

                manageAddressesFromProfile() {
                    this.addressBackRoute = '/profile';
                    this.view = 'select-saved-address';
                    this.setPageTitle('{{ __('Delivery Addresses') }}');
                },

                createNewAddress() {
                    this.resetAddressForm();
                    this.view = 'address';
                    this.setPageTitle('{{ __('Add Delivery Address') }}');
                    this.initAddressMap();
                },

                editSavedAddress(addr) {
                    this.selectedAddressId = addr.id;
                    this.address = {
                        type: addr.type || 'Home',
                        block: addr.block || '',
                        street: addr.street || '',
                        avenue: addr.avenue || '',
                        building: addr.building || '',
                        floor: addr.floor || '',
                        flat_no: addr.flat_no || '',
                        additional_directions: addr.additional_directions || '',
                        paci: addr.paci || '',
                        latitude: parseFloat(addr.latitude || 29.3759),
                        longitude: parseFloat(addr.longitude || 47.9774)
                    };
                    this.setAsDefaultAddress = !!addr.is_default;
                    this.view = 'address';
                    this.setPageTitle('{{ __('Edit Address') }}');
                    this.initAddressMap();
                    this.updateMapPosition(this.address.latitude, this.address.longitude);
                },

                resetAddressForm() {
                    this.selectedAddressId = null;
                    this.address = {
                        type: 'Home',
                        block: '',
                        street: '',
                        avenue: '',
                        building: '',
                        floor: '',
                        flat_no: '',
                        additional_directions: '',
                        paci: '',
                        latitude: 29.3759,
                        longitude: 47.9774
                    };
                    this.pastedMapUrl = '';
                    this.setAsDefaultAddress = false;
                    this.updateMapPosition(29.3759, 47.9774);
                },

                selectSavedAddress(saved, notify = true) {
                    this.selectedAddressId = saved.id;
                    this.address = {
                        type: saved.type || 'Home',
                        block: saved.block || '',
                        street: saved.street || '',
                        avenue: saved.avenue || '',
                        building: saved.building || '',
                        floor: saved.floor || '',
                        flat_no: saved.flat_no || '',
                        additional_directions: saved.additional_directions || '',
                        paci: saved.paci || '',
                        latitude: parseFloat(saved.latitude || 29.3759),
                        longitude: parseFloat(saved.longitude || 47.9774)
                    };
                    this.setAsDefaultAddress = !!saved.is_default;
                    this.updateMapPosition(this.address.latitude, this.address.longitude);
                    if (notify) {
                        this.showAlert(
                            `{{ __('Selected address:') }} ${saved.type} (Block ${saved.block}, Street ${saved.street})`
                            );
                    }
                },

                applyAddressAndProceed(addr) {
                    this.selectSavedAddress(addr, false);
                    this.view = 'checkout';
                    this.setPageTitle('{{ __('Payment Option') }}');
                },

                saveCurrentAddressToStorage() {
                    const email = this.currentUser?.email || this.customer?.email || '';
                    const phone = this.currentUser?.phone || this.customer?.phone || '';
                    const name = this.currentUser?.name || this.customer?.name || '';

                    const payload = {
                        id: this.selectedAddressId,
                        name: name,
                        email: email,
                        phone: phone,
                        type: this.address.type || 'Home',
                        block: this.address.block,
                        street: this.address.street,
                        avenue: this.address.avenue,
                        building: this.address.building,
                        floor: this.address.floor,
                        flat_no: this.address.flat_no,
                        additional_directions: this.address.additional_directions,
                        paci: this.address.paci,
                        latitude: this.address.latitude,
                        longitude: this.address.longitude,
                        is_default: this.setAsDefaultAddress
                    };

                    fetch('/customer/addresses/save', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.getCsrfToken(),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.fetchAddressesFromDb();
                            }
                        })
                        .catch(() => {
                            const itemToSave = {
                                ...payload,
                                id: this.selectedAddressId || Date.now()
                            };
                            const idx = this.savedAddresses.findIndex(a => a.id === itemToSave.id);
                            if (idx > -1) this.savedAddresses[idx] = itemToSave;
                            else this.savedAddresses.unshift(itemToSave);
                            localStorage.setItem('otherwise_saved_addresses', JSON.stringify(this.savedAddresses));
                        });
                },

                markAsDefaultAddress(id) {
                    const email = this.currentUser?.email || this.customer?.email || '';

                    fetch(`/customer/addresses/${id}/default`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.getCsrfToken(),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                email: email
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.showAlert(data.message);
                                this.fetchAddressesFromDb();
                            }
                        })
                        .catch(() => {
                            this.savedAddresses.forEach(a => a.is_default = (a.id === id));
                            localStorage.setItem('otherwise_saved_addresses', JSON.stringify(this.savedAddresses));
                            this.showAlert('{{ __('Default address updated.') }}');
                        });
                },

                deleteSavedAddress(id) {
                    this.showConfirm('{{ __('Delete this saved address?') }}', () => {
                        const email = this.currentUser?.email || this.customer?.email || '';

                        fetch(`/customer/addresses/${id}?email=${encodeURIComponent(email)}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': this.getCsrfToken(),
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    if (this.selectedAddressId === id) this.resetAddressForm();
                                    this.fetchAddressesFromDb();
                                }
                            })
                            .catch(() => {
                                this.savedAddresses = this.savedAddresses.filter(a => a.id !== id);
                                localStorage.setItem('otherwise_saved_addresses', JSON.stringify(this
                                    .savedAddresses));
                                if (this.selectedAddressId === id) this.resetAddressForm();
                            });
                    });
                },

                applyPastedLocation() {
                    if (!this.pastedMapUrl.trim()) return;

                    let lat = null;
                    let lng = null;

                    const coordsMatch = this.pastedMapUrl.match(/(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)/);
                    if (coordsMatch) {
                        lat = parseFloat(coordsMatch[1]);
                        lng = parseFloat(coordsMatch[2]);
                    } else {
                        const urlMatch = this.pastedMapUrl.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/) ||
                            this.pastedMapUrl.match(/q=(-?\d+\.\d+),(-?\d+\.\d+)/) ||
                            this.pastedMapUrl.match(/ll=(-?\d+\.\d+),(-?\d+\.\d+)/);
                        if (urlMatch) {
                            lat = parseFloat(urlMatch[1]);
                            lng = parseFloat(urlMatch[2]);
                        }
                    }

                    if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
                        this.address.latitude = lat;
                        this.address.longitude = lng;
                        this.updateMapPosition(lat, lng);
                        this.showAlert(`{{ __('Coordinates set:') }} ${lat.toFixed(4)}, ${lng.toFixed(4)}`);
                    } else {
                        this.showAlert('{{ __('Could not detect coordinates from link. Please drag pin manually.') }}');
                    }
                },

                updateMapPosition(lat, lng) {
                    if (this.leafletMap && this.leafletMarker) {
                        this.leafletMarker.setLatLng([lat, lng]);
                        this.leafletMap.setView([lat, lng], 15);
                    }
                },

                validateAndProceedAddress() {
                    if (!this.address.block || !this.address.street || !this.address.building) {
                        this.showAlert(
                            '{{ __('Please fill out all mandatory address fields (Block, Street, Building/House)') }}'
                        );
                        return;
                    }

                    if (this.saveAddressToBook) {
                        this.saveCurrentAddressToStorage();
                    }

                    this.view = 'checkout';
                    this.setPageTitle('{{ __('Payment Option') }}');
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
                    this.showConfirm('{{ __('Are you sure you want to cancel this order?') }}', () => {
                        fetch(`/api/customer/orders/${orderId}/cancel`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': this.getCsrfToken(),
                                    'Accept': 'application/json'
                                }
                            })
                            .then(async res => {
                                const data = await res.json();
                                if (!res.ok) throw new Error(data.message || 'Could not cancel order');
                                return data;
                            })
                            .then(data => {
                                this.showAlert(data.message);
                                this.fetchCustomerOrders();
                            })
                            .catch(err => {
                                this.showAlert(err.message);
                            });
                    });
                },

                // ------------------ AUTH LOGIC ------------------
                submitSignIn() {
                    this.authError = '';
                    this.authSuccess = '';

                    if (!this.authForm.email || !this.authForm.password) {
                        this.authError = '{{ __('Please fill all required fields') }}';
                        return;
                    }
                    this.authLoading = true;

                    fetch('{{ route('customer.login') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.getCsrfToken(),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.authForm)
                        })
                        .then(async res => {
                            const data = await res.json().catch(() => ({}));
                            if (!res.ok) {
                                if (res.status === 419) {
                                    throw new Error('{{ __('Session expired. Please try logging in again.') }}');
                                }
                                throw new Error(data.message || 'Login failed');
                            }
                            return data;
                        })
                        .then(data => {
                            if (data.success) {
                                if (data.csrf_token) {
                                    this.updateCsrfToken(data.csrf_token);
                                }
                                this.currentUser = data.user;
                                this.customer.name = data.user.name;
                                this.customer.email = data.user.email;
                                this.customer.phone = data.user.phone;
                                this.isAuthenticated = true;
                                this.authForm = {
                                    email: '',
                                    password: ''
                                };
                                this.fetchAddressesFromDb();
                                this.navigate('/profile', '{{ __('Profile') }}');
                            }
                        })
                        .catch(err => {
                            this.authError = err.message;
                        })
                        .finally(() => {
                            this.authLoading = false;
                        });
                },

                submitSignUp() {
                    this.authError = '';
                    this.authSuccess = '';

                    if (!this.regForm.name || !this.regForm.email || !this.regForm.phone || !this.regForm.password) {
                        this.authError = '{{ __('Please fill all required fields') }}';
                        return;
                    }
                    this.authLoading = true;

                    fetch('{{ route('customer.register') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.getCsrfToken(),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.regForm)
                        })
                        .then(async res => {
                            const data = await res.json().catch(() => ({}));
                            if (!res.ok) {
                                if (res.status === 419) {
                                    throw new Error('{{ __('Session expired. Please try again.') }}');
                                }
                                if (data.errors) {
                                    const firstError = Object.values(data.errors)[0][0];
                                    throw new Error(firstError);
                                }
                                throw new Error(data.message || 'Registration failed');
                            }
                            return data;
                        })
                        .then(data => {
                            if (data.csrf_token) {
                                this.updateCsrfToken(data.csrf_token);
                            }
                            if (data.requires_verify) {
                                this.regForm = {
                                    name: '',
                                    email: '',
                                    phone: '',
                                    password: ''
                                };
                                this.authTab = 'login';
                                this.authSuccess = data.message;
                            }
                        })
                        .catch(err => {
                            this.authError = err.message;
                        })
                        .finally(() => {
                            this.authLoading = false;
                        });
                },

                submitForgotPassword() {
                    this.authError = '';
                    this.authSuccess = '';

                    if (!this.forgotEmail) {
                        this.authError = '{{ __('Please enter your email address') }}';
                        return;
                    }

                    this.authLoading = true;

                    fetch('{{ route('customer.password.email') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.getCsrfToken(),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                email: this.forgotEmail
                            })
                        })
                        .then(async res => {
                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || 'Unable to send reset link');
                            return data;
                        })
                        .then(data => {
                            this.authSuccess = data.message;
                            this.forgotEmail = '';
                        })
                        .catch(err => {
                            this.authError = err.message;
                        })
                        .finally(() => {
                            this.authLoading = false;
                        });
                },

                submitPasswordReset() {
                    this.authError = '';
                    this.authSuccess = '';

                    if (!this.resetForm.password || !this.resetForm.password_confirmation) {
                        this.authError = '{{ __('Please fill all required fields') }}';
                        return;
                    }

                    if (this.resetForm.password !== this.resetForm.password_confirmation) {
                        this.authError = '{{ __('Passwords do not match') }}';
                        return;
                    }

                    this.authLoading = true;

                    fetch('{{ route('customer.password.update') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.resetForm)
                        })
                        .then(async res => {
                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || 'Failed to reset password');
                            return data;
                        })
                        .then(data => {
                            this.authSuccess = data.message;
                            this.authTab = 'login';
                            this.resetForm = {
                                token: '',
                                email: '',
                                password: '',
                                password_confirmation: ''
                            };
                            window.history.replaceState({}, document.title, '/profile/email-signin');
                        })
                        .catch(err => {
                            this.authError = err.message;
                        })
                        .finally(() => {
                            this.authLoading = false;
                        });
                },

                signOut() {
                    fetch('{{ route('customer.logout') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.getCsrfToken(),
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json().catch(() => ({})))
                        .then(data => {
                            if (data.csrf_token) {
                                this.updateCsrfToken(data.csrf_token);
                            }
                            this.isAuthenticated = false;
                            this.currentUser = {
                                name: '',
                                email: '',
                                phone: ''
                            };
                            this.savedAddresses = [];
                            this.navigate('/', '{{ __('otherwise') }}');
                        })
                        .catch(() => {
                            this.isAuthenticated = false;
                            this.navigate('/', '{{ __('otherwise') }}');
                        });
                },

                deleteAccount() {
                    this.showConfirm('{{ __('Are you sure you want to delete your account?') }}', () => {
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
                    });
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
                        addons: flattenedAddons,
                        notes: this.itemRemarks ? this.itemRemarks.trim() : null
                    });

                    this.saveCart();
                    this.navigate('/category/' + (this.activeCategory?.slug || ''), this.activeCategory?.name);
                },

                increaseQty(idx) {
                    this.cart[idx].quantity++;
                    this.cart[idx].total_price = this.cart[idx].unit_price * this.cart[idx].quantity;
                    this.saveCart();
                },

                decreaseQty(idx) {
                    if (this.cart[idx].quantity > 1) {
                        this.cart[idx].quantity--;
                        this.cart[idx].total_price = this.cart[idx].unit_price * this.cart[idx].quantity;
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
                    const minOrderAmount = parseFloat('{{ $settings['min_order'] ?? 3.75 }}');
                    const subtotal = this.calculateSubtotal();
                    const grandTotal = this.calculateGrandTotal();

                    if (grandTotal < minOrderAmount) {
                        this.showAlert(
                            `{{ __('Minimum order amount is') }} ${minOrderAmount.toFixed(3)} {{ __('KD') }}`);
                        return;
                    }

                    let targetStoreId = null;
                    let targetAreaId = null;

                    if (this.method === 'pickup') {
                        targetStoreId = this.selectedPickupStore?.id || (this.storesList.length > 0 ? this.storesList[0]
                            .id : null);
                    } else {
                        targetAreaId = this.selectedDeliveryArea?.id || null;
                    }

                    const chosenLocation = this.method === 'delivery' ?
                        (this.selectedDeliveryArea ? this.selectedDeliveryArea.name : '') :
                        (this.selectedPickupStore ? this.selectedPickupStore.name : '');

                    const payload = {
                        order_type: this.method,
                        store_id: targetStoreId,
                        area_id: targetAreaId,
                        customer_name: this.customer.name,
                        customer_email: this.customer.email,
                        customer_phone: this.customer.phone,
                        area_name: chosenLocation,
                        address_type: this.address.type,
                        block: this.address.block,
                        street: this.address.street,
                        avenue: this.address.avenue,
                        building: this.address.building,
                        floor: this.address.floor,
                        flat_no: this.address.flat_no,
                        additional_directions: this.address.additional_directions,
                        paci: this.address.paci,
                        latitude: this.address.latitude,
                        longitude: this.address.longitude,
                        subtotal: subtotal,
                        delivery_fee: this.method === 'delivery' ? this.deliveryFee : 0,
                        total: grandTotal,
                        payment_method: this.paymentMethod,
                        special_remarks: this.orderSpecialRemarks || null,
                        save_address_to_db: this.saveAddressToBook,
                        set_default_address: this.setAsDefaultAddress,
                        items: this.cart
                    };

                    fetch('/api/orders/place', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.getCsrfToken(),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        })
                        .then(async res => {
                            let data = {};
                            try {
                                data = await res.json();
                            } catch (e) {}

                            if (!res.ok) throw new Error(data.message || 'Server error');
                            return data;
                        })
                        .then(res => {
                            if (res.success) {
                                if (res.redirect_url) {
                                    window.location.href = res.redirect_url;
                                    return;
                                }

                                this.placedOrderNo = res.order_number;
                                this.cart = [];
                                localStorage.removeItem('otherwise_cart');
                                this.view = 'success';
                                this.setPageTitle('{{ __('Order Confirmed!') }}');
                                window.history.pushState({}, '', '/');
                            }
                        })
                        .catch(err => {
                            this.showAlert(err.message);
                        });
                },

                initAddressMap() {
                    // Fetch addresses if empty
                    if (this.savedAddresses.length === 0) {
                        this.fetchAddressesFromDb();
                    }

                    this.$nextTick(() => {
                        const container = document.getElementById('addressPickerMap');
                        if (!container) return;

                        const defaultLat = this.address.latitude || 29.3759;
                        const defaultLng = this.address.longitude || 47.9774;

                        if (!this.leafletMap) {
                            this.leafletMap = L.map('addressPickerMap').setView([defaultLat, defaultLng], 13);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; OpenStreetMap contributors'
                            }).addTo(this.leafletMap);

                            this.leafletMarker = L.marker([defaultLat, defaultLng], {
                                draggable: true
                            }).addTo(this.leafletMap);

                            this.leafletMarker.on('dragend', (e) => {
                                const pos = e.target.getLatLng();
                                this.address.latitude = pos.lat;
                                this.address.longitude = pos.lng;
                            });

                            this.leafletMap.on('click', (e) => {
                                this.leafletMarker.setLatLng(e.latlng);
                                this.address.latitude = e.latlng.lat;
                                this.address.longitude = e.latlng.lng;
                            });
                        } else {
                            this.leafletMap.invalidateSize();
                            this.leafletMarker.setLatLng([defaultLat, defaultLng]);
                            this.leafletMap.setView([defaultLat, defaultLng], 13);
                        }
                    });
                },

                detectCurrentLocation() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (pos) => {
                                const lat = pos.coords.latitude;
                                const lng = pos.coords.longitude;
                                this.address.latitude = lat;
                                this.address.longitude = lng;
                                this.updateMapPosition(lat, lng);
                            },
                            () => {
                                this.showAlert('{{ __('Could not retrieve your current location.') }}');
                            }
                        );
                    }
                },
            };
        }
    </script>
</body>

</html>
