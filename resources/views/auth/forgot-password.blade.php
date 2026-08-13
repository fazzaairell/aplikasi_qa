<!DOCTYPE html>
<html lang="id" class="h-full bg-[#0c0f1a]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - QA Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full flex items-center justify-center font-sans text-slate-100">

    <div class="w-full max-w-md p-8 rounded-2xl bg-[#0c0f1a]">

        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-white mb-2">Lupa Password</h1>
            <p class="text-sm text-slate-400">Masukkan email kamu, kami akan kirim link untuk reset password.</p>
        </div>

        @if(session('status'))
            <div class="mb-5 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 p-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-semibold">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </span>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="andi@company.com"
                        class="w-full pl-11 pr-4 py-3 bg-[#111827] border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm transition">
                </div>
            </div>

            <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-medium rounded-xl shadow-lg shadow-indigo-500/25 transition duration-200 text-sm">
                Kirim Link Reset Password
            </button>
        </form>

        <p class="text-center text-xs text-slate-500 mt-8">
            Ingat password kamu? <a href="{{ route('login') }}" class="text-indigo-400 hover:underline">Kembali ke Login</a>
        </p>

    </div>

</body>
</html>