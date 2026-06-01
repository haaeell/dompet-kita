@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    @php
        $balance = $monthlyIncome - $monthlyExpense;
        $selectedMember = $selectedUserId ? $coupleMembers->firstWhere('id', $selectedUserId) : null;
        $spendingPercent = $monthlyIncome > 0 ? min(100, round(($monthlyExpense / $monthlyIncome) * 100)) : 0;
        $topTarget = $targets->first();
    @endphp

    <style>
        .dashboard-mobile-view {
            display: none;
        }

        .achievement-icon {
            width: 40px;
            height: 40px;
            flex-shrink: 0;
            display: grid;
            place-items: center;
            border-radius: 14px;
            color: #fff;
        }

        @media (max-width: 768px) {
            .dashboard-desktop-view {
                display: none;
            }

            .dashboard-mobile-view {
                display: block;
                margin: -8px -4px 0;
            }

            .mobile-home-hero {
                position: relative;
                overflow: hidden;
                border-radius: 28px;
                padding: 20px;
                color: white;
                background:
                    radial-gradient(circle at 84% 18%, rgba(255, 255, 255, 0.26) 0 14%, transparent 28%),
                    linear-gradient(135deg, #ec4899 0%, #db2777 48%, #9d174d 100%);
                box-shadow: 0 18px 38px rgba(219, 39, 119, 0.26);
            }

            .mobile-home-hero::after {
                content: '';
                position: absolute;
                width: 154px;
                height: 154px;
                right: -64px;
                bottom: -72px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.14);
            }

            .mobile-home-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                position: relative;
                z-index: 1;
            }

            .mobile-avatar-stack {
                display: flex;
                align-items: center;
                flex-shrink: 0;
            }

            .mobile-avatar-stack .avatar {
                width: 34px;
                height: 34px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                background: #fff1f2;
                color: #9d174d;
                border: 2px solid rgba(255, 255, 255, 0.9);
                font-size: 14px;
                font-weight: 700;
            }

            .mobile-avatar-stack .avatar + .avatar {
                margin-left: -10px;
            }

            .mobile-balance {
                position: relative;
                z-index: 1;
                margin-top: 22px;
            }

            .mobile-balance-label {
                font-size: 11px;
                font-weight: 700;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                opacity: 0.78;
            }

            .mobile-balance-value {
                margin-top: 6px;
                font-size: clamp(25px, 8vw, 34px);
                font-weight: 800;
                line-height: 1.05;
                word-break: break-word;
            }

            .mobile-money-pair {
                position: relative;
                z-index: 1;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
                margin-top: 18px;
            }

            .mobile-money-mini {
                border-radius: 18px;
                padding: 12px;
                background: rgba(255, 255, 255, 0.15);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            .mobile-money-mini span {
                display: block;
                font-size: 10px;
                font-weight: 700;
                opacity: 0.76;
            }

            .mobile-money-mini strong {
                display: block;
                margin-top: 4px;
                font-size: 13px;
                line-height: 1.2;
                word-break: break-word;
            }

            .mobile-section {
                margin-top: 20px;
            }

            .mobile-filter-card {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 12px;
                border-radius: 22px;
                background: white;
                border: 1px solid #fce7f3;
                box-shadow: 0 10px 26px rgba(15, 23, 42, 0.05);
            }

            .mobile-filter-icon {
                width: 42px;
                height: 42px;
                border-radius: 16px;
                flex-shrink: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                color: var(--pink-dark);
                background: var(--pink-light);
            }

            .mobile-filter-select {
                width: 100%;
                min-width: 0;
                border: 0;
                outline: none;
                color: var(--text-primary);
                background: transparent;
                font-size: 13px;
                font-weight: 800;
                font-family: 'Poppins', sans-serif;
            }

            .mobile-metrics-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            .mobile-metric-card {
                min-height: 128px;
                padding: 14px;
                border-radius: 24px;
                background: white;
                border: 1px solid var(--border);
                box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
            }

            .mobile-metric-card.featured {
                grid-column: 1 / -1;
                min-height: 0;
                display: flex;
                align-items: center;
                gap: 12px;
                border-color: #bfdbfe;
                background: linear-gradient(135deg, #eff6ff, #ffffff);
            }

            .mobile-metric-icon {
                width: 42px;
                height: 42px;
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                font-size: 16px;
            }

            .mobile-metric-label {
                color: var(--text-secondary);
                font-size: 10px;
                font-weight: 800;
                letter-spacing: 0.06em;
                text-transform: uppercase;
            }

            .mobile-metric-value {
                margin-top: 7px;
                color: var(--text-primary);
                font-size: 17px;
                font-weight: 900;
                line-height: 1.15;
                word-break: break-word;
            }

            .mobile-metric-note {
                margin-top: 6px;
                color: var(--text-secondary);
                font-size: 10px;
                line-height: 1.35;
            }

            .mobile-section-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                margin-bottom: 10px;
            }

            .mobile-section-title {
                margin: 0;
                color: var(--text-primary);
                font-size: 16px;
                font-weight: 800;
            }

            .mobile-link {
                color: var(--pink-dark);
                font-size: 12px;
                font-weight: 700;
                text-decoration: none;
            }

            .quick-action-grid {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 10px;
            }

            .quick-action {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 7px;
                min-height: 84px;
                padding: 10px 6px;
                border-radius: 20px;
                background: white;
                border: 1px solid #fce7f3;
                color: var(--text-primary);
                text-decoration: none;
                box-shadow: 0 10px 26px rgba(15, 23, 42, 0.05);
            }

            .quick-action i {
                width: 38px;
                height: 38px;
                border-radius: 15px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: var(--pink-light);
                color: var(--pink-dark);
                font-size: 15px;
            }

            .quick-action span {
                max-width: 100%;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                font-size: 11px;
                font-weight: 700;
            }

            .mobile-insight-card,
            .mobile-list-card {
                border-radius: 24px;
                padding: 16px;
                background: white;
                border: 1px solid var(--border);
                box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
            }

            .mobile-reminder-card {
                border-radius: 24px;
                padding: 14px;
                background: linear-gradient(135deg, #fff7ed, #ffffff);
                border: 1px solid #fed7aa;
                box-shadow: 0 12px 28px rgba(249, 115, 22, 0.09);
            }

            .mobile-reminder-item {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 11px 0;
                border-bottom: 1px solid rgba(251, 146, 60, 0.16);
            }

            .mobile-reminder-item:last-child {
                border-bottom: 0;
                padding-bottom: 0;
            }

            .mobile-reminder-icon {
                width: 38px;
                height: 38px;
                border-radius: 15px;
                flex-shrink: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                background: white;
                color: #f97316;
                box-shadow: 0 8px 16px rgba(249, 115, 22, 0.12);
            }

            .mobile-reminder-badge {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 54px;
                padding: 4px 8px;
                border-radius: 999px;
                font-size: 10px;
                font-weight: 800;
                color: #9a3412;
                background: #ffedd5;
                white-space: nowrap;
            }

            .mobile-insight-card {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .mobile-insight-icon {
                width: 46px;
                height: 46px;
                border-radius: 17px;
                flex-shrink: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #fff7ed;
                color: #f97316;
                font-size: 18px;
            }

            .mobile-progress {
                height: 8px;
                margin-top: 10px;
                overflow: hidden;
                border-radius: 99px;
                background: #f1f5f9;
            }

            .mobile-progress span {
                display: block;
                height: 100%;
                border-radius: inherit;
                background: linear-gradient(90deg, var(--pink), var(--pink-dark));
            }

            .mobile-badge-strip {
                display: flex;
                gap: 10px;
                overflow-x: auto;
                margin: 0 -16px;
                padding: 0 16px 4px;
                scroll-snap-type: x mandatory;
            }

            .mobile-achievement-badge {
                min-width: 210px;
                display: flex;
                align-items: center;
                gap: 10px;
                border-radius: 18px;
                border: 1px solid #f1f5f9;
                background: #fff;
                padding: 12px;
                box-shadow: 0 10px 26px rgba(15, 23, 42, .05);
                scroll-snap-align: start;
            }


            .mobile-tx-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 12px 0;
                border-bottom: 1px solid #f1f5f9;
            }

            .mobile-tx-item:last-child {
                border-bottom: 0;
                padding-bottom: 0;
            }

            .mobile-tx-icon {
                width: 42px;
                height: 42px;
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                font-size: 18px;
            }

            .mobile-tx-title {
                color: var(--text-primary);
                font-size: 13px;
                font-weight: 800;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .mobile-tx-meta {
                margin-top: 3px;
                color: var(--text-secondary);
                font-size: 11px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .mobile-tx-amount {
                margin-left: auto;
                flex-shrink: 0;
                text-align: right;
                font-size: 12px;
                font-weight: 800;
                white-space: nowrap;
            }
        }
    </style>

    <div class="dashboard-mobile-view">
        <section class="mobile-home-hero">
            <div class="mobile-home-row">
                <div class="min-w-0">
                    <div class="text-[12px] font-semibold opacity-80">Halo, {{ auth()->user()->name }}</div>
                    <div class="text-[18px] font-extrabold leading-tight mt-1 truncate">
                        Keuangan {{ $couple->couple_name ?? 'berdua' }}
                    </div>
                </div>
                <div class="mobile-avatar-stack">
                    @foreach($coupleMembers as $member)
                        <div class="avatar">
                            @if($member->profile_photo_url)
                                <img src="{{ $member->profile_photo_url }}" alt="{{ $member->name }}"
                                    class="w-full h-full object-cover">
                            @else
                                {{ $member->avatar }}
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mobile-balance">
                <div class="mobile-balance-label">Saldo bulan ini</div>
                <div class="mobile-balance-value">Rp {{ number_format($balance, 0, ',', '.') }}</div>
                <div class="text-[12px] font-semibold opacity-80 mt-2">
                    {{ $selectedMember ? 'Filter: ' . $selectedMember->name : 'Semua transaksi pasangan' }}
                </div>
            </div>

            <div class="mobile-money-pair">
                <div class="mobile-money-mini">
                    <span>Pemasukan</span>
                    <strong>Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</strong>
                </div>
                <div class="mobile-money-mini">
                    <span>Pengeluaran</span>
                    <strong>Rp {{ number_format($monthlyExpense, 0, ',', '.') }}</strong>
                </div>
            </div>
        </section>

        <section class="mobile-section">
            <form action="{{ url()->current() }}" method="GET" id="mobileFilterForm" class="m-0">
                <label class="mobile-filter-card" for="mobileUserFilter">
                    <span class="mobile-filter-icon">
                        <i class="fa-solid fa-user-group"></i>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-[10px] uppercase tracking-[0.08em] font-extrabold text-[var(--text-secondary)] mb-1">
                            Lihat data
                        </span>
                        <select id="mobileUserFilter" name="user_id" class="mobile-filter-select"
                            onchange="document.getElementById('mobileFilterForm').submit();">
                            <option value="">Semua transaksi pasangan</option>
                            @foreach($coupleMembers as $member)
                                <option value="{{ $member->id }}" {{ $selectedUserId == $member->id ? 'selected' : '' }}>
                                    {{ $member->id == auth()->id() ? 'Saya (' . $member->name . ')' : $member->name }}
                                </option>
                            @endforeach
                        </select>
                    </span>
                </label>
            </form>
        </section>

        <section class="mobile-section">
            <div class="mobile-section-head">
                <h2 class="mobile-section-title">Ringkasan Bersama</h2>
                <span class="text-[11px] font-bold text-[var(--text-secondary)]">{{ now()->isoFormat('MMMM Y') }}</span>
            </div>
            <div class="mobile-metrics-grid">
                <div class="mobile-metric-card featured">
                    <div class="mobile-metric-icon bg-blue-50 text-blue-700">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="mobile-metric-label">{{ $selectedUserId ? 'Total Kekayaan Pribadi' : 'Total Kekayaan' }}</div>
                        <div class="mobile-metric-value text-blue-700">Rp {{ number_format($totalWealth, 0, ',', '.') }}</div>
                        <div class="mobile-metric-note">
                            Termasuk rekening aktif{{ $selectedMember ? ' milik ' . $selectedMember->name : '' }}.
                            Dengan piutang: Rp {{ number_format($totalWealthIncludingPiutang, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if($debtReminders->isNotEmpty())
            <section class="mobile-section">
                <div class="mobile-section-head">
                    <h2 class="mobile-section-title">Pengingat Hari Ini</h2>
                    <a href="{{ route('debts.index') }}" class="mobile-link">Detail</a>
                </div>
                <div class="mobile-reminder-card">
                    @foreach($debtReminders as $reminder)
                        @php
                            $daysUntilDue = now()->startOfDay()->diffInDays($reminder->due_date->startOfDay(), false);
                            $dueLabel = $daysUntilDue < 0
                                ? 'Telat ' . abs($daysUntilDue) . 'h'
                                : ($daysUntilDue === 0 ? 'Hari ini' : 'H-' . $daysUntilDue);
                        @endphp
                        <a href="{{ route('debts.index', ['type' => $reminder->type, 'user_id' => $selectedUserId]) }}"
                            class="mobile-reminder-item no-underline">
                            <span class="mobile-reminder-icon">
                                <i class="fa-solid {{ $reminder->type === 'hutang' ? 'fa-hand-holding-dollar' : 'fa-hand-holding-hand' }}"></i>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block text-[13px] font-extrabold text-[var(--text-primary)] truncate">
                                    {{ $reminder->type === 'hutang' ? 'Bayar hutang' : 'Tagih piutang' }} {{ $reminder->counterparty }}
                                </span>
                                <span class="block text-[11px] text-[var(--text-secondary)] truncate">
                                    {{ $reminder->user->name }} - Rp {{ number_format($reminder->amount, 0, ',', '.') }} - {{ $reminder->due_date->isoFormat('D MMM Y') }}
                                </span>
                            </span>
                            <span class="mobile-reminder-badge">{{ $dueLabel }}</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="mobile-section" id="tourMobileQuickActions">
            <div class="quick-action-grid">
                <a href="{{ route('transactions.create') }}" class="quick-action">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah</span>
                </a>
                <a href="{{ route('transactions.index') }}" class="quick-action">
                    <i class="fa-solid fa-arrow-right-arrow-left"></i>
                    <span>Transaksi</span>
                </a>
                <a href="{{ route('banks.index') }}" class="quick-action">
                    <i class="fa-solid fa-building-columns"></i>
                    <span>Rekening</span>
                </a>
                <a href="{{ route('banks.transfer') }}" class="quick-action">
                    <i class="fa-solid fa-arrow-right-arrow-left"></i>
                    <span>Transfer</span>
                </a>
            </div>
        </section>

        <section class="mobile-section">
            <div class="mobile-insight-card">
                <div class="mobile-insight-icon">
                    <i class="fa-solid fa-heart-circle-check"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-[13px] font-extrabold text-[var(--text-primary)]">
                        @if($monthlyIncome > $monthlyExpense)
                            Masih manis, saldo bulan ini positif.
                        @elseif($monthlyIncome > 0)
                            Pengeluaran mulai mendekati pemasukan.
                        @else
                            Yuk mulai catat pemasukan pertama.
                        @endif
                    </div>
                    <div class="text-[11px] text-[var(--text-secondary)] mt-1">
                        Pengeluaran terpakai {{ $spendingPercent }}% dari pemasukan bulan ini.
                    </div>
                    <div class="mobile-progress">
                        <span style="width: {{ $spendingPercent }}%;"></span>
                    </div>
                </div>
            </div>
        </section>

        @if($achievementBadges->isNotEmpty())
            <section class="mobile-section">
                <div class="mobile-section-head">
                    <h2 class="mobile-section-title">Badge Kamu</h2>
                    <span class="text-[11px] font-bold text-[var(--text-secondary)]">{{ $achievementBadges->count() }} aktif</span>
                </div>
                <div class="mobile-badge-strip">
                    @foreach($achievementBadges as $badge)
                        <div class="mobile-achievement-badge">
                            <div class="achievement-icon" style="background: {{ $badge['color'] }};">
                                <i class="fa-solid {{ $badge['icon'] }}"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="text-[13px] font-extrabold text-[var(--text-primary)] truncate">{{ $badge['title'] }}</div>
                                <div class="text-[11px] leading-snug text-[var(--text-secondary)] mt-1">{{ $badge['description'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if($topTarget)
            <section class="mobile-section">
                <div class="mobile-section-head">
                    <h2 class="mobile-section-title">Target Favorit</h2>
                    <a href="{{ route('targets.index') }}" class="mobile-link">Kelola</a>
                </div>
                <div class="mobile-insight-card">
                    <div class="mobile-insight-icon" style="background: {{ $topTarget->color }}14; color: {{ $topTarget->color }};">
                        <span>{{ $topTarget->icon }}</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-2">
                            <div class="text-[13px] font-extrabold text-[var(--text-primary)] truncate">{{ $topTarget->name }}</div>
                            <div class="text-[12px] font-extrabold" style="color: {{ $topTarget->color }};">
                                {{ $topTarget->progress_percent }}%
                            </div>
                        </div>
                        <div class="mobile-progress">
                            <span style="width: {{ $topTarget->progress_percent }}%; background: {{ $topTarget->color }};"></span>
                        </div>
                        <div class="text-[11px] text-[var(--text-secondary)] mt-2 truncate">
                            Rp {{ number_format($topTarget->current_amount, 0, ',', '.') }} / Rp
                            {{ number_format($topTarget->target_amount, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <section class="mobile-section">
            <div class="mobile-section-head">
                <h2 class="mobile-section-title">Transaksi Terbaru</h2>
                <a href="{{ route('transactions.index') }}" class="mobile-link">Lihat semua</a>
            </div>
            <div class="mobile-list-card">
                @forelse($transactions->take(5) as $tx)
                    <div class="mobile-tx-item">
                        <div class="mobile-tx-icon" style="background:{{ $tx->category->color }}18;">
                            {{ $tx->category->icon }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="mobile-tx-title">{{ $tx->description }}</div>
                            <div class="mobile-tx-meta">{{ $tx->user->name }} - {{ $tx->date->isoFormat('D MMM, HH:mm:ss') }} - {{ $tx->bank->name }}</div>
                        </div>
                        <div class="mobile-tx-amount {{ $tx->type === 'income' ? 'text-green-700' : 'text-rose-600' }}">
                            {{ $tx->type === 'income' ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="w-12 h-12 rounded-2xl bg-pink-50 text-pink-600 flex items-center justify-center mx-auto mb-3">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <p class="text-[13px] text-[var(--text-secondary)] m-0">Belum ada transaksi bulan ini.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <div class="dashboard-desktop-view">
    {{-- Page Header --}}
    <div class="flex items-center justify-between flex-wrap gap-4 mb-6" id="tourDashboardHeader">
        <div class="flex-1 min-w-[250px]">
            <h1 class="page-title mb-1">Halo, {{ auth()->user()->name }}! 👋</h1>
            <p class="page-subtitle m-0">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
        </div>

        {{-- Filter Pasangan --}}
        <form action="{{ url()->current() }}" method="GET" id="filterForm" class="m-0" data-tour="member-filter">
            <select name="user_id" onchange="document.getElementById('filterForm').submit();"
                class="input-field py-2 px-3 rounded-xl min-w-[160px] cursor-pointer h-auto text-[13px] font-semibold">
                <option value="">👨‍👩‍ Semua Transaksi</option>
                @foreach($coupleMembers as $member)
                    <option value="{{ $member->id }}" {{ $selectedUserId == $member->id ? 'selected' : '' }}>
                        {{ $member->avatar ?? '👤' }}
                        {{ $member->id == auth()->id() ? 'Saya (' . $member->name . ')' : $member->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    @if($debtReminders->isNotEmpty())
        <div class="card p-4 mb-6 border-orange-200 bg-orange-50">
            <div class="flex items-center justify-between gap-3 mb-3">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="w-10 h-10 rounded-xl bg-white text-orange-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-bell"></i>
                    </span>
                    <div class="min-w-0">
                        <h3 class="text-base font-bold text-orange-900 m-0">Pengingat Hutang/Piutang</h3>
                        <p class="text-xs text-orange-700 m-0">Muncul dari H-3 sampai selesai dibayar.</p>
                    </div>
                </div>
                <a href="{{ route('debts.index') }}" class="text-xs font-bold text-orange-700 no-underline shrink-0">Lihat semua</a>
            </div>
            <div class="grid grid-cols-[repeat(auto-fit,minmax(240px,1fr))] gap-3">
                @foreach($debtReminders as $reminder)
                    @php
                        $daysUntilDue = now()->startOfDay()->diffInDays($reminder->due_date->startOfDay(), false);
                        $dueLabel = $daysUntilDue < 0
                            ? 'Telat ' . abs($daysUntilDue) . ' hari'
                            : ($daysUntilDue === 0 ? 'Jatuh tempo hari ini' : 'H-' . $daysUntilDue);
                    @endphp
                    <a href="{{ route('debts.index', ['type' => $reminder->type, 'user_id' => $selectedUserId]) }}"
                        class="block rounded-xl bg-white border border-orange-100 p-3 no-underline">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <div class="text-sm font-bold text-[var(--text-primary)] truncate">
                                    {{ $reminder->type === 'hutang' ? 'Bayar hutang' : 'Tagih piutang' }} {{ $reminder->counterparty }}
                                </div>
                                <div class="text-xs text-[var(--text-secondary)] mt-1 truncate">
                                    {{ $reminder->user->name }} - {{ $reminder->purpose }}
                                </div>
                            </div>
                            <span class="text-[10px] font-bold text-orange-700 bg-orange-100 rounded-full px-2 py-1 whitespace-nowrap">
                                {{ $dueLabel }}
                            </span>
                        </div>
                        <div class="text-sm font-extrabold text-orange-700 mt-3">
                            Rp {{ number_format($reminder->amount, 0, ',', '.') }}
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Summary Cards --}}
    @php $balance = $monthlyIncome - $monthlyExpense; @endphp
    <div class="grid grid-cols-[repeat(auto-fit,minmax(240px,1fr))] gap-4 mb-7" id="tourSummaryCards">

        <div class="card border-l-4 border-pink-400 p-4">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold tracking-widest uppercase text-[var(--text-secondary)]">Saldo
                    Bersih</span>
                <span
                    class="w-[34px] h-[34px] rounded-[10px] bg-[var(--pink-light)] flex items-center justify-center text-[var(--pink-dark)] shrink-0">
                    <i class="fa-solid fa-wallet text-sm"></i>
                </span>
            </div>
            <div class="text-2xl font-bold mb-1 break-all {{ $balance >= 0 ? 'text-green-600' : 'text-rose-600' }}">
                Rp {{ number_format($balance, 0, ',', '.') }}
            </div>
            <div class="text-xs text-[var(--text-secondary)]">{{ $balance >= 0 ? '+' : '-' }} Bulan
                {{ now()->isoFormat('MMMM Y') }}
            </div>
        </div>

        <div class="card border-l-4 border-blue-500 p-4">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold tracking-widest uppercase text-[var(--text-secondary)]">
                    {{ $selectedUserId ? 'Total Kekayaan Pribadi' : 'Total Kekayaan' }}
                </span>
                <span
                    class="w-[34px] h-[34px] rounded-[10px] bg-blue-50 flex items-center justify-center text-blue-700 shrink-0">
                    <i class="fa-solid fa-coins text-sm"></i>
                </span>
            </div>
            <div class="text-2xl font-bold text-blue-700 mb-1 break-all">
                Rp {{ number_format($totalWealth, 0, ',', '.') }}
            </div>
            <div class="text-xs text-[var(--text-secondary)]">
                @if($selectedUserId)
                    Saldo rekening aktif milik {{ $coupleMembers->firstWhere('id', $selectedUserId)->name ?? 'pasangan' }}
                @else
                    Saldo seluruh rekening aktif
                @endif
            </div>
            <div class="text-xs text-blue-700 mt-2 font-medium">
                Jika ditambah piutang: Rp {{ number_format($totalWealthIncludingPiutang, 0, ',', '.') }}
            </div>
        </div>

        <div class="card border-l-4 border-rose-500 p-4">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold tracking-widest uppercase text-[var(--text-secondary)]">Hutang
                    Belum Dibayar</span>
                <span
                    class="w-[34px] h-[34px] rounded-[10px] bg-rose-50 flex items-center justify-center text-rose-600 shrink-0">
                    <i class="fa-solid fa-hand-holding-dollar text-sm"></i>
                </span>
            </div>
            <div class="text-2xl font-bold text-rose-600 mb-1 break-all">
                Rp {{ number_format($outstandingHutang, 0, ',', '.') }}
            </div>
            <div class="text-xs text-[var(--text-secondary)]">
                {{ $selectedUserId ? 'Total hutang orang yang dipilih yang harus dibayar' : 'Total hutang yang harus dibayar' }}
            </div>
        </div>

        <div class="card border-l-4 border-green-500 p-4">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold tracking-widest uppercase text-[var(--text-secondary)]">Piutang
                    Belum Kembali</span>
                <span
                    class="w-[34px] h-[34px] rounded-[10px] bg-green-50 flex items-center justify-center text-green-700 shrink-0">
                    <i class="fa-solid fa-hand-holding-hand text-sm"></i>
                </span>
            </div>
            <div class="text-2xl font-bold text-green-700 mb-1 break-all">
                Rp {{ number_format($outstandingPiutang, 0, ',', '.') }}
            </div>
            <div class="text-xs text-[var(--text-secondary)]">
                {{ $selectedUserId ? 'Total piutang orang yang dipilih yang belum kembali' : 'Total piutang yang belum kembali' }}
            </div>
        </div>
    </div>

    @if($achievementBadges->isNotEmpty())
        <div class="card p-4 mb-7">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-base font-bold text-[var(--text-primary)] m-0">Badge Achievement</h3>
                    <p class="text-xs text-[var(--text-secondary)] m-0">Pencapaian otomatis dari aktivitas kalian.</p>
                </div>
                <span class="rounded-full bg-pink-50 px-3 py-1 text-xs font-bold text-pink-700">{{ $achievementBadges->count() }} badge</span>
            </div>
            <div class="grid grid-cols-[repeat(auto-fit,minmax(210px,1fr))] gap-3">
                @foreach($achievementBadges as $badge)
                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-3 flex items-start gap-3">
                        <span class="achievement-icon" style="background: {{ $badge['color'] }};">
                            <i class="fa-solid {{ $badge['icon'] }}"></i>
                        </span>
                        <span class="min-w-0">
                            <span class="block text-sm font-extrabold text-[var(--text-primary)]">{{ $badge['title'] }}</span>
                            <span class="block text-xs leading-relaxed text-[var(--text-secondary)] mt-1">{{ $badge['description'] }}</span>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Mid Row Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-5 mb-5">

        {{-- Recent Transactions --}}
        <div class="card p-4">
            <div class="flex items-center justify-between mb-5 flex-wrap gap-2">
                <h3 class="text-lg font-bold text-[var(--text-primary)] m-0">Transaksi Terbaru</h3>
                <a href="{{ route('transactions.index') }}"
                    class="text-[13px] text-[var(--pink-dark)] font-semibold no-underline">Lihat Semua →</a>
            </div>
            <div class="flex flex-col gap-1">
                @forelse($transactions as $tx)
                    <div class="flex items-center gap-3.5 px-2.5 py-2.5 rounded-xl transition-colors hover:bg-gray-50
                                                            max-[576px]:flex-wrap max-[576px]:gap-2.5">
                        <div class="w-[42px] h-[42px] rounded-xl flex items-center justify-center text-lg shrink-0"
                            style="background:{{ $tx->category->color }}18;">
                            {{ $tx->category->icon }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div
                                class="text-sm font-semibold text-[var(--text-primary)] whitespace-nowrap overflow-hidden text-ellipsis">
                                {{ $tx->description }}
                            </div>
                            <div class="text-xs text-[var(--text-secondary)] mt-0.5 flex items-center gap-1.5 flex-wrap">
                                <span class="whitespace-nowrap inline-flex items-center gap-2">
                                    @if($tx->user->profile_photo_url)
                                        <img src="{{ $tx->user->profile_photo_url }}" alt="{{ $tx->user->name }}" style="width:20px; height:20px; border-radius:50%; object-fit:cover;" />
                                    @else
                                        <span style="display:inline-flex; width:20px; height:20px; border-radius:50%; background:#f3f4f6; color:#374151; align-items:center; justify-content:center; font-size:12px;">{{ $tx->user->avatar }}</span>
                                    @endif
                                    {{ $tx->user->name }}
                                </span>
                                <span class="opacity-40">•</span>
                                <span class="whitespace-nowrap">{{ $tx->date->isoFormat('D MMM, HH:mm:ss') }}</span>
                                <span class="opacity-40">•</span>
                                <span class="whitespace-nowrap">{{ $tx->bank->icon }} {{ $tx->bank->name }}</span>
                            </div>
                        </div>
                        <div
                            class="text-right shrink-0 flex flex-col items-end gap-1
                                                                max-[576px]:w-full max-[576px]:flex-row max-[576px]:justify-between max-[576px]:items-center max-[576px]:border-t max-[576px]:border-dashed max-[576px]:border-gray-100 max-[576px]:pt-2 max-[576px]:mt-1">
                            <div
                                class="text-sm font-bold whitespace-nowrap {{ $tx->type === 'income' ? 'text-green-700' : 'text-rose-600' }}">
                                {{ $tx->type === 'income' ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </div>
                            <span class="{{ $tx->type === 'income' ? 'income-badge' : 'expense-badge' }}">
                                {{ $tx->type === 'income' ? 'Masuk' : 'Keluar' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10">
                        <div class="text-4xl mb-2">💸</div>
                        <p class="text-[13px] text-[var(--text-secondary)] m-0">Belum ada transaksi</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Right Side Column --}}
        <div
            class="flex flex-col gap-5 max-[992px]:grid max-[992px]:grid-cols-[repeat(auto-fit,minmax(280px,1fr))] max-[576px]:grid-cols-1">

            {{-- Top Pengeluaran --}}
            <div class="card p-4">
                <h3 class="text-base font-bold text-[var(--text-primary)] mt-0 mb-4">Top Pengeluaran</h3>
                @php $totalExp = $expenseByCategory->sum('amount') ?: 1; @endphp
                @forelse($expenseByCategory as $cat)
                    <div class="mb-3.5">
                        <div class="flex items-center justify-between mb-1.5 gap-2">
                            <span
                                class="text-[13px] text-[var(--text-primary)] flex items-center gap-2 min-w-0 overflow-hidden text-ellipsis whitespace-nowrap">
                                <span>{{ $cat['icon'] }}</span> {{ $cat['name'] }}
                            </span>
                            <span class="text-xs font-bold text-[var(--text-secondary)] shrink-0">
                                {{ number_format($cat['amount'] / $totalExp * 100, 1) }}%
                            </span>
                        </div>
                        <div class="h-1.5 rounded-full bg-slate-100">
                            <div class="h-1.5 rounded-full transition-[width] duration-[0.6s]"
                                style="width:{{ $cat['amount'] / $totalExp * 100 }}%; background:{{ $cat['color'] }};"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-[13px] text-[var(--text-secondary)] text-center py-4 m-0">Belum ada pengeluaran 🎉</p>
                @endforelse
            </div>

            {{-- Rekening --}}
            <div class="card p-4">
                <div class="flex items-center justify-between mb-4 gap-2">
                    <h3 class="text-base font-bold text-[var(--text-primary)] m-0">Rekening</h3>
                    <a href="{{ route('banks.index') }}"
                        class="text-xs text-[var(--pink-dark)] font-semibold no-underline">Kelola →</a>
                </div>
                <div class="flex flex-col gap-2.5">
                    @forelse($banks as $bank)
                        <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl min-w-0"
                            style="background:{{ $bank->color }}12; border:1px solid {{ $bank->color }}28;">
                            <span class="text-xl shrink-0">{{ $bank->icon }}</span>
                            <div class="flex-1 min-w-0">
                                <div
                                    class="text-[13px] font-semibold text-[var(--text-primary)] overflow-hidden text-ellipsis whitespace-nowrap">
                                    {{ $bank->name }} ({{ $bank->account_name }})
                                </div>
                                <div class="text-xs font-bold overflow-hidden text-ellipsis whitespace-nowrap"
                                    data-sensitive-money
                                    data-mask-text="Rp •••••••"
                                    style="color:{{ $bank->color }};">
                                    Rp {{ number_format($bank->current_balance, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-[var(--text-secondary)] text-center py-2 m-0">Tambahkan rekening dulu</p>
                    @endforelse
                </div>
            </div>

            {{-- Target Nabung --}}
            <div class="card p-4">
                <div class="flex items-center justify-between mb-4 gap-2">
                    <h3 class="text-base font-bold text-[var(--text-primary)] m-0">Target Nabung</h3>
                    <a href="{{ route('targets.index') }}"
                        class="text-xs text-[var(--pink-dark)] font-semibold no-underline">Kelola →</a>
                </div>
                <div class="flex flex-col gap-2.5">
                    @forelse($targets as $target)
                        <div class="p-3 rounded-xl"
                            style="background:{{ $target->color }}10; border:1px solid {{ $target->color }}25;">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="shrink-0">{{ $target->icon }}</span>
                                <span
                                    class="text-[13px] font-semibold text-[var(--text-primary)] flex-1 overflow-hidden text-ellipsis whitespace-nowrap">
                                    {{ $target->name }}
                                </span>
                                <span class="text-xs font-bold shrink-0" style="color:{{ $target->color }};">
                                    {{ $target->progress_percent }}%
                                </span>
                            </div>
                            <div class="h-1.5 rounded-full bg-slate-100 mb-1.5">
                                <div class="h-1.5 rounded-full"
                                    style="width:{{ $target->progress_percent }}%; background:{{ $target->color }};"></div>
                            </div>
                            <div
                                class="text-[11px] text-[var(--text-secondary)] overflow-hidden text-ellipsis whitespace-nowrap">
                                Rp {{ number_format($target->current_amount, 0, ',', '.') }} / Rp
                                {{ number_format($target->target_amount, 0, ',', '.') }}
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-[var(--text-secondary)] text-center py-2 m-0">Buat target dulu!</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
    </div>
@endsection

@if(session('show_onboarding'))
    @push('scripts')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.3.6/dist/driver.css">
        <script src="https://cdn.jsdelivr.net/npm/driver.js@1.3.6/dist/driver.js.iife.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const driverFactory = window.driver?.js?.driver;
                if (!driverFactory) return;

                const firstVisible = selectors => selectors
                    .map(selector => document.querySelector(selector))
                    .find(element => element && element.offsetParent !== null);

                const rawSteps = [
                    {
                        element: firstVisible(['#tourDashboardHeader', '.mobile-home-hero']),
                        popover: {
                            title: 'Selamat datang di DompetKita',
                            description: 'Ini ringkasan keuangan kamu dan pasangan. Dari sini kalian bisa mulai melihat kondisi bulan ini.',
                        },
                    },
                    {
                        element: firstVisible(['#inviteBlock', '#tourMobileInvite', '#profileInviteLink']),
                        popover: {
                            title: 'Undang pasangan',
                            description: 'Salin link undangan, kirim ke pasangan, lalu kode akan otomatis terisi saat dia daftar.',
                        },
                    },
                    {
                        element: firstVisible(['#tourSummaryCards', '.mobile-metrics-grid']),
                        popover: {
                            title: 'Pantau saldo dan kekayaan',
                            description: 'Kartu ini membantu melihat pemasukan, pengeluaran, rekening aktif, hutang, dan piutang.',
                        },
                    },
                    {
                        element: firstVisible(['[data-tour="member-filter"]', '#mobileUserFilter']),
                        popover: {
                            title: 'Filter per orang',
                            description: 'Pakai filter ini untuk melihat semua transaksi pasangan atau hanya data salah satu orang.',
                        },
                    },
                    {
                        element: firstVisible(['#tourNavTransactions', '#tourMobileQuickActions']),
                        popover: {
                            title: 'Catat transaksi pertama',
                            description: 'Mulai dari transaksi. Setelah rutin dicatat, laporan dan target tabungan jadi lebih bermakna.',
                        },
                    },
                    {
                        element: firstVisible(['#tourNavBanks', '#tourNavTargets']),
                        popover: {
                            title: 'Lengkapi rekening dan target',
                            description: 'Tambahkan rekening atau e-wallet, lalu buat target nabung berdua agar progresnya terlihat.',
                        },
                    },
                ].filter(step => step.element);

                if (!rawSteps.length) return;

                driverFactory({
                    showProgress: true,
                    animate: true,
                    allowClose: true,
                    nextBtnText: 'Lanjut',
                    prevBtnText: 'Kembali',
                    doneBtnText: 'Selesai',
                    popoverClass: 'dompetkita-driver-popover',
                    steps: rawSteps,
                }).drive();
            });
        </script>
    @endpush
@endif
