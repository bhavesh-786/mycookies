@extends('admin.layout')
@section('title', 'Orders Management')

@section('content')
    <div class="bg-white border rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-stone-50 border-b text-stone-500 font-bold uppercase tracking-wider">
                <tr>
                    <th class="p-4">Order #</th>
                    <th class="p-4">Customer</th>
                    <th class="p-4">Type / Area</th>
                    <th class="p-4">Items & Addons</th>
                    <th class="p-4">Total</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $ord)
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-4 font-bold text-stone-900">{{ $ord->order_number }}</td>
                        <td class="p-4">
                            <strong class="text-stone-800">{{ $ord->customer_name }}</strong>
                            <div class="text-[11px] text-gray-400">{{ $ord->customer_phone }}</div>
                        </td>
                        <td class="p-4">
                            <span
                                class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $ord->order_type === 'delivery' ? 'bg-blue-50 text-blue-700' : 'bg-green-50 text-green-700' }}">
                                {{ $ord->order_type }}
                            </span>
                            <div class="text-stone-500 text-[11px] mt-0.5">{{ $ord->area_name ?? 'Branch Pickup' }}</div>
                        </td>
                        <td class="p-4 space-y-1">
                            @foreach ($ord->items as $it)
                                <div>
                                    <span class="font-bold">{{ $it->product_name }}</span> (x{{ $it->quantity }})
                                    @if (!empty($it->selected_addons))
                                        <div class="text-[11px] text-stone-400 pl-2">
                                            @foreach ($it->selected_addons as $ad)
                                                • {{ $ad['name'] ?? '' }}
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </td>
                        <td class="p-4 font-extrabold text-[#b5122b]">{{ number_format($ord->total, 3) }} KD</td>
                        <td class="p-4">
                            <form action="{{ route('admin.orders.status', $ord->id) }}" method="POST">
                                @csrf
                                <select name="order_status" onchange="this.form.submit()"
                                    class="border text-xs rounded-lg px-2 py-1 font-bold bg-white cursor-pointer">
                                    <option value="pending" {{ $ord->order_status === 'pending' ? 'selected' : '' }}>
                                        Pending</option>
                                    <option value="preparing" {{ $ord->order_status === 'preparing' ? 'selected' : '' }}>
                                        Preparing</option>
                                    <option value="out_for_delivery"
                                        {{ $ord->order_status === 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery
                                    </option>
                                    <option value="completed" {{ $ord->order_status === 'completed' ? 'selected' : '' }}>
                                        Completed</option>
                                </select>
                            </form>
                        </td>
                        <td class="p-4 text-stone-400">{{ $ord->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-400">No orders placed yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $orders->links() }}</div>
@endsection
