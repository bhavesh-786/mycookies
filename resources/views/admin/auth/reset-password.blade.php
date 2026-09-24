<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password - Otherwise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-stone-100 flex items-center justify-center min-h-screen p-4">

    <div class="max-w-md w-full bg-white rounded-3xl p-8 border border-stone-200 shadow-xl space-y-6">
        <div class="text-center space-y-2">
            <div class="inline-flex bg-[#8F966C] text-white font-extrabold text-lg px-4 py-2 rounded-2xl shadow-sm">
                OTHERWISE
            </div>
            <h1 class="text-xl font-extrabold text-stone-900">Set New Password</h1>
            <p class="text-xs text-stone-400">Choose a new secure password for your account.</p>
        </div>

        @if ($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-2xl text-xs font-semibold">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.password.update') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label class="block font-bold text-stone-700 mb-1.5">Email Address</label>
                <input type="email" name="email" value="{{ $email ?? old('email') }}" required autofocus
                    class="w-full border border-stone-200 p-3 rounded-xl outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] transition">
            </div>

            <div>
                <label class="block font-bold text-stone-700 mb-1.5">New Password</label>
                <input type="password" name="password" required
                    class="w-full border border-stone-200 p-3 rounded-xl outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] transition">
            </div>

            <div>
                <label class="block font-bold text-stone-700 mb-1.5">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                    class="w-full border border-stone-200 p-3 rounded-xl outline-none focus:border-[#8F966C] focus:ring-1 focus:ring-[#8F966C] transition">
            </div>

            <button type="submit"
                class="w-full bg-[#8F966C] hover:bg-[#8F966C] text-white font-extrabold py-3.5 rounded-xl shadow-md transition">
                Reset Password
            </button>
        </form>
    </div>

</body>

</html>
