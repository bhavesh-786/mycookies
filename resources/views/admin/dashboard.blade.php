@extends('admin.layout')
@section('title', 'Dashboard Overview')

@section('content')
    <div class="space-y-6">
        <!-- Stat Metrics: 1 column on mobile, 3 columns on tablet/desktop -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            <div class="bg-white p-5 sm:p-6 rounded-2xl border border-stone-200/80 shadow-sm flex items-center space-x-4">
                <div
                    class="w-12 h-12 rounded-2xl bg-rose-50 text-[#b5122b] flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-stone-900">{{ $ordersCount }}</div>
                    <div class="text-xs text-stone-400 font-bold uppercase tracking-wider">Total Orders</div>
                </div>
            </div>

            <div class="bg-white p-5 sm:p-6 rounded-2xl border border-stone-200/80 shadow-sm flex items-center space-x-4">
                <div
                    class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-cookie"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-stone-900">{{ $productsCount }}</div>
                    <div class="text-xs text-stone-400 font-bold uppercase tracking-wider">Total Products</div>
                </div>
            </div>

            <div
                class="bg-white p-5 sm:p-6 rounded-2xl border border-stone-200/80 shadow-sm flex items-center space-x-4 sm:col-span-2 lg:col-span-1">
                <div
                    class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-stone-900">{{ $categoriesCount }}</div>
                    <div class="text-xs text-stone-400 font-bold uppercase tracking-wider">Total Categories</div>
                </div>
            </div>
        </div>

        <!-- Recent Orders Table with horizontal scroll support -->
        <div class="bg-white border border-stone-200/80 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-stone-100 flex items-center justify-between">
                <h3 class="font-extrabold text-xs uppercase tracking-wider text-stone-900">Recent Orders</h3>
                <a href="{{ route('admin.orders.index') }}" class="text-xs text-[#b5122b] hover:underline font-bold">View
                    All &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-stone-50 border-b text-stone-500 font-bold uppercase text-[11px]">
                        <tr>
                            <th class="py-3 px-4">Order #</th>
                            <th class="py-3 px-4">Customer</th>
                            <th class="py-3 px-4">Total</th>
                            <th class="py-3 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($recentOrders as $ro)
                            <tr class="hover:bg-stone-50/60 transition">
                                <td class="py-3.5 px-4 font-bold text-stone-900 whitespace-nowrap">{{ $ro->order_number }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">{{ $ro->customer_name }}</td>
                                <td class="py-3.5 px-4 font-black text-[#b5122b] whitespace-nowrap">
                                    {{ number_format($ro->total, 3) }} KD</td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $ro->order_status === 'completed' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ $ro->order_status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-6 text-center text-stone-400">No orders yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
