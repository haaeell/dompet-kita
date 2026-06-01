{{-- ========== auth/login.blade.php ========== --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Masuk - DompetKita</title>

    <meta name="title" content="Masuk - DompetKita">
    <meta name="description"
        content="Masuk ke DompetKita untuk mengelola keuangan bersama, mencatat pemasukan dan pengeluaran, memantau target, hutang piutang, serta laporan keuangan dalam satu aplikasi modern.">
    <meta name="keywords"
        content="DompetKita, aplikasi keuangan, keuangan pasangan, pencatatan keuangan, pengeluaran, pemasukan, target keuangan, laporan keuangan">
    <meta name="author" content="DompetKita">
    <meta name="application-name" content="DompetKita">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#db2777">
    <meta name="color-scheme" content="light">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('logo.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('logo.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('logo.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('logo.png') }}">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:locale" content="id_ID">
    <meta property="og:site_name" content="DompetKita">
    <meta property="og:title" content="Masuk - DompetKita">
    <meta property="og:description"
        content="Kelola keuangan bersama dengan lebih rapi, transparan, dan modern melalui DompetKita.">
    <meta property="og:image" content="{{ asset('logo.png') }}">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:alt" content="Logo DompetKita">
    <meta property="og:url" content="{{ url()->current() }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Masuk - DompetKita">
    <meta name="twitter:description"
        content="Kelola transaksi, target, hutang piutang, dan laporan keuangan bersama dalam satu dashboard.">
    <meta name="twitter:image" content="{{ asset('logo.png') }}">
    <meta name="twitter:image:alt" content="Logo DompetKita">

    <!-- Mobile App -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="DompetKita">

    <!-- Microsoft -->
    <meta name="msapplication-TileColor" content="#db2777">
    <meta name="msapplication-TileImage" content="{{ asset('logo.png') }}">

    <title>Masuk - DompetKita</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .font-display {
            font-family: 'Bricolage Grotesque', sans-serif;
        }

        body {
            background:
                radial-gradient(circle at top left, rgba(236, 72, 153, .16), transparent 34%),
                radial-gradient(circle at bottom right, rgba(59, 130, 246, .10), transparent 30%),
                #fff7fb;
            color: #141827;
        }

        .glass-card {
            background: rgba(255, 255, 255, .78);
            border: 1px solid rgba(255, 255, 255, .75);
            box-shadow: 0 30px 90px rgba(219, 39, 119, .12);
            backdrop-filter: blur(18px);
        }

        .brand-gradient {
            background: linear-gradient(135deg, #ec4899, #db2777, #be185d);
        }

        .soft-gradient {
            background:
                radial-gradient(circle at 15% 20%, rgba(255, 255, 255, .35), transparent 24%),
                linear-gradient(135deg, #ec4899 0%, #db2777 48%, #be185d 100%);
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 14px;
        }

        .input-field {
            width: 100%;
            border-radius: 18px;
            border: 1px solid #eef2f7;
            background: rgba(248, 250, 252, .86);
            padding: 15px 48px 15px 44px;
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            outline: none;
            transition: .2s ease;
        }

        .input-field::placeholder {
            color: #9ca3af;
            font-weight: 500;
        }

        .input-field:focus {
            background: #ffffff;
            border-color: #ec4899;
            box-shadow: 0 0 0 4px rgba(236, 72, 153, .12);
        }

        .btn-primary {
            width: 100%;
            border-radius: 18px;
            padding: 15px 20px;
            color: white;
            font-weight: 800;
            font-size: 15px;
            background: linear-gradient(135deg, #ec4899, #db2777);
            box-shadow: 0 18px 36px rgba(219, 39, 119, .28);
            transition: .2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 22px 44px rgba(219, 39, 119, .35);
        }

        .btn-primary:disabled {
            opacity: .75;
            cursor: not-allowed;
            transform: none;
        }

        .mini-card {
            background: rgba(255, 255, 255, .18);
            border: 1px solid rgba(255, 255, 255, .25);
        }

        .floating-dot {
            position: absolute;
            border-radius: 999px;
            filter: blur(.2px);
            opacity: .65;
        }

        @media (max-width: 768px) {
            body {
                background: #fff7fb;
            }
        }
    </style>
</head>

<body class="min-h-screen overflow-x-hidden">

    <main class="relative min-h-screen flex items-center justify-center px-4 py-8">

        <div class="floating-dot w-24 h-24 bg-pink-200 top-10 left-8"></div>
        <div class="floating-dot w-16 h-16 bg-blue-100 bottom-12 right-10"></div>
        <div class="floating-dot w-10 h-10 bg-rose-100 top-1/3 right-24"></div>

        <section class="relative z-10 w-full max-w-6xl grid lg:grid-cols-2 gap-6 items-stretch">

            {{-- LEFT BRAND PANEL --}}
            <div class="hidden lg:flex glass-card rounded-[34px] p-8 flex-col justify-between overflow-hidden relative">

                <div class="absolute -top-24 -right-24 w-72 h-72 rounded-full bg-pink-200/40"></div>
                <div class="absolute -bottom-24 -left-24 w-72 h-72 rounded-full bg-blue-100/60"></div>

                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-10">
                        <div
                            class="w-13 h-13 rounded-2xl brand-gradient flex items-center justify-center shadow-lg shadow-pink-200">
                            <i class="fa-solid fa-heart text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="font-display text-2xl font-extrabold text-slate-950 leading-none">
                                DompetKita
                            </h1>
                            <p class="text-xs font-bold tracking-[.18em] text-slate-400 mt-1">
                                PASANGAN
                            </p>
                        </div>
                    </div>

                    <div class="soft-gradient rounded-[32px] p-8 text-white shadow-2xl shadow-pink-200/60">
                        <p class="text-white/80 font-semibold mb-2">
                            Keuangan bersama
                        </p>

                        <h2 class="font-display text-4xl font-extrabold leading-tight mb-6">
                            Lebih rapi,<br>
                            lebih transparan.
                        </h2>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="mini-card rounded-2xl p-4">
                                <p class="text-white/70 text-xs font-bold tracking-widest mb-2">
                                    SALDO BERSIH
                                </p>
                                <h3 class="text-2xl font-extrabold">Rp 1,9 jt</h3>
                            </div>

                            <div class="mini-card rounded-2xl p-4">
                                <p class="text-white/70 text-xs font-bold tracking-widest mb-2">
                                    PIUTANG
                                </p>
                                <h3 class="text-2xl font-extrabold">Rp 3,3 jt</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative z-10 grid grid-cols-3 gap-4 mt-8">
                    <div class="rounded-3xl bg-white p-5 border border-pink-100">
                        <div
                            class="w-10 h-10 rounded-2xl bg-pink-50 text-pink-600 flex items-center justify-center mb-4">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                        <p class="text-xs text-slate-500 font-semibold">Catat pemasukan dan pengeluaran harian.</p>
                    </div>

                    <div class="rounded-3xl bg-white p-5 border border-blue-100">
                        <div
                            class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                            <i class="fa-solid fa-chart-pie"></i>
                        </div>
                        <p class="text-xs text-slate-500 font-semibold">Pantau laporan bersama lebih mudah.</p>
                    </div>

                    <div class="rounded-3xl bg-white p-5 border border-emerald-100">
                        <div
                            class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <p class="text-xs text-slate-500 font-semibold">Lokasi, target, dan chat dalam satu aplikasi.
                        </p>
                    </div>
                </div>
            </div>

            {{-- RIGHT LOGIN FORM --}}
            <div class="glass-card rounded-[34px] p-6 sm:p-8 lg:p-10">

                <div class="lg:hidden flex items-center gap-3 mb-8">
                    <div
                        class="w-12 h-12 rounded-2xl brand-gradient flex items-center justify-center shadow-lg shadow-pink-200">
                        <i class="fa-solid fa-heart text-white"></i>
                    </div>
                    <div>
                        <h1 class="font-display text-2xl font-extrabold text-slate-950 leading-none">
                            DompetKita
                        </h1>
                        <p class="text-xs font-bold tracking-[.16em] text-slate-400 mt-1">
                            PASANGAN
                        </p>
                    </div>
                </div>

                <div class="mb-8">
                    <div
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-pink-50 text-pink-700 text-xs font-bold mb-5">
                        <i class="fa-solid fa-shield-heart"></i>
                        Aman untuk data keuangan bersama
                    </div>

                    <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-slate-950 leading-tight">
                        Masuk ke akun
                    </h2>

                    <p class="text-slate-500 mt-3 text-sm sm:text-base leading-relaxed">
                        Kelola transaksi, hutang piutang, target, dan laporan pasangan dalam satu dashboard.
                    </p>
                </div>

                <form onsubmit="doLogin(event)" class="space-y-5">
                    <div>
                        <label for="email" class="block text-sm font-bold text-slate-700 mb-2">
                            Email
                        </label>
                        <div class="input-group">
                            <i class="fa-regular fa-envelope input-icon"></i>
                            <input type="email" id="email" name="email" autocomplete="email"
                                placeholder="Masukkan email" class="input-field" required>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="password" class="block text-sm font-bold text-slate-700">
                                Password
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                    class="text-xs font-bold text-pink-600 hover:text-pink-700">
                                    Lupa password?
                                </a>
                            @endif
                        </div>

                        <div class="input-group">
                            <i class="fa-solid fa-lock input-icon"></i>
                            <input type="password" id="password" name="password" autocomplete="current-password"
                                placeholder="Masukkan password" class="input-field" required>

                            <button type="button" onclick="togglePassword()"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-pink-600 transition">
                                <i id="passwordIcon" class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" id="loginBtn" class="btn-primary">
                        <span id="btnText">
                            Masuk
                        </span>
                    </button>
                </form>

                <div class="mt-7 text-center">
                    <p class="text-sm text-slate-500">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="font-extrabold text-pink-600 hover:text-pink-700">
                            Daftar sekarang
                        </a>
                    </p>
                    <p class="mt-3 text-xs text-slate-400">
                        Dengan masuk, kamu menyetujui
                        <a href="{{ route('terms') }}" class="font-bold text-pink-600">Syarat Penggunaan</a>
                        dan
                        <a href="{{ route('privacy') }}" class="font-bold text-pink-600">Kebijakan Privasi</a>.
                    </p>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100">
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="rounded-2xl bg-white/70 border border-pink-50 p-3">
                            <i class="fa-solid fa-receipt text-pink-500 mb-2"></i>
                            <p class="text-[11px] font-bold text-slate-500">Transaksi</p>
                        </div>

                        <div class="rounded-2xl bg-white/70 border border-blue-50 p-3">
                            <i class="fa-solid fa-chart-simple text-blue-500 mb-2"></i>
                            <p class="text-[11px] font-bold text-slate-500">Laporan</p>
                        </div>

                        <div class="rounded-2xl bg-white/70 border border-emerald-50 p-3">
                            <i class="fa-solid fa-bullseye text-emerald-500 mb-2"></i>
                            <p class="text-[11px] font-bold text-slate-500">Target</p>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </main>

    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const icon = document.getElementById('passwordIcon');

            const isPassword = password.type === 'password';
            password.type = isPassword ? 'text' : 'password';

            icon.classList.toggle('fa-eye', !isPassword);
            icon.classList.toggle('fa-eye-slash', isPassword);
        }

        async function doLogin(event) {
            event.preventDefault();

            const btn = document.getElementById('loginBtn');
            const btnText = document.getElementById('btnText');

            btn.disabled = true;
            btnText.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Memproses...';

            try {
                const response = await fetch('{{ route("login") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        email: document.getElementById('email').value,
                        password: document.getElementById('password').value,
                        remember: document.getElementById('remember')?.checked ?? false
                    })
                });

                const res = await response.json();

                if (res.success) {
                    window.location.href = res.redirect;
                    return;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Login gagal',
                    text: res.message || 'Email atau password tidak sesuai.',
                    confirmButtonColor: '#db2777',
                    background: '#ffffff',
                    color: '#111827'
                });

            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi kesalahan',
                    text: 'Silakan coba lagi beberapa saat.',
                    confirmButtonColor: '#db2777',
                    background: '#ffffff',
                    color: '#111827'
                });
            }

            btn.disabled = false;
            btnText.textContent = 'Masuk';
        }
    </script>

</body>

</html>