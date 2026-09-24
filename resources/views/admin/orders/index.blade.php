@extends('admin.layout')
@section('title', __('Orders Management'))

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm">
            <div class="space-y-1">
                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-stone-100 text-stone-700">
                        {{ $orders->total() }} {{ __('Total Orders') }}
                    </span>
                </div>
                <p class="text-xs text-stone-500">{{ __('Track and update order fulfillment statuses.') }}</p>
            </div>
        </div>

        @if (session('success'))
            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white border border-stone-200/80 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left rtl:text-right border-collapse text-xs">
                    <thead
                        class="bg-stone-50/75 border-b border-stone-200 text-stone-500 font-bold text-[11px] uppercase tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4">{{ __('Order #') }}</th>
                            <th class="py-3.5 px-4">{{ __('Customer') }}</th>
                            <th class="py-3.5 px-4">{{ __('Type / Area') }}</th>
                            <th class="py-3.5 px-4">{{ __('Items & Addons') }}</th>
                            <th class="py-3.5 px-4">{{ __('Total') }}</th>
                            <th class="py-3.5 px-4">{{ __('Status') }}</th>
                            <th class="py-3.5 px-4">{{ __('Date') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($orders as $ord)
                            @php
                                $currentStatus = $ord->order_status ?? ($ord->status ?? 'pending');
                            @endphp
                            <tr class="hover:bg-stone-50/60 transition">
                                <td class="py-4 px-4 font-bold text-stone-900 whitespace-nowrap">
                                    {{ $ord->order_number }}
                                </td>
                                <td class="py-4 px-4">
                                    <strong class="text-stone-800">{{ $ord->customer_name }}</strong>
                                    <div class="text-[11px] text-stone-400 mt-0.5">{{ $ord->customer_phone }}</div>
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $ord->order_type === 'delivery' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                        {{ $ord->order_type }}
                                    </span>
                                    <div class="text-stone-500 text-[11px] mt-1">
                                        {{ $ord->area_name ?? __('Branch Pickup') }}</div>
                                </td>
                                <td class="py-4 px-4 space-y-1 max-w-xs">
                                    @foreach ($ord->items as $it)
                                        <div class="leading-snug">
                                            <span
                                                class="font-bold text-stone-800">{{ $it->product_name ?? $it->name }}</span>
                                            <span class="text-stone-500 font-bold">(x{{ $it->quantity }})</span>
                                            @if (!empty($it->selected_addons))
                                                <div class="text-[10px] text-stone-400">
                                                    @foreach ($it->selected_addons as $ad)
                                                        • {{ is_array($ad) ? $ad['name'] ?? '' : $ad }}
                                                    @endforeach
                                                </div>
                                            @elseif(!empty($it->addons))
                                                <div class="text-[10px] text-stone-400">
                                                    @foreach ($it->addons as $ad)
                                                        • {{ is_array($ad) ? $ad['name'] ?? '' : $ad }}
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </td>
                                <td class="py-4 px-4 font-extrabold text-[#8F966C] whitespace-nowrap text-[13px]">
                                    {{ number_format($ord->total, 3) }} <span
                                        class="text-[10px] font-bold text-stone-500">{{ __('KD') }}</span>
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <form action="{{ route('admin.orders.status', $ord->id) }}" method="POST">
                                        @csrf
                                        <select name="order_status" onchange="this.form.submit()"
                                            class="text-xs rounded-xl px-2.5 py-1.5 font-bold cursor-pointer border outline-none transition bg-white text-stone-800 border-stone-200">
                                            <option value="placed" {{ $currentStatus === 'placed' ? 'selected' : '' }}>
                                                {{ __('Placed') }}</option>
                                            <option value="pending" {{ $currentStatus === 'pending' ? 'selected' : '' }}>
                                                {{ __('Pending') }}</option>
                                            <option value="preparing"
                                                {{ $currentStatus === 'preparing' ? 'selected' : '' }}>
                                                {{ __('Preparing') }}</option>
                                            <option value="shipped" {{ $currentStatus === 'shipped' ? 'selected' : '' }}>
                                                {{ __('Out for delivery / Shipped') }}</option>
                                            <option value="delivered"
                                                {{ $currentStatus === 'delivered' ? 'selected' : '' }}>
                                                {{ __('Delivered') }}</option>
                                            <option value="cancelled"
                                                {{ $currentStatus === 'cancelled' ? 'selected' : '' }}>
                                                {{ __('Cancelled') }}</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="py-4 px-4 text-stone-400 whitespace-nowrap text-[11px]">
                                    {{ $ord->created_at ? $ord->created_at->diffForHumans() : '' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-stone-400">
                                    <i class="fa-regular fa-clock text-3xl mb-2 text-stone-300"></i>
                                    <p class="font-bold text-xs text-stone-600">{{ __('No orders placed yet.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($orders->hasPages())
                <div class="p-4 border-t border-stone-200 bg-stone-50/50">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
