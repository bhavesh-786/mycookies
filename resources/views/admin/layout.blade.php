<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'otherwise') }} - {{ __('Choose well.') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: {{ app()->getLocale() === 'ar' ? "'Cairo', sans-serif" : "'Plus Jakarta Sans', sans-serif" }};
        }
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-stone-50 text-stone-800 antialiased h-screen overflow-hidden" 
      x-data="adminGlobal()" 
      x-init="initGlobal()">

    <!-- Mobile Backdrop Overlay -->
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
        x-transition:enter="transition-opacity ease-linear duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-stone-950/60 z-40 lg:hidden">
    </div>

    <div class="flex h-full w-full overflow-hidden">

        <!-- Sidebar -->
        <aside
            :class="sidebarOpen ? 'translate-x-0' : ('{{ app()->getLocale() }}' === 'ar' ? 'translate-x-full lg:translate-x-0' : '-translate-x-full lg:translate-x-0')"
            class="fixed inset-y-0 {{ app()->getLocale() === 'ar' ? 'right-0' : 'left-0' }} z-50 w-72 lg:w-64 bg-stone-900 text-white flex flex-col justify-between p-5 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 shrink-0 shadow-2xl lg:shadow-none">

            <div class="space-y-6">
                <!-- Brand Logo & Mobile Close Button -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 rtl:space-x-reverse">
                        <div class="w-10 h-10 bg-[#8F966C] text-white flex items-center justify-center font-black rounded-xl text-sm uppercase">
                            OW
                        </div>
                        <div>
                            <h1 class="font-extrabold text-sm text-white tracking-tight leading-tight">{{ __('otherwise') }}</h1>
                            <p class="text-[11px] text-stone-400 font-medium">{{ __('Choose well.') }}</p>
                        </div>
                    </div>

                    <button @click="sidebarOpen = false" class="lg:hidden text-stone-400 hover:text-white p-1.5 rounded-lg hover:bg-stone-800 transition">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <!-- Navigation Links -->
                <nav class="space-y-1.5 text-xs font-semibold">
                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center space-x-3 rtl:space-x-reverse px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#8F966C] text-white shadow-sm' : 'text-stone-400 hover:bg-stone-800 hover:text-stone-200' }}">
                        <i class="fa-solid fa-chart-pie w-4 text-center"></i>
                        <span>{{ __('Dashboard') }}</span>
                    </a>

                    @can('manage-orders')
                        <a href="{{ route('admin.orders.index') }}"
                            class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.orders.*') ? 'bg-[#8F966C] text-white shadow-sm' : 'text-stone-400 hover:bg-stone-800 hover:text-stone-200' }}">
                            <div class="flex items-center space-x-3 rtl:space-x-reverse">
                                <i class="fa-solid fa-receipt w-4 text-center"></i>
                                <span>{{ __('Orders') }}</span>
                            </div>
                            <template x-if="unacknowledgedOrders.length > 0">
                                <span class="bg-rose-600 text-white text-[10px] px-2 py-0.5 rounded-full font-black animate-pulse"
                                      x-text="unacknowledgedOrders.length"></span>
                            </template>
                        </a>
                    @endcan

                    @can('manage-products')
                        <a href="{{ route('admin.products.index') }}"
                            class="flex items-center space-x-3 rtl:space-x-reverse px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.products.*') ? 'bg-[#8F966C] text-white shadow-sm' : 'text-stone-400 hover:bg-stone-800 hover:text-stone-200' }}">
                            <i class="fa-solid fa-cookie w-4 text-center"></i>
                            <span>{{ __('Products & Addons') }}</span>
                        </a>
                    @endcan

                    @can('manage-categories')
                        <a href="{{ route('admin.categories.index') }}"
                            class="flex items-center space-x-3 rtl:space-x-reverse px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.categories.*') ? 'bg-[#8F966C] text-white shadow-sm' : 'text-stone-400 hover:bg-stone-800 hover:text-stone-200' }}">
                            <i class="fa-solid fa-layer-group w-4 text-center"></i>
                            <span>{{ __('Categories') }}</span>
                        </a>
                    @endcan

                    @can('manage-customers')
                        <a href="{{ route('admin.customers.index') }}"
                            class="flex items-center space-x-3 rtl:space-x-reverse px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.customers.*') ? 'bg-[#8F966C] text-white shadow-sm' : 'text-stone-400 hover:bg-stone-800 hover:text-stone-200' }}">
                            <i class="fa-solid fa-users w-4 text-center"></i>
                            <span>{{ __('Customers') }}</span>
                        </a>
                    @endcan

                    @can('manage-governorates')
                        <a href="{{ route('admin.governorates.index') }}"
                            class="flex items-center space-x-3 rtl:space-x-reverse px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.governorates.*') ? 'bg-[#8F966C] text-white shadow-sm' : 'text-stone-400 hover:bg-stone-800 hover:text-stone-200' }}">
                            <i class="fa-solid fa-map-location-dot w-4 text-center"></i>
                            <span>{{ __('Governorates') }}</span>
                        </a>
                    @endcan

                    @can('manage-areas')
                        <a href="{{ route('admin.areas.index') }}"
                            class="flex items-center space-x-3 rtl:space-x-reverse px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.areas.*') ? 'bg-[#8F966C] text-white shadow-sm' : 'text-stone-400 hover:bg-stone-800 hover:text-stone-200' }}">
                            <i class="fa-solid fa-truck-ramp-box w-4 text-center"></i>
                            <span>{{ __('Areas') }}</span>
                        </a>
                    @endcan

                    @can('manage-stores')
                        <a href="{{ route('admin.pickstores.index') }}"
                            class="flex items-center space-x-3 rtl:space-x-reverse px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.pickstores.*') ? 'bg-[#8F966C] text-white shadow-sm' : 'text-stone-400 hover:bg-stone-800 hover:text-stone-200' }}">
                            <i class="fa-solid fa-store w-4 text-center"></i>
                            <span>{{ __('Stores') }}</span>
                        </a>
                    @endcan

                    @can('manage-users')
                        <a href="{{ route('admin.users.index') }}"
                            class="flex items-center space-x-3 rtl:space-x-reverse px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.users.*') ? 'bg-[#8F966C] text-white shadow-sm' : 'text-stone-400 hover:bg-stone-800 hover:text-stone-200' }}">
                            <i class="fa-solid fa-user-gear w-4 text-center"></i>
                            <span>{{ __('Users') }}</span>
                        </a>

                        <a href="{{ route('admin.roles.index') }}"
                            class="flex items-center space-x-3 rtl:space-x-reverse px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.roles.*') ? 'bg-[#8F966C] text-white shadow-sm' : 'text-stone-400 hover:bg-stone-800 hover:text-stone-200' }}">
                            <i class="fa-solid fa-shield-halved w-4 text-center"></i>
                            <span>{{ __('Roles') }}</span>
                        </a>
                    @endcan

                    @can('manage-settings')
                        <a href="{{ route('admin.settings.index') }}"
                            class="flex items-center space-x-3 rtl:space-x-reverse px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.settings.*') ? 'bg-[#8F966C] text-white shadow-sm' : 'text-stone-400 hover:bg-stone-800 hover:text-stone-200' }}">
                            <i class="fa-solid fa-sliders w-4 text-center"></i>
                            <span>{{ __('Settings') }}</span>
                        </a>
                    @endcan
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
                        <div class="font-bold text-stone-200 truncate">{{ auth()->user()->name ?? __('Admin User') }}</div>
                        <div class="text-[10px] text-stone-500 truncate">{{ auth()->user()->email ?? '' }}</div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Right Main Scrollable Viewport -->
        <div class="flex-1 flex flex-col h-full min-w-0 overflow-hidden">

            <!-- Responsive Top Header -->
            <header class="bg-white border-b border-stone-200/80 px-4 sm:px-6 lg:px-8 py-3.5 flex justify-between items-center sticky top-0 z-30 shrink-0">
                <div class="flex items-center space-x-3 rtl:space-x-reverse min-w-0">
                    <button @click="sidebarOpen = true" type="button"
                        class="lg:hidden p-2 rounded-xl text-stone-600 hover:bg-stone-100 border border-stone-200 transition focus:outline-none">
                        <i class="fa-solid fa-bars text-sm"></i>
                    </button>
                    <h1 class="font-extrabold text-sm sm:text-base text-stone-900 tracking-tight truncate">
                        @yield('title')
                    </h1>
                </div>

                <div class="flex items-center space-x-2.5 sm:space-x-4 rtl:space-x-reverse">
                    <!-- Notification Bell with Live Counter -->
                    <a href="{{ route('admin.dashboard') }}" title="{{ __('View Orders') }}"
                        class="relative w-9 h-9 rounded-xl border border-stone-200 flex items-center justify-center text-stone-600 hover:text-stone-900 hover:bg-stone-50 transition active:scale-95 shrink-0">
                        <i class="fa-solid fa-bell text-sm" :class="unacknowledgedOrders.length > 0 ? 'animate-bounce text-rose-600' : ''"></i>
                        <template x-if="unacknowledgedOrders.length > 0">
                            <span class="absolute -top-1 -right-1 rtl:right-auto rtl:-left-1 bg-rose-600 text-white text-[10px] font-black w-4 h-4 rounded-full flex items-center justify-center animate-pulse shadow-sm"
                                  x-text="unacknowledgedOrders.length"></span>
                        </template>
                    </a>

                    <!-- Language Switcher -->
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

            <!-- Alert Banners Wrapper -->
<div class="px-4 sm:px-6 lg:px-8 pt-4 space-y-3 shrink-0" 
     x-show="!$store.admin.isAudioUnlocked || $store.admin.unacknowledgedOrders.length > 0" 
     x-cloak>

    <!-- 1. Audio Permission Banner -->
    <div x-show="!$store.admin.isAudioUnlocked"
        @click="$store.admin.unlockAudio()"
        class="cursor-pointer p-3 sm:p-3.5 bg-amber-50 border border-amber-200/80 rounded-2xl flex items-center justify-between shadow-sm hover:bg-amber-100/60 transition">
        <div class="flex items-center space-x-3 rtl:space-x-reverse min-w-0">
            <span class="w-8 h-8 rounded-xl bg-amber-500/15 text-amber-700 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-volume-xmark text-xs"></i>
            </span>
            <div class="text-xs min-w-0">
                <p class="font-extrabold text-amber-900 leading-tight">
                    {{ __('Audio alarm is inactive.') }}
                </p>
                <p class="text-[11px] text-amber-700/90 font-medium truncate mt-0.5">
                    {{ __('Click anywhere on the screen to enable real-time alarm sounds.') }}
                </p>
            </div>
        </div>
        <button type="button" 
            class="px-3.5 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl font-bold text-xs shadow-xs transition shrink-0 ml-3 rtl:ml-0 rtl:mr-3">
            {{ __('Enable') }}
        </button>
    </div>

    <!-- 2. High-Priority Order Alarm Banner -->
    <div x-show="$store.admin.unacknowledgedOrders.length > 0"
        class="p-3.5 sm:p-4 bg-gradient-to-r from-rose-600 to-rose-700 text-white rounded-2xl flex items-center justify-between shadow-lg shadow-rose-600/20 border border-rose-500/40">
        <div class="flex items-center space-x-3.5 rtl:space-x-reverse min-w-0">
            <span class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center shrink-0 animate-bounce">
                <i class="fa-solid fa-bell text-sm"></i>
            </span>
            <div class="min-w-0">
                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                    <h4 class="font-black text-xs sm:text-sm tracking-tight truncate">
                        {{ __('NEW ORDER ALERT') }}
                    </h4>
                    <span class="bg-white/20 text-white text-[10px] font-black px-2 py-0.5 rounded-full border border-white/25">
                        <span x-text="$store.admin.unacknowledgedOrders.length"></span> {{ __('pending') }}
                    </span>
                </div>
                <p class="text-[11px] text-rose-100/90 font-medium mt-0.5 truncate">
                    {{ __('Alarm rings continuously until an action (Accept or Cancel) is taken.') }}
                </p>
            </div>
        </div>

        <div class="flex items-center space-x-2 rtl:space-x-reverse shrink-0 ml-3 rtl:ml-0 rtl:mr-3">
            @if (!request()->routeIs('admin.dashboard'))
                <a href="{{ route('admin.dashboard') }}"
                    class="px-3 py-1.5 bg-white text-rose-700 hover:bg-rose-50 rounded-xl text-xs font-black transition shadow-xs">
                    {{ __('Review') }} &rarr;
                </a>
            @endif
            <button type="button" @click="$store.admin.toggleMute()"
                class="w-8 h-8 rounded-xl bg-white/15 hover:bg-white/25 flex items-center justify-center text-xs transition"
                title="{{ __('Mute sound') }}">
                <i class="fa-solid" :class="$store.admin.isMuted ? 'fa-volume-xmark' : 'fa-volume-high'"></i>
            </button>
        </div>
    </div>

</div>

            <!-- Dynamic Content Body -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                @if (session('success'))
                    <div class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-bold flex items-center shadow-xs">
                        <i class="fa-solid fa-circle-check mr-2 rtl:mr-0 rtl:ml-2 text-emerald-600 text-sm"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-5 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-2xl text-xs font-bold flex items-center shadow-xs">
                        <i class="fa-solid fa-circle-exclamation mr-2 rtl:mr-0 rtl:ml-2 text-rose-600 text-sm"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>

        </div>
    </div>

    <!-- Global Pusher & Audio Controller -->
    <!-- Global Pusher & Audio Controller with Alpine Store -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('admin', {
                unacknowledgedOrders: JSON.parse(localStorage.getItem('admin_unack_orders') || '[]'),
                isAudioUnlocked: false,
                isMuted: false,
                alarmInterval: null,
                audioCtx: null,

                saveOrders() {
                    localStorage.setItem('admin_unack_orders', JSON.stringify(this.unacknowledgedOrders));
                },

                init() {
                    const unlockEvents = ['click', 'touchstart', 'keydown'];
                    const handleGesture = () => {
                        this.unlockAudio();
                        unlockEvents.forEach(evt => window.removeEventListener(evt, handleGesture));
                    };
                    unlockEvents.forEach(evt => window.addEventListener(evt, handleGesture, { once: true }));

                    if (this.unacknowledgedOrders.length > 0) {
                        this.startContinuousAlarm();
                    }

                    this.initPusher();
                },

                unlockAudio() {
                    try {
                        const AudioClass = window.AudioContext || window.webkitAudioContext;
                        if (!AudioClass) return;

                        if (!this.audioCtx) {
                            this.audioCtx = new AudioClass();
                        }

                        if (this.audioCtx.state === 'suspended') {
                            this.audioCtx.resume().then(() => {
                                this.isAudioUnlocked = true;
                            });
                        } else if (this.audioCtx.state === 'running') {
                            this.isAudioUnlocked = true;
                        }
                    } catch (e) {
                        console.warn('Audio gesture pending:', e);
                    }
                },

                playAlarmTone() {
                    if (!this.audioCtx || this.audioCtx.state !== 'running') {
                        this.unlockAudio();
                    }
                    if (!this.audioCtx || this.audioCtx.state !== 'running') return;

                    try {
                        const now = this.audioCtx.currentTime;
                        const triggerBeep = (startTime, freq) => {
                            const osc = this.audioCtx.createOscillator();
                            const gain = this.audioCtx.createGain();
                            osc.type = 'square';
                            osc.frequency.setValueAtTime(freq, startTime);
                            gain.gain.setValueAtTime(0.85, startTime);
                            gain.gain.exponentialRampToValueAtTime(0.001, startTime + 0.22);
                            osc.connect(gain);
                            gain.connect(this.audioCtx.destination);
                            osc.start(startTime);
                            osc.stop(startTime + 0.22);
                        };

                        triggerBeep(now, 1100);
                        triggerBeep(now + 0.25, 1400);
                    } catch (e) {
                        console.error('Audio play error:', e);
                    }
                },

                startContinuousAlarm() {
                    if (this.alarmInterval) return;

                    this.playAlarmTone();

                    this.alarmInterval = setInterval(() => {
                        if (this.unacknowledgedOrders.length === 0) {
                            this.stopContinuousAlarm();
                            return;
                        }
                        if (!this.isMuted) {
                            this.playAlarmTone();
                        }
                    }, 2500);
                },

                stopContinuousAlarm() {
                    if (this.alarmInterval) {
                        clearInterval(this.alarmInterval);
                        this.alarmInterval = null;
                    }
                },

                toggleMute() {
                    this.isMuted = !this.isMuted;
                },

                removeOrderFromQueue(orderId) {
                    this.unacknowledgedOrders = this.unacknowledgedOrders.filter(id => Number(id) !== Number(orderId));
                    this.saveOrders();

                    if (this.unacknowledgedOrders.length === 0) {
                        this.stopContinuousAlarm();
                    }
                },

                initPusher() {
                    const pusherKey = '{{ config('broadcasting.connections.pusher.key') }}';
                    const cluster = '{{ config('broadcasting.connections.pusher.options.cluster', 'ap2') }}';
                    const channelName = '{{ app()->environment() }}-admin-orders';

                    if (!pusherKey) return;

                    const pusher = new Pusher(pusherKey, {
                        cluster: cluster,
                        forceTLS: true
                    });

                    const channel = pusher.subscribe(channelName);

                    channel.bind('order.placed', (data) => {
                        const order = data.orderData || data;

                        if (!this.unacknowledgedOrders.includes(order.id)) {
                            this.unacknowledgedOrders.push(order.id);
                            this.saveOrders();
                        }

                        window.dispatchEvent(new CustomEvent('new-order-received', { detail: order }));
                        this.startContinuousAlarm();
                    });
                }
            });
        });
    </script>
</body>
</html>
</body>
</html>