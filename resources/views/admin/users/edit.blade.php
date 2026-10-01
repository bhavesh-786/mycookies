@extends('admin.layout')
@section('title', __('Edit Admin User'))

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-extrabold text-stone-900">{{ __('Edit User') }}: {{ $user->name }}</h2>
                <p class="text-xs text-stone-500">{{ __('Modify user account details, role, or credentials.') }}</p>
            </div>
            <a href="{{ route('admin.users.index') }}"
                class="px-3 py-2 rounded-xl border border-stone-200 text-stone-600 hover:bg-stone-50 font-bold text-xs transition">
                {{ __('Back to Users') }}
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
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-bold text-stone-700 mb-1">{{ __('Full Name') }} <span
                            class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full border border-stone-200 rounded-xl p-3 outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] transition">
                </div>

                <div>
                    <label class="block font-bold text-stone-700 mb-1">{{ __('Email Address') }} <span
                            class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full border border-stone-200 rounded-xl p-3 outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] transition">
                </div>

                <div>
                    <label class="block font-bold text-stone-700 mb-1">{{ __('Role') }} <span
                            class="text-rose-500">*</span></label>
                    <select name="role" required
                        class="w-full border border-stone-200 rounded-xl p-3 outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] bg-white transition">
                        <option value="">{{ __('Select Role') }}</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}"
                                {{ old('role', $userRole) === $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-stone-700 mb-1">
                        {{ __('Password') }}
                        <span class="text-stone-400 font-normal">({{ __('Leave blank to keep current password') }})</span>
                    </label>
                    <input type="password" name="password" minlength="6" placeholder="••••••••"
                        class="w-full border border-stone-200 rounded-xl p-3 outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] transition">
                </div>

                <div class="flex items-center space-x-2 rtl:space-x-reverse pt-4 border-t border-stone-100">
                    <a href="{{ route('admin.users.index') }}"
                        class="py-3 px-5 border border-stone-200 rounded-xl font-bold text-stone-600 hover:bg-stone-50 transition text-center">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit"
                        class="flex-1 py-3 bg-[#8F966C] hover:bg-[#7B825B] text-white rounded-xl font-bold shadow-sm transition active:scale-95 text-center">
                        {{ __('Update User') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
