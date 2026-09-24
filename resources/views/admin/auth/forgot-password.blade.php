<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - MY Cookies</title>
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
            <h1 class="text-xl font-extrabold text-stone-900">Forgot Password</h1>
            <p class="text-xs text-stone-400">Enter your email address and we'll send you a password reset link.</p>
        </div>

        @if (session('status'))
            <div
                class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl text-xs font-semibold">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-2xl text-xs font-semibold">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.password.email') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-stone-700 mb-1.5">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full border border-stone-200 p-3 rounded-xl outline-none focus:border-[#b5122b] focus:ring-1 focus:ring-[#b5122b] transition">
            </div>

            <button type="submit"
                class="w-full bg-[#b5122b] hover:bg-rose-900 text-white font-extrabold py-3.5 rounded-xl shadow-md transition">
                Send Reset Link
            </button>
        </form>

        <div class="text-center">
            <a href="{{ route('admin.login') }}" class="text-xs text-stone-400 hover:text-stone-700 transition">
                &larr; Back to Login
            </a>
        </div>
    </div>

</body>

</html>
