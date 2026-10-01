@extends('admin.layout')
@section('title', __('Create Role'))

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-extrabold text-stone-900">{{ __('Create Role') }}</h2>
                <p class="text-xs text-stone-500">{{ __('Add a new backend role and configure permissions.') }}</p>
            </div>
            <a href="{{ route('admin.roles.index') }}"
                class="px-3 py-2 rounded-xl border border-stone-200 text-stone-600 hover:bg-stone-50 font-bold text-xs transition">
                {{ __('Back to Roles') }}
            </a>
        </div>

        @if ($errors->any())
            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white border border-stone-200/80 rounded-2xl p-6 shadow-sm">
            <form action="{{ route('admin.roles.store') }}" method="POST" class="space-y-5 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-stone-700 mb-1">{{ __('Role Name') }} <span
                            class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Supervisor" required
                        class="w-full border border-stone-200 rounded-xl p-3 outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] transition">
                </div>

                <div>
                    <label class="block font-bold text-stone-700 mb-2">{{ __('Assign Permissions') }}</label>
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 p-4 rounded-xl border border-stone-200/80 bg-stone-50/50">
                        @foreach ($permissions as $perm)
                            <label
                                class="flex items-center space-x-2.5 rtl:space-x-reverse cursor-pointer p-2 rounded-lg hover:bg-white border border-transparent hover:border-stone-200 transition">
                                <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                    {{ is_array(old('permissions')) && in_array($perm->name, old('permissions')) ? 'checked' : '' }}
                                    class="w-4 h-4 text-[#8F966C] rounded border-stone-300 focus:ring-[#8F966C]">
                                <span class="font-semibold text-stone-800 text-xs">{{ $perm->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center space-x-2 rtl:space-x-reverse pt-4 border-t border-stone-100">
                    <a href="{{ route('admin.roles.index') }}"
                        class="py-3 px-5 border border-stone-200 rounded-xl font-bold text-stone-600 hover:bg-stone-50 transition text-center">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit"
                        class="flex-1 py-3 bg-[#8F966C] hover:bg-[#7B825B] text-white rounded-xl font-bold shadow-sm transition active:scale-95 text-center">
                        {{ __('Save Role') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
