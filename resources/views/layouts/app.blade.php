<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Meta --}}
    <meta name="description"
        content="DompetKita – Aplikasi keuangan bersama untuk pasangan. Catat pemasukan, pengeluaran, dan raih target nabung berdua.">
    <meta name="keywords" content="keuangan bersama, dompet pasangan, catat keuangan, tabungan bersama">
    <meta name="author" content="DompetKita">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#db2777">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'DompetKita') – Keuangan Bersama">
    <meta property="og:description"
        content="Aplikasi keuangan bersama untuk pasangan. Catat pemasukan, pengeluaran, dan raih target nabung berdua.">
    <meta property="og:image" content="https://placehold.co/1200x630/db2777/ffffff?text=DompetKita+💗">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="DompetKita">
    <meta property="og:locale" content="id_ID">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'DompetKita') – Keuangan Bersama">
    <meta name="twitter:description" content="Aplikasi keuangan bersama untuk pasangan.">
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

    {{-- Favicon & Icons --}}
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>💗</text></svg>">
    <link rel="apple-touch-icon"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>💗</text></svg>">

    <title>@yield('title', 'DompetKita') – Keuangan Bersama</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                        display: ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>

    <style>
        :root {
            --pink: #f472b6;
            --pink-light: #fce7f3;
            --pink-mid: #fbcfe8;
            --pink-dark: #db2777;
            --pink-deep: #9d174d;
            --text-primary: #1a1a2e;
            --text-secondary: #64748b;
            --border: #f1f5f9;
            --surface: #ffffff;
            --bg: #fafafa;
        }

        * {
            scroll-behavior: smooth;
            box-sizing: border-box;
        }

        body {
            background: var(--bg);
            color: var(--text-primary);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        ::-webkit-scrollbar {
            width: 4px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--pink-mid);
            border-radius: 99px;
        }

        /* Desktop Layout Wrapper */
        .layout-wrapper {
            display: flex;
            height: 100dvh;
            width: 100%;
            overflow: hidden;
        }

        aside.sidebar {
            width: 260px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            background: var(--surface);
            border-right: 1px solid var(--border);
            padding: 24px 16px;
            overflow-y: auto;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 8px;
            margin-bottom: 32px;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--pink), var(--pink-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            flex-shrink: 0;
        }

        .logo-name {
            font-family: 'Poppins', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1;
        }

        .logo-sub {
            font-size: 11px;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        .partner-card {
            background: var(--pink-light);
            border: 1px solid var(--pink-mid);
            border-radius: 14px;
            padding: 14px;
            margin-bottom: 28px;
        }

        .partner-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--pink-dark);
            margin-bottom: 10px;
        }

        .partner-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 4px 0;
        }

        .partner-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            border: 1px solid var(--pink-mid);
            flex-shrink: 0;
            overflow: hidden;
        }

        .partner-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .partner-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .partner-role {
            font-size: 11px;
            color: var(--pink-dark);
        }

        .nav-section {
            flex: 1;
        }

        .nav-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-secondary);
            padding: 0 10px;
            margin-bottom: 8px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 12px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.15s;
            margin-bottom: 2px;
            cursor: pointer;
            border: 1px solid transparent;
        }

        .nav-link i {
            width: 18px;
            text-align: center;
            font-size: 15px;
            flex-shrink: 0;
        }

        .nav-link:hover {
            background: var(--pink-light);
            color: var(--pink-dark);
            border-color: var(--pink-mid);
        }

        .nav-link.active {
            background: var(--pink-light);
            color: var(--pink-dark);
            border-color: var(--pink-mid);
            font-weight: 600;
        }

        .nav-link.active i {
            color: var(--pink-dark);
        }

        .invite-block {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px 14px;
            margin-top: 20px;
            margin-bottom: 12px;
        }

        .invite-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }

        .invite-code-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .invite-code {
            font-family: 'DM Mono', monospace;
            font-size: 14px;
            font-weight: 700;
            color: var(--pink-dark);
            letter-spacing: 0.12em;
        }

        .copy-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-secondary);
            padding: 4px;
            border-radius: 6px;
            transition: all 0.15s;
            line-height: 1;
        }

        .copy-btn:hover {
            color: var(--pink-dark);
            background: var(--pink-light);
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 12px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            color: #ef4444;
            cursor: pointer;
            transition: all 0.15s;
            border: 1px solid transparent;
            width: 100%;
            background: none;
            text-align: left;
        }

        .logout-btn:hover {
            background: #fef2f2;
            border-color: #fecaca;
        }

        /* Main Content Desktop View */
        .main-content {
            flex: 1;
            height: 100%;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            padding: 36px 40px;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--pink), var(--pink-dark));
            color: white;
            border: none;
            border-radius: 12px;
            padding: 11px 22px;
            font-weight: 600;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(219, 39, 119, 0.25);
        }

        .btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: white;
            color: var(--text-secondary);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 11px 22px;
            font-weight: 500;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-ghost:hover {
            background: var(--pink-light);
            border-color: var(--pink-mid);
            color: var(--pink-dark);
        }

        .card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 24px;
        }

        .input-field {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 16px;
            color: var(--text-primary);
            font-size: 14px;
            width: 100%;
            outline: none;
            transition: all 0.15s;
            font-family: 'Poppins', sans-serif;
        }

        .input-field:focus {
            border-color: var(--pink);
            box-shadow: 0 0 0 3px rgba(244, 114, 182, 0.12);
        }

        .input-field::placeholder {
            color: #94a3b8;
        }

        .label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-secondary);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .income-badge {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 2px 10px;
            font-size: 12px;
            font-weight: 700;
        }

        .expense-badge {
            background: #fff1f2;
            color: #e11d48;
            border: 1px solid #fecdd3;
            border-radius: 8px;
            padding: 2px 10px;
            font-size: 12px;
            font-weight: 700;
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 10, 40, 0.35);
            backdrop-filter: blur(6px);
            z-index: 100;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-y: auto;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-box {
            background: white;
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 30px;
            width: 100%;
            max-width: 480px;
            max-height: 90vh;
            overflow-y: auto;
            animation: modalIn 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
            margin: auto;
        }

        @keyframes modalIn {
            from {
                opacity: 0;
                transform: scale(0.92) translateY(16px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .modal-title {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 22px;
        }

        .pink-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--pink);
            margin-right: 4px;
        }

        /* PERBAIKAN TOTAL: Fixed Bottom Nav Bar Styling */
        .bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 68px;
            background: white;
            border-top: 1px solid var(--border);
            z-index: 999;
            padding: 0 8px;
            padding-bottom: env(safe-area-inset-bottom);
            box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.04);
        }

        .bottom-nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            height: 100%;
        }

        .bottom-nav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            text-decoration: none;
            color: #94a3b8;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.02em;
            height: 100%;
            position: relative;
            transition: color 0.15s;
        }

        .bottom-nav-item i {
            font-size: 18px;
            line-height: 1;
            transition: transform 0.15s;
        }

        .bottom-nav-item.active {
            color: var(--pink-dark);
        }

        .bottom-nav-item.active::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 24px;
            height: 3px;
            border-radius: 0 0 4px 4px;
            background: var(--pink);
        }

        .bottom-nav-item.active i {
            transform: translateY(-1px);
        }

        .bottom-nav-center {
            width: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .bottom-nav-fab {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--pink), var(--pink-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(219, 39, 119, 0.3);
            transition: all 0.2s;
            transform: translateY(-4px);
            /* Efek melayang sedikit */
        }

        .bottom-nav-fab:active {
            transform: scale(0.92) translateY(-4px);
        }

        .page-header {
            margin-bottom: 32px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .page-subtitle {
            font-size: 14px;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        @media (max-width: 768px) {

            .modal-overlay {
                padding: 0;
                align-items: flex-start;
                /* Modal mulai dari atas */
            }

            .modal-box {
                max-height: none;
                /* Hilangkan batasan tinggi */
                min-height: 100vh;
                /* Full screen di mobile */
                border-radius: 0;
                /* Hilangkan border radius di mobile */
                padding: 20px 16px;
                padding-bottom: 40px;
                /* Extra padding bawah */
                margin: 0;
            }

            /* Perbaikan untuk input fields di mobile */
            .modal-box .input-field {
                font-size: 16px !important;
                /* Prevent zoom on iOS */
                padding: 14px 16px;
                /* Lebih besar, lebih mudah tap */
            }

            .modal-box select.input-field {
                font-size: 16px !important;
                padding: 14px 16px;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23666' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 12px center;
                padding-right: 40px;
                appearance: none;
                -webkit-appearance: none;
            }

            .modal-box .btn-primary,
            .modal-box .btn-ghost {
                padding: 14px 22px;
                /* Tombol lebih besar */
                font-size: 15px;
            }

            /* Header modal di mobile */
            .modal-box .modal-title {
                font-size: 18px;
            }

            /* Type toggle buttons lebih besar */
            .modal-box button[id^="type"] {
                padding: 12px !important;
                font-size: 14px !important;
            }

            .layout-wrapper {
                height: auto;
                min-height: 100vh;
                display: block;
                overflow: visible;
            }

            aside.sidebar {
                display: none;
            }

            .bottom-nav {
                display: flex;
            }

            #mobileTopbar {
                display: flex !important;
            }

            .main-content {
                display: block;
                width: 100%;
                height: auto;
                overflow: visible;
                padding: 24px 16px;
                padding-bottom: 120px;
                min-height: calc(100vh - 68px);
                padding-top: 84px;
            }

            .label {
                font-size: 13px;
                margin-bottom: 8px;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <header id="mobileTopbar"
        style="display:none; position:fixed; top:0; left:0; right:0; height:60px; background:white; border-bottom:1px solid var(--border); z-index:990; padding:0 16px; align-items:center; justify-content:space-between; box-shadow:0 1px 8px rgba(0,0,0,0.04);">

        {{-- Kiri: Logo + Nama Couple --}}
        <div style="display:flex; align-items:center; gap:8px;">
            <div
                style="width:32px; height:32px; border-radius:10px; background:linear-gradient(135deg,var(--pink),var(--pink-dark)); display:flex; align-items:center; justify-content:center; color:white; font-size:14px; flex-shrink:0;">
                <i class="fa-solid fa-heart"></i>
            </div>


            {{-- Avatar bertumpuk --}}
            <div style="display:flex; align-items:center;">
                @foreach(auth()->user()->couple->users ?? [] as $i => $member)
                    <div
                        style="width:30px; height:30px; border-radius:50%; background:var(--pink-light); border:2px solid white; display:flex; align-items:center; justify-content:center; font-size:15px; position:relative; overflow:hidden; {{ $i > 0 ? 'margin-left:-8px;' : '' }}">
                        @if($member->profile_photo_url)
                            <img src="{{ $member->profile_photo_url }}" alt="{{ $member->name }}"
                                style="width:100%; height:100%; object-fit:cover; display:block;" />
                        @else
                            {{ $member->avatar }}
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Kanan: Avatar + Kode Undangan --}}
        <div style="display:flex; align-items:center; gap:10px;">

            {{-- Divider --}}
            <div style="width:1px; height:28px; background:var(--border);"></div>

            {{-- Kode Undangan --}}
            <div style="display:flex; flex-direction:column; align-items:flex-end; gap:1px;">
                <span
                    style="font-size:9px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:var(--text-secondary);">Kode
                    Undangan</span>
                <div style="display:flex; align-items:center; gap:5px;">
                    <span
                        style="font-size:13px; font-weight:700; color:var(--pink-dark); letter-spacing:0.1em; font-family:monospace;">
                        {{ auth()->user()->couple->invite_code ?? '' }}
                    </span>
                    <button onclick="copyInvite()"
                        style="background:var(--pink-light); border:none; border-radius:6px; padding:3px 7px; cursor:pointer; color:var(--pink-dark); font-size:11px; line-height:1;">
                        <i class="fa-regular fa-copy"></i>
                    </button>
                </div>
            </div>

            {{-- Divider --}}
            <div style="width:1px; height:28px; background:var(--border);"></div>

            {{-- Logout --}}
            <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                @csrf
                <button type="submit"
                    style="background:none; border:none; cursor:pointer; width:34px; height:34px; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#ef4444; transition:all 0.15s;"
                    onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='none'"
                    title="Keluar">
                    <i class="fa-solid fa-right-from-bracket" style="font-size:15px;"></i>
                </button>
            </form>

        </div>
    </header>
    <div class="layout-wrapper">

        <aside class="sidebar">
            <div class="logo-area">
                <div class="logo-icon">
                    <i class="fa-solid fa-heart"></i>
                </div>
                <div>
                    <div class="logo-name">DompetKita</div>
                    <div class="logo-sub">{{ auth()->user()->couple->couple_name ?? 'Keuangan Bersama' }}</div>
                </div>
            </div>

            <div class="partner-card">
                <div class="partner-label">Pasangan</div>
                @foreach(auth()->user()->couple->users ?? [] as $member)
                    <div class="partner-item">
                        <div class="partner-avatar">
                            @if($member->profile_photo_url)
                                <img src="{{ $member->profile_photo_url }}" alt="{{ $member->name }}" />
                            @else
                                {{ $member->avatar }}
                            @endif
                        </div>
                        <div>
                            <div class="partner-name">{{ $member->name }}</div>
                            <div class="partner-role">{{ $member->role === 'owner' ? '👑 Pemilik' : '💝 Pasangan' }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="nav-section">
                <div class="nav-label">Menu Utama</div>
                <a href="{{ route('dashboard') }}"
                    class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-house"></i> Dashboard
                </a>
                <a href="{{ route('transactions.index') }}"
                    class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-arrow-right-arrow-left"></i> Transaksi
                </a>
                <a href="{{ route('debts.index') }}"
                    class="nav-link {{ request()->routeIs('debts.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-hand-holding-dollar"></i> Hutang / Piutang
                </a>
                <a href="{{ route('categories.index') }}"
                    class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-tag"></i> Kategori
                </a>
                <a href="{{ route('banks.index') }}"
                    class="nav-link {{ request()->routeIs('banks.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-building-columns"></i> Rekening
                </a>
                <a href="{{ route('targets.index') }}"
                    class="nav-link {{ request()->routeIs('targets.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-bullseye"></i> Target
                </a>
                <a href="{{ route('reports.index') }}"
                    class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-column"></i> Laporan
                </a>
            </div>

            <div class="invite-block">
                <div class="invite-label">Kode Undangan</div>
                <div class="invite-code-row">
                    <span class="invite-code">{{ auth()->user()->couple->invite_code ?? '' }}</span>
                    <button class="copy-btn" onclick="copyInvite()">
                        <i class="fa-regular fa-copy" style="font-size:13px;"></i>
                    </button>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fa-solid fa-right-from-bracket" style="font-size:14px;"></i> Keluar
                </button>
            </form>
        </aside>

        <main class="main-content">
            @yield('content')
        </main>
    </div>

    <!-- NAVBAR MOBILE -->
    <nav class="bottom-nav">
        <div class="bottom-nav-inner">
            <a href="{{ route('dashboard') }}"
                class="bottom-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i>
                <span>Beranda</span>
            </a>
            <a href="{{ route('transactions.index') }}"
                class="bottom-nav-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                <i class="fa-solid fa-arrow-right-arrow-left"></i>
                <span>Transaksi</span>
            </a>

            <div class="bottom-nav-center">
                <a href="{{ route('transactions.create') }}" class="bottom-nav-fab">
                    <i class="fa-solid fa-plus"></i>
                </a>
            </div>

            <a href="{{ route('categories.index') }}"
                class="bottom-nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="fa-solid fa-tag"></i>
                <span>Kategori</span>
            </a>
            <a href="javascript:void(0)" onclick="toggleMobileMenu()" id="btnMoreMenu" class="bottom-nav-item">
                <i class="fa-solid fa-bars"></i>
                <span>Lainnya</span>
            </a>
        </div>
    </nav>

    <!-- POP-UP MENU LAINNYA -->
    <div id="mobileMoreMenu"
        class="fixed inset-0 z-[998] hidden bg-black/40 backdrop-blur-sm transition-opacity duration-200"
        onclick="toggleMobileMenu()">
        <div class="absolute bottom-[80px] left-4 right-4 bg-white rounded-2xl p-4 shadow-xl border border-slate-100 animate-[slideUp_0.2s_ease-out]"
            onclick="event.stopPropagation()">
            <div class="grid grid-cols-3 gap-3 text-center">
                <a href="{{ route('banks.index') }}"
                    class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-pink-50 text-slate-600 {{ request()->routeIs('banks.*') ? 'text-pink-600 bg-pink-50' : '' }}">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-base"><i
                            class="fa-solid fa-building-columns"></i></div>
                    <span class="text-xs font-medium">Rekening</span>
                </a>
                <a href="{{ route('targets.index') }}"
                    class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-pink-50 text-slate-600 {{ request()->routeIs('targets.*') ? 'text-pink-600 bg-pink-50' : '' }}">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-base"><i
                            class="fa-solid fa-bullseye"></i></div>
                    <span class="text-xs font-medium">Target</span>
                </a>
                <a href="{{ route('debts.index') }}"
                    class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-pink-50 text-slate-600 {{ request()->routeIs('debts.*') ? 'text-pink-600 bg-pink-50' : '' }}">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-base"><i
                            class="fa-solid fa-hand-holding-dollar"></i></div>
                    <span class="text-xs font-medium">Hutang</span>
                </a>
                <a href="{{ route('reports.index') }}"
                    class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-pink-50 text-slate-600 {{ request()->routeIs('reports.*') ? 'text-pink-600 bg-pink-50' : '' }}">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-base"><i
                            class="fa-solid fa-chart-column"></i></div>
                    <span class="text-xs font-medium">Laporan</span>
                </a>
                <a href="{{ route('profile') }}"
                    class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-pink-50 text-slate-600 {{ request()->routeIs('profile') ? 'text-pink-600 bg-pink-50' : '' }}">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-base"><i
                            class="fa-solid fa-user"></i></div>
                    <span class="text-xs font-medium">Profil</span>
                </a>
            </div>
        </div>
    </div>


    <script>
        const Toast = Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false,
            timer: 3000, timerProgressBar: true,
            background: '#fff', color: '#1a1a2e',
            iconColor: '#db2777',
        });

        function copyInvite() {
            const code = '{{ auth()->user()->couple->invite_code ?? '' }}';
            navigator.clipboard.writeText(code);
            Toast.fire({ icon: 'success', title: 'Kode disalin!' });
        }

        function formatRupiah(num) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num);
        }

        function handleAjaxResponse(res, onSuccess) {
            if (res.success) {
                Toast.fire({ icon: 'success', title: res.message });
                if (onSuccess) onSuccess(res);
            } else {
                Swal.fire({ icon: 'error', title: 'Oops!', text: res.message, background: '#fff', color: '#1a1a2e', confirmButtonColor: '#db2777' });
            }
        }

        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMoreMenu');
            if (!menu) return;
            menu.classList.toggle('hidden');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const rupiahInputs = document.querySelectorAll('.rupiah');
            const normalize = value => value.replace(/[^\d]/g, '');
            const formatRupiah = value => {
                const digits = normalize(value);
                if (!digits) return '';
                return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            };

            rupiahInputs.forEach(input => {
                input.addEventListener('focus', function () {
                    this.value = normalize(this.value);
                });
                input.addEventListener('input', function () {
                    const cursorPos = this.selectionStart || 0;
                    const prevLength = this.value.length;
                    this.value = formatRupiah(this.value);
                    const newLength = this.value.length;
                    const diff = newLength - prevLength;
                    this.setSelectionRange(cursorPos + diff, cursorPos + diff);
                });
                input.addEventListener('blur', function () {
                    this.value = formatRupiah(this.value);
                });
                if (input.value) {
                    input.value = formatRupiah(input.value);
                }
            });

            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function () {
                    this.querySelectorAll('.rupiah').forEach(input => {
                        input.value = normalize(input.value);
                    });
                });
            });
        });

        async function deleteConfirm(url, callback) {
            const result = await Swal.fire({
                title: 'Yakin hapus?', text: 'Data yang dihapus tidak bisa dikembalikan!',
                icon: 'warning', showCancelButton: true,
                confirmButtonText: 'Hapus', cancelButtonText: 'Batal',
                confirmButtonColor: '#e11d48', cancelButtonColor: '#94a3b8',
                background: '#fff', color: '#1a1a2e',
            });
            if (!result.isConfirmed) return;

            const res = await $.ajax({
                url: url,
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content') }
            });
            handleAjaxResponse(res, callback);
        }
    </script>

    @stack('scripts')
</body>

</html>