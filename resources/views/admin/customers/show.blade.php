@extends('admin.layout')
@section('title', __('Customer Profile') . ' - ' . $customer->name)

@section('content')
    <div class="space-y-6" x-data="{
        deleteModalOpen: false,
        deleteUrl: '{{ route('admin.customers.destroy', $customer->id) }}',
        customerName: '{{ addslashes($customer->name) }}'
    }">
        <!-- Top Action & Navigation Bar -->
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm">
            <div class="flex items-center space-x-3 rtl:space-x-reverse">
                <a href="{{ route('admin.customers.index') }}"
                    class="p-2 rounded-xl border border-stone-200 text-stone-500 hover:text-stone-800 hover:bg-stone-50 transition">
                    <i class="fa-solid fa-arrow-left rtl:rotate-180 text-xs"></i>
                </a>
                <div>
                    <h2 class="text-sm font-extrabold text-stone-900">{{ __('Customer Details') }}</h2>
                    <p class="text-xs text-stone-400">
                        {{ __('Registered patron overview and order statistics.') }}
                    </p>
                </div>
            </div>

            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                <button type="button" @click="deleteModalOpen = true"
                    class="inline-flex items-center space-x-1.5 rtl:space-x-reverse px-3 py-2 rounded-xl text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 font-bold transition text-xs">
                    <i class="fa-solid fa-trash-can text-[11px]"></i>
                    <span>{{ __('Delete Customer') }}</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Customer Profile Card -->
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-2xl border border-stone-200/80 shadow-sm text-center">
                    <div
                        class="w-20 h-20 rounded-full bg-stone-100 border border-stone-200 text-stone-700 flex items-center justify-center font-extrabold text-2xl mx-auto uppercase shadow-inner">
                        {{ substr($customer->name, 0, 2) }}
                    </div>

                    <h3 class="mt-4 text-base font-extrabold text-stone-900">{{ $customer->name }}</h3>
                    <p class="text-xs text-stone-400 font-mono mt-0.5">ID: #{{ $customer->id }}</p>

                    <div
                        class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $customer->email_verified_at ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                        <i
                            class="fa-solid {{ $customer->email_verified_at ? 'fa-circle-check' : 'fa-clock' }} mr-1.5 rtl:ml-1.5 text-[11px]"></i>
                        {{ $customer->email_verified_at ? __('Email Verified') : __('Pending Verification') }}
                    </div>

                    <hr class="my-5 border-stone-100">

                    <!-- Contact & Meta Details -->
                    <div class="text-left rtl:text-right space-y-3 text-xs">
                        <div class="flex items-center justify-between py-1 border-b border-stone-50">
                            <span class="text-stone-400 font-medium">{{ __('Email') }}</span>
                            <span class="font-bold text-stone-800 break-all">{{ $customer->email }}</span>
                        </div>
                        <div class="flex items-center justify-between py-1 border-b border-stone-50">
                            <span class="text-stone-400 font-medium">{{ __('Phone') }}</span>
                            <span class="font-bold text-stone-800 font-mono">{{ $customer->phone ?? __('N/A') }}</span>
                        </div>
                        <div class="flex items-center justify-between py-1 border-b border-stone-50">
                            <span class="text-stone-400 font-medium">{{ __('Joined Date') }}</span>
                            <span
                                class="font-bold text-stone-800">{{ $customer->created_at ? $customer->created_at->format('M d, Y h:i A') : __('N/A') }}</span>
                        </div>
                        <div class="flex items-center justify-between py-1">
                            <span class="text-stone-400 font-medium">{{ __('Last Updated') }}</span>
                            <span
                                class="font-bold text-stone-800">{{ $customer->updated_at ? $customer->updated_at->diffForHumans() : __('N/A') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Coffee Shop Loyalty / Favorite Card -->
                <div class="bg-gradient-to-br from-stone-900 to-stone-800 text-white p-5 rounded-2xl shadow-sm space-y-3">
                    <div class="flex justify-between items-center">
                        <span
                            class="text-[11px] font-bold uppercase tracking-wider text-stone-400">{{ __('Coffee Rewards') }}</span>
                        <i class="fa-solid fa-mug-hot text-[#8F966C] text-lg"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-black">5 / 10</div>
                        <p class="text-[11px] text-stone-400">{{ __('Stamps collected toward a free beverage.') }}</p>
                    </div>
                    <div class="w-full bg-stone-700 h-2 rounded-full overflow-hidden">
                        <div class="bg-[#8F966C] h-full rounded-full" style="width: 50%;"></div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Coffee Orders & Activity -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Summary Stats Bar -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white p-4 rounded-2xl border border-stone-200/80 shadow-sm">
                        <span
                            class="text-[11px] font-bold text-stone-400 uppercase tracking-wide">{{ __('Total Orders') }}</span>
                        <div class="text-lg font-black text-stone-900 mt-1">
                            {{ $totalOrders ?? 0 }}
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-2xl border border-stone-200/80 shadow-sm">
                        <span
                            class="text-[11px] font-bold text-stone-400 uppercase tracking-wide">{{ __('Total Spent') }}</span>
                        <div class="text-lg font-black text-stone-900 mt-1">
                            {{ number_format($totalSpent ?? 0, 3) }} <span
                                class="text-[10px] text-stone-500 font-bold">{{ __('KD') }}</span>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-2xl border border-stone-200/80 shadow-sm">
                        <span
                            class="text-[11px] font-bold text-stone-400 uppercase tracking-wide">{{ __('Customer Since') }}</span>
                        <div class="text-sm font-extrabold text-[#8F966C] mt-1 truncate">
                            {{ $customer->created_at ? $customer->created_at->format('M Y') : __('N/A') }}
                        </div>
                    </div>
                </div>

                <!-- Order History Table -->
                <div class="bg-white border border-stone-200/80 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-stone-100 flex justify-between items-center">
                        <h4 class="text-xs font-extrabold text-stone-800 uppercase tracking-wider">
                            {{ __('Recent Order History') }}</h4>
                        <span class="text-[11px] text-stone-400">{{ __('Showing recent orders') }}</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left rtl:text-right border-collapse">
                            <thead>
                                <tr
                                    class="bg-stone-50/75 border-b border-stone-200 text-stone-500 font-bold text-[10px] uppercase tracking-wider">
                                    <th class="py-3 px-4">{{ __('Order #') }}</th>
                                    <th class="py-3 px-4">{{ __('Date') }}</th>
                                    <th class="py-3 px-4">{{ __('Payment / Type') }}</th>
                                    <th class="py-3 px-4">{{ __('Total') }}</th>
                                    <th class="py-3 px-4">{{ __('Status') }}</th>
                                    <th class="py-3 px-4 text-right rtl:text-left">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-100 text-xs">
                                @forelse ($orders as $order)
                                    <tr class="hover:bg-stone-50/60 transition">
                                        <td class="py-3.5 px-4 font-mono font-bold text-stone-900">
                                            #{{ $order->order_number ?? $order->id }}
                                        </td>
                                        <td class="py-3.5 px-4 text-stone-500">
                                            {{ $order->created_at ? $order->created_at->format('M d, Y h:i A') : __('N/A') }}
                                        </td>
                                        <td class="py-3.5 px-4 text-stone-700 capitalize">
                                            {{ $order->payment_method ?? ($order->order_type ?? __('Takeaway')) }}
                                        </td>
                                        <td class="py-3.5 px-4 font-bold text-stone-900">
                                            {{ number_format($order->total_amount ?? ($order->total ?? 0), 3) }}
                                            {{ __('KD') }}
                                        </td>
                                        <td class="py-3.5 px-4">
                                            @php
                                                $status = strtolower($order->status ?? 'completed');
                                                $statusColors = [
                                                    'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                    'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                    'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200',
                                                    'processing' => 'bg-sky-50 text-sky-700 border-sky-200',
                                                ];
                                                $colorClass =
                                                    $statusColors[$status] ??
                                                    'bg-stone-100 text-stone-600 border-stone-200';
                                            @endphp
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border {{ $colorClass }} uppercase">
                                                {{ __($order->status ?? 'Completed') }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-right rtl:text-left">
                                            @if (\Illuminate\Support\Facades\Route::has('admin.orders.show'))
                                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                                    class="text-[#8F966C] hover:underline font-bold text-[11px]">
                                                    {{ __('View Invoice') }} &rarr;
                                                </a>
                                            @else
                                                <span class="text-stone-300">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-10 text-center text-stone-400">
                                            <i class="fa-solid fa-receipt text-2xl text-stone-300 mb-2"></i>
                                            <p class="font-bold text-xs text-stone-600">{{ __('No orders placed yet') }}
                                            </p>
                                            <p class="text-[11px] text-stone-400">
                                                {{ __('Orders made by this customer will appear here.') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($orders->hasPages())
                        <div class="p-3 border-t border-stone-100 bg-stone-50/50">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div x-show="deleteModalOpen" x-cloak class="relative z-50">
            <div class="fixed inset-0 bg-stone-900/40 backdrop-blur-xs transition-opacity" x-show="deleteModalOpen"
                @click="deleteModalOpen = false"></div>

            <div class="fixed inset-0 z-10 overflow-y-auto flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl border border-stone-200/80 text-center space-y-4"
                    x-show="deleteModalOpen">
                    <div
                        class="w-12 h-12 rounded-full bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center mx-auto text-lg">
                        <i class="fa-solid fa-user-xmark"></i>
                    </div>

                    <div class="space-y-1">
                        <h3 class="font-extrabold text-stone-900 text-sm">{{ __('Delete Customer Account') }}</h3>
                        <p class="text-xs text-stone-500">
                            {{ __('Are you sure you want to remove') }} <strong class="text-stone-800"
                                x-text="customerName"></strong>? {{ __('This action cannot be undone.') }}
                        </p>
                    </div>

                    <div class="flex items-center space-x-2 rtl:space-x-reverse pt-2">
                        <button type="button" @click="deleteModalOpen = false"
                            class="flex-1 py-2.5 px-4 rounded-xl border border-stone-200 text-stone-600 text-xs font-bold hover:bg-stone-50 transition">
                            {{ __('Cancel') }}
                        </button>

                        <form :action="deleteUrl" method="POST" class="flex-1 m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-sm active:scale-95">
                                {{ __('Yes, Delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
