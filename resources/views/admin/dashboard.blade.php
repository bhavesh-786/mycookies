@extends('admin.layout')
@section('title', __('Dashboard Overview'))

@section('content')
    <div class="space-y-6" x-data="adminDashboard()" x-init="initDashboard()">

        <!-- Stat Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            <div class="bg-white p-5 sm:p-6 rounded-2xl border border-stone-200/80 shadow-sm flex items-center space-x-4 rtl:space-x-reverse">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-[#8F966C] flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-stone-900" x-text="ordersCount"></div>
                    <div class="text-xs text-stone-400 font-bold uppercase tracking-wider">{{ __('Total Orders') }}</div>
                </div>
            </div>

            <div class="bg-white p-5 sm:p-6 rounded-2xl border border-stone-200/80 shadow-sm flex items-center space-x-4 rtl:space-x-reverse">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-cookie"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-stone-900">{{ $productsCount }}</div>
                    <div class="text-xs text-stone-400 font-bold uppercase tracking-wider">{{ __('Total Products') }}</div>
                </div>
            </div>

            <div class="bg-white p-5 sm:p-6 rounded-2xl border border-stone-200/80 shadow-sm flex items-center space-x-4 rtl:space-x-reverse sm:col-span-2 lg:col-span-1">
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-stone-900">{{ $categoriesCount }}</div>
                    <div class="text-xs text-stone-400 font-bold uppercase tracking-wider">{{ __('Total Categories') }}</div>
                </div>
            </div>
        </div>

        <!-- Recent Orders Table -->
        <div class="bg-white border border-stone-200/80 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-stone-100 flex items-center justify-between">
                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                    <h3 class="font-extrabold text-xs uppercase tracking-wider text-stone-900">{{ __('Recent Orders') }}</h3>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 rtl:mr-0 rtl:ml-1.5 animate-pulse"></span>
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
                        <!-- Real-time incoming rows via WebSockets -->
                        <template x-for="ord in recentOrdersList" :key="ord.id">
                            <tr :class="isOrderPending(ord.id, ord.order_status) ? 'bg-rose-50/70 font-semibold' : 'hover:bg-stone-50/60'" class="transition">
                                <td class="py-3.5 px-4 font-bold text-stone-900 whitespace-nowrap">
                                    <span x-text="ord.order_number"></span>
                                    <template x-if="isOrderPending(ord.id, ord.order_status)">
                                        <span class="ml-1 text-[9px] font-black uppercase px-1.5 py-0.5 rounded bg-rose-600 text-white animate-pulse">
                                            NEW
                                        </span>
                                    </template>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap" x-text="ord.customer_name"></td>
                                <td class="py-3.5 px-4 font-black text-[#8F966C] whitespace-nowrap" x-text="`${ord.total} KD`"></td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase"
                                        :class="ord.order_status === 'completed' || ord.order_status === 'preparing' ? 'bg-emerald-50 text-emerald-700' : (ord.order_status === 'cancelled' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700')"
                                        x-text="ord.order_status">
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right rtl:text-left whitespace-nowrap">
                                    <template x-if="isOrderPending(ord.id, ord.order_status)">
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
                                    <template x-if="!isOrderPending(ord.id, ord.order_status)">
                                        <a href="{{ route('admin.orders.index') }}" class="text-stone-400 hover:text-stone-700 font-bold text-xs">
                                            {{ __('Details') }} &rarr;
                                        </a>
                                    </template>
                                </td>
                            </tr>
                        </template>

                        <!-- Existing Database rows -->
                        @forelse($recentOrders as $ro)
                            @php
                                $status = strtolower($ro->order_status ?? $ro->status ?? 'pending');
                                $isPendingStatus = in_array($status, ['pending', 'placed']);
                            @endphp
                            <tr class="hover:bg-stone-50/60 transition" x-show="!isOverridden({{ $ro->id }})"
                                :class="isOrderPending({{ $ro->id }}, '{{$status }}') ? 'bg-rose-50/70' : ''">
                                <td class="py-3.5 px-4 font-bold text-stone-900 whitespace-nowrap">
                                    {{ $ro->order_number }}
                                    <template x-if="isOrderPending({{ $ro->id }}, '{{$status }}')">
                                        <span class="ml-1 text-[9px] font-black uppercase px-1.5 py-0.5 rounded bg-rose-600 text-white animate-pulse">
                                            NEW
                                        </span>
                                    </template>
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">{{ $ro->customer_name }}</td>
                                <td class="py-3.5 px-4 font-black text-[#8F966C] whitespace-nowrap">
                                    {{ number_format($ro->total, 3) }} KD
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $status === 'completed' || $status === 'preparing' ? 'bg-emerald-50 text-emerald-700' : ($status === 'cancelled' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                                        {{ $ro->order_status ?? $ro->status }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right rtl:text-left whitespace-nowrap">
                                    <template x-if="isOrderPending({{ $ro->id }}, '{{$status }}')">
                                        <div class="inline-flex items-center space-x-1.5 rtl:space-x-reverse">
                                            <button type="button" @click="handleOrderStatus({{ $ro->id }}, 'preparing')"
                                                class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-sm transition active:scale-95 flex items-center space-x-1 rtl:space-x-reverse">
                                                <i class="fa-solid fa-check text-[10px]"></i>
                                                <span>{{ __('Accept') }}</span>
                                            </button>

                                            <button type="button" @click="handleOrderStatus({{ $ro->id }}, 'cancelled')"
                                                class="px-2.5 py-1.5 rounded-lg bg-rose-100 hover:bg-rose-200 text-rose-700 font-bold text-[11px] transition active:scale-95 flex items-center space-x-1 rtl:space-x-reverse">
                                                <i class="fa-solid fa-xmark text-[10px]"></i>
                                                <span>{{ __('Cancel') }}</span>
                                            </button>
                                        </div>
                                    </template>
                                    <template x-if="!isOrderPending({{ $ro->id }}, '{{$status }}')">
                                        <a href="{{ route('admin.orders.index') }}" class="text-stone-400 hover:text-stone-700 font-bold text-xs">
                                            {{ __('Details') }} &rarr;
                                        </a>
                                    </template>
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
                handledOrders: {},

                initDashboard() {
                    window.addEventListener('new-order-received', (e) => {
                        this.ordersCount++;
                        this.recentOrdersList.unshift(e.detail);
                    });
                },

                isOrderPending(orderId, currentStatus) {
                    // If already handled in this view, respect the updated status
                    if (this.handledOrders[orderId]) {
                        return this.handledOrders[orderId] === 'pending';
                    }

                    const inUnackQueue = Alpine.store('admin') ?
                        Alpine.store('admin').unacknowledgedOrders.map(Number).includes(Number(orderId)) : false;

                    const isPendingStr = String(currentStatus).toLowerCase() === 'pending' || String(currentStatus).toLowerCase() === 'placed';

                    return inUnackQueue || isPendingStr;
                },

                isOverridden(id) {
                    return this.recentOrdersList.some(o => Number(o.id) === Number(id));
                },

                handleOrderStatus(orderId, newStatus) {
                    fetch(`{{ url('backend/orders') }}/${orderId}/status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ status: newStatus })
                    })
                    .then(res => res.json())
                    .then(data => {
                        // Mark as handled locally
                        this.handledOrders[orderId] = newStatus;

                        // Remove from the global alarm store to silence audio
                        if (Alpine.store('admin')) {
                            Alpine.store('admin').removeOrderFromQueue(orderId);
                        }

                        // Update in-memory real-time list if present
                        const ord = this.recentOrdersList.find(o => Number(o.id) === Number(orderId));
                        if (ord) {
                            ord.order_status = newStatus;
                        }

                        // Force Alpine reactivity update
                        this.recentOrdersList = [...this.recentOrdersList];
                    })
                    .catch(err => {
                        console.error('Failed to update status:', err);
                    });
                }
            };
        }
    </script>
@endsection