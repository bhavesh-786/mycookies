<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MY Cookies Admin Login</title>
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
            <div class="inline-flex bg-[#b5122b] text-white font-extrabold text-lg px-4 py-2 rounded-2xl shadow-sm">
                MY
            </div>
            <h1 class="text-xl font-extrabold text-stone-900">MY COOKIES</h1>
            <p class="text-xs text-stone-400">Sign in to manage your storefront & orders</p>
        </div>

        @if ($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-2xl text-xs font-semibold">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-stone-700 mb-1.5">Email Address</label>
                <input type="email" name="email" value="{{ old('email', 'admin@urcookies.com') }}" required
                    autofocus
                    class="w-full border border-stone-200 p-3 rounded-xl outline-none focus:border-[#b5122b] focus:ring-1 focus:ring-[#b5122b] transition">
            </div>

            <div>
                <label class="block font-bold text-stone-700 mb-1.5">Password</label>
                <input type="password" name="password" required
                    class="w-full border border-stone-200 p-3 rounded-xl outline-none focus:border-[#b5122b] focus:ring-1 focus:ring-[#b5122b] transition">
            </div>

            <div class="flex items-center justify-between text-xs text-stone-500 pt-1">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded text-[#b5122b] focus:ring-0">
                    <span>Remember me</span>
                </label>
                <a href="{{ route('admin.password.request') }}" class="text-[#b5122b] hover:underline font-semibold">
                    Forgot Password?
                </a>
            </div>

            <button type="submit"
                class="w-full bg-[#b5122b] hover:bg-rose-900 text-white font-extrabold py-3.5 rounded-xl shadow-md transition">
                Sign In to Dashboard
            </button>
        </form>

        <div class="text-center">
            <a href="/" class="text-xs text-stone-400 hover:text-stone-700 transition">
                &larr; Return to Storefront
            </a>
        </div>
    </div>

</body>

</html>
