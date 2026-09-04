<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - QA Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }

        body {
            background: #0c0f1a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        /* Subtle noise texture overlay */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
        }

        /* Very subtle glow at top */
        body::after {
            content: '';
            position: fixed;
            top: -200px;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 400px;
            background: radial-gradient(ellipse, rgba(99, 102, 241, 0.07) 0%, transparent 70%);
            pointer-events: none;
        }

        .card {
            background: #111827;
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 20px;
            width: 100%;
            max-width: 420px;
            padding: 40px;
            position: relative;
            z-index: 1;
            animation: cardIn 0.4s ease both;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Logo mark */
        .logo-mark {
            width: 42px; 
            height: 42px;
            border-radius: 12px;
            display:flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 28px;
            overflow: hidden;
            /* Wajib agar gambar terpotong rapi mengikuti border-radius */
        }

        /* Pastikan tag img di dalam logo-mark ikut menyesuaikan ukuran kotak */
        .logo-mark img {
            width: 100%;
            height: 100%;
            object-fit: cover;

}

        h1 {
            font-size: 22px;
            font-weight: 700;
            color: #f8fafc;
            line-height: 1.3;
            margin-bottom: 6px;
            letter-spacing: -0.3px;
        }

        .subtitle {
            font-size: 13.5px;
            color: #64748b;
            margin-bottom: 32px;
        }

        /* Form elements */
        .form-group { margin-bottom: 16px; }

        label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #94a3b8;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .input-wrap { position: relative; }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #475569;
            pointer-events: none;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            background: #0c0f1a;
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 10px;
            color: #f1f5f9;
            font-size: 14px;
            padding: 11px 14px 11px 40px;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="text"]:focus {
            border-color: rgba(99, 102, 241, 0.5);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.08);
        }

        input::placeholder { color: #334155; }

        .pw-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #475569;
            cursor: pointer;
            padding: 2px;
            transition: color 0.2s;
        }
        .pw-toggle:hover { color: #94a3b8; }

        .row-between {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .forgot-link {
            font-size: 12px;
            color: #6366f1;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        .forgot-link:hover { color: #818cf8; }

        /* Remember me */
        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
        }
        .checkbox-row input[type="checkbox"] {
            width: 15px; height: 15px;
            padding: 0;
            accent-color: #6366f1;
            border: none;
            border-radius: 4px;
        }
        .checkbox-row span {
            font-size: 13px;
            color: #64748b;
        }

        /* Submit button */
        .btn-submit {
            width: 100%;
            background: #4f46e5;
            color: white;
            font-size: 14px;
            font-weight: 600;
            padding: 12px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
            margin-bottom: 28px;
        }
        .btn-submit:hover { background: #4338ca; transform: translateY(-1px); }
        .btn-submit:active { transform: translateY(0); }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.05);
        }
        .divider span {
            font-size: 11px;
            color: #334155;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        /* Quick login cards */
        .quick-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 28px; }

        .quick-btn {
            background: #0c0f1a;
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 10px;
            padding: 12px;
            text-align: left;
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
        }
        .quick-btn:hover {
            border-color: rgba(99, 102, 241, 0.25);
            background: rgba(79, 70, 229, 0.04);
        }

        .quick-btn-inner { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }

        .quick-avatar {
            width: 24px; height: 24px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
        }

        .quick-name { font-size: 12px; font-weight: 600; color: #cbd5e1; }
        .quick-email { font-size: 11px; color: #334155; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

        /* Footer */
        .footer-text {
            text-align: center;
            font-size: 12.5px;
            color: #334155;
        }
        .footer-text a { color: #6366f1; text-decoration: none; font-weight: 500; }
        .footer-text a:hover { color: #818cf8; }

        /* Error */
        .error-box {
            background: rgba(239, 68, 68, 0.06);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 13px;
            color: #f87171;
            margin-bottom: 20px;
        }
        .brand-text {
            font-size: 21px;
            font-weight: 700;
            color: #f8fafc;
            letter-spacing: -0.3px;
            text-shadow: 0 0 20px rgba(99, 102, 241, 0.35);
        }

        /* Welcome text animation - subtle fade in words */
        .word { display: inline-block; opacity: 0; animation: fadeWord 0.5s ease forwards; }
        .word:nth-child(1) { animation-delay: 0.05s; }
        .word:nth-child(2) { animation-delay: 0.15s; }
        .word:nth-child(3) { animation-delay: 0.25s; }
        .word:nth-child(4) { animation-delay: 0.35s; }

        .purple { color: #818cf8; }

        @keyframes fadeWord {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .subtitle-anim { opacity: 0; animation: fadeWord 0.4s ease 0.45s forwards; }
        .form-anim { opacity: 0; animation: fadeWord 0.4s ease 0.2s forwards; }
    </style>

</head>
<body>

    <div class="card">

        <!-- Logo & Brand -->
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 28px;">
            <div class="logo-mark" style="margin-bottom: 0;">
                <img src="{{ asset('image/icon-aldo.png') }}" alt="Logo">
            </div>
            <div style="width: 1px; height: 22px; background: rgba(255,255,255,0.08);"></div>
            <div>
                <span class="brand-text">Testify</span>
            </div>
        </div>

        <!-- Heading -->
        <h1>
            <span class="word">Welcome</span>
            <span class="word">to </span>
            <span class="word purple">QA</span>
            <span class="word purple">Management</span>
        </h1>
        <p class="subtitle subtitle-anim">Masuk untuk melanjutkan ke platform pengujian.</p>

        @if($errors->any())
            <div class="error-box form-anim">{{ $errors->first() }}</div>
        @endif

        <!-- Form -->
        <form action="{{ route('login') }}" method="POST" class="form-anim" x-data="{ showPass: false }">
            @csrf

            <!-- Email -->
            <div class="form-group">
                <label>Email</label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </span>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="nama@company.com">
                </div>
            </div>

            <!-- Password -->
            <div class="form-group">
                <div class="row-between">
                    <label style="margin-bottom:0">Password</label>
                    <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>
                </div>
                <div class="input-wrap" x-data="{ show: false }">
                    <span class="input-icon">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    <input :type="show ? 'text' : 'password'" name="password" required placeholder="••••••••" style="padding-right: 40px;">
                    <button type="button" class="pw-toggle" @click="show = !show">
                        <svg x-show="!show" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="show" width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
            </div>

            <!-- Remember -->
            <div class="checkbox-row">
                <input type="checkbox" name="remember" id="remember">
                <span>Ingat saya selama 30 hari</span>
            </div>

            <button type="submit" class="btn-submit">Masuk</button>
        </form>

        <p class="footer-text">Belum punya akun? <a href="#">Hubungi PA MAKS</a></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>