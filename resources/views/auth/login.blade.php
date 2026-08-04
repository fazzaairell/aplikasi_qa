<!DOCTYPE html>
<html lang="id" class="h-full bg-[#0b0f19]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - QA Platform</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full flex items-center justify-center font-sans text-slate-100">

    <div class="w-full max-w-md p-8 rounded-2xl bg-[#0b0f19]">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-white mb-2">Selamat datang kembali</h1>
            <p class="text-sm text-slate-400">Masuk ke akun QA Platform Anda</p>
        </div>

        <!-- Form Login -->
        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Input Email -->
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                        <!-- Ikon Email -->
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </span>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="andi@company.com" 
                        class="w-full pl-11 pr-4 py-3 bg-[#131b2e] border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm transition">
                </div>
            </div>

            <!-- Input Password -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-slate-300">Password</label>
                    <a href="#" class="text-xs text-indigo-400 hover:text-indigo-300 transition">Lupa password?</a>
                </div>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                        <!-- Ikon Gembok -->
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a
                    2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    <input type="password" name="password" required placeholder="••••••••" 
                        class="w-full pl-11 pr-10 py-3 bg-[#131b2e] border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 text-sm transition">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-500 cursor-pointer">
                        <!-- Ikon Mata -->
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </span>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded bg-[#131b2e] border-slate-800 text-indigo-600 focus:ring-indigo-500">
                <label for="remember" class="ml-2 text-sm text-slate-400">Ingat saya selama 30 hari</label>
            </div>

            <!-- Tombol Submit -->
            <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-medium rounded-xl shadow-lg shadow-indigo-500/25 transition duration-200 text-sm">
                Masuk ke Platform
            </button>
        </form>

        <!-- Divider -->
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-800"></div></div>
            <div class="relative flex justify-center text-xs uppercase"><span class="bg-[#0b0f19] px-3 text-slate-500">atau masuk sebagai</span></div>
        </div>

        <!-- Quick Login Cards (Sesuai Desain) -->
        <div class="grid grid-cols-2 gap-3">
            <!-- QA Lead Quick Login -->
            <button onclick="quickLogin('qalead@qa.com', 'password')" class="p-3 bg-[#131b2e] hover:bg-[#1a233a] border border-slate-800/80 rounded-xl text-left transition group">
                <div class="flex items-center space-x-2.5 mb-1">
                    <span class="w-6 h-6 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-xs font-bold">Q</span>
                    <span class="text-xs font-semibold text-slate-200 group-hover:text-indigo-400 transition">QA Lead</span>
                </div>
                <p class="text-[11px] text-slate-500 truncate">sari@company.com</p>
            </button>

            <!-- Developer Quick Login -->
            <button onclick="quickLogin('dev@qa.com', 'password')" class="p-3 bg-[#131b2e] hover:bg-[#1a233a] border border-slate-800/80 rounded-xl text-left transition group">
                <div class="flex items-center space-x-2.5 mb-1">
                    <span class="w-6 h-6 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center text-xs font-bold">D</span>
                    <span class="text-xs font-semibold text-slate-200 group-hover:text-amber-400 transition">Developer</span>
                </div>
                <p class="text-[11px] text-slate-500 truncate">donı@company.com</p>
            </button>
        </div>

        <!-- Footer -->
        <p class="text-center text-xs text-slate-500 mt-8">
            Belum punya akun? <a href="#" class="text-indigo-400 hover:underline">Hubungi Admin</a>
        </p>

    </div>

    <!-- Script kecil untuk fitur Quick Login agar otomatis mengisi form -->
    <script>
        function quickLogin(email, password) {
            document.querySelector('input[name="email"]').value = email;
            document.querySelector('input[name="password"]').value = password;
        }
    </script>
</body>
</html>