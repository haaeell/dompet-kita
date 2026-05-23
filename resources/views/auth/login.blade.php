{{-- ========== auth/login.blade.php ========== --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Meta --}}
    <meta name="description" content="Masuk ke DompetKita – Aplikasi keuangan bersama untuk pasangan.">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#db2777">
    <meta name="author" content="DompetKita">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="Masuk – DompetKita">
    <meta property="og:description" content="Masuk ke DompetKita dan kelola keuangan bersama pasangan.">
    <meta property="og:image" content="https://placehold.co/1200x630/db2777/ffffff?text=DompetKita+💗">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="DompetKita">
    <meta property="og:locale" content="id_ID">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Masuk – DompetKita">
    <meta name="twitter:description" content="Masuk ke DompetKita dan kelola keuangan bersama pasangan.">
    <meta name="twitter:image" content="{{ asset('images/og-image.png') }}">

    {{-- PWA & Mobile --}}
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="DompetKita">
    <meta name="application-name" content="DompetKita">
    <meta name="msapplication-TileColor" content="#db2777">
    <meta name="msapplication-tap-highlight" content="no">
    <meta name="format-detection" content="telephone=no">

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>💗</text></svg>">
    <link rel="apple-touch-icon"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>💗</text></svg>">

    <title>Masuk – DompetKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1f2937;
        }

        .gradient-text {
            background: linear-gradient(135deg, #ec4899, #f43f5e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .input-field {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 14px 18px;
            color: #1f2937;
            font-size: 15px;
            width: 100%;
            outline: none;
            transition: all 0.2s;
        }

        .input-field:focus {
            border-color: #ec4899;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.15);
        }

        .input-field::placeholder {
            color: #9ca3af;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ec4899, #f43f5e);
            color: white;
            border: none;
            border-radius: 14px;
            padding: 15px 24px;
            font-weight: 700;
            font-size: 15px;
            width: 100%;
            cursor: pointer;
            transition: all 0.2s;
            font-family: inherit;
        }

        .btn-primary:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            box-shadow: 0 10px 25px rgba(244, 63, 94, 0.3);
        }

        .mesh {
            position: fixed;
            inset: 0;
            pointer-events: none;
            background: radial-gradient(ellipse at 20% 20%, rgba(236, 72, 153, 0.08) 0%, transparent 50%), radial-gradient(ellipse at 80% 80%, rgba(244, 63, 94, 0.05) 0%, transparent 50%);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center px-4">
    <div class="mesh"></div>
    <div class="relative z-10 w-full max-w-md">
        <div class="text-center mb-8">
            <div class="text-5xl mb-3">💑</div>
            <h1 class="font-display font-bold text-4xl gradient-text mb-2"
                style="font-family: 'Bricolage Grotesque', sans-serif;">DompetKita</h1>
            <p class="text-gray-500">Kelola keuangan bersama pasangan</p>
        </div>

        <div
            style="background: #ffffff; border: 1px solid #f3f4f6; border-radius: 24px; padding: 36px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);">
            <h2 class="text-xl font-bold text-gray-800 mb-6" style="font-family: 'Bricolage Grotesque', sans-serif;">
                Masuk
                ke Akun</h2>
            <div class="space-y-4">
                <div>
                    <input type="email" id="email" placeholder="Email kamu" class="input-field">
                </div>
                <div>
                    <input type="password" id="password" placeholder="Password" class="input-field">
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="remember"
                        class="rounded text-pink-500 focus:ring-pink-400 border-gray-300">
                    <label for="remember" class="text-sm text-gray-500 cursor-pointer select-none">Ingat saya</label>
                </div>
            </div>
            <button onclick="doLogin()" class="btn-primary mt-6">Masuk</button>
            <p class="text-center text-gray-400 text-sm mt-5">
                Belum punya akun? <a href="{{ route('register') }}"
                    class="hover:text-pink-600 font-semibold transition-colors" style="color: #ec4899;">Daftar
                    sekarang</a>
            </p>
        </div>
    </div>

    <script>
        async function doLogin() {
            const btn = document.querySelector('.btn-primary');
            btn.textContent = 'Memproses...'; btn.disabled = true;
            const res = await fetch('{{ route("login") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                body: JSON.stringify({ email: document.getElementById('email').value, password: document.getElementById('password').value, remember: document.getElementById('remember').checked })
            }).then(r => r.json()).catch(() => ({ success: false, message: 'Terjadi kesalahan!' }));

            if (res.success) { window.location.href = res.redirect; }
            else {
                Swal.fire({
                    icon: 'error',
                    title: 'Login Gagal',
                    text: res.message,
                    background: '#ffffff',
                    color: '#1f2937',
                    confirmButtonColor: '#ec4899'
                });
                btn.textContent = 'Masuk'; btn.disabled = false;
            }
        }
        document.addEventListener('keydown', e => { if (e.key === 'Enter') doLogin(); });
    </script>
</body>

</html>