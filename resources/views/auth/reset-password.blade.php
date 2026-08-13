<!DOCTYPE html>
<html lang="id" class="h-full bg-[#0c0f1a]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - QA Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full flex items-center justify-center font-sans text-slate-100">

    <div class="w-full max-w-md p-8 rounded-2xl bg-[#0c0f1a]">

        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-white mb-2">Buat Password Baru</h1>
            <p class="text-sm text-slate-400">Masukkan password baru untuk akun kamu.</p>
        </div>

        @if($errors->any())
            <div class="mb-5 p-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-semibold space-y-1">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email', $email) }}" required readonly
                    class="w-full px-4 py-3 bg-[#111827] border border-slate-800 rounded-xl text-slate-400 text-sm cursor-not-allowed">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Password Baru</label>
                <input type="password" name="password" required placeholder="••••••••"
                    class="w-full px-4 py-3 bg-[#111827] border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" required placeholder="••••••••"
                    class="w-full px-4 py-3 bg-[#111827] border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm transition">
            </div>

            <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-medium rounded-xl shadow-lg shadow-indigo-500/25 transition duration-200 text-sm">
                Reset Password
            </button>
        </form>

    </div>

</body>
</html>