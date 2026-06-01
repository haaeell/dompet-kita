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
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">

    {{-- Favicon & Icons --}}
    <link rel="icon" type="image/png" href="{{ asset('images/app-logo-dompetkita.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/pwa-icon-dompetkita-192.png') }}">

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
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

        .flatpickr-input[readonly] {
            background: #fff;
            cursor: pointer;
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

        .invite-link-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px dashed var(--border);
        }

        .invite-link {
            flex: 1;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-secondary);
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

        .bottom-nav {
            display: none;
            position: fixed;
            left: 14px;
            right: 14px;
            bottom: 12px;
            min-height: 70px;
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(251, 207, 232, 0.8);
            border-radius: 26px;
            z-index: 999;
            padding: 8px 10px calc(8px + env(safe-area-inset-bottom));
            box-shadow: 0 18px 42px rgba(157, 23, 77, 0.18);
            backdrop-filter: blur(16px);
        }

        .bottom-nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            height: 100%;
            gap: 4px;
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
            font-weight: 700;
            letter-spacing: 0.02em;
            min-width: 0;
            height: 54px;
            position: relative;
            transition: color 0.18s ease, transform 0.18s ease;
            border-radius: 18px;
        }

        .bottom-nav-item i {
            font-size: 18px;
            line-height: 1;
            transition: transform 0.18s ease;
        }

        .bottom-nav-item span {
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .bottom-nav-item:active {
            transform: translateY(1px) scale(0.96);
        }

        .bottom-nav-item.active {
            color: var(--pink-dark);
            background: linear-gradient(180deg, #fdf2f8, #ffffff);
            box-shadow: inset 0 0 0 1px rgba(251, 207, 232, 0.75);
        }

        .bottom-nav-item.active::before {
            content: '';
            position: absolute;
            top: 7px;
            left: 50%;
            transform: translateX(-50%);
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--pink);
            box-shadow: 0 0 0 4px rgba(244, 114, 182, 0.12);
        }

        .bottom-nav-item.active i {
            transform: translateY(-2px) scale(1.06);
        }

        .bottom-nav-center {
            width: 66px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .bottom-nav-fab {
            position: relative;
            width: 58px;
            height: 58px;
            border-radius: 22px;
            background: linear-gradient(135deg, var(--pink), var(--pink-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            text-decoration: none;
            box-shadow: 0 12px 26px rgba(219, 39, 119, 0.34);
            transition: all 0.2s;
            transform: translateY(-14px);
            border: 4px solid white;
        }

        .bottom-nav-fab::after {
            content: '';
            position: absolute;
            top: 7px;
            right: 8px;
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #fff7ed;
            box-shadow: -18px 26px 0 -2px rgba(255, 255, 255, 0.75);
        }

        .bottom-nav-fab .fab-heart {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            color: var(--pink-dark);
            border: 1px solid var(--pink-mid);
            font-size: 10px;
            box-shadow: 0 8px 16px rgba(219, 39, 119, 0.18);
            animation: lovePulse 1.35s ease-in-out infinite;
        }

        .bottom-nav-fab:active {
            transform: scale(0.94) translateY(-14px);
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

        .app-splash {
            position: fixed;
            inset: 0;
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background:
                radial-gradient(circle at 28% 18%, rgba(252, 231, 243, 0.95) 0 18%, transparent 32%),
                radial-gradient(circle at 78% 78%, rgba(251, 207, 232, 0.85) 0 16%, transparent 32%),
                linear-gradient(145deg, #fff7fb 0%, #ffffff 48%, #fdf2f8 100%);
            transition: opacity 0.36s ease, visibility 0.36s ease;
        }

        .splash-seen .app-splash {
            display: none;
        }

        .app-splash.is-hiding {
            opacity: 0;
            visibility: hidden;
        }

        .page-love-loader {
            position: fixed;
            inset: 0;
            z-index: 1800;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(255, 247, 251, 0.76);
            backdrop-filter: blur(8px);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.18s ease, visibility 0.18s ease;
        }

        .page-love-loader.is-active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .love-loader-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            min-width: 170px;
            padding: 18px 20px;
            border-radius: 22px;
            background: white;
            border: 1px solid var(--pink-mid);
            box-shadow: 0 18px 44px rgba(219, 39, 119, 0.18);
            transform: translateY(8px) scale(0.96);
            transition: transform 0.18s ease;
        }

        .page-love-loader.is-active .love-loader-box {
            transform: translateY(0) scale(1);
        }

        .love-loader-icon {
            position: relative;
            width: 58px;
            height: 46px;
        }

        .love-loader-icon i {
            position: absolute;
            color: var(--pink-dark);
            filter: drop-shadow(0 8px 12px rgba(219, 39, 119, 0.2));
            animation: lovePulse 0.95s ease-in-out infinite;
        }

        .love-loader-icon i:nth-child(1) {
            left: 16px;
            top: 8px;
            font-size: 28px;
        }

        .love-loader-icon i:nth-child(2) {
            left: 0;
            top: 18px;
            color: #fb7185;
            font-size: 18px;
            animation-delay: 0.16s;
        }

        .love-loader-icon i:nth-child(3) {
            right: 0;
            top: 0;
            color: #f9a8d4;
            font-size: 16px;
            animation-delay: 0.28s;
        }

        .love-loader-text {
            margin: 0;
            color: var(--pink-deep);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        .splash-card {
            width: min(320px, 100%);
            text-align: center;
            transform: translateY(0);
            animation: splashFloat 1.8s ease-in-out infinite;
        }

        .splash-icon-wrap {
            position: relative;
            width: 118px;
            height: 118px;
            margin: 0 auto 22px;
            animation: splashWiggle 2.4s ease-in-out infinite;
        }

        .splash-icon {
            position: relative;
            z-index: 2;
            width: 100%;
            height: 100%;
            border-radius: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            box-shadow: 0 18px 42px rgba(219, 39, 119, 0.28);
            padding: 12px;
            overflow: hidden;
        }

        .splash-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        .splash-orbit {
            position: absolute;
            inset: -18px;
            z-index: 1;
            border-radius: 50%;
            animation: splashOrbit 5.5s linear infinite;
        }

        .splash-orbit i {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: white;
            color: #fb7185;
            box-shadow: 0 8px 18px rgba(219, 39, 119, 0.14);
            font-size: 12px;
        }

        .splash-orbit i:nth-child(1) {
            top: 2px;
            left: 22px;
        }

        .splash-orbit i:nth-child(2) {
            right: 0;
            top: 62px;
            color: #f59e0b;
        }

        .splash-orbit i:nth-child(3) {
            bottom: 6px;
            left: 42px;
            color: var(--pink-dark);
        }

        .splash-bubble {
            position: absolute;
            z-index: 3;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            color: var(--pink-dark);
            border: 1px solid var(--pink-mid);
            box-shadow: 0 10px 22px rgba(219, 39, 119, 0.16);
            font-size: 16px;
        }

        .splash-bubble.one {
            top: -8px;
            right: -8px;
            animation: splashPop 1.5s ease-in-out infinite, splashKiss 2.2s ease-in-out infinite;
        }

        .splash-bubble.two {
            bottom: 4px;
            left: -12px;
            animation: splashPop 1.5s ease-in-out 0.24s infinite, splashKiss 2.2s ease-in-out 0.35s infinite;
        }

        .splash-sweet-row {
            display: flex;
            justify-content: center;
            gap: 7px;
            margin: 14px 0 6px;
        }

        .splash-sweet-row span {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: var(--pink);
            opacity: 0.52;
            animation: splashBeat 1.1s ease-in-out infinite;
        }

        .splash-sweet-row span:nth-child(2) {
            width: 11px;
            height: 11px;
            background: var(--pink-dark);
            opacity: 0.8;
            animation-delay: 0.15s;
        }

        .splash-sweet-row span:nth-child(3) {
            animation-delay: 0.3s;
        }

        .splash-title {
            margin: 0;
            color: var(--text-primary);
            font-size: 28px;
            font-weight: 800;
            line-height: 1.1;
        }

        .splash-subtitle {
            margin: 8px 0 18px;
            color: var(--text-secondary);
            font-size: 13px;
            font-weight: 500;
        }

        .splash-loader {
            position: relative;
            width: 150px;
            height: 8px;
            margin: 0 auto;
            border-radius: 99px;
            overflow: hidden;
            background: var(--pink-light);
        }

        .splash-loader::after {
            content: '';
            position: absolute;
            inset: 0 auto 0 0;
            width: 46%;
            border-radius: inherit;
            background: linear-gradient(90deg, var(--pink), var(--pink-dark));
            animation: splashLoad 1s ease-in-out infinite;
        }

        @keyframes splashFloat {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        @keyframes splashWiggle {

            0%,
            100% {
                transform: rotate(-2deg) scale(1);
            }

            50% {
                transform: rotate(2deg) scale(1.03);
            }
        }

        @keyframes splashOrbit {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @keyframes splashPop {

            0%,
            100% {
                transform: scale(1) rotate(0deg);
            }

            50% {
                transform: scale(1.08) rotate(8deg);
            }
        }

        @keyframes splashKiss {

            0%,
            100% {
                filter: drop-shadow(0 0 0 rgba(244, 114, 182, 0));
            }

            45% {
                filter: drop-shadow(0 0 12px rgba(244, 114, 182, 0.42));
            }
        }

        @keyframes splashBeat {

            0%,
            100% {
                transform: translateY(0) scale(0.88);
            }

            50% {
                transform: translateY(-5px) scale(1.18);
            }
        }

        @keyframes splashLoad {
            0% {
                transform: translateX(-110%);
            }

            100% {
                transform: translateX(230%);
            }
        }

        @keyframes lovePulse {

            0%,
            100% {
                transform: translateY(0) scale(0.9);
                opacity: 0.58;
            }

            50% {
                transform: translateY(-6px) scale(1.12);
                opacity: 1;
            }
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
                padding-bottom: 132px;
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
    <script>
            (function () {
                try {
                    if (sessionStorage.getItem('dompetkitaSplashSeen') === '1') {
                        document.documentElement.classList.add('splash-seen');
                    }
                } catch (error) {
                    document.documentElement.classList.add('splash-seen');
                }
            })();
    </script>

    <div id="appSplash" class="app-splash" aria-label="Memuat DompetKita">
        <div class="splash-card" role="status" aria-live="polite">
            <div class="splash-icon-wrap" aria-hidden="true">
                <div class="splash-orbit">
                    <i class="fa-solid fa-heart"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-heart"></i>
                </div>
                <div class="splash-icon">
                    <img src="{{ asset('images/pwa-icon-dompetkita-192.png') }}" alt="DompetKita">
                </div>
                <div class="splash-bubble one">
                    <i class="fa-solid fa-heart"></i>
                </div>
                <div class="splash-bubble two">
                    <i class="fa-solid fa-coins"></i>
                </div>
            </div>
            <h1 class="splash-title">DompetKita</h1>
            <p class="splash-subtitle">Nyiapin dompet kecil buat cerita berdua...</p>
            <div class="splash-sweet-row" aria-hidden="true">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <div class="splash-loader" aria-hidden="true"></div>
        </div>
    </div>

    <div id="pageLoveLoader" class="page-love-loader" aria-label="Memuat halaman">
        <div class="love-loader-box" role="status" aria-live="polite">
            <div class="love-loader-icon" aria-hidden="true">
                <i class="fa-solid fa-heart"></i>
                <i class="fa-solid fa-heart"></i>
                <i class="fa-solid fa-heart"></i>
            </div>
            <p class="love-loader-text">Sebentar ya...</p>
        </div>
    </div>

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
            <div id="tourMobileInvite" style="display:flex; flex-direction:column; align-items:flex-end; gap:1px;">
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
                    <button onclick="copyInviteLink()"
                        style="background:var(--pink-light); border:none; border-radius:6px; padding:3px 7px; cursor:pointer; color:var(--pink-dark); font-size:11px; line-height:1;"
                        title="Salin link undangan">
                        <i class="fa-solid fa-link"></i>
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
                <a href="{{ route('dashboard') }}" id="tourNavDashboard"
                    class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-house"></i> Dashboard
                </a>
                <a href="{{ route('transactions.index') }}" id="tourNavTransactions"
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
                <a href="{{ route('budgets.index') }}"
                    class="nav-link {{ request()->routeIs('budgets.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-pie"></i> Budget
                </a>
                <a href="{{ route('reminders.index') }}"
                    class="nav-link {{ request()->routeIs('reminders.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-bell"></i> Reminder
                </a>
                <a href="{{ route('banks.index') }}" id="tourNavBanks"
                    class="nav-link {{ request()->routeIs('banks.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-building-columns"></i> Rekening
                </a>
                <a href="{{ route('assets.index') }}"
                    class="nav-link {{ request()->routeIs('assets.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-gem"></i> Aset
                </a>
                <a href="{{ route('targets.index') }}" id="tourNavTargets"
                    class="nav-link {{ request()->routeIs('targets.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-bullseye"></i> Target
                </a>
                <a href="{{ route('locations.index') }}"
                    class="nav-link {{ request()->routeIs('locations.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-location-dot"></i> Lokasi
                </a>
                <a href="{{ route('chats.index') }}"
                    class="nav-link {{ request()->routeIs('chats.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-comments"></i> Chat
                </a>
                <a href="{{ route('reports.index') }}"
                    class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-column"></i> Laporan
                </a>

                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-shield-heart"></i> Admin Panel
                    </a>
                @endif
            </div>

            <div class="invite-block" id="inviteBlock">
                <div class="invite-label">Kode Undangan</div>
                <div class="invite-code-row">
                    <span class="invite-code">{{ auth()->user()->couple->invite_code ?? '' }}</span>
                    <button class="copy-btn" onclick="copyInvite()">
                        <i class="fa-regular fa-copy" style="font-size:13px;"></i>
                    </button>
                </div>
                <div class="invite-link-row">
                    <span class="invite-link">{{ route('register', ['invite' => auth()->user()->couple->invite_code ?? '', 'action' => 'join']) }}</span>
                    <button class="copy-btn" onclick="copyInviteLink()" title="Salin link undangan">
                        <i class="fa-solid fa-link" style="font-size:13px;"></i>
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
                    <span class="fab-heart"><i class="fa-solid fa-heart"></i></span>
                    <i class="fa-solid fa-plus"></i>
                </a>
            </div>

            <a href="{{ route('chats.index') }}"
                class="bottom-nav-item {{ request()->routeIs('chats.*') ? 'active' : '' }}">
                <i class="fa-solid fa-comments"></i>
                <span>Chat</span>
            </a>
            <a href="javascript:void(0)" onclick="toggleMobileMenu()" id="btnMoreMenu" class="bottom-nav-item">
                <i class="fa-solid fa-grip"></i>
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
                <a href="{{ route('banks.transfer') }}"
                    class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-pink-50 text-slate-600 {{ request()->routeIs('banks.transfer') ? 'text-pink-600 bg-pink-50' : '' }}">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-base"><i
                            class="fa-solid fa-arrow-right-arrow-left"></i></div>
                    <span class="text-xs font-medium">Transfer</span>
                </a>
                <a href="{{ route('targets.index') }}"
                    class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-pink-50 text-slate-600 {{ request()->routeIs('targets.*') ? 'text-pink-600 bg-pink-50' : '' }}">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-base"><i
                            class="fa-solid fa-bullseye"></i></div>
                    <span class="text-xs font-medium">Target</span>
                </a>
                <a href="{{ route('assets.index') }}"
                    class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-pink-50 text-slate-600 {{ request()->routeIs('assets.*') ? 'text-pink-600 bg-pink-50' : '' }}">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-base"><i
                            class="fa-solid fa-gem"></i></div>
                    <span class="text-xs font-medium">Aset</span>
                </a>
                <a href="{{ route('debts.index') }}"
                    class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-pink-50 text-slate-600 {{ request()->routeIs('debts.*') ? 'text-pink-600 bg-pink-50' : '' }}">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-base"><i
                            class="fa-solid fa-hand-holding-dollar"></i></div>
                    <span class="text-xs font-medium">Hutang</span>
                </a>
                <a href="{{ route('categories.index') }}"
                    class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-pink-50 text-slate-600 {{ request()->routeIs('categories.*') ? 'text-pink-600 bg-pink-50' : '' }}">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-base"><i
                            class="fa-solid fa-tag"></i></div>
                    <span class="text-xs font-medium">Kategori</span>
                </a>
                <a href="{{ route('budgets.index') }}"
                    class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-pink-50 text-slate-600 {{ request()->routeIs('budgets.*') ? 'text-pink-600 bg-pink-50' : '' }}">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-base"><i
                            class="fa-solid fa-chart-pie"></i></div>
                    <span class="text-xs font-medium">Budget</span>
                </a>
                <a href="{{ route('reminders.index') }}"
                    class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-pink-50 text-slate-600 {{ request()->routeIs('reminders.*') ? 'text-pink-600 bg-pink-50' : '' }}">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-base"><i
                            class="fa-solid fa-bell"></i></div>
                    <span class="text-xs font-medium">Reminder</span>
                </a>
                <a href="{{ route('locations.index') }}"
                    class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-pink-50 text-slate-600 {{ request()->routeIs('locations.*') ? 'text-pink-600 bg-pink-50' : '' }}">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-base"><i
                            class="fa-solid fa-location-dot"></i></div>
                    <span class="text-xs font-medium">Lokasi</span>
                </a>
                <a href="{{ route('chats.index') }}"
                    class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-pink-50 text-slate-600 {{ request()->routeIs('chats.*') ? 'text-pink-600 bg-pink-50' : '' }}">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-base"><i
                            class="fa-solid fa-comments"></i></div>
                    <span class="text-xs font-medium">Chat</span>
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
                <button type="button" data-install-app
                    class="hidden flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-pink-50 text-slate-600">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-base"><i
                            class="fa-solid fa-download"></i></div>
                    <span class="text-xs font-medium">Install</span>
                </button>
            </div>
        </div>
    </div>

    <div id="offlineFloatingAlert"
        class="fixed right-4 top-4 z-[1200] hidden max-w-[calc(100vw-2rem)] rounded-2xl border px-4 py-3 shadow-xl backdrop-blur md:right-6 md:top-6"
        role="status" aria-live="polite">
        <div class="flex items-start gap-3">
            <div id="offlineFloatingIcon"
                class="mt-0.5 flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full">
                <i class="fa-solid fa-wifi"></i>
            </div>
            <div class="min-w-0">
                <div id="offlineFloatingTitle" class="text-sm font-bold"></div>
                <div id="offlineFloatingMessage" class="text-xs leading-relaxed"></div>
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

        window.DompetKitaOffline = (function () {
            const queueKey = 'dompetkita.offline.transactions.v1';
            const syncUrl = '{{ route('transactions.store') }}';
            let isSyncing = false;

            function readQueue() {
                try {
                    return JSON.parse(localStorage.getItem(queueKey) || '[]');
                } catch (error) {
                    return [];
                }
            }

            function writeQueue(queue) {
                localStorage.setItem(queueKey, JSON.stringify(queue));
                window.dispatchEvent(new CustomEvent('dompetkita:offline-queue', {
                    detail: { count: queue.length }
                }));
            }

            function makeUuid() {
                if (window.crypto && window.crypto.randomUUID) {
                    return window.crypto.randomUUID();
                }

                return 'offline-' + Date.now() + '-' + Math.random().toString(16).slice(2);
            }

            function withCurrentTime(dateValue, date = new Date()) {
                if (!dateValue || /\d{1,2}:\d{2}/.test(String(dateValue))) {
                    return dateValue;
                }

                const pad = value => String(value).padStart(2, '0');
                return `${dateValue} ${pad(date.getHours())}:${pad(date.getMinutes())}:${pad(date.getSeconds())}`;
            }

            function toFormBody(payload) {
                const params = new URLSearchParams();
                Object.entries(payload).forEach(([key, value]) => {
                    params.append(key, value ?? '');
                });
                return params.toString();
            }

            function queueTransaction(payload) {
                const queue = readQueue();
                const clientUuid = payload.client_uuid || makeUuid();
                const createdAt = new Date();
                const item = {
                    client_uuid: clientUuid,
                    payload: {
                        ...payload,
                        date: withCurrentTime(payload.date, createdAt),
                        client_uuid: clientUuid,
                    },
                    created_at: createdAt.toISOString(),
                };

                item.payload.client_uuid = item.client_uuid;
                queue.push(item);
                writeQueue(queue);

                Toast.fire({
                    icon: 'info',
                    title: 'Disimpan offline. Akan disinkronkan saat internet aktif.'
                });

                if (navigator.onLine) {
                    syncTransactions();
                }

                return item;
            }

            async function sendItem(item) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

                const response = await fetch(syncUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: toFormBody(item.payload),
                });

                if (!response.ok) {
                    const error = new Error('Sync failed');
                    error.status = response.status;
                    try {
                        error.response = await response.json();
                    } catch (jsonError) {
                        error.response = null;
                    }
                    throw error;
                }

                return response.json();
            }

            async function syncTransactions() {
                if (isSyncing || !navigator.onLine) return;

                let queue = readQueue();
                if (queue.length === 0) return;

                isSyncing = true;
                let syncedCount = 0;
                const failedItems = [];

                for (const item of queue) {
                    try {
                        await sendItem(item);
                        syncedCount++;
                    } catch (error) {
                        if (error.status && error.status !== 0 && error.status < 500) {
                            failedItems.push({
                                ...item,
                                last_error: error.response?.message || 'Data tidak bisa disinkronkan.',
                            });
                            continue;
                        }

                        failedItems.push(item);
                    }
                }

                writeQueue(failedItems);
                isSyncing = false;

                if (syncedCount > 0) {
                    Toast.fire({
                        icon: 'success',
                        title: `${syncedCount} transaksi offline tersinkron.`
                    });
                }
            }

            window.addEventListener('online', syncTransactions);
            window.addEventListener('load', syncTransactions);

            return {
                queueTransaction,
                syncTransactions,
                pendingTransactions: readQueue,
                pendingCount: () => readQueue().length,
            };
        })();

        window.DompetKitaLocationTracker = (function () {
            const updateUrl = '{{ route('locations.update') }}';
            const intervalMs = 5 * 60 * 1000;
            const sharingKey = 'dompetkita.location.sharing';
            let timer = null;
            let lastSentAt = 0;
            let isSending = false;

            function dispatch(name, detail = {}) {
                window.dispatchEvent(new CustomEvent(name, { detail }));
            }

            function isAvailable() {
                return Boolean(navigator.geolocation);
            }

            function isSharingEnabled() {
                return localStorage.getItem(sharingKey) === '1';
            }

            function setSharingEnabled(enabled) {
                localStorage.setItem(sharingKey, enabled ? '1' : '0');
            }

            function cacheKey(latitude, longitude) {
                return `dompetkita.location.address.${Number(latitude).toFixed(4)},${Number(longitude).toFixed(4)}`;
            }

            function simplifyAddress(data) {
                const address = data?.address || {};
                return {
                    address_text: data?.display_name || null,
                    road: address.road || address.pedestrian || address.footway || address.path || null,
                    neighbourhood: address.neighbourhood || address.hamlet || null,
                    suburb: address.suburb || null,
                    village: address.village || address.town || null,
                    district: address.city_district || address.district || address.county || null,
                    city: address.city || address.municipality || address.regency || null,
                    state: address.state || null,
                    postcode: address.postcode || null,
                };
            }

            async function reverseGeocode(latitude, longitude) {
                const key = cacheKey(latitude, longitude);
                const cached = sessionStorage.getItem(key);
                if (cached) {
                    try {
                        return JSON.parse(cached);
                    } catch (error) {
                        sessionStorage.removeItem(key);
                    }
                }

                try {
                    const params = new URLSearchParams({
                        format: 'jsonv2',
                        lat: latitude,
                        lon: longitude,
                        zoom: 18,
                        addressdetails: 1,
                        'accept-language': 'id',
                    });
                    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?${params.toString()}`, {
                        headers: { 'Accept': 'application/json' },
                    });

                    if (!response.ok) return {};

                    const address = simplifyAddress(await response.json());
                    sessionStorage.setItem(key, JSON.stringify(address));
                    return address;
                } catch (error) {
                    return {};
                }
            }

            async function sendPosition(position, label = 'Otomatis dari aplikasi') {
                if (isSending) return null;

                isSending = true;
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

                try {
                    const address = await reverseGeocode(position.coords.latitude, position.coords.longitude);
                    const response = await fetch(updateUrl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude,
                            accuracy: position.coords.accuracy,
                            label,
                            ...address,
                        }),
                    });

                    if (!response.ok) throw new Error('Lokasi otomatis belum bisa disimpan.');

                    const result = await response.json();
                    lastSentAt = Date.now();
                    dispatch('dompetkita:location-updated', result);
                    return result;
                } catch (error) {
                    dispatch('dompetkita:location-error', { message: error.message });
                    return null;
                } finally {
                    isSending = false;
                }
            }

            function requestNow(options = {}) {
                if (!isAvailable()) {
                    dispatch('dompetkita:location-error', { message: 'Browser ini belum mendukung akses lokasi.' });
                    return;
                }

                const force = options.force ?? false;
                const label = options.label || 'Otomatis dari aplikasi';
                if (!force && Date.now() - lastSentAt < 30000) return;

                navigator.geolocation.getCurrentPosition(
                    position => sendPosition(position, label),
                    error => dispatch('dompetkita:location-error', {
                        message: error.message || 'Izin lokasi belum diberikan.',
                    }),
                    { enableHighAccuracy: true, timeout: 12000, maximumAge: 30000 }
                );
            }

            function start() {
                if (!isSharingEnabled()) return;
                requestNow({ force: true });

                if (timer) clearInterval(timer);
                timer = window.setInterval(() => requestNow(), intervalMs);
            }

            function stop() {
                setSharingEnabled(false);
                if (timer) clearInterval(timer);
                timer = null;
            }

            function enableSharing() {
                setSharingEnabled(true);
            }

            window.addEventListener('load', function () {
                if (isSharingEnabled()) start();
            });
            document.addEventListener('visibilitychange', function () {
                if (!document.hidden && isSharingEnabled()) requestNow();
            });

            return { start, stop, enableSharing, requestNow, isSharingEnabled };
        })();

        function copyInvite() {
            const code = '{{ auth()->user()->couple->invite_code ?? '' }}';
            navigator.clipboard.writeText(code);
            Toast.fire({ icon: 'success', title: 'Kode disalin!' });
        }

        function copyInviteLink() {
            const link = @json(route('register', ['invite' => auth()->user()->couple->invite_code ?? '', 'action' => 'join']));
            navigator.clipboard.writeText(link);
            Toast.fire({ icon: 'success', title: 'Link undangan disalin!' });
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

        function updateOfflineFloatingAlert() {
            const alert = document.getElementById('offlineFloatingAlert');
            const icon = document.getElementById('offlineFloatingIcon');
            const title = document.getElementById('offlineFloatingTitle');
            const message = document.getElementById('offlineFloatingMessage');
            if (!alert || !icon || !title || !message) return;

            const pendingCount = window.DompetKitaOffline?.pendingCount?.() || 0;
            const isOffline = !navigator.onLine;

            if (!isOffline && pendingCount === 0) {
                alert.classList.add('hidden');
                return;
            }

            alert.classList.remove('hidden');
            alert.className = 'fixed right-4 top-4 z-[1200] max-w-[calc(100vw-2rem)] rounded-2xl border px-4 py-3 shadow-xl backdrop-blur md:right-6 md:top-6';
            icon.className = 'mt-0.5 flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full';

            if (isOffline) {
                alert.classList.add('border-amber-200', 'bg-amber-50/95', 'text-amber-900');
                icon.classList.add('bg-amber-100', 'text-amber-700');
                title.textContent = 'Mode offline';
                message.textContent = pendingCount > 0
                    ? `${pendingCount} transaksi tersimpan di perangkat dan akan sinkron saat internet kembali.`
                    : 'Kamu tetap bisa mencatat transaksi. Data akan disimpan dulu di perangkat ini.';
                return;
            }

            alert.classList.add('border-pink-200', 'bg-white/95', 'text-slate-800');
            icon.classList.add('bg-pink-50', 'text-pink-600');
            title.textContent = 'Menunggu sinkronisasi';
            message.textContent = `${pendingCount} transaksi offline masih dalam antrean.`;
        }

        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMoreMenu');
            if (!menu) return;
            menu.classList.toggle('hidden');
        }

        let deferredInstallPrompt = null;

        function isAppInstalled() {
            return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
        }

        function isIosDevice() {
            return /iphone|ipad|ipod/i.test(window.navigator.userAgent);
        }

        function showInstallButtons() {
            if (isAppInstalled()) return;
            document.querySelectorAll('[data-install-app]').forEach(button => {
                button.classList.remove('hidden');
            });
        }

        function showPageLoveLoader() {
            const loader = document.getElementById('pageLoveLoader');
            if (!loader) return;
            loader.classList.add('is-active');
        }

        function shouldShowLoveLoaderForLink(link, event) {
            if (!link || !link.href) return false;
            if (event.defaultPrevented || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return false;
            if (link.target && link.target !== '_self') return false;
            if (link.hasAttribute('download')) return false;
            if (link.getAttribute('href').startsWith('javascript:')) return false;

            const url = new URL(link.href, window.location.href);
            if (url.origin !== window.location.origin) return false;
            if (url.pathname === window.location.pathname && url.search === window.location.search) return false;

            return true;
        }

        window.addEventListener('beforeinstallprompt', function (event) {
            event.preventDefault();
            deferredInstallPrompt = event;
            showInstallButtons();
        });

        window.addEventListener('appinstalled', function () {
            deferredInstallPrompt = null;
            document.querySelectorAll('[data-install-app]').forEach(button => {
                button.classList.add('hidden');
            });
            Toast.fire({ icon: 'success', title: 'Aplikasi berhasil diinstall!' });
        });

        document.addEventListener('DOMContentLoaded', function () {
            updateOfflineFloatingAlert();
            window.addEventListener('online', updateOfflineFloatingAlert);
            window.addEventListener('offline', updateOfflineFloatingAlert);
            window.addEventListener('dompetkita:offline-queue', updateOfflineFloatingAlert);

            const splash = document.getElementById('appSplash');
            if (splash) {
                if (document.documentElement.classList.contains('splash-seen')) {
                    splash.remove();
                } else {
                    try {
                        sessionStorage.setItem('dompetkitaSplashSeen', '1');
                    } catch (error) {
                        // Abaikan jika browser memblokir sessionStorage.
                    }

                    window.setTimeout(function () {
                        splash.classList.add('is-hiding');
                        window.setTimeout(function () {
                            splash.remove();
                        }, 420);
                    }, 850);
                }
            }

            if (isIosDevice() && !isAppInstalled()) {
                showInstallButtons();
            }

            document.querySelectorAll('[data-install-app]').forEach(button => {
                button.addEventListener('click', async function () {
                    if (deferredInstallPrompt) {
                        deferredInstallPrompt.prompt();
                        await deferredInstallPrompt.userChoice;
                        deferredInstallPrompt = null;
                        return;
                    }

                    Swal.fire({
                        icon: 'info',
                        title: 'Install DompetKita',
                        html: 'Di Safari, tekan tombol <b>Share</b>, lalu pilih <b>Add to Home Screen</b>.',
                        confirmButtonColor: '#db2777',
                        background: '#fff',
                        color: '#1a1a2e',
                    });
                });
            });

            if (window.flatpickr) {
                flatpickr.localize(flatpickr.l10ns.id);

                document.querySelectorAll('.js-date-picker').forEach(input => {
                    flatpickr(input, {
                        altInput: true,
                        altFormat: input.dataset.altFormat || 'j F Y',
                        dateFormat: input.dataset.format || 'Y-m-d',
                        locale: 'id',
                        disableMobile: true,
                        allowInput: false,
                        defaultDate: input.value || null,
                    });
                });
            }

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
                    if (!this.hasAttribute('data-skip-page-loader')) {
                        showPageLoveLoader();
                    }

                    this.querySelectorAll('.rupiah').forEach(input => {
                        input.value = normalize(input.value);
                    });
                });
            });

            document.addEventListener('click', function (event) {
                const link = event.target.closest('a');
                if (shouldShowLoveLoaderForLink(link, event)) {
                    showPageLoveLoader();
                }
            });

            initSensitiveMoneyPrivacy();
        });

        function isSensitiveMoneyHidden() {
            try {
                return localStorage.getItem('dompetkitaHideSensitiveMoney') === '1';
            } catch (error) {
                return false;
            }
        }

        function setSensitiveMoneyHidden(hidden) {
            try {
                localStorage.setItem('dompetkitaHideSensitiveMoney', hidden ? '1' : '0');
            } catch (error) {
                // Abaikan jika browser memblokir localStorage.
            }

            applySensitiveMoneyPrivacy(hidden);
        }

        function applySensitiveMoneyPrivacy(hidden = isSensitiveMoneyHidden()) {
            document.querySelectorAll('[data-sensitive-money]').forEach(element => {
                if (!element.dataset.visibleText) {
                    element.dataset.visibleText = element.textContent.trim();
                }

                element.textContent = hidden
                    ? (element.dataset.maskText || 'Rp •••••••')
                    : element.dataset.visibleText;
            });

            document.querySelectorAll('[data-toggle-sensitive-money]').forEach(button => {
                button.setAttribute('aria-pressed', hidden ? 'true' : 'false');
                button.title = hidden ? 'Tampilkan saldo' : 'Sembunyikan saldo';

                const label = button.querySelector('[data-sensitive-money-toggle-label]');
                if (label) {
                    label.textContent = hidden ? 'Tampilkan Saldo' : 'Sembunyikan Saldo';
                }

                const icon = button.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-eye', hidden);
                    icon.classList.toggle('fa-eye-slash', !hidden);
                }
            });
        }

        function initSensitiveMoneyPrivacy() {
            document.querySelectorAll('[data-toggle-sensitive-money]').forEach(button => {
                button.addEventListener('click', function () {
                    setSensitiveMoneyHidden(!isSensitiveMoneyHidden());
                });
            });

            applySensitiveMoneyPrivacy();
        }

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

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('{{ asset('sw.js') }}').catch(function (error) {
                    console.warn('Service worker registration failed:', error);
                });
            });
        }
    </script>

    @stack('scripts')
</body>

</html>
