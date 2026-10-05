@extends('admin.layout')
@section('title', __('Dashboard Overview'))

@section('content')
    <div class="space-y-6" x-data="adminDashboard()" x-init="initDashboard()">

        <!-- ================= BROWSER AUTOPLAY UNLOCK PROMPT ================= -->
        <template x-if="audioSuspended">
            <div x-show="!isAudioUnlocked" x-cloak @click="unlockAudio()"
                class="cursor-pointer p-3.5 rounded-2xl bg-amber-500/10 border border-amber-300 text-amber-900 flex items-center justify-between shadow-xs transition hover:bg-amber-500/20">
                <div class="flex items-center space-x-2.5 rtl:space-x-reverse text-xs font-bold">
                    <span class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-volume-xmark text-sm"></i>
                    </span>
                    <div>
                        <p class="font-extrabold">{{ __('Audio notifications are awaiting activation.') }}</p>
                        <p class="text-[11px] text-amber-700 font-medium">
                            {{ __('Click anywhere on the dashboard so browser allows the buzzer.') }}</p>
                    </div>
                </div>
                <button type="button"
                    class="px-3.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-black rounded-xl shadow-xs transition">
                    {{ __('Activate Audio') }}
                </button>
            </div>
        </template>

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
                                            <button type="button" @click="handleOrderStatus(ord.id, 'preparing')"
                                                class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-sm transition active:scale-95 flex items-center space-x-1 rtl:space-x-reverse">
                                                <i class="fa-solid fa-check text-[10px]"></i>
                                                <span>{{ __('Accept') }}</span>
                                            </button>

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
                unacknowledgedOrders: [],
                alarmInterval: null,
                isMuted: false,
                audioCtx: null,
                isAudioUnlocked: false,

                initDashboard() {
                    // Listen for any first user gesture to unlock audio cleanly
                    const unlockEvents = ['click', 'touchstart', 'keydown'];
                    const handleFirstGesture = () => {
                        this.unlockAudio();
                        unlockEvents.forEach(evt => window.removeEventListener(evt, handleFirstGesture));
                    };

                    unlockEvents.forEach(evt => {
                        window.addEventListener(evt, handleFirstGesture, {
                            once: true
                        });
                    });

                    this.initPusher();
                },

                unlockAudio() {
                    try {
                        const AudioContextClass = window.AudioContext || window.webkitAudioContext;
                        if (!AudioContextClass) return;

                        // Only construct or resume AFTER a user gesture
                        if (!this.audioCtx) {
                            this.audioCtx = new AudioContextClass();
                        }

                        if (this.audioCtx.state === 'suspended') {
                            this.audioCtx.resume().then(() => {
                                this.isAudioUnlocked = true;
                            });
                        } else if (this.audioCtx.state === 'running') {
                            this.isAudioUnlocked = true;
                        }
                    } catch (e) {
                        console.warn('Audio unlock pending user interaction:', e);
                    }
                },

                playChimeTone() {
                    // If the user hasn't clicked yet, try unlocking once
                    if (!this.audioCtx || this.audioCtx.state !== 'running') {
                        this.unlockAudio();
                    }

                    if (!this.audioCtx || this.audioCtx.state !== 'running') {
                        console.warn('AudioContext not running yet. Awaiting staff click.');
                        return;
                    }

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
                        console.error('Audio playback error:', e);
                    }
                },

                startContinuousAlarm() {
                    if (this.alarmInterval) return;

                    // Play first pulse
                    this.playChimeTone();

                    // Repeat every 2.5 seconds
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
                            this.unacknowledgedOrders = this.unacknowledgedOrders.filter(id => id !== orderId);

                            if (this.unacknowledgedOrders.length === 0) {
                                this.stopContinuousAlarm();
                            }

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
                    const channelName = '{{ app()->environment() }}-admin-orders';

                    if (!pusherKey) {
                        console.warn('Pusher key missing');
                        return;
                    }

                    const pusher = new Pusher(pusherKey, {
                        cluster: cluster,
                        forceTLS: true
                    });

                    const channel = pusher.subscribe(channelName);

                    channel.bind('order.placed', (data) => {
                        const order = data.orderData || data;

                        this.ordersCount++;
                        this.recentOrdersList.unshift(order);

                        if (!this.unacknowledgedOrders.includes(order.id)) {
                            this.unacknowledgedOrders.push(order.id);
                        }

                        this.startContinuousAlarm();
                    });
                }
            };
        }
    </script>
@endsection
