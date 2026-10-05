@extends('admin.layout')
@section('title', __('Dashboard Overview'))

@section('content')
    <div class="space-y-6" x-data="adminDashboard()" x-init="initPusher()">

        <!-- ================= STICKY AUDIO ALARM BANNER ================= -->
        <template x-if="unacknowledgedOrders.length > 0">
            <div
                class="p-4 rounded-2xl bg-rose-600 text-white shadow-xl shadow-rose-600/25 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 animate-pulse">
                <div class="flex items-center space-x-3 rtl:space-x-reverse">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-lg shrink-0">
                        <i class="fa-solid fa-bell animate-bounce"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-sm">
                            {{ __('ATTENTION: Incoming Orders Pending Action!') }}
                            (<span x-text="unacknowledgedOrders.length"></span>)
                        </h4>
                        <p class="text-xs text-rose-100">
                            {{ __('Alarm will continue playing until staff accepts or cancels the order.') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center space-x-2 rtl:space-x-reverse w-full sm:w-auto">
                    <!-- Quick Mute Sound Button (Optional emergency mute) -->
                    <button type="button" @click="toggleMute()"
                        class="px-3 py-2 bg-white/20 hover:bg-white/30 rounded-xl text-xs font-bold transition flex items-center space-x-1 rtl:space-x-reverse">
                        <i class="fa-solid" :class="isMuted ? 'fa-volume-xmark' : 'fa-volume-high'"></i>
                        <span x-text="isMuted ? '{{ __('Unmute') }}' : '{{ __('Silence Audio') }}'"></span>
                    </button>
                </div>
            </div>
        </template>

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

        <!-- Recent Orders Table -->
        <div class="bg-white border border-stone-200/80 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-stone-100 flex items-center justify-between">
                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                    <h3 class="font-extrabold text-xs uppercase tracking-wider text-stone-900">{{ __('Recent Orders') }}
                    </h3>
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span
                            class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 rtl:mr-0 rtl:ml-1.5 animate-pulse"></span>
                        Live
                    </span>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs text-[#8F966C] hover:underline font-bold">
                    {{ __('View All Orders') }} &rarr;
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
                            <th class="py-3 px-4 text-right rtl:text-left">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        <!-- Real-time incoming rows -->
                        <template x-for="ord in recentOrdersList" :key="ord.id">
                            <tr :class="isOrderAlerting(ord.id) ? 'bg-rose-50/50' : 'hover:bg-stone-50/60'"
                                class="transition">
                                <td class="py-3.5 px-4 font-bold text-stone-900 whitespace-nowrap">
                                    <span x-text="ord.order_number"></span>
                                    <template x-if="isOrderAlerting(ord.id)">
                                        <span
                                            class="ml-1 text-[9px] font-black uppercase px-1.5 py-0.5 rounded bg-rose-600 text-white animate-pulse">
                                            NEW
                                        </span>
                                    </template>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap" x-text="ord.customer_name"></td>
                                <td class="py-3.5 px-4 font-black text-[#8F966C] whitespace-nowrap"
                                    x-text="`${ord.total} KD`"></td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase"
                                        :class="ord.order_status === 'completed' || ord.order_status === 'preparing' ?
                                            'bg-emerald-50 text-emerald-700' : (ord.order_status === 'cancelled' ?
                                                'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700')"
                                        x-text="ord.order_status">
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right rtl:text-left whitespace-nowrap">
                                    <template x-if="isOrderAlerting(ord.id)">
                                        <div class="inline-flex items-center space-x-1.5 rtl:space-x-reverse">
                                            <!-- Approve / Accept Button -->
                                            <button type="button" @click="handleOrderStatus(ord.id, 'preparing')"
                                                class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-sm transition active:scale-95 flex items-center space-x-1 rtl:space-x-reverse">
                                                <i class="fa-solid fa-check text-[10px]"></i>
                                                <span>{{ __('Accept') }}</span>
                                            </button>

                                            <!-- Cancel / Reject Button -->
                                            <button type="button" @click="handleOrderStatus(ord.id, 'cancelled')"
                                                class="px-2.5 py-1.5 rounded-lg bg-rose-100 hover:bg-rose-200 text-rose-700 font-bold text-[11px] transition active:scale-95 flex items-center space-x-1 rtl:space-x-reverse">
                                                <i class="fa-solid fa-xmark text-[10px]"></i>
                                                <span>{{ __('Cancel') }}</span>
                                            </button>
                                        </div>
                                    </template>
                                    <template x-if="!isOrderAlerting(ord.id)">
                                        <a :href="`{{ url('backend/orders') }}`"
                                            class="text-stone-400 hover:text-stone-700 font-bold text-xs">
                                            {{ __('Details') }} &rarr;
                                        </a>
                                    </template>
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
                                <td class="py-3.5 px-4 font-black text-[#8F966C] whitespace-nowrap">
                                    {{ number_format($ro->total, 3) }} KD
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $ro->order_status === 'completed' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ $ro->order_status ?? $ro->status }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right rtl:text-left whitespace-nowrap">
                                    <a href="{{ route('admin.orders.index') }}"
                                        class="text-stone-400 hover:text-stone-700 font-bold text-xs">
                                        {{ __('Details') }} &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr x-show="recentOrdersList.length === 0">
                                <td colspan="5" class="p-6 text-center text-stone-400">{{ __('No orders yet.') }}</td>
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
                unacknowledgedOrders: [], // Array of order IDs waiting for action
                alarmInterval: null,
                isMuted: false,

                isOverridden(id) {
                    return this.recentOrdersList.some(o => o.id === id);
                },

                isOrderAlerting(id) {
                    return this.unacknowledgedOrders.includes(id);
                },

                // Continuous Alarm System
                startContinuousAlarm() {
                    if (this.alarmInterval) return; // Already looping

                    // Play immediately
                    this.playChimeTone();

                    // Repeat chime every 2.5 seconds
                    this.alarmInterval = setInterval(() => {
                        if (this.unacknowledgedOrders.length === 0) {
                            this.stopContinuousAlarm();
                            return;
                        }
                        if (!this.isMuted) {
                            this.playChimeTone();
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

                playChimeTone() {
                    try {
                        const AudioContext = window.AudioContext || window.webkitAudioContext;
                        if (!AudioContext) return;

                        const ctx = new AudioContext();
                        if (ctx.state === 'suspended') {
                            ctx.resume();
                        }

                        const now = ctx.currentTime;
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();

                        osc.type = 'triangle';
                        // Alert sound: 950Hz -> 650Hz
                        osc.frequency.setValueAtTime(950, now);
                        osc.frequency.exponentialRampToValueAtTime(650, now + 0.35);

                        gain.gain.setValueAtTime(0.5, now);
                        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.9);

                        osc.connect(gain);
                        gain.connect(ctx.destination);

                        osc.start(now);
                        osc.stop(now + 0.9);
                    } catch (e) {
                        console.warn('Audio play restricted by browser policy:', e);
                    }
                },

                // Accept or Cancel Action
                handleOrderStatus(orderId, newStatus) {
                    fetch(`{{ url('backend/orders') }}/${orderId}/status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                status: newStatus
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            // 1. Remove from alert queue
                            this.unacknowledgedOrders = this.unacknowledgedOrders.filter(id => id !== orderId);

                            // 2. Stop alarm if no more orders need review
                            if (this.unacknowledgedOrders.length === 0) {
                                this.stopContinuousAlarm();
                            }

                            // 3. Update status in table
                            const ord = this.recentOrdersList.find(o => o.id === orderId);
                            if (ord) {
                                ord.order_status = newStatus;
                            }
                        })
                        .catch(err => {
                            console.error('Failed to update status:', err);
                        });
                },

                initPusher() {
                    Pusher.logToConsole = true;

                    const pusherKey = '{{ config('broadcasting.connections.pusher.key') }}';
                    const cluster = '{{ config('broadcasting.connections.pusher.options.cluster', 'ap2') }}';

                    if (!pusherKey) {
                        console.warn('Pusher key missing');
                        return;
                    }

                    const pusher = new Pusher(pusherKey, {
                        cluster: cluster,
                        forceTLS: true
                    });

                    const channel = pusher.subscribe('admin-orders');

                    channel.bind('order.placed', (data) => {
                        const order = data.orderData || data;

                        // Increment order counter
                        this.ordersCount++;

                        // Insert at top of recent orders
                        this.recentOrdersList.unshift(order);

                        // Add order ID to unacknowledged queue
                        if (!this.unacknowledgedOrders.includes(order.id)) {
                            this.unacknowledgedOrders.push(order.id);
                        }

                        // Start continuous loop alarm
                        this.startContinuousAlarm();
                    });
                }
            };
        }
    </script>
@endsection
