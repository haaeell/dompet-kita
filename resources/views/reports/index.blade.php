@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
    <style>
        .report-mobile-view {
            display: none;
        }

        @media (max-width: 768px) {
            .report-desktop-view {
                display: none;
            }

            .report-mobile-view {
                display: block;
                margin: -8px -4px 0;
            }

            .report-hero {
                position: relative;
                overflow: hidden;
                border-radius: 28px;
                padding: 20px;
                color: white;
                background:
                    radial-gradient(circle at 82% 16%, rgba(255, 255, 255, 0.24) 0 14%, transparent 28%),
                    linear-gradient(135deg, #0ea5e9 0%, #db2777 62%, #9d174d 100%);
                box-shadow: 0 18px 38px rgba(219, 39, 119, 0.2);
            }

            .report-hero::after {
                content: '';
                position: absolute;
                width: 150px;
                height: 150px;
                right: -60px;
                bottom: -72px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.14);
            }

            .report-hero-inner {
                position: relative;
                z-index: 1;
            }

            .report-hero-label {
                font-size: 11px;
                font-weight: 800;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                opacity: 0.78;
            }

            .report-hero-value {
                margin-top: 8px;
                font-size: clamp(26px, 8vw, 34px);
                font-weight: 900;
                line-height: 1.05;
                word-break: break-word;
            }

            .report-hero-meta {
                margin-top: 8px;
                font-size: 12px;
                font-weight: 700;
                opacity: 0.82;
            }

            .report-filter-card,
            .report-mobile-card {
                border-radius: 24px;
                background: white;
                border: 1px solid var(--border);
                box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
            }

            .report-filter-card {
                margin-top: 16px;
                padding: 14px;
            }

            .report-filter-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .report-filter-grid .full {
                grid-column: 1 / -1;
            }

            .report-mobile-label {
                display: block;
                margin-bottom: 6px;
                color: var(--text-secondary);
                font-size: 10px;
                font-weight: 800;
                letter-spacing: 0.06em;
                text-transform: uppercase;
            }

            .report-mobile-input {
                width: 100%;
                min-height: 44px;
                border: 1px solid #e2e8f0;
                border-radius: 16px;
                background: #fff;
                color: var(--text-primary);
                padding: 10px 12px;
                font-family: 'Poppins', sans-serif;
                font-size: 13px;
                font-weight: 700;
                outline: none;
            }

            .report-mobile-section {
                margin-top: 20px;
            }

            .report-section-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                margin-bottom: 10px;
            }

            .report-section-title {
                margin: 0;
                color: var(--text-primary);
                font-size: 16px;
                font-weight: 900;
            }

            .report-metric-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
            }

            .report-metric {
                min-height: 118px;
                border-radius: 22px;
                padding: 14px;
                background: white;
                border: 1px solid var(--border);
                box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
            }

            .report-metric-icon {
                width: 38px;
                height: 38px;
                border-radius: 15px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 10px;
                font-size: 15px;
            }

            .report-metric-label {
                color: var(--text-secondary);
                font-size: 10px;
                font-weight: 800;
                letter-spacing: 0.06em;
                text-transform: uppercase;
            }

            .report-metric-value {
                margin-top: 6px;
                color: var(--text-primary);
                font-size: 16px;
                font-weight: 900;
                line-height: 1.16;
                word-break: break-word;
            }

            .report-mobile-card {
                padding: 16px;
            }

            .report-chart-box {
                position: relative;
                width: 100%;
                height: 230px;
            }

            .report-category-item,
            .report-person-item,
            .report-tx-item,
            .report-debt-item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 12px 0;
                border-bottom: 1px solid #f1f5f9;
            }

            .report-category-item:last-child,
            .report-person-item:last-child,
            .report-tx-item:last-child,
            .report-debt-item:last-child {
                border-bottom: 0;
                padding-bottom: 0;
            }

            .report-dot,
            .report-tx-icon {
                width: 40px;
                height: 40px;
                border-radius: 15px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .report-small-title {
                color: var(--text-primary);
                font-size: 13px;
                font-weight: 900;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .report-small-meta {
                margin-top: 3px;
                color: var(--text-secondary);
                font-size: 11px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .report-amount {
                margin-left: auto;
                flex-shrink: 0;
                text-align: right;
                font-size: 12px;
                font-weight: 900;
                white-space: nowrap;
            }
        }
    </style>

    <div class="report-mobile-view">
        <section class="report-hero">
            <div class="report-hero-inner">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="report-hero-label">Saldo Bersih</div>
                        <div class="report-hero-value">
                            {{ $balance >= 0 ? '+' : '-' }}Rp {{ number_format(abs($balance), 0, ',', '.') }}
                        </div>
                        <div class="report-hero-meta">
                            {{ $startDate->isoFormat('D MMM') }} - {{ $endDate->isoFormat('D MMM Y') }}
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-white/20 border border-white/20 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mt-5">
                    <div class="rounded-2xl bg-white/15 border border-white/20 p-3">
                        <div class="text-[10px] font-extrabold opacity-75 uppercase">Pemasukan</div>
                        <div class="text-[13px] font-black mt-1 break-words">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
                    </div>
                    <div class="rounded-2xl bg-white/15 border border-white/20 p-3">
                        <div class="text-[10px] font-extrabold opacity-75 uppercase">Pengeluaran</div>
                        <div class="text-[13px] font-black mt-1 break-words">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </section>

        <form method="GET" class="report-filter-card">
            <div class="report-filter-grid">
                <div class="full">
                    <label class="report-mobile-label">Lihat laporan</label>
                    <select name="user_filter" class="report-mobile-input">
                        <option value="all" {{ $userFilter == 'all' ? 'selected' : '' }}>Berdua (Gabungan)</option>
                        <option value="me" {{ $userFilter == 'me' ? 'selected' : '' }}>Saya Sendiri</option>
                        <option value="partner" {{ $userFilter == 'partner' ? 'selected' : '' }}>Pasangan</option>
                    </select>
                </div>
                <div>
                    <label class="report-mobile-label">Bulan</label>
                    <select name="month" id="mobileReportMonth" class="report-mobile-input">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m)->isoFormat('MMMM') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="report-mobile-label">Tahun</label>
                    <select name="year" id="mobileReportYear" class="report-mobile-input">
                        @foreach([now()->year, now()->year - 1, now()->year - 2] as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="report-mobile-label">Dari</label>
                    <input type="date" id="mobileReportStartDate" name="start_date"
                        value="{{ $startDate->toDateString() }}" class="report-mobile-input">
                </div>
                <div>
                    <label class="report-mobile-label">Sampai</label>
                    <input type="date" id="mobileReportEndDate" name="end_date"
                        value="{{ $endDate->toDateString() }}" class="report-mobile-input">
                </div>
                <button type="submit" class="btn-primary justify-center full">
                    <i class="fa-solid fa-filter"></i> Terapkan Filter
                </button>
            </div>
        </form>

        <section class="report-mobile-section">
            <div class="report-section-head">
                <h2 class="report-section-title">Ringkasan</h2>
                <span class="text-[11px] font-bold text-[var(--text-secondary)]">{{ $transactions->count() }} transaksi</span>
            </div>
            <div class="report-metric-grid">
                <div class="report-metric">
                    <div class="report-metric-icon bg-blue-50 text-blue-700">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                    <div class="report-metric-label">Total Kekayaan</div>
                    <div class="report-metric-value text-blue-700">Rp {{ number_format($totalWealth, 0, ',', '.') }}</div>
                </div>
                <div class="report-metric">
                    <div class="report-metric-icon bg-violet-50 text-violet-700">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>
                    <div class="report-metric-label">Hutang/Piutang</div>
                    <div class="report-metric-value text-violet-700">{{ $debts->count() }} Catatan</div>
                </div>
                <div class="report-metric">
                    <div class="report-metric-icon bg-rose-50 text-rose-600">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                    <div class="report-metric-label">Hutang</div>
                    <div class="report-metric-value text-rose-600">Rp {{ number_format($outstandingHutang, 0, ',', '.') }}</div>
                </div>
                <div class="report-metric">
                    <div class="report-metric-icon bg-emerald-50 text-emerald-700">
                        <i class="fa-solid fa-hand-holding-hand"></i>
                    </div>
                    <div class="report-metric-label">Piutang</div>
                    <div class="report-metric-value text-emerald-700">Rp {{ number_format($outstandingPiutang, 0, ',', '.') }}</div>
                </div>
            </div>
        </section>

        <section class="report-mobile-section">
            <div class="report-section-head">
                <h2 class="report-section-title">{{ $startDate->diffInDays($endDate) <= 30 ? 'Tren Harian' : 'Tren Bulanan' }}</h2>
            </div>
            <div class="report-mobile-card">
                <div class="report-chart-box">
                    <canvas id="mobileTrendChart"></canvas>
                </div>
            </div>
        </section>

        <section class="report-mobile-section">
            <div class="report-section-head">
                <h2 class="report-section-title">Kategori Boros</h2>
            </div>
            <div class="report-mobile-card">
                <div class="report-chart-box" style="height: 180px;">
                    <canvas id="mobilePieChart"></canvas>
                </div>
                <div class="mt-3">
                    @forelse($expenseByCategory->take(5) as $cat)
                        <div class="report-category-item">
                            <div class="report-dot" style="background: {{ $cat['color'] }}18; color: {{ $cat['color'] }};">
                                {{ $cat['icon'] }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="report-small-title">{{ $cat['name'] }}</div>
                                <div class="report-small-meta">{{ $cat['count'] }} transaksi</div>
                            </div>
                            <div class="report-amount">Rp {{ number_format($cat['amount'], 0, ',', '.') }}</div>
                        </div>
                    @empty
                        <p class="text-center text-[13px] text-[var(--text-secondary)] py-5 m-0">Tidak ada pengeluaran.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="report-mobile-section">
            <div class="report-section-head">
                <h2 class="report-section-title">Per Orang</h2>
            </div>
            <div class="report-mobile-card">
                @foreach($userSummary as $summary)
                    <div class="report-person-item">
                        @if($summary['user']->profile_photo_url)
                            <img src="{{ $summary['user']->profile_photo_url }}" alt="{{ $summary['user']->name }}"
                                class="w-10 h-10 rounded-2xl object-cover shrink-0">
                        @else
                            <div class="report-dot bg-pink-50 text-pink-700">{{ $summary['user']->avatar ?? '👤' }}</div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <div class="report-small-title">{{ $summary['user']->name }}</div>
                            <div class="report-small-meta">
                                Masuk Rp {{ number_format($summary['income'], 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="report-amount text-rose-600">
                            Rp {{ number_format($summary['expense'], 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="report-mobile-section">
            <div class="report-section-head">
                <h2 class="report-section-title">Transaksi</h2>
            </div>
            <div class="report-mobile-card">
                @forelse($transactions->take(8) as $tx)
                    <div class="report-tx-item">
                        <div class="report-tx-icon" style="background: {{ $tx->category->color }}18;">
                            {{ $tx->category->icon }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="report-small-title">{{ $tx->description }}</div>
                            <div class="report-small-meta">{{ $tx->user->name }} - {{ $tx->date->isoFormat('D MMM Y') }}</div>
                        </div>
                        <div class="report-amount {{ $tx->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $tx->type === 'income' ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                        </div>
                    </div>
                @empty
                    <p class="text-center text-[13px] text-[var(--text-secondary)] py-5 m-0">Tidak ada transaksi.</p>
                @endforelse
            </div>
        </section>
    </div>

    <div class="report-desktop-view">
    {{-- Header Laporan --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="font-display text-3xl font-bold text-[var(--text-primary)]" style="color: #1a1a2e;">Laporan Keuangan
            </h1>
            <p class="text-gray-500 text-sm mt-1">Pantau kondisi keuangan kalian bersama</p>
        </div>

        {{-- Ganti form filter lama kamu di bagian atas dengan ini --}}
        <form method="GET" id="reportFilterForm" class="flex items-center gap-2 flex-wrap">
            {{-- Filter Berdua / Individu --}}
            <select name="user_filter" class="input-field"
                style="width:150px; background: #fff; border: 1px solid #e2e8f0; color: #1a1a2e;">
                <option value="all" {{ $userFilter == 'all' ? 'selected' : '' }}>👥 Berdua (Gabungan)</option>
                <option value="me" {{ $userFilter == 'me' ? 'selected' : '' }}>👤 Saya Sendiri</option>
                <option value="partner" {{ $userFilter == 'partner' ? 'selected' : '' }}>💝 Pasangan</option>
            </select>

            {{-- Filter Bulan --}}
            <select name="month" id="reportMonth" class="input-field"
                style="width:130px; background: #fff; border: 1px solid #e2e8f0; color: #1a1a2e;">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create(null, $m)->isoFormat('MMMM') }}
                    </option>
                @endforeach
            </select>

            {{-- Filter Tahun --}}
            <select name="year" id="reportYear" class="input-field"
                style="width:100px; background: #fff; border: 1px solid #e2e8f0; color: #1a1a2e;">
                @foreach([now()->year, now()->year - 1, now()->year - 2] as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>

            <input type="text" id="reportStartDate" name="start_date" value="{{ $startDate->toDateString() }}"
                class="input-field js-date-picker" data-format="Y-m-d" data-alt-format="j F Y"
                style="width:160px; background: #fff; border: 1px solid #e2e8f0; color: #1a1a2e;">

            <input type="text" id="reportEndDate" name="end_date" value="{{ $endDate->toDateString() }}"
                class="input-field js-date-picker" data-format="Y-m-d" data-alt-format="j F Y"
                style="width:160px; background: #fff; border: 1px solid #e2e8f0; color: #1a1a2e;">

            <button type="submit" class="btn-primary">Terapkan</button>
        </form>
    </div>

    <div class="mb-6 text-sm text-gray-500">
        Periode laporan: <span class="font-semibold text-gray-700">{{ $startDate->isoFormat('D MMMM Y') }}</span> sampai
        <span class="font-semibold text-gray-700">{{ $endDate->isoFormat('D MMMM Y') }}</span>
    </div>

    {{-- Ringkasan / Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
        <div class="card p-6"
            style="background: #fff; border-left: 4px solid #2563eb; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-3">Total Kekayaan</div>
            <div class="font-display text-3xl font-bold text-sky-700">
                Rp {{ number_format($totalWealth, 0, ',', '.') }}
            </div>
            <div class="text-xs text-gray-500 mt-2">
                {{ $userFilter === 'all' ? 'Saldo semua rekening aktif' : 'Saldo rekening aktif sesuai filter orang yang dipilih' }}
            </div>
            <div class="text-xs text-sky-700 mt-2 font-semibold">
                Jika ditambah piutang: Rp {{ number_format($totalWealthIncludingPiutang, 0, ',', '.') }}
            </div>
        </div>
        <div class="card p-6"
            style="background: #fff; border-left: 4px solid #22c55e; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-3">Total Pemasukan</div>
            <div class="font-display text-3xl font-bold text-emerald-600">
                Rp {{ number_format($totalIncome, 0, ',', '.') }}
            </div>
        </div>
        <div class="card p-6"
            style="background: #fff; border-left: 4px solid #f43f5e; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-3">Total Pengeluaran</div>
            <div class="font-display text-3xl font-bold text-rose-600">
                Rp {{ number_format($totalExpense, 0, ',', '.') }}
            </div>
        </div>
        <div class="card p-6"
            style="background: #fff; border-left: 4px solid #f472b6; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-3">Saldo Bersih</div>
            <div class="font-display text-3xl font-bold {{ $balance >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                {{ $balance >= 0 ? '+' : '' }}Rp {{ number_format($balance, 0, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- Debt & Receivable Summary --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-8">
        <div class="card p-6" style="background:#fff; border-left:4px solid #ef4444; border-radius:16px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);">
            <div class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-3">Hutang Belum Dibayar</div>
            <div class="font-display text-3xl font-bold text-rose-600">Rp {{ number_format($outstandingHutang, 0, ',', '.') }}</div>
            <div class="text-xs text-gray-500 mt-2">
                {{ $userFilter === 'all' ? 'Semua hutang yang belum diselesaikan' : 'Hutang sesuai orang yang sedang dipilih' }}
            </div>
        </div>
        <div class="card p-6" style="background:#fff; border-left:4px solid #16a34a; border-radius:16px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);">
            <div class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-3">Piutang Belum Kembali</div>
            <div class="font-display text-3xl font-bold text-emerald-600">Rp {{ number_format($outstandingPiutang, 0, ',', '.') }}</div>
            <div class="text-xs text-gray-500 mt-2">
                {{ $userFilter === 'all' ? 'Piutang yang masih harus dikembalikan' : 'Piutang sesuai orang yang sedang dipilih' }}
            </div>
        </div>
        <div class="card p-6" style="background:#fff; border-left:4px solid #8b5cf6; border-radius:16px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);">
            <div class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-3">Total Catatan Hutang/Piutang</div>
            <div class="font-display text-3xl font-bold text-violet-700">{{ $debts->count() }}</div>
            <div class="text-xs text-gray-500 mt-2">Jumlah catatan hutang/piutang</div>
        </div>
    </div>

    {{-- Seksi Grafik & Visualisasi --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Tren Periode --}}
        <div class="card p-6 lg:col-span-2 flex flex-col justify-between"
            style="background: #fff; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <h3 class="font-display font-bold text-gray-800 mb-4 text-lg">
                {{ $startDate->diffInDays($endDate) <= 30 ? 'Tren Harian' : 'Tren per Bulan' }}
            </h3>
            <div class="relative w-full flex-1" style="min-height: 250px;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        {{-- Pie Chart Kategori --}}
        <div class="card p-6 flex flex-col justify-between"
            style="background: #fff; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <h3 class="font-display font-bold text-gray-800 mb-4 text-lg">Pengeluaran per Kategori</h3>
            <div class="flex flex-col sm:flex-row items-center gap-6 flex-1">
                <div class="relative flex-shrink-0" style="width:140px; height:140px;">
                    <canvas id="pieChart"></canvas>
                </div>
                <div class="flex-1 w-full space-y-2 max-h-48 overflow-y-auto pr-1">
                    @forelse($expenseByCategory as $cat)
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-3 h-3 rounded-full flex-shrink-0" style="background: {{ $cat['color'] }};"></div>
                                <span class="text-xs text-gray-600 truncate">{{ $cat['icon'] }} {{ $cat['name'] }}</span>
                            </div>
                            <span class="text-xs font-bold text-gray-800 flex-shrink-0">
                                Rp {{ number_format($cat['amount'], 0, ',', '.') }}
                            </span>
                        </div>
                    @empty
                        <p class="text-gray-400 text-xs text-center py-4">Tidak ada pengeluaran</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Kontribusi Transaksi per User --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
        @foreach($userSummary as $summary)
            <div class="card p-6" style="background: #fff; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                <div class="flex items-center gap-3 mb-4">
                    @if($summary['user']->profile_photo_url)
                        <img src="{{ $summary['user']->profile_photo_url }}" alt="{{ $summary['user']->name }}" style="width:48px; height:48px; border-radius:50%; object-fit:cover;" />
                    @else
                        <div style="width:48px; height:48px; border-radius:50%; background:#f8fafc; color:#475569; display:flex; align-items:center; justify-content:center; font-size:22px;">
                            {{ $summary['user']->avatar ?? '👤' }}
                        </div>
                    @endif
                    <div>
                        <h3 class="font-display font-bold text-gray-800">{{ $summary['user']->name }}</h3>
                        <p class="text-xs text-pink-500 font-semibold">
                            {{ ($summary['user']->role ?? '') === 'owner' ? '👑 Pemilik' : '💝 Pasangan' }}
                        </p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3 rounded-xl" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                        <div class="text-xs text-gray-500 mb-1 font-medium">Pemasukan</div>
                        <div class="font-bold text-emerald-600 text-sm">
                            Rp {{ number_format($summary['income'], 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="p-3 rounded-xl" style="background: #fff1f2; border: 1px solid #fecdd3;">
                        <div class="text-xs text-gray-500 mb-1 font-medium">Pengeluaran</div>
                        <div class="font-bold text-rose-600 text-sm">
                            Rp {{ number_format($summary['expense'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Tabel Riwayat Transaksi --}}
    <div class="card overflow-hidden"
        style="background: #fff; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-display font-bold text-gray-800 text-lg">Semua Transaksi di Rentang Ini</h3>
            <span class="text-xs bg-pink-50 px-2.5 py-1 rounded-full text-pink-600 font-bold">
                {{ $transactions->count() }} Transaksi
            </span>
        </div>
        <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
            @forelse($transactions as $tx)
                <div class="flex items-center gap-4 p-4 hover:bg-gray-50 transition-colors">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-base flex-shrink-0"
                        style="background: {{ $tx->category->color }}15;">
                        {{ $tx->category->icon }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-gray-800 truncate">{{ $tx->description }}</div>
                        <div class="text-xs text-gray-400 flex items-center gap-1.5 mt-0.5">
                            <span class="inline-flex items-center gap-2">
                                @if($tx->user->profile_photo_url)
                                    <img src="{{ $tx->user->profile_photo_url }}" alt="{{ $tx->user->name }}" style="width:18px; height:18px; border-radius:50%; object-fit:cover;" />
                                @else
                                    <span style="display:inline-flex; width:18px; height:18px; border-radius:50%; background:#f3f4f6; color:#475569; align-items:center; justify-content:center; font-size:10px;">{{ $tx->user->avatar ?? '👤' }}</span>
                                @endif
                                {{ $tx->user->name }}
                            </span>
                            <span class="text-gray-200">•</span>
                            <span>{{ $tx->date->isoFormat('D MMM Y') }}</span>
                        </div>
                    </div>
                    <div
                        class="font-bold text-sm flex-shrink-0 {{ $tx->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $tx->type === 'income' ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-400">
                    <p class="text-sm">Tidak ada transaksi di periode ini</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Daftar Hutang dan Piutang --}}
    <div class="card p-6 mb-8" style="background:#fff; border-radius:16px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);">
        <div class="flex items-center justify-between mb-4 gap-3">
            <h3 class="font-display font-bold text-gray-800 text-lg">Daftar Hutang & Piutang</h3>
            <span class="text-xs bg-slate-100 px-3 py-1 rounded-full text-slate-700">{{ $debts->count() }} Catatan</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[12px] uppercase text-[var(--text-secondary)] tracking-[0.12em] border-b border-slate-200">
                        <th class="px-4 py-3">Tipe</th>
                        <th class="px-4 py-3">Pemilik</th>
                        <th class="px-4 py-3">Pihak</th>
                        <th class="px-4 py-3">Jumlah</th>
                        <th class="px-4 py-3">Rekening</th>
                        <th class="px-4 py-3">Jatuh Tempo</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($debts as $debt)
                        <tr class="border-b border-slate-200">
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $debt->type === 'hutang' ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-700' }}">
                                    {{ ucfirst($debt->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-semibold">{{ $debt->user->name }}</div>
                                <div class="text-xs text-[var(--text-secondary)]">
                                    {{ $debt->user->id === auth()->id() ? 'Saya' : 'Pasangan' }}
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-semibold">{{ $debt->counterparty }}</div>
                                <div class="text-xs text-[var(--text-secondary)]">{{ $debt->purpose }}</div>
                            </td>
                            <td class="px-4 py-4">
                                Rp {{ number_format($debt->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-4">
                                {{ $debt->bank->name }}
                            </td>
                            <td class="px-4 py-4">
                                {{ $debt->due_date->isoFormat('D MMM Y') }}
                                @if($debt->paid_at)
                                    <div class="text-xs text-[var(--text-secondary)]">Lunas {{ $debt->paid_at->isoFormat('D MMM Y') }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $debt->status === 'pending' ? 'bg-yellow-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }}">
                                    {{ ucfirst($debt->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-[var(--text-secondary)]">Belum ada catatan hutang/piutang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </div>
@endsection

@push('scripts')
    <script>
        const reportMonth = document.getElementById('reportMonth');
        const reportYear = document.getElementById('reportYear');
        const reportStartDate = document.getElementById('reportStartDate');
        const reportEndDate = document.getElementById('reportEndDate');
        const mobileReportMonth = document.getElementById('mobileReportMonth');
        const mobileReportYear = document.getElementById('mobileReportYear');
        const mobileReportStartDate = document.getElementById('mobileReportStartDate');
        const mobileReportEndDate = document.getElementById('mobileReportEndDate');

        function syncReportDateRange() {
            if (!reportMonth || !reportYear || !reportStartDate || !reportEndDate) return;

            const year = Number(reportYear.value);
            const month = Number(reportMonth.value);
            const lastDay = new Date(year, month, 0).getDate();
            const paddedMonth = String(month).padStart(2, '0');
            const startValue = `${year}-${paddedMonth}-01`;
            const endValue = `${year}-${paddedMonth}-${String(lastDay).padStart(2, '0')}`;

            if (reportStartDate._flatpickr) {
                reportStartDate._flatpickr.setDate(startValue, true);
            } else {
                reportStartDate.value = startValue;
            }

            if (reportEndDate._flatpickr) {
                reportEndDate._flatpickr.setDate(endValue, true);
            } else {
                reportEndDate.value = endValue;
            }
        }

        function syncMobileReportDateRange() {
            if (!mobileReportMonth || !mobileReportYear || !mobileReportStartDate || !mobileReportEndDate) return;

            const year = Number(mobileReportYear.value);
            const month = Number(mobileReportMonth.value);
            const lastDay = new Date(year, month, 0).getDate();
            const paddedMonth = String(month).padStart(2, '0');

            mobileReportStartDate.value = `${year}-${paddedMonth}-01`;
            mobileReportEndDate.value = `${year}-${paddedMonth}-${String(lastDay).padStart(2, '0')}`;
        }

        reportMonth?.addEventListener('change', syncReportDateRange);
        reportYear?.addEventListener('change', syncReportDateRange);
        mobileReportMonth?.addEventListener('change', syncMobileReportDateRange);
        mobileReportYear?.addEventListener('change', syncMobileReportDateRange);

        // 1. Inisialisasi Tren Chart 12 Bulan (Light Mode)
        const trendCanvas = document.getElementById('trendChart');
        if (trendCanvas) {
            const trendCtx = trendCanvas.getContext('2d');
            const trendData = @json($monthlyTrend ?? []);

            if (trendData && trendData.length > 0) {
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: trendData.map(t => t.label),
                        datasets: [
                            {
                                label: 'Pemasukan',
                                data: trendData.map(t => t.income),
                                borderColor: '#16a34a',
                                backgroundColor: 'rgba(22,163,74,0.05)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 2,
                                pointRadius: 3
                            },
                            {
                                label: 'Pengeluaran',
                                data: trendData.map(t => t.expense),
                                borderColor: '#e11d48',
                                backgroundColor: 'rgba(225,29,72,0.05)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 2,
                                pointRadius: 3
                            },
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { labels: { color: '#4b5563', font: { size: 11, weight: '600' }, boxWidth: 12 } }
                        },
                        scales: {
                            x: { ticks: { color: '#9ca3af', font: { size: 10 } }, grid: { color: '#f3f4f6' } },
                            y: { ticks: { color: '#9ca3af', font: { size: 10 }, callback: v => 'Rp ' + (v / 1e6).toFixed(1) + 'jt' }, grid: { color: '#f3f4f6' } },
                        }
                    }
                });
            } else {
                trendCanvas.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-gray-400 text-sm py-12">Belum ada data tren</div>';
            }
        }

        const mobileTrendCanvas = document.getElementById('mobileTrendChart');
        if (mobileTrendCanvas) {
            const mobileTrendCtx = mobileTrendCanvas.getContext('2d');
            const trendData = @json($monthlyTrend ?? []);

            if (trendData && trendData.length > 0) {
                new Chart(mobileTrendCtx, {
                    type: 'line',
                    data: {
                        labels: trendData.map(t => t.label),
                        datasets: [
                            {
                                label: 'Masuk',
                                data: trendData.map(t => t.income),
                                borderColor: '#16a34a',
                                backgroundColor: 'rgba(22,163,74,0.08)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 2,
                                pointRadius: 2
                            },
                            {
                                label: 'Keluar',
                                data: trendData.map(t => t.expense),
                                borderColor: '#e11d48',
                                backgroundColor: 'rgba(225,29,72,0.08)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 2,
                                pointRadius: 2
                            },
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { color: '#64748b', font: { size: 11, weight: '700' }, boxWidth: 10 }
                            }
                        },
                        scales: {
                            x: { ticks: { color: '#94a3b8', font: { size: 10 }, maxRotation: 0 }, grid: { display: false } },
                            y: {
                                ticks: {
                                    color: '#94a3b8',
                                    font: { size: 10 },
                                    callback: v => v >= 1000000 ? 'Rp ' + (v / 1000000).toFixed(1) + 'jt' : 'Rp ' + (v / 1000).toFixed(0) + 'rb'
                                },
                                grid: { color: '#f1f5f9' }
                            },
                        }
                    }
                });
            } else {
                mobileTrendCanvas.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-gray-400 text-sm">Belum ada data tren</div>';
            }
        }

        // 2. Inisialisasi Pie Chart Kategori (Light Mode)
        const pieCanvas = document.getElementById('pieChart');
        if (pieCanvas) {
            const pieCtx = pieCanvas.getContext('2d');
            const cats = @json($expenseByCategory->values() ?? []);

            if (cats && cats.length > 0) {
                new Chart(pieCtx, {
                    type: 'doughnut',
                    data: {
                        labels: cats.map(c => c.name),
                        datasets: [{
                            data: cats.map(c => c.amount),
                            backgroundColor: cats.map(c => c.color),
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        cutout: '70%',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                    }
                });
            } else {
                pieCanvas.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-gray-400 text-sm">Tidak ada data</div>';
            }
        }

        const mobilePieCanvas = document.getElementById('mobilePieChart');
        if (mobilePieCanvas) {
            const mobilePieCtx = mobilePieCanvas.getContext('2d');
            const cats = @json($expenseByCategory->values() ?? []);

            if (cats && cats.length > 0) {
                new Chart(mobilePieCtx, {
                    type: 'doughnut',
                    data: {
                        labels: cats.map(c => c.name),
                        datasets: [{
                            data: cats.map(c => c.amount),
                            backgroundColor: cats.map(c => c.color),
                            borderWidth: 3,
                            borderColor: '#ffffff',
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        cutout: '68%',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { color: '#64748b', font: { size: 11, weight: '700' }, boxWidth: 10 }
                            }
                        },
                    }
                });
            } else {
                mobilePieCanvas.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-gray-400 text-sm">Tidak ada data</div>';
            }
        }
    </script>
@endpush
