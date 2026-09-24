<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('Admin Panel')) - {{ __('MY Cookies') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Cairo for Arabic, Plus Jakarta Sans for English -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: {{ app()->getLocale() === 'ar' ? "'Cairo', sans-serif" : "'Plus Jakarta Sans', sans-serif" }};
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-stone-50 text-stone-800 antialiased h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    <!-- Mobile Backdrop Overlay -->
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
        x-transition:enter="transition-opacity ease-linear duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-stone-950/60 z-40 lg:hidden">
    </div>

    <div class="flex h-full w-full overflow-hidden">

        <!-- Sidebar (Drawer on Mobile / Static on Desktop) -->
        <aside
            :class="sidebarOpen ? 'translate-x-0' : ('{{ app()->getLocale() }}'
                === 'ar' ? 'translate-x-full lg:translate-x-0' : '-translate-x-full lg:translate-x-0')"
            class="fixed inset-y-0 {{ app()->getLocale() === 'ar' ? 'right-0' : 'left-0' }} z-50 w-72 lg:w-64 bg-stone-900 text-white flex flex-col justify-between p-5 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 shrink-0 shadow-2xl lg:shadow-none">

            <div class="space-y-6">
                <!-- Brand Logo & Mobile Close Button -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 rtl:space-x-reverse">
                        <div
                            class="bg-[#b5122b] text-white font-black px-3 py-1.5 rounded-xl text-sm shadow-sm tracking-wider">
                            MY
                        </div>
                        <div>
                            <h2 class="font-extrabold text-sm tracking-wide leading-tight">{{ __('MY Cookies') }}</h2>
                            <span
                                class="text-[10px] text-stone-400 font-semibold tracking-wider uppercase">{{ __('Admin Workspace') }}</span>
                        </div>
                    </div>

                    <!-- Close button for mobile -->
                    <button @click="sidebarOpen = false"
                        class="lg:hidden text-stone-400 hover:text-white p-1.5 rounded-lg hover:bg-stone-800 transition">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <!-- Navigation Links -->
                <nav class="space-y-1.5 text-xs font-semibold">
                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center space-x-3 rtl:space-x-reverse px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#b5122b] text-white shadow-sm' : 'text-stone-400 hover:bg-stone-800 hover:text-stone-200' }}">
                        <i class="fa-solid fa-chart-pie w-4 text-center"></i>
                        <span>{{ __('Dashboard') }}</span>
                    </a>

                    <a href="{{ route('admin.orders.index') }}"
                        class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.orders.*') ? 'bg-[#b5122b] text-white shadow-sm' : 'text-stone-400 hover:bg-stone-800 hover:text-stone-200' }}">
                        <div class="flex items-center space-x-3 rtl:space-x-reverse">
                            <i class="fa-solid fa-receipt w-4 text-center"></i>
                            <span>{{ __('Orders') }}</span>
                        </div>
                        @if (isset($pendingOrdersCount) && $pendingOrdersCount > 0)
                            <span class="bg-stone-800 text-stone-200 text-[10px] px-2 py-0.5 rounded-full font-bold">
                                {{ $pendingOrdersCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('admin.products.index') }}"
                        class="flex items-center space-x-3 rtl:space-x-reverse px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.products.*') ? 'bg-[#b5122b] text-white shadow-sm' : 'text-stone-400 hover:bg-stone-800 hover:text-stone-200' }}">
                        <i class="fa-solid fa-cookie w-4 text-center"></i>
                        <span>{{ __('Products & Addons') }}</span>
                    </a>

                    <a href="{{ route('admin.categories.index') }}"
                        class="flex items-center space-x-3 rtl:space-x-reverse px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.categories.*') ? 'bg-[#b5122b] text-white shadow-sm' : 'text-stone-400 hover:bg-stone-800 hover:text-stone-200' }}">
                        <i class="fa-solid fa-layer-group w-4 text-center"></i>
                        <span>{{ __('Categories') }}</span>
                    </a>
                </nav>
            </div>

            <!-- Bottom Storefront & User Info -->
            <div class="pt-4 border-t border-stone-800 space-y-3">
                <a href="/" target="_blank"
                    class="flex items-center space-x-2 rtl:space-x-reverse text-xs text-stone-400 hover:text-white transition px-1">
                    <i class="fa-solid fa-arrow-up-right-from-square text-[11px] rtl:rotate-[-90deg]"></i>
                    <span>{{ __('Open Live Storefront') }}</span>
                </a>

                <div class="flex items-center justify-between px-1 text-xs">
                    <div class="truncate max-w-[170px]">
                        <div class="font-bold text-stone-200 truncate">{{ auth()->user()->name ?? __('Admin User') }}
                        </div>
                        <div class="text-[10px] text-stone-500 truncate">{{ auth()->user()->email ?? '' }}</div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Right Main Scrollable Viewport -->
        <div class="flex-1 flex flex-col h-full min-w-0 overflow-hidden">

            <!-- Responsive Top Header -->
            <header
                class="bg-white border-b border-stone-200/80 px-4 sm:px-6 lg:px-8 py-3.5 flex justify-between items-center sticky top-0 z-30 shrink-0">

                <!-- Left: Hamburger button for Mobile + Page Title -->
                <div class="flex items-center space-x-3 rtl:space-x-reverse min-w-0">
                    <button @click="sidebarOpen = true" type="button"
                        class="lg:hidden p-2 rounded-xl text-stone-600 hover:bg-stone-100 hover:text-stone-900 border border-stone-200 transition focus:outline-none">
                        <i class="fa-solid fa-bars text-sm"></i>
                    </button>

                    <h1 class="font-extrabold text-sm sm:text-base text-stone-900 tracking-tight truncate">
                        @yield('title')
                    </h1>
                </div>

                <!-- Right: Notification Icon, Location, Language & Logout -->
                <div class="flex items-center space-x-2.5 sm:space-x-4 rtl:space-x-reverse">
                    <!-- Branch Badge -->
                    <div
                        class="hidden sm:flex items-center space-x-1.5 rtl:space-x-reverse text-xs bg-stone-100 text-stone-600 px-3 py-1.5 rounded-full font-bold border border-stone-200/70">
                        <i class="fa-solid fa-location-dot text-[#b5122b] text-[11px]"></i>
                        <span>{{ __('Kuwait City') }}</span>
                    </div>

                    <!-- Notification Bell -->
                    <a href="{{ route('admin.orders.index') }}" title="{{ __('View Orders') }}"
                        class="relative w-9 h-9 rounded-xl border border-stone-200 flex items-center justify-center text-stone-600 hover:text-stone-900 hover:bg-stone-50 transition active:scale-95 shrink-0">
                        <i class="fa-solid fa-bell text-sm"></i>
                        @if (isset($pendingOrdersCount) && $pendingOrdersCount > 0)
                            <span
                                class="absolute -top-1 -right-1 rtl:right-auto rtl:-left-1 bg-[#b5122b] text-white text-[10px] font-black w-4 h-4 rounded-full flex items-center justify-center animate-pulse shadow-sm">
                                {{ $pendingOrdersCount > 9 ? '9+' : $pendingOrdersCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Language Switcher Button -->
                    <a href="{{ route('lang.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}"
                        class="inline-flex items-center space-x-1.5 rtl:space-x-reverse px-3 py-1.5 rounded-xl border border-stone-200 text-xs font-bold text-stone-700 hover:bg-stone-50 transition active:scale-95">
                        <i class="fa-solid fa-globe text-stone-400"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}</span>
                    </a>

                    <div class="h-4 sm:h-5 w-[1px] bg-stone-200"></div>

                    <!-- Header Logout -->
                    <form action="{{ route('admin.logout') }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit" title="{{ __('Sign Out') }}"
                            class="inline-flex items-center space-x-1.5 rtl:space-x-reverse px-2.5 sm:px-3 py-1.5 rounded-xl text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-100 font-bold text-xs transition active:scale-95">
                            <i class="fa-solid fa-arrow-right-from-bracket text-xs rtl:rotate-180"></i>
                            <span class="hidden sm:inline">{{ __('Logout') }}</span>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Dynamic Content Body -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                @if (session('success'))
                    <div
                        class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-bold flex items-center shadow-xs">
                        <i class="fa-solid fa-circle-check mr-2 rtl:mr-0 rtl:ml-2 text-emerald-600 text-sm"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div
                        class="mb-5 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl text-xs font-bold flex items-center shadow-xs">
                        <i class="fa-solid fa-circle-exclamation mr-2 rtl:mr-0 rtl:ml-2 text-rose-600 text-sm"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>

        </div>
    </div>

</body>

</html>
