@extends('admin.layout')
@section('title', 'Dashboard Overview')

@section('content')
    <div class="space-y-6" x-data="adminDashboard()" x-init="initPusher()">

        <!-- Stat Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            <div
                class="bg-white p-5 sm:p-6 rounded-2xl border border-stone-200/80 shadow-sm flex items-center space-x-4 rtl:space-x-reverse">
                <div
                    class="w-12 h-12 rounded-2xl bg-rose-50 text-[#b5122b] flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-stone-900" x-text="ordersCount"></div>
                    <div class="text-xs text-stone-400 font-bold uppercase tracking-wider">{{ __('Total Orders') }}</div>
                </div>
            </div>

            <div
                class="bg-white p-5 sm:p-6 rounded-2xl border border-stone-200/80 shadow-sm flex items-center space-x-4 rtl:space-x-reverse">
                <div
                    class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-cookie"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-stone-900">{{ $productsCount }}</div>
                    <div class="text-xs text-stone-400 font-bold uppercase tracking-wider">{{ __('Total Products') }}</div>
                </div>
            </div>

            <div
                class="bg-white p-5 sm:p-6 rounded-2xl border border-stone-200/80 shadow-sm flex items-center space-x-4 rtl:space-x-reverse sm:col-span-2 lg:col-span-1">
                <div
                    class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-stone-900">{{ $categoriesCount }}</div>
                    <div class="text-xs text-stone-400 font-bold uppercase tracking-wider">{{ __('Total Categories') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Live Incoming Order Floating Alert Banner -->
        <template x-if="newOrderAlert">
            <div x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="-translate-y-4 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
                class="p-4 rounded-2xl bg-emerald-500 text-white flex items-center justify-between shadow-lg shadow-emerald-500/20">
                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-lg animate-bounce">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-sm"
                            x-text="`{{ __('New Order Received!') }} #${newOrderAlert.order_number}`"></h4>
                        <p class="text-xs text-emerald-100"
                            x-text="`${newOrderAlert.customer_name} • ${newOrderAlert.total} KD`"></p>
                    </div>
                </div>
                <button type="button" @click="newOrderAlert = null"
                    class="w-8 h-8 rounded-lg hover:bg-white/20 flex items-center justify-center transition">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </template>

        <!-- Recent Orders Table -->
        <div class="bg-white border border-stone-200/80 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-stone-100 flex items-center justify-between">
                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                    <h3 class="font-extrabold text-xs uppercase tracking-wider text-stone-900">{{ __('Recent Orders') }}
                    </h3>
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                        Live
                    </span>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs text-[#b5122b] hover:underline font-bold">
                    {{ __('View All') }} &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left rtl:text-right text-xs border-collapse">
                    <thead class="bg-stone-50 border-b text-stone-500 font-bold uppercase text-[11px]">
                        <tr>
                            <th class="py-3 px-4">{{ __('Order #') }}</th>
                            <th class="py-3 px-4">{{ __('Customer') }}</th>
                            <th class="py-3 px-4">{{ __('Total') }}</th>
                            <th class="py-3 px-4">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        <!-- Real-time incoming rows -->
                        <template x-for="ord in recentOrdersList" :key="ord.id">
                            <tr class="hover:bg-stone-50/60 transition bg-emerald-50/30">
                                <td class="py-3.5 px-4 font-bold text-stone-900 whitespace-nowrap"
                                    x-text="ord.order_number"></td>
                                <td class="py-3.5 px-4 whitespace-nowrap" x-text="ord.customer_name"></td>
                                <td class="py-3.5 px-4 font-black text-[#b5122b] whitespace-nowrap"
                                    x-text="`${ord.total} KD`"></td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-50 text-amber-700"
                                        x-text="ord.order_status"></span>
                                </td>
                            </tr>
                        </template>

                        <!-- Static initial blade rows -->
                        @forelse($recentOrders as $ro)
                            <tr class="hover:bg-stone-50/60 transition" x-show="!isOverridden({{ $ro->id }})">
                                <td class="py-3.5 px-4 font-bold text-stone-900 whitespace-nowrap">
                                    {{ $ro->order_number }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">{{ $ro->customer_name }}</td>
                                <td class="py-3.5 px-4 font-black text-[#b5122b] whitespace-nowrap">
                                    {{ number_format($ro->total, 3) }} KD
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $ro->order_status === 'completed' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ $ro->order_status ?? $ro->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr x-show="recentOrdersList.length === 0">
                                <td colspan="4" class="p-6 text-center text-stone-400">{{ __('No orders yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function adminDashboard() {
            return {
                ordersCount: {{ $ordersCount ?? 0 }},
                recentOrdersList: [],
                newOrderAlert: null,

                isOverridden(id) {
                    return this.recentOrdersList.some(o => o.id === id);
                },

                playAlarmSound() {
                    try {
                        const AudioContext = window.AudioContext || window.webkitAudioContext;
                        if (!AudioContext) return;

                        const ctx = new AudioContext();

                        // Resume AudioContext if browser suspended it due to autoplay restrictions
                        if (ctx.state === 'suspended') {
                            ctx.resume();
                        }

                        const now = ctx.currentTime;
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();

                        osc.type = 'sine';
                        // Two-tone alert chime (880Hz down to 587Hz)
                        osc.frequency.setValueAtTime(880, now);
                        osc.frequency.exponentialRampToValueAtTime(587.33, now + 0.25);

                        gain.gain.setValueAtTime(0.4, now);
                        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.8);

                        osc.connect(gain);
                        gain.connect(ctx.destination);

                        osc.start(now);
                        osc.stop(now + 0.8);
                    } catch (e) {
                        console.warn('Audio playback inhibited by browser policy:', e);
                    }
                },

                initPusher() {

                    Pusher.logToConsole = true;

                    const pusherKey = '{{ env('PUSHER_APP_KEY') }}';
                    const cluster = '{{ env('PUSHER_APP_CLUSTER', 'mt1') }}';

                    if (!pusherKey) {
                        console.warn('PUSHER_APP_KEY not set in .env');
                        return;
                    }

                    const pusher = new Pusher(pusherKey, {
                        cluster: cluster,
                        forceTLS: true
                    });

                    const channel = pusher.subscribe('admin-orders');

                    channel.bind('order.placed', (data) => {
                        console.log('Order received via Pusher:', data);
                        const order = data.orderData;

                        // Increment orders counter
                        this.ordersCount++;

                        // Prepend to recent list
                        this.recentOrdersList.unshift(order);

                        // Trigger audio chime
                        this.playAlarmSound();

                        // Show floating alert
                        this.newOrderAlert = order;
                        setTimeout(() => {
                            if (this.newOrderAlert && this.newOrderAlert.id === order.id) {
                                this.newOrderAlert = null;
                            }
                        }, 8000);
                    });
                }
            };
        }
    </script>
@endsection
