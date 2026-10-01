@extends('admin.layout')
@section('title', __('Customer Management'))

@section('content')
    <div class="space-y-6" x-data="{
        deleteModalOpen: false,
        deleteUrl: '',
        customerName: '',
        openDeleteModal(url, name) {
            this.deleteUrl = url;
            this.customerName = name;
            this.deleteModalOpen = true;
        }
    }">
        <!-- Header Summary & Search Bar -->
        <div
            class="flex flex-col md:flex-row justify-between items-stretch md:items-center gap-4 bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm">
            <div class="space-y-1">
                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-stone-100 text-stone-700">
                        {{ $customers->total() }} {{ __('Total Customers') }}
                    </span>
                </div>
                <p class="text-xs text-stone-500">
                    {{ __('View registered coffee shop patrons, contact details, and account histories.') }}
                </p>
            </div>

            <!-- Search Form -->
            <div class="flex flex-col sm:flex-row items-center gap-3">
                <form method="GET" action="{{ route('admin.customers.index') }}" class="relative w-full sm:w-80">
                    @if (request('sort_by'))
                        <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                    @endif
                    @if (request('sort_dir'))
                        <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}">
                    @endif

                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="{{ __('Search by name, email, or phone...') }}"
                        class="w-full bg-stone-50/70 border border-stone-200 rounded-xl pl-9 pr-8 rtl:pr-9 rtl:pl-8 py-2 text-xs text-stone-800 placeholder-stone-400 focus:outline-none focus:ring-1 focus:ring-[#8F966C] focus:bg-white transition">

                    <span
                        class="absolute inset-y-0 left-3 rtl:left-auto rtl:right-3 flex items-center pointer-events-none text-stone-400">
                        <i class="fa-solid fa-magnifying-glass text-[11px]"></i>
                    </span>

                    @if (request('search'))
                        <a href="{{ route('admin.customers.index') }}"
                            class="absolute inset-y-0 right-2.5 rtl:right-auto rtl:left-2.5 flex items-center text-stone-400 hover:text-stone-600 text-xs">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Customers Table -->
        <div class="bg-white border border-stone-200/80 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left rtl:text-right border-collapse">
                    <thead>
                        <tr
                            class="bg-stone-50/75 border-b border-stone-200 text-stone-500 font-bold text-[11px] uppercase tracking-wider">
                            <!-- SORT BY CUSTOMER -->
                            <th class="py-3.5 px-5">
                                <a href="{{ request()->fullUrlWithQuery([
                                    'sort_by' => 'name',
                                    'sort_dir' => request('sort_by') === 'name' && request('sort_dir') === 'asc' ? 'desc' : 'asc',
                                ]) }}"
                                    class="inline-flex items-center space-x-1.5 rtl:space-x-reverse hover:text-[#8F966C] transition">
                                    <span>{{ __('Customer') }}</span>
                                    <span class="inline-flex flex-col text-[8px] leading-[6px]">
                                        <i
                                            class="fa-solid fa-caret-up {{ request('sort_by') === 'name' && request('sort_dir') === 'asc' ? 'text-[#8F966C]' : 'text-stone-300' }}"></i>
                                        <i
                                            class="fa-solid fa-caret-down {{ request('sort_by') === 'name' && request('sort_dir') === 'desc' ? 'text-[#8F966C]' : 'text-stone-300' }}"></i>
                                    </span>
                                </a>
                            </th>

                            <th class="py-3.5 px-4">{{ __('Contact Info') }}</th>
                            <th class="py-3.5 px-4">{{ __('Verification') }}</th>

                            <!-- SORT BY JOINED DATE -->
                            <th class="py-3.5 px-4">
                                <a href="{{ request()->fullUrlWithQuery([
                                    'sort_by' => 'created_at',
                                    'sort_dir' => request('sort_by') === 'created_at' && request('sort_dir') === 'asc' ? 'desc' : 'asc',
                                ]) }}"
                                    class="inline-flex items-center space-x-1.5 rtl:space-x-reverse hover:text-[#8F966C] transition">
                                    <span>{{ __('Registered') }}</span>
                                    <span class="inline-flex flex-col text-[8px] leading-[6px]">
                                        <i
                                            class="fa-solid fa-caret-up {{ request('sort_by') === 'created_at' && request('sort_dir') === 'asc' ? 'text-[#8F966C]' : 'text-stone-300' }}"></i>
                                        <i
                                            class="fa-solid fa-caret-down {{ request('sort_by') === 'created_at' && request('sort_dir') === 'desc' ? 'text-[#8F966C]' : 'text-stone-300' }}"></i>
                                    </span>
                                </a>
                            </th>

                            <th class="py-3.5 px-5 text-right rtl:text-left">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100 text-xs">
                        @forelse($customers as $c)
                            <tr class="hover:bg-stone-50/60 transition duration-150 group">
                                <!-- Customer Avatar + Name -->
                                <td class="py-4 px-5">
                                    <div class="flex items-center space-x-3.5 rtl:space-x-reverse">
                                        <div
                                            class="w-10 h-10 rounded-full bg-stone-100 border border-stone-200 text-stone-600 flex items-center justify-center font-bold text-xs shrink-0 uppercase">
                                            {{ substr($c->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <span
                                                class="font-extrabold text-stone-900 group-hover:text-[#8F966C] transition">
                                                {{ $c->name }}
                                            </span>
                                            <p class="text-[11px] text-stone-400">
                                                ID: #{{ $c->id }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Phone & Email -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <div class="space-y-0.5">
                                        <div
                                            class="flex items-center space-x-1.5 rtl:space-x-reverse text-stone-700 font-medium">
                                            <i class="fa-solid fa-phone text-[10px] text-stone-400"></i>
                                            <span>{{ $c->phone ?? __('N/A') }}</span>
                                        </div>
                                        <div
                                            class="flex items-center space-x-1.5 rtl:space-x-reverse text-stone-400 text-[11px]">
                                            <i class="fa-solid fa-envelope text-[10px]"></i>
                                            <span>{{ $c->email }}</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Verification Status Badge -->
                                <td class="py-4 px-4 whitespace-nowrap">
                                    @if ($c->email_verified_at)
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="fa-solid fa-check text-[9px] mr-1 rtl:ml-1"></i> {{ __('Verified') }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <i class="fa-solid fa-clock text-[9px] mr-1 rtl:ml-1"></i>
                                            {{ __('Unverified') }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Joined Date -->
                                <td class="py-4 px-4 whitespace-nowrap text-stone-500 text-[11px]">
                                    {{ $c->created_at ? $c->created_at->format('M d, Y') : __('N/A') }}
                                    <span class="block text-[10px] text-stone-400">
                                        {{ $c->created_at ? $c->created_at->diffForHumans() : '' }}
                                    </span>
                                </td>

                                <!-- Action Buttons -->
                                <td class="py-4 px-5 text-right rtl:text-left whitespace-nowrap">
                                    <div class="inline-flex items-center space-x-1.5 rtl:space-x-reverse">
                                        <!-- View / Order History -->
                                        <a href="{{ route('admin.customers.show', $c->id) }}"
                                            title="{{ __('View Profile & Orders') }}"
                                            class="inline-flex items-center space-x-1 rtl:space-x-reverse px-2.5 py-1.5 rounded-lg text-stone-600 bg-stone-100 hover:bg-stone-200/80 font-bold transition text-[11px]">
                                            <i class="fa-solid fa-receipt text-[11px]"></i>
                                            <span>{{ __('Orders') }}</span>
                                        </a>

                                        <!-- Delete Button -->
                                        <button type="button"
                                            @click="openDeleteModal('{{ route('admin.customers.destroy', $c->id) }}', '{{ addslashes($c->name) }}')"
                                            title="{{ __('Delete Customer') }}"
                                            class="inline-flex items-center px-2 py-1.5 rounded-lg text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-100 transition text-[11px]">
                                            <i class="fa-solid fa-trash-can text-[11px]"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-stone-400">
                                    <div
                                        class="w-12 h-12 rounded-full bg-stone-100 text-stone-400 flex items-center justify-center mx-auto mb-3 text-lg">
                                        <i class="fa-solid fa-users-slash"></i>
                                    </div>
                                    <span class="font-bold text-sm text-stone-700">{{ __('No customers found') }}</span>
                                    <p class="text-xs text-stone-400 mt-1">
                                        {{ __('Try searching with another keyword.') }}
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($customers->hasPages())
                <div class="p-4 border-t border-stone-200 bg-stone-50/50">
                    {{ $customers->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        <!-- Alpine.js Delete Confirmation Modal -->
        <div x-show="deleteModalOpen" x-cloak class="relative z-50">
            <div class="fixed inset-0 bg-stone-900/40 backdrop-blur-xs transition-opacity" x-show="deleteModalOpen"
                x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="deleteModalOpen = false">
            </div>

            <div class="fixed inset-0 z-10 overflow-y-auto flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl border border-stone-200/80 text-center space-y-4"
                    x-show="deleteModalOpen" x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95">

                    <div
                        class="w-12 h-12 rounded-full bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center mx-auto text-lg">
                        <i class="fa-solid fa-user-xmark"></i>
                    </div>

                    <div class="space-y-1">
                        <h3 class="font-extrabold text-stone-900 text-sm">{{ __('Delete Customer Account') }}</h3>
                        <p class="text-xs text-stone-500">
                            {{ __('Are you sure you want to remove') }} <strong class="text-stone-800"
                                x-text="customerName"></strong>?
                            {{ __('Their profile data and saved credentials will be permanently removed.') }}
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
