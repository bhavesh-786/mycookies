@extends('admin.layout')
@section('title', __('Roles & Permissions'))

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-5 rounded-2xl border border-stone-200/80 shadow-sm">
            <div>
                <h2 class="text-base font-extrabold text-stone-900">{{ __('Roles & Permissions') }}</h2>
                <p class="text-xs text-stone-500">{{ __('Define access levels and assign permissions to backend roles.') }}
                </p>
            </div>
            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                <a href="{{ route('admin.users.index') }}"
                    class="px-3.5 py-2.5 rounded-xl border border-stone-200 text-stone-700 hover:bg-stone-50 font-bold text-xs transition">
                    <i class="fa-solid fa-users text-[11px] mr-1"></i> {{ __('Users') }}
                </a>
                <a href="{{ route('admin.roles.create') }}"
                    class="bg-[#8F966C] hover:bg-[#7B825B] text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition active:scale-95 inline-flex items-center space-x-1 rtl:space-x-reverse">
                    <i class="fa-solid fa-plus text-[11px]"></i>
                    <span>{{ __('Add Role') }}</span>
                </a>
            </div>
        </div>

        @if (session('success'))
            <div
                class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold flex items-center space-x-2 rtl:space-x-reverse">
                <i class="fa-solid fa-circle-check shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div
                class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold flex items-center space-x-2 rtl:space-x-reverse">
                <i class="fa-solid fa-circle-exclamation shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Roles Table -->
        <div class="bg-white border border-stone-200/80 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left rtl:text-right border-collapse text-xs">
                    <thead>
                        <tr
                            class="bg-stone-50/75 border-b border-stone-200 text-stone-500 font-bold uppercase text-[11px] tracking-wider">
                            <th class="py-3.5 px-5">{{ __('Role Name') }}</th>
                            <th class="py-3.5 px-4">{{ __('Assigned Users') }}</th>
                            <th class="py-3.5 px-5">{{ __('Permissions') }}</th>
                            <th class="py-3.5 px-5 text-right rtl:text-left">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse ($roles as $role)
                            <tr class="hover:bg-stone-50/60 transition">
                                <td class="py-4 px-5">
                                    <span class="font-extrabold text-stone-900 text-xs">{{ $role->name }}</span>
                                </td>
                                <td class="py-4 px-4 whitespace-nowrap">
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-stone-100 text-stone-700 border border-stone-200">
                                        {{ $role->users->count() }} {{ __('User(s)') }}
                                    </span>
                                </td>
                                <td class="py-4 px-5">
                                    <div class="flex flex-wrap gap-1.5 max-w-xl">
                                        @forelse ($role->permissions as $perm)
                                            <span
                                                class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-[#8F966C]/15 text-[#394326]">
                                                {{ $perm->name }}
                                            </span>
                                        @empty
                                            <span
                                                class="text-stone-400 text-[11px] italic">{{ __('No permissions assigned') }}</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="py-4 px-5 text-right rtl:text-left whitespace-nowrap">
                                    <div class="inline-flex items-center space-x-1.5 rtl:space-x-reverse">
                                        <a href="{{ route('admin.roles.edit', $role->id) }}"
                                            class="inline-flex items-center space-x-1 rtl:space-x-reverse px-2.5 py-1.5 rounded-lg text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-100 font-bold transition text-[11px]">
                                            <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                                            <span>{{ __('Edit') }}</span>
                                        </a>

                                        @if ($role->name !== 'Super Admin')
                                            <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST"
                                                class="inline m-0"
                                                onsubmit="return confirm('{{ __('Are you sure you want to delete this role?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center px-2 py-1.5 rounded-lg text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-100 transition text-[11px]"
                                                    title="{{ __('Delete') }}">
                                                    <i class="fa-solid fa-trash-can text-[11px]"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-stone-400">
                                    {{ __('No roles found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($roles->hasPages())
                <div class="p-4 border-t border-stone-200 bg-stone-50/50">
                    {{ $roles->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
