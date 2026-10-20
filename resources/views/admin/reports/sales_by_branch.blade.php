@extends('admin.layout')
@section('title', __('Sales by Branch'))

@section('content')
    <div class="space-y-6" x-data="salesByBranchReport()" x-init="fetchReport()">

        <!-- Header & Action Toolbar -->
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm no-print">
            <div>
                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                    <h2 class="text-base font-extrabold text-stone-900">{{ __('Sales by Branch') }}</h2>
                    <span
                        class="w-4 h-4 rounded-full bg-[#8F966C]/15 text-[#5D643E] text-[10px] inline-flex items-center justify-center font-bold">?</span>
                </div>
                <p class="text-xs text-stone-500 mt-0.5">
                    {{ __('Track gross revenue, orders count, delivery fees, and net sales per branch.') }}
                </p>
            </div>

            <!-- Action Buttons Toolbar -->
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto justify-start sm:justify-end">

                <!-- 1. Export Menu -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" type="button"
                        class="px-3.5 py-2 border border-stone-200 hover:border-stone-300 rounded-xl text-xs font-bold bg-white text-stone-700 flex items-center space-x-1.5 rtl:space-x-reverse shadow-2xs hover:bg-stone-50 transition active:scale-95">
                        <i class="fa-solid fa-cloud-arrow-down text-[#8F966C]"></i>
                        <span>{{ __('Export') }}</span>
                        <i class="fa-solid fa-chevron-down text-[9px] text-stone-400 ml-1"></i>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak
                        class="absolute right-0 rtl:right-auto rtl:left-0 mt-1.5 w-36 bg-white border border-stone-200 rounded-xl shadow-lg z-20 py-1 text-xs">
                        <a :href="`{{ route('admin.reports.sales_by_branch.export', 'csv') }}?start_date=${startDate}&end_date=${endDate}&branch_id=${selectedBranch}&group_by=${groupBy}`"
                            class="block px-3.5 py-2 hover:bg-[#8F966C]/10 hover:text-[#5D643E] transition font-semibold">
                            <i class="fa-solid fa-file-csv mr-1.5 text-stone-400"></i> {{ __('To CSV') }}
                        </a>
                        <a :href="`{{ route('admin.reports.sales_by_branch.export', 'excel') }}?start_date=${startDate}&end_date=${endDate}&branch_id=${selectedBranch}&group_by=${groupBy}`"
                            class="block px-3.5 py-2 hover:bg-[#8F966C]/10 hover:text-[#5D643E] transition font-semibold">
                            <i class="fa-solid fa-file-excel mr-1.5 text-stone-400"></i> {{ __('To Excel') }}
                        </a>
                    </div>
                </div>

                <!-- 2. Group By Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" type="button"
                        class="px-3.5 py-2 border border-stone-200 hover:border-stone-300 rounded-xl text-xs font-bold bg-white text-stone-700 flex items-center space-x-1.5 rtl:space-x-reverse shadow-2xs hover:bg-stone-50 transition active:scale-95">
                        <i class="fa-solid fa-layer-group text-[#8F966C]"></i>
                        <span x-text="`{{ __('Group By') }}: ${formatGroupName(groupBy)}`"></span>
                        <i class="fa-solid fa-chevron-down text-[9px] text-stone-400 ml-1"></i>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak
                        class="absolute right-0 rtl:right-auto rtl:left-0 mt-1.5 w-48 bg-white border border-stone-200 rounded-xl shadow-lg z-20 py-1 text-xs max-h-60 overflow-y-auto">
                        <template x-for="g in groupOptions" :key="g.key">
                            <button type="button" @click="groupBy = g.key; open = false; fetchReport()"
                                class="w-full text-left rtl:text-right px-3.5 py-2 hover:bg-[#8F966C]/10 hover:text-[#5D643E] transition"
                                :class="groupBy === g.key ? 'font-black text-[#5D643E] bg-[#8F966C]/15' : 'text-stone-700'"
                                x-text="g.label"></button>
                        </template>
                    </div>
                </div>

                <!-- 3. Date Range Preset Picker -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" type="button"
                        class="px-3.5 py-2 border border-stone-200 hover:border-stone-300 rounded-xl text-xs font-bold bg-white text-stone-700 flex items-center space-x-1.5 rtl:space-x-reverse shadow-2xs hover:bg-stone-50 transition active:scale-95">
                        <i class="fa-regular fa-calendar text-[#8F966C]"></i>
                        <span x-text="`${startDate} ~ ${endDate}`"></span>
                        <i class="fa-solid fa-chevron-down text-[9px] text-stone-400 ml-1"></i>
                    </button>

                    <div x-show="open" @click.outside="open = false" x-cloak
                        class="absolute right-0 rtl:right-auto rtl:left-0 mt-1.5 w-64 bg-white border border-stone-200 rounded-2xl shadow-xl z-20 p-3.5 text-xs space-y-2.5">
                        <div class="grid grid-cols-2 gap-1.5 border-b border-stone-100 pb-2.5">
                            <button type="button" @click="setPreset('today'); open = false;"
                                class="p-1.5 rounded-lg hover:bg-stone-50 text-stone-600 font-semibold text-left rtl:text-right">{{ __('Today') }}</button>
                            <button type="button" @click="setPreset('yesterday'); open = false;"
                                class="p-1.5 rounded-lg hover:bg-stone-50 text-stone-600 font-semibold text-left rtl:text-right">{{ __('Yesterday') }}</button>
                            <button type="button" @click="setPreset('this_week'); open = false;"
                                class="p-1.5 rounded-lg hover:bg-stone-50 text-stone-600 font-semibold text-left rtl:text-right">{{ __('This Week') }}</button>
                            <button type="button" @click="setPreset('last_week'); open = false;"
                                class="p-1.5 rounded-lg hover:bg-stone-50 text-stone-600 font-semibold text-left rtl:text-right">{{ __('Last Week') }}</button>
                            <button type="button" @click="setPreset('this_month'); open = false;"
                                class="p-1.5 rounded-lg hover:bg-[#8F966C]/10 font-black text-[#5D643E] text-left rtl:text-right">{{ __('This Month') }}</button>
                            <button type="button" @click="setPreset('last_month'); open = false;"
                                class="p-1.5 rounded-lg hover:bg-stone-50 text-stone-600 font-semibold text-left rtl:text-right">{{ __('Last Month') }}</button>
                        </div>
                        <div class="space-y-1.5">
                            <label
                                class="text-[10px] text-stone-400 block uppercase font-bold">{{ __('Custom Range') }}</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="date" x-model="startDate"
                                    class="border border-stone-200 rounded-lg p-1.5 text-xs outline-none focus:border-[#8F966C]">
                                <input type="date" x-model="endDate"
                                    class="border border-stone-200 rounded-lg p-1.5 text-xs outline-none focus:border-[#8F966C]">
                            </div>
                            <button type="button" @click="open = false; fetchReport()"
                                class="w-full bg-[#8F966C] hover:bg-[#7B825B] text-white rounded-lg py-1.5 font-bold transition text-xs mt-1">
                                {{ __('Apply Range') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 4. Filter Trigger Button -->
                <button type="button" @click="filterOpen = true"
                    class="px-3.5 py-2 border rounded-xl text-xs font-bold transition active:scale-95 flex items-center space-x-1.5 rtl:space-x-reverse shadow-2xs"
                    :class="selectedBranch !== 'all' ? 'border-[#8F966C] bg-[#8F966C]/10 text-[#5D643E]' :
                        'border-stone-200 bg-white text-stone-700 hover:bg-stone-50'">
                    <i class="fa-solid fa-sliders text-[#8F966C]"></i>
                    <span>{{ __('Filter') }}</span>
                    <span x-show="selectedBranch !== 'all'" class="w-1.5 h-1.5 rounded-full bg-[#8F966C]"></span>
                </button>

                <!-- 5. Print Button -->
                <button type="button" onclick="window.print()"
                    class="px-3.5 py-2 border border-stone-200 hover:border-stone-300 rounded-xl text-xs font-bold bg-white text-stone-700 flex items-center space-x-1.5 rtl:space-x-reverse shadow-2xs hover:bg-stone-50 transition active:scale-95">
                    <i class="fa-solid fa-print text-stone-400"></i>
                    <span>{{ __('Print') }}</span>
                </button>
            </div>
        </div>

        <!-- Sales Report Data Table -->
        <div class="bg-white border border-stone-200/80 rounded-2xl shadow-sm overflow-hidden">

            <!-- Loading Indicator -->
            <div x-show="loading" class="text-center py-16 text-stone-400 text-xs">
                <i class="fa-solid fa-circle-notch fa-spin text-2xl mb-2 text-[#8F966C]"></i>
                <p class="font-semibold">{{ __('Generating sales report...') }}</p>
            </div>

            <div x-show="!loading" class="overflow-x-auto">
                <table class="w-full text-left rtl:text-right border-collapse text-xs">
                    <thead>
                        <tr
                            class="bg-stone-50/75 border-b border-stone-200 text-stone-500 font-bold uppercase text-[11px] tracking-wider">
                            <th class="py-3.5 px-5">{{ __('Store Branch Name') }}</th>
                            <th x-show="groupBy !== 'none'" class="py-3.5 px-5">{{ __('Group Period') }}</th>
                            <th class="py-3.5 px-5 text-center">{{ __('Orders Count') }}</th>
                            <th class="py-3.5 px-5 text-right rtl:text-left">{{ __('Gross Sales') }}</th>
                            <th class="py-3.5 px-5 text-right rtl:text-left">{{ __('Delivery Fees') }}</th>
                            <th class="py-3.5 px-5 text-right rtl:text-left">{{ __('Net Sales') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        <template x-for="row in rows" :key="row.branch_name + (row.group_period || '')">
                            <tr class="hover:bg-stone-50/60 transition duration-150">
                                <td class="py-4 px-5">
                                    <div class="flex items-center space-x-2.5 rtl:space-x-reverse">
                                        <div
                                            class="w-7 h-7 rounded-lg bg-[#8F966C]/10 text-[#5D643E] flex items-center justify-center shrink-0">
                                            <i class="fa-solid fa-store text-xs"></i>
                                        </div>
                                        <div>
                                            <span class="font-extrabold text-stone-900 block"
                                                x-text="row.branch_name"></span>
                                            <span class="text-[10px] text-stone-400 font-mono" x-show="row.branch_id > 0"
                                                x-text="`ID: #${row.branch_id}`"></span>
                                        </div>
                                    </div>
                                </td>
                                <td x-show="groupBy !== 'none'" class="py-4 px-5 font-bold text-[#5D643E]"
                                    x-text="row.group_period"></td>
                                <td class="py-4 px-5 text-center">
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-stone-100 text-stone-700 border border-stone-200"
                                        x-text="row.total_orders"></span>
                                </td>
                                <td class="py-4 px-5 text-right rtl:text-left font-bold text-stone-800"
                                    x-text="`${parseFloat(row.gross_sales || 0).toFixed(3)} KD`"></td>
                                <td class="py-4 px-5 text-right rtl:text-left font-bold text-stone-600"
                                    x-text="`${parseFloat(row.total_delivery_fees || 0).toFixed(3)} KD`"></td>
                                <td class="py-4 px-5 text-right rtl:text-left font-black text-emerald-700 text-sm"
                                    x-text="`${parseFloat(row.net_sales || 0).toFixed(3)} KD`"></td>
                            </tr>
                        </template>

                        <!-- Empty State -->
                        <template x-if="rows.length === 0">
                            <tr>
                                <td :colspan="groupBy !== 'none' ? 6 : 5" class="py-12 text-center text-stone-400">
                                    <i class="fa-solid fa-chart-line text-2xl mb-2 text-stone-300 block"></i>
                                    {{ __('No sales orders match your selected filter and date criteria.') }}
                                </td>
                            </tr>
                        </template>
                    </tbody>

                    <!-- Summary Totals Row -->
                    <tfoot class="border-t-2 border-stone-200 bg-stone-50 font-bold text-stone-900 text-xs">
                        <tr>
                            <td class="py-4 px-5 uppercase font-black">{{ __('Totals') }}</td>
                            <td x-show="groupBy !== 'none'" class="py-4 px-5"></td>
                            <td class="py-4 px-5 text-center font-black" x-text="totals.total_orders || 0"></td>
                            <td class="py-4 px-5 text-right rtl:text-left font-black"
                                x-text="`${parseFloat(totals.gross_sales || 0).toFixed(3)} KD`"></td>
                            <td class="py-4 px-5 text-right rtl:text-left font-black text-stone-600"
                                x-text="`${parseFloat(totals.total_delivery_fees || 0).toFixed(3)} KD`"></td>
                            <td class="py-4 px-5 text-right rtl:text-left font-black text-emerald-700 text-sm"
                                x-text="`${parseFloat(totals.net_sales || 0).toFixed(3)} KD`"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Filter Drawer Modal -->
        <div x-show="filterOpen" x-cloak class="relative z-50 no-print">
            <div class="fixed inset-0 bg-black/40 backdrop-blur-xs transition-opacity" @click="filterOpen = false"></div>
            <div
                class="fixed inset-y-0 right-0 rtl:right-auto rtl:left-0 w-full max-w-sm bg-white shadow-2xl flex flex-col h-full border-l rtl:border-l-0 rtl:border-r border-stone-200">
                <div class="p-4 border-b border-stone-200 flex items-center justify-between">
                    <h3 class="font-extrabold text-sm text-stone-900">{{ __('Filter Report') }}</h3>
                    <button type="button" @click="filterOpen = false"
                        class="w-7 h-7 rounded-lg hover:bg-stone-100 text-stone-400 hover:text-stone-700 flex items-center justify-center transition">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <div class="p-5 flex-1 space-y-4 text-xs overflow-y-auto">
                    <div>
                        <label class="block font-bold text-stone-700 mb-1.5">{{ __('Select Store Branch') }}</label>
                        <select x-model="selectedBranch"
                            class="w-full border border-stone-200 rounded-xl p-2.5 text-xs outline-none focus:border-[#8F966C] focus:ring-2 focus:ring-[#8F966C]/20 bg-white">
                            <option value="all">{{ __('All Branches') }}</option>
                            @foreach ($branches as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-stone-700 mb-1.5">{{ __('Group View By') }}</label>
                        <select x-model="groupBy"
                            class="w-full border border-stone-200 rounded-xl p-2.5 text-xs outline-none focus:border-[#8F966C] focus:ring-2 focus:ring-[#8F966C]/20 bg-white">
                            <template x-for="g in groupOptions" :key="g.key">
                                <option :value="g.key" x-text="g.label"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="p-4 border-t border-stone-200 flex items-center space-x-2 rtl:space-x-reverse bg-stone-50">
                    <button type="button"
                        @click="selectedBranch = 'all'; groupBy = 'none'; filterOpen = false; fetchReport()"
                        class="flex-1 py-2.5 border border-stone-200 rounded-xl text-stone-600 text-xs font-bold hover:bg-stone-100 transition">
                        {{ __('Reset') }}
                    </button>
                    <button type="button" @click="filterOpen = false; fetchReport()"
                        class="flex-1 py-2.5 bg-[#8F966C] hover:bg-[#7B825B] text-white rounded-xl text-xs font-bold transition shadow-sm active:scale-95">
                        {{ __('Apply Filter') }}
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- Alpine.js Reporting Engine -->
    <script>
        function salesByBranchReport() {
            return {
                loading: false,
                filterOpen: false,
                startDate: '{{ $startDate }}',
                endDate: '{{ $endDate }}',
                groupBy: 'none',
                selectedBranch: 'all',
                rows: [],
                totals: {},

                groupOptions: [{
                        key: 'none',
                        label: '{{ __('None') }}'
                    },
                    {
                        key: 'day',
                        label: '{{ __('Day') }}'
                    },
                    {
                        key: 'week',
                        label: '{{ __('Week') }}'
                    },
                    {
                        key: 'month',
                        label: '{{ __('Month') }}'
                    },
                    {
                        key: 'order_type',
                        label: '{{ __('Order Type') }}'
                    }
                ],

                formatGroupName(k) {
                    const match = this.groupOptions.find(o => o.key === k);
                    return match ? match.label : 'None';
                },

                setPreset(type) {
                    const today = new Date();
                    const formatDate = (d) => {
                        const year = d.getFullYear();
                        const month = String(d.getMonth() + 1).padStart(2, '0');
                        const day = String(d.getDate()).padStart(2, '0');
                        return `${year}-${month}-${day}`;
                    };

                    if (type === 'today') {
                        this.startDate = formatDate(today);
                        this.endDate = formatDate(today);
                    } else if (type === 'yesterday') {
                        const y = new Date(today);
                        y.setDate(y.getDate() - 1);
                        this.startDate = formatDate(y);
                        this.endDate = formatDate(y);
                    } else if (type === 'this_week') {
                        const d = new Date(today);
                        const day = d.getDay();
                        const diff = d.getDate() - day + (day === 0 ? -6 : 1);
                        const monday = new Date(d.setDate(diff));
                        this.startDate = formatDate(monday);
                        this.endDate = formatDate(today);
                    } else if (type === 'last_week') {
                        const d = new Date(today);
                        const day = d.getDay();
                        const diff = d.getDate() - day - 6;
                        const lastMon = new Date(d.setDate(diff));
                        const lastSun = new Date(lastMon);
                        lastSun.setDate(lastMon.getDate() + 6);
                        this.startDate = formatDate(lastMon);
                        this.endDate = formatDate(lastSun);
                    } else if (type === 'this_month') {
                        const first = new Date(today.getFullYear(), today.getMonth(), 1);
                        const last = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                        this.startDate = formatDate(first);
                        this.endDate = formatDate(last);
                    } else if (type === 'last_month') {
                        const first = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                        const last = new Date(today.getFullYear(), today.getMonth(), 0);
                        this.startDate = formatDate(first);
                        this.endDate = formatDate(last);
                    }

                    this.fetchReport();
                },

                fetchReport() {
                    this.loading = true;
                    const params = new URLSearchParams({
                        start_date: this.startDate,
                        end_date: this.endDate,
                        group_by: this.groupBy,
                        branch_id: this.selectedBranch
                    });

                    fetch(`{{ route('admin.reports.sales_by_branch') }}?${params.toString()}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => {
                            if (!res.ok) throw new Error('Network response failed');
                            return res.json();
                        })
                        .then(data => {
                            this.rows = data.data || [];
                            this.totals = data.totals || {};
                        })
                        .catch(err => {
                            console.error('Report fetch error:', err);
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                }
            };
        }
    </script>
@endsection
