@extends('admin.layout')
@section('title', __('Pickup Stores'))

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm">
            <div>
                <h2 class="text-base font-extrabold text-stone-900">{{ __('Pickup Stores') }}</h2>
                <p class="text-xs text-stone-500">
                    {{ __('Manage self-pickup store branches, addresses, and operating statuses.') }}</p>
            </div>
            <a href="{{ route('admin.pickstores.create') }}"
                class="bg-[#8F966C] hover:bg-[#7B825B] text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition active:scale-95 inline-flex items-center space-x-1.5 rtl:space-x-reverse">
                <i class="fa-solid fa-plus text-[11px]"></i>
                <span>{{ __('Add Store Branch') }}</span>
            </a>
        </div>

        <!-- Stores Table -->
        <div class="bg-white border border-stone-200/80 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left rtl:text-right border-collapse text-xs">
                    <thead>
                        <tr
                            class="bg-stone-50/75 border-b border-stone-200 text-stone-500 font-bold uppercase text-[11px] tracking-wider">
                            <th class="py-3.5 px-5 w-16">#</th>
                            <th class="py-3.5 px-5">{{ __('Store Branch Name') }}</th>
                            <th class="py-3.5 px-5">{{ __('Location / Address Description') }}</th>
                            <th class="py-3.5 px-4 text-center">{{ __('Status') }}</th>
                            <th class="py-3.5 px-5 text-right rtl:text-left">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($stores as $store)
                            <tr class="hover:bg-stone-50/60 transition duration-150">
                                <td class="py-4 px-5 font-mono text-stone-400 font-bold">
                                    {{ $store->id }}
                                </td>
                                <td class="py-4 px-5">
                                    <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                        <div
                                            class="w-7 h-7 rounded-lg bg-[#8F966C]/10 text-[#394326] flex items-center justify-center shrink-0">
                                            <i class="fa-solid fa-store text-xs"></i>
                                        </div>
                                        <span class="font-extrabold text-stone-900">{{ $store->name }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-5 text-stone-600 max-w-sm">
                                    <span class="line-clamp-2">
                                        {{ $store->location_description ?? ($store->description ?? '—') }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center whitespace-nowrap">
                                    @if ($store->is_active)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            {{ __('Active') }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            {{ __('Inactive') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-5 text-right rtl:text-left whitespace-nowrap">
                                    <div class="inline-flex items-center space-x-1.5 rtl:space-x-reverse">
                                        <a href="{{ route('admin.pickstores.edit', $store) }}"
                                            class="inline-flex items-center space-x-1 rtl:space-x-reverse px-2.5 py-1.5 rounded-lg text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-100 font-bold transition text-[11px]">
                                            <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                            <span>{{ __('Edit') }}</span>
                                        </a>

                                        <form action="{{ route('admin.pickstores.destroy', $store) }}" method="POST"
                                            class="inline m-0"
                                            onsubmit="return confirm('{{ __('Are you sure you want to delete this pickup store location?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center space-x-1 rtl:space-x-reverse px-2.5 py-1.5 rounded-lg text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-100 font-bold transition text-[11px]"
                                                title="{{ __('Delete') }}">
                                                <i class="fa-solid fa-trash-can text-[11px]"></i>
                                                <span>{{ __('Delete') }}</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-stone-400">
                                    <i class="fa-solid fa-store text-2xl mb-2 text-stone-300 block"></i>
                                    {{ __('No pickup store branches found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($stores->hasPages())
                <div class="p-4 border-t border-stone-200 bg-stone-50/50">
                    {{ $stores->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
