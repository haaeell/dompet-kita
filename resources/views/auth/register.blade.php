{{-- ========== auth/register.blade.php ========== --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="description" content="Daftar ke DompetKita untuk mulai mengelola keuangan bersama pasangan.">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#db2777">

    <title>Daftar - DompetKita</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
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
            background: rgba(255, 255, 255, .82);
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
            background: rgba(248, 250, 252, .88);
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

        .tab-btn {
            flex: 1;
            border-radius: 16px;
            padding: 12px 14px;
            font-size: 13px;
            font-weight: 800;
            color: #64748b;
            transition: .2s ease;
        }

        .tab-btn.active {
            background: #ffffff;
            color: #db2777;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .06);
        }

        .mini-card {
            background: rgba(255, 255, 255, .18);
            border: 1px solid rgba(255, 255, 255, .25);
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

        <section class="relative z-10 w-full max-w-6xl grid lg:grid-cols-2 gap-6 items-stretch">

            {{-- LEFT PANEL --}}
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
                            Mulai dari satu akun
                        </p>

                        <h2 class="font-display text-4xl font-extrabold leading-tight mb-6">
                            Catat bersama,<br>
                            pantau bersama.
                        </h2>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="mini-card rounded-2xl p-4">
                                <p class="text-white/70 text-xs font-bold tracking-widest mb-2">
                                    TRANSAKSI
                                </p>
                                <h3 class="text-2xl font-extrabold">Realtime</h3>
                            </div>

                            <div class="mini-card rounded-2xl p-4">
                                <p class="text-white/70 text-xs font-bold tracking-widest mb-2">
                                    UNDANGAN
                                </p>
                                <h3 class="text-2xl font-extrabold">Mudah</h3>
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
                            <i class="fa-solid fa-link"></i>
                        </div>
                        <p class="text-xs text-slate-500 font-semibold">Buat pasangan baru atau gabung pakai kode.</p>
                    </div>

                    <div class="rounded-3xl bg-white p-5 border border-emerald-100">
                        <div
                            class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                            <i class="fa-solid fa-chart-simple"></i>
                        </div>
                        <p class="text-xs text-slate-500 font-semibold">Laporan keuangan bersama lebih rapi.</p>
                    </div>
                </div>
            </div>

            {{-- REGISTER FORM --}}
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
                            PASANGAN HOT
                        </p>
                    </div>
                </div>

                <div class="mb-7">
                    <div
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-pink-50 text-pink-700 text-xs font-bold mb-5">
                        <i class="fa-solid fa-user-plus"></i>
                        Buat akun baru
                    </div>

                    <h2 class="font-display text-3xl sm:text-4xl font-extrabold text-slate-950 leading-tight">
                        Daftar DompetKita
                    </h2>

                    <p class="text-slate-500 mt-3 text-sm sm:text-base leading-relaxed">
                        Isi data akun, lalu pilih buat pasangan baru atau gabung menggunakan kode undangan.
                    </p>
                </div>

                <form onsubmit="doRegister(event)" class="space-y-5">

                    <div>
                        <label for="name" class="block text-sm font-bold text-slate-700 mb-2">Nama Lengkap</label>
                        <div class="input-group">
                            <i class="fa-regular fa-user input-icon"></i>
                            <input type="text" id="name" name="name" class="input-field"
                                placeholder="Masukkan nama kamu" required>
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                        <div class="input-group">
                            <i class="fa-regular fa-envelope input-icon"></i>
                            <input type="email" id="email" name="email" class="input-field" placeholder="Masukkan email"
                                required>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-bold text-slate-700 mb-2">Password</label>
                            <div class="input-group">
                                <i class="fa-solid fa-lock input-icon"></i>
                                <input type="password" id="password" name="password" class="input-field"
                                    placeholder="Min. 8 karakter" required>
                                <button type="button" onclick="togglePassword('password', 'passwordIcon')"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-pink-600">
                                    <i id="passwordIcon" class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="password_confirmation"
                                class="block text-sm font-bold text-slate-700 mb-2">Konfirmasi</label>
                            <div class="input-group">
                                <i class="fa-solid fa-shield-halved input-icon"></i>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="input-field" placeholder="Ulangi password" required>
                                <button type="button" onclick="togglePassword('password_confirmation', 'confirmIcon')"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-pink-600">
                                    <i id="confirmIcon" class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2">
                        <label class="block text-sm font-bold text-slate-700 mb-3">Tipe Pendaftaran</label>

                        <div class="flex gap-2 p-1.5 rounded-[20px] bg-slate-100">
                            <button type="button" id="tabCreate" class="tab-btn active" onclick="setTab('create')">
                                <i class="fa-solid fa-plus mr-1"></i>
                                Buat Pasangan
                            </button>

                            <button type="button" id="tabJoin" class="tab-btn" onclick="setTab('join')">
                                <i class="fa-solid fa-link mr-1"></i>
                                Gabung Kode
                            </button>
                        </div>

                        <input type="hidden" id="coupleAction" value="create">
                    </div>

                    <div id="panelCreate" class="rounded-3xl bg-white/70 border border-pink-100 p-5">
                        <label for="coupleName" class="block text-sm font-bold text-slate-700 mb-2">Nama
                            Pasangan</label>
                        <div class="input-group">
                            <i class="fa-solid fa-heart input-icon text-pink-400"></i>
                            <input type="text" id="coupleName" name="couple_name" class="input-field"
                                placeholder="Contoh: PASANGAN HOT">
                        </div>
                        <p class="text-xs font-semibold text-slate-400 mt-3">
                            Kamu akan menjadi pemilik pasangan dan mendapat kode undangan.
                        </p>
                    </div>

                    <div id="panelJoin" class="hidden rounded-3xl bg-white/70 border border-blue-100 p-5">
                        <label for="inviteCode" class="block text-sm font-bold text-slate-700 mb-2">Kode
                            Undangan</label>
                        <div class="input-group">
                            <i class="fa-solid fa-ticket input-icon text-blue-400"></i>
                            <input type="text" id="inviteCode" name="invite_code" maxlength="8"
                                class="input-field uppercase tracking-[.25em] text-center font-extrabold"
                                placeholder="C0OQTQWE" oninput="this.value = this.value.toUpperCase()">
                        </div>
                        <p class="text-xs font-semibold text-slate-400 mt-3">
                            Masukkan kode dari pasangan yang sudah punya akun.
                        </p>
                    </div>

                    <button type="submit" id="registerBtn" class="btn-primary">
                        <span id="btnText">Daftar Sekarang</span>
                    </button>
                </form>

                <div class="mt-7 text-center">
                    <p class="text-sm text-slate-500">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="font-extrabold text-pink-600 hover:text-pink-700">
                            Masuk
                        </a>
                    </p>
                </div>

            </div>
        </section>
    </main>

    <script>
        function setTab(tab) {
            document.getElementById('coupleAction').value = tab;

            document.getElementById('tabCreate').className = 'tab-btn' + (tab === 'create' ? ' active' : '');
            document.getElementById('tabJoin').className = 'tab-btn' + (tab === 'join' ? ' active' : '');

            document.getElementById('panelCreate').className = tab === 'create'
                ? 'rounded-3xl bg-white/70 border border-pink-100 p-5'
                : 'hidden rounded-3xl bg-white/70 border border-pink-100 p-5';

            document.getElementById('panelJoin').className = tab === 'join'
                ? 'rounded-3xl bg-white/70 border border-blue-100 p-5'
                : 'hidden rounded-3xl bg-white/70 border border-blue-100 p-5';
        }

        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';

            icon.classList.toggle('fa-eye', !isPassword);
            icon.classList.toggle('fa-eye-slash', isPassword);
        }

        async function doRegister(event) {
            event.preventDefault();

            const btn = document.getElementById('registerBtn');
            const btnText = document.getElementById('btnText');

            const data = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                password_confirmation: document.getElementById('password_confirmation').value,
                couple_action: document.getElementById('coupleAction').value,
                couple_name: document.getElementById('coupleName').value,
                invite_code: document.getElementById('inviteCode').value,
            };

            btn.disabled = true;
            btnText.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Memproses...';

            try {
                const response = await fetch('{{ route("register") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const res = await response.json();

                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Akun berhasil dibuat.',
                        confirmButtonColor: '#db2777',
                        background: '#ffffff',
                        color: '#111827',
                        timer: 1600
                    }).then(() => {
                        window.location.href = res.redirect;
                    });

                    return;
                }

                const message = res.errors
                    ? Object.values(res.errors).flat().join('\n')
                    : (res.message || 'Pendaftaran gagal.');

                Swal.fire({
                    icon: 'error',
                    title: 'Pendaftaran gagal',
                    text: message,
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
            btnText.textContent = 'Daftar Sekarang';
        }
    </script>

</body>

</html>