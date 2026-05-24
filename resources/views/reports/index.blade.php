@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
    {{-- Header Laporan --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="font-display text-3xl font-bold text-[var(--text-primary)]" style="color: #1a1a2e;">Laporan Keuangan
            </h1>
            <p class="text-gray-500 text-sm mt-1">Pantau kondisi keuangan kalian bersama</p>
        </div>

        {{-- Ganti form filter lama kamu di bagian atas dengan ini --}}
        <form method="GET" id="reportFilterForm" class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
            {{-- Filter Berdua / Individu --}}
            <select name="user_filter" class="input-field"
                style="width:150px; background: #fff; border: 1px solid #e2e8f0; color: #1a1a2e;"
                onchange="document.getElementById('reportFilterForm').submit();">
                <option value="all" {{ $userFilter == 'all' ? 'selected' : '' }}>👥 Berdua (Gabungan)</option>
                <option value="me" {{ $userFilter == 'me' ? 'selected' : '' }}>👤 Saya Sendiri</option>
                <option value="partner" {{ $userFilter == 'partner' ? 'selected' : '' }}>💝 Pasangan</option>
            </select>

            {{-- Filter Bulan --}}
            <select name="month" class="input-field"
                style="width:130px; background: #fff; border: 1px solid #e2e8f0; color: #1a1a2e;"
                onchange="document.getElementById('reportFilterForm').submit();">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create(null, $m)->isoFormat('MMMM') }}
                    </option>
                @endforeach
            </select>

            {{-- Filter Tahun --}}
            <select name="year" class="input-field"
                style="width:100px; background: #fff; border: 1px solid #e2e8f0; color: #1a1a2e;"
                onchange="document.getElementById('reportFilterForm').submit();">
                @foreach([now()->year, now()->year - 1, now()->year - 2] as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Ringkasan / Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">
        <div class="card p-6"
            style="background: #fff; border-left: 4px solid #2563eb; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-3">Total Kekayaan</div>
            <div class="font-display text-3xl font-bold text-sky-700">
                Rp {{ number_format($totalWealth, 0, ',', '.') }}
            </div>
            <div class="text-xs text-gray-500 mt-2">Saldo semua rekening aktif</div>
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
            <div class="text-xs text-gray-500 mt-2">Semua hutang yang belum diselesaikan</div>
        </div>
        <div class="card p-6" style="background:#fff; border-left:4px solid #16a34a; border-radius:16px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);">
            <div class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-3">Piutang Belum Kembali</div>
            <div class="font-display text-3xl font-bold text-emerald-600">Rp {{ number_format($outstandingPiutang, 0, ',', '.') }}</div>
            <div class="text-xs text-gray-500 mt-2">Piutang yang masih harus dikembalikan</div>
        </div>
        <div class="card p-6" style="background:#fff; border-left:4px solid #8b5cf6; border-radius:16px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05);">
            <div class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-3">Total Catatan Hutang/Piutang</div>
            <div class="font-display text-3xl font-bold text-violet-700">{{ $debts->count() }}</div>
            <div class="text-xs text-gray-500 mt-2">Jumlah catatan hutang/piutang</div>
        </div>
    </div>

    {{-- Seksi Grafik & Visualisasi --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Tren 12 Bulan --}}
        <div class="card p-6 lg:col-span-2 flex flex-col justify-between"
            style="background: #fff; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <h3 class="font-display font-bold text-gray-800 mb-4 text-lg">Tren 12 Bulan Terakhir</h3>
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
            <h3 class="font-display font-bold text-gray-800 text-lg">Semua Transaksi Periode Ini</h3>
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
                            <span>{{ $tx->date->format('d M Y') }}</span>
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
                                {{ $debt->due_date->format('d M Y') }}
                                @if($debt->paid_at)
                                    <div class="text-xs text-[var(--text-secondary)]">Lunas {{ $debt->paid_at->format('d M Y') }}</div>
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
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-[var(--text-secondary)]">Belum ada catatan hutang/piutang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
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
    </script>
@endpush