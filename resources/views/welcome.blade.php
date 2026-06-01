<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#db2777">
    <meta name="description" content="DompetKita membantu pasangan Indonesia mencatat uang bersama, budget, tagihan, target tabungan, aset, dan net worth.">
    <meta name="keywords" content="keuangan pasangan, aplikasi keuangan pasangan, catat uang bersama, budget pasangan, dompet pasangan">
    <meta property="og:title" content="DompetKita - Keuangan Bersama Pasangan">
    <meta property="og:description" content="Kelola uang berdua dengan lebih rapi, transparan, dan nyaman.">
    <meta property="og:image" content="{{ asset('images/app-logo-dompetkita.png') }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="id_ID">
    <title>DompetKita - Keuangan Bersama Pasangan</title>
    <link rel="icon" type="image/png" href="{{ asset('images/app-logo-dompetkita.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/pwa-icon-dompetkita-192.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            box-sizing: border-box;
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            color: #10172a;
            background: #fff7fb;
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.82);
            border-bottom: 1px solid rgba(251, 207, 232, 0.8);
            backdrop-filter: blur(18px);
        }

        .hero-shell {
            background:
                radial-gradient(circle at 15% 12%, rgba(244, 114, 182, 0.22), transparent 28%),
                radial-gradient(circle at 88% 20%, rgba(251, 207, 232, 0.45), transparent 26%),
                linear-gradient(180deg, #fff7fb 0%, #ffffff 72%);
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border-radius: 18px;
            background: linear-gradient(135deg, #f472b6, #db2777);
            color: #fff;
            padding: 14px 22px;
            font-weight: 800;
            text-decoration: none;
            box-shadow: 0 18px 34px rgba(219, 39, 119, 0.24);
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 22px 42px rgba(219, 39, 119, 0.3);
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border-radius: 18px;
            border: 1px solid #fbcfe8;
            background: #fff;
            color: #be185d;
            padding: 14px 22px;
            font-weight: 800;
            text-decoration: none;
        }

        .phone-frame {
            width: min(360px, 100%);
            border-radius: 38px;
            border: 10px solid #1f2937;
            background: #fff;
            box-shadow: 0 26px 80px rgba(157, 23, 77, 0.22);
            overflow: hidden;
        }

        .phone-top {
            height: 34px;
            background: #1f2937;
            display: grid;
            place-items: center;
        }

        .phone-speaker {
            width: 82px;
            height: 6px;
            border-radius: 999px;
            background: #475569;
        }

        .app-preview {
            background: linear-gradient(180deg, #fff7fb, #fff);
            padding: 18px;
        }

        .mini-card {
            border: 1px solid #fce7f3;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.9);
            padding: 14px;
        }

        .feature-card {
            border: 1px solid #fce7f3;
            border-radius: 24px;
            background: #fff;
            padding: 24px;
            box-shadow: 0 12px 34px rgba(157, 23, 77, 0.06);
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            border-radius: 18px;
            display: grid;
            place-items: center;
            color: #db2777;
            background: #fce7f3;
            margin-bottom: 18px;
        }

        .section-band {
            background: #fff;
        }

        .step-dot {
            width: 44px;
            height: 44px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            background: #db2777;
            color: #fff;
            font-weight: 900;
            flex-shrink: 0;
        }

        .preview-bar {
            height: 8px;
            border-radius: 999px;
            background: #fce7f3;
            overflow: hidden;
        }

        .preview-bar span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #f472b6, #db2777);
        }

        @media (max-width: 768px) {
            .hero-shell {
                padding-top: 22px;
            }

            .phone-frame {
                border-width: 8px;
                border-radius: 32px;
            }
        }
    </style>
</head>

<body>
    <header class="glass-nav sticky top-0 z-50">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4 sm:px-6">
            <a href="/" class="flex items-center gap-3 text-slate-900 no-underline">
                <img src="{{ asset('images/app-logo-dompetkita.png') }}" alt="DompetKita" class="h-10 w-10 rounded-2xl object-cover">
                <div>
                    <div class="text-base font-extrabold leading-none">DompetKita</div>
                    <div class="mt-1 text-[11px] font-bold uppercase tracking-wide text-pink-600">Keuangan Pasangan</div>
                </div>
            </a>
            <nav class="hidden items-center gap-6 text-sm font-bold text-slate-500 md:flex">
                <a href="#fitur" class="hover:text-pink-600">Fitur</a>
                <a href="#cara-kerja" class="hover:text-pink-600">Cara Kerja</a>
                <a href="#privasi" class="hover:text-pink-600">Privasi</a>
            </nav>
            <div class="flex items-center gap-2">
                <a href="{{ route('login') }}" class="rounded-2xl px-4 py-2 text-sm font-extrabold text-slate-600 no-underline hover:bg-pink-50 hover:text-pink-700">Login</a>
                <a href="{{ route('register') }}" class="rounded-2xl bg-pink-600 px-4 py-2 text-sm font-extrabold text-white no-underline shadow-lg shadow-pink-100">Daftar</a>
            </div>
        </div>
    </header>

    <main>
        <section class="hero-shell">
            <div class="mx-auto grid min-h-[calc(100vh-64px)] max-w-6xl items-center gap-10 px-4 py-10 sm:px-6 lg:grid-cols-[1fr_420px] lg:py-16">
                <div>
                    <div class="mb-5 inline-flex items-center gap-2 rounded-full border border-pink-200 bg-white px-4 py-2 text-xs font-extrabold uppercase tracking-wide text-pink-700">
                        <i class="fa-solid fa-heart"></i>
                        Dibuat untuk pasangan Indonesia
                    </div>
                    <h1 class="max-w-3xl text-4xl font-black leading-tight tracking-tight text-slate-950 sm:text-5xl lg:text-6xl">
                        Kelola uang berdua tanpa drama spreadsheet.
                    </h1>
                    <p class="mt-5 max-w-2xl text-base font-medium leading-relaxed text-slate-600 sm:text-lg">
                        DompetKita membantu kamu dan pasangan mencatat pemasukan, pengeluaran, budget, tagihan, target tabungan, aset, sampai net worth dalam satu tempat yang rapi dan nyaman dipakai harian.
                    </p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('register') }}" class="btn-primary">
                            Mulai Gratis <i class="fa-solid fa-arrow-right"></i>
                        </a>
                        <a href="#fitur" class="btn-secondary">
                            Lihat Fitur <i class="fa-solid fa-chevron-down"></i>
                        </a>
                    </div>
                    <div class="mt-8 grid max-w-2xl gap-3 sm:grid-cols-3">
                        <div class="rounded-3xl border border-pink-100 bg-white/80 p-4">
                            <div class="text-2xl font-black text-pink-600">2 akun</div>
                            <div class="mt-1 text-xs font-bold text-slate-500">Untuk kamu dan pasangan</div>
                        </div>
                        <div class="rounded-3xl border border-pink-100 bg-white/80 p-4">
                            <div class="text-2xl font-black text-pink-600">1 dompet</div>
                            <div class="mt-1 text-xs font-bold text-slate-500">Data keuangan bersama</div>
                        </div>
                        <div class="rounded-3xl border border-pink-100 bg-white/80 p-4">
                            <div class="text-2xl font-black text-pink-600">Privat</div>
                            <div class="mt-1 text-xs font-bold text-slate-500">Mode rahasia tersedia</div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center lg:justify-end">
                    <div class="phone-frame">
                        <div class="phone-top">
                            <div class="phone-speaker"></div>
                        </div>
                        <div class="app-preview">
                            <div class="mb-4 flex items-center justify-between">
                                <div>
                                    <div class="text-[11px] font-extrabold uppercase tracking-wide text-pink-600">Ringkasan</div>
                                    <div class="text-xl font-black text-slate-900">Net Worth</div>
                                </div>
                                <div class="grid h-11 w-11 place-items-center rounded-2xl bg-pink-600 text-white">
                                    <i class="fa-solid fa-heart"></i>
                                </div>
                            </div>
                            <div class="mini-card mb-3 bg-pink-600 text-white">
                                <div class="text-xs font-bold opacity-80">Total Kekayaan Bersih</div>
                                <div class="mt-2 text-2xl font-black">Rp 18.450.000</div>
                                <div class="mt-2 text-[11px] font-semibold opacity-85">Saldo + aset + piutang - hutang</div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="mini-card">
                                    <div class="text-[11px] font-bold text-slate-400">Budget Makan</div>
                                    <div class="mt-2 text-lg font-black text-slate-900">62%</div>
                                    <div class="preview-bar mt-2"><span style="width:62%"></span></div>
                                </div>
                                <div class="mini-card">
                                    <div class="text-[11px] font-bold text-slate-400">Target Bali</div>
                                    <div class="mt-2 text-lg font-black text-slate-900">78%</div>
                                    <div class="preview-bar mt-2"><span style="width:78%"></span></div>
                                </div>
                            </div>
                            <div class="mini-card mt-3">
                                <div class="mb-3 flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-black text-slate-900">Kalender Tagihan</div>
                                        <div class="text-[11px] font-semibold text-slate-400">Juni 2026</div>
                                    </div>
                                    <i class="fa-solid fa-calendar-days text-pink-600"></i>
                                </div>
                                <div class="grid grid-cols-7 gap-1 text-center text-[10px] font-bold text-slate-400">
                                    @foreach(['S','S','R','K','J','S','M'] as $day)
                                        <span>{{ $day }}</span>
                                    @endforeach
                                    @for($day = 1; $day <= 21; $day++)
                                        <span class="{{ in_array($day, [5, 12, 18]) ? 'rounded-lg bg-pink-100 text-pink-700' : '' }} py-1">{{ $day }}</span>
                                    @endfor
                                </div>
                            </div>
                            <div class="mt-4 grid grid-cols-4 gap-2 text-center text-[10px] font-bold text-slate-400">
                                <div><i class="fa-solid fa-house block text-base text-pink-600"></i>Home</div>
                                <div><i class="fa-solid fa-arrow-right-arrow-left block text-base"></i>Trx</div>
                                <div><i class="fa-solid fa-bell block text-base"></i>Bill</div>
                                <div><i class="fa-solid fa-grip block text-base"></i>Menu</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="fitur" class="section-band py-16 sm:py-20">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <div class="mx-auto max-w-2xl text-center">
                    <p class="text-sm font-extrabold uppercase tracking-wide text-pink-600">Fitur Utama</p>
                    <h2 class="mt-2 text-3xl font-black text-slate-950 sm:text-4xl">Yang biasanya ribet, dibuat lebih gampang.</h2>
                    <p class="mt-4 text-sm leading-relaxed text-slate-500 sm:text-base">
                        Fokusnya bukan cuma catat uang, tapi bikin pasangan sama-sama paham kondisi keuangan.
                    </p>
                </div>
                <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach([
                        ['icon' => 'fa-arrow-right-arrow-left', 'title' => 'Transaksi Bersama', 'text' => 'Catat pemasukan dan pengeluaran dari rekening yang berbeda, lengkap dengan kategori dan pemilik transaksi.'],
                        ['icon' => 'fa-chart-pie', 'title' => 'Budget Per Kategori', 'text' => 'Atur budget hanya untuk kategori yang penting, seperti makanan, transport, atau hiburan.'],
                        ['icon' => 'fa-bell', 'title' => 'Reminder Tagihan', 'text' => 'Pantau listrik, internet, kontrakan, cicilan, dan subscription dalam kalender bulanan.'],
                        ['icon' => 'fa-bullseye', 'title' => 'Target Tabungan', 'text' => 'Bikin target liburan, dana darurat, atau rencana besar lain dengan progress yang mudah dipahami.'],
                        ['icon' => 'fa-gem', 'title' => 'Aset & Net Worth', 'text' => 'Lacak saldo rekening, aset, piutang, hutang, lalu lihat kekayaan bersih secara otomatis.'],
                        ['icon' => 'fa-file-export', 'title' => 'Export Laporan', 'text' => 'Rekap keuangan bisa diexport ke PDF atau Excel untuk diskusi bulanan bersama pasangan.'],
                    ] as $feature)
                        <article class="feature-card">
                            <div class="feature-icon"><i class="fa-solid {{ $feature['icon'] }}"></i></div>
                            <h3 class="text-lg font-black text-slate-900">{{ $feature['title'] }}</h3>
                            <p class="mt-3 text-sm leading-relaxed text-slate-500">{{ $feature['text'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="cara-kerja" class="bg-pink-50 py-16 sm:py-20">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <div class="grid gap-10 lg:grid-cols-[0.8fr_1fr] lg:items-center">
                    <div>
                        <p class="text-sm font-extrabold uppercase tracking-wide text-pink-600">Cara Kerja</p>
                        <h2 class="mt-2 text-3xl font-black text-slate-950 sm:text-4xl">Mulai berdua dalam beberapa menit.</h2>
                        <p class="mt-4 text-sm leading-relaxed text-slate-600 sm:text-base">
                            Cocok untuk pasangan yang baru mulai terbuka soal uang, atau yang sudah punya banyak rekening dan rencana bersama.
                        </p>
                    </div>
                    <div class="grid gap-4">
                        @foreach([
                            ['Daftar akun', 'Buat akun pertama, lalu sistem menyiapkan ruang keuangan pasangan.'],
                            ['Undang pasangan', 'Bagikan kode atau link undangan agar pasangan masuk ke ruang yang sama.'],
                            ['Catat dan evaluasi', 'Mulai input transaksi, rekening, budget, tagihan, target, dan cek rekap bersama.'],
                        ] as $index => $step)
                            <div class="flex gap-4 rounded-3xl border border-pink-100 bg-white p-5">
                                <div class="step-dot">{{ $index + 1 }}</div>
                                <div>
                                    <h3 class="font-black text-slate-900">{{ $step[0] }}</h3>
                                    <p class="mt-1 text-sm leading-relaxed text-slate-500">{{ $step[1] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section id="privasi" class="section-band py-16 sm:py-20">
            <div class="mx-auto grid max-w-6xl gap-8 px-4 sm:px-6 lg:grid-cols-2 lg:items-center">
                <div>
                    <p class="text-sm font-extrabold uppercase tracking-wide text-pink-600">Tetap Nyaman</p>
                    <h2 class="mt-2 text-3xl font-black text-slate-950 sm:text-4xl">Transparan, tapi tetap ada ruang pribadi.</h2>
                    <p class="mt-4 text-sm leading-relaxed text-slate-500 sm:text-base">
                        Tidak semua transaksi harus jadi bahan diskusi. DompetKita menyediakan mode privasi untuk transaksi tertentu, plus kontrol saldo rekening yang bisa disembunyikan atau ditampilkan.
                    </p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <span class="rounded-full bg-pink-50 px-4 py-2 text-xs font-extrabold text-pink-700">Mode Privasi</span>
                        <span class="rounded-full bg-pink-50 px-4 py-2 text-xs font-extrabold text-pink-700">Hide Saldo</span>
                        <span class="rounded-full bg-pink-50 px-4 py-2 text-xs font-extrabold text-pink-700">Data Per Pasangan</span>
                    </div>
                </div>
                <div class="rounded-[32px] border border-pink-100 bg-pink-50 p-6">
                    <div class="rounded-3xl bg-white p-5 shadow-sm">
                        <div class="mb-4 flex items-center justify-between">
                            <div>
                                <div class="text-sm font-black text-slate-900">Rekap Mingguan</div>
                                <div class="text-xs font-semibold text-slate-400">Diskusi uang jadi lebih tenang</div>
                            </div>
                            <i class="fa-solid fa-chart-line text-pink-600"></i>
                        </div>
                        @foreach([
                            ['Makan & jajan', 'Rp 580.000', '72%'],
                            ['Transport', 'Rp 210.000', '41%'],
                            ['Tagihan', 'Rp 750.000', '100%'],
                        ] as $row)
                            <div class="mb-4 last:mb-0">
                                <div class="mb-2 flex justify-between text-sm font-bold">
                                    <span>{{ $row[0] }}</span>
                                    <span class="text-pink-600">{{ $row[1] }}</span>
                                </div>
                                <div class="preview-bar"><span style="width:{{ $row[2] }}"></span></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-slate-950 py-16 text-white sm:py-20">
            <div class="mx-auto max-w-4xl px-4 text-center sm:px-6">
                <h2 class="text-3xl font-black sm:text-4xl">Siap bikin keuangan berdua lebih rapi?</h2>
                <p class="mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-slate-300 sm:text-base">
                    Mulai dari catatan kecil hari ini. Nanti pelan-pelan, kamu dan pasangan punya gambaran yang lebih jelas soal uang, target, dan kebiasaan belanja.
                </p>
                <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                    <a href="{{ route('register') }}" class="btn-primary">Buat Akun Sekarang</a>
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/15 px-5 py-3 text-sm font-extrabold text-white no-underline hover:bg-white/10">Sudah punya akun</a>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-white py-6">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-3 px-4 text-center text-xs font-semibold text-slate-400 sm:flex-row sm:px-6">
            <div>© {{ date('Y') }} DompetKita. Keuangan bersama untuk pasangan.</div>
            <div class="flex gap-4">
                <a href="{{ route('privacy') }}" class="text-slate-400 no-underline hover:text-pink-600">Privasi</a>
                <a href="{{ route('terms') }}" class="text-slate-400 no-underline hover:text-pink-600">Ketentuan</a>
            </div>
        </div>
    </footer>
</body>

</html>
