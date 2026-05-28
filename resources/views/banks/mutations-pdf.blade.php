<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Mutasi Rekening {{ $bank->name }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #0f172a;
            font-size: 11px;
            line-height: 1.45;
            margin: 24px;
        }

        .header-table,
        .summary-table,
        .mutations-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-title {
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 4px 0;
        }

        .subtitle {
            font-size: 11px;
            color: #475569;
            margin: 0;
        }

        .header-meta {
            text-align: right;
            font-size: 10px;
            color: #64748b;
        }

        .section-label {
            display: inline-block;
            padding: 4px 10px;
            border: 1px solid #f9a8d4;
            color: #be185d;
            background: #fdf2f8;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            margin: 18px 0 10px 0;
        }

        .summary-table td {
            width: 25%;
            padding-right: 12px;
            vertical-align: top;
        }

        .summary-card {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 12px;
            min-height: 78px;
        }

        .summary-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            margin-bottom: 8px;
        }

        .summary-value {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
        }

        .summary-value.income {
            color: #047857;
        }

        .summary-value.expense {
            color: #be123c;
        }

        .filters-box {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 12px 14px;
            margin-top: 14px;
            background: #f8fafc;
        }

        .filters-box strong {
            color: #334155;
        }

        .mutations-table {
            margin-top: 14px;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            overflow: hidden;
        }

        .mutations-table th {
            background: #f8fafc;
            color: #475569;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 10px 8px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }

        .mutations-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .mutations-table tbody tr:last-child td {
            border-bottom: none;
        }

        .text-right {
            text-align: right;
        }

        .mutasi-income {
            color: #047857;
            font-weight: 700;
        }

        .mutasi-expense {
            color: #be123c;
            font-weight: 700;
        }

        .small {
            font-size: 10px;
            color: #64748b;
        }

        .empty-state {
            padding: 30px 0;
            text-align: center;
            color: #64748b;
        }
    </style>
</head>
<body>
    @php
        $firstTransaction = $transactions->first();
        $lastTransaction = $transactions->last();
        $filteredOpeningBalance = $firstTransaction?->opening_balance ?? $bank->initial_balance;
        $filteredClosingBalance = $lastTransaction?->closing_balance ?? $bank->initial_balance;
        $typeLabel = match ($filters['type'] ?? null) {
            'income' => 'Pemasukan',
            'expense' => 'Pengeluaran',
            default => 'Semua Tipe',
        };
        $periodLabel = ($filters['start_date'] ?? null) || ($filters['end_date'] ?? null)
            ? trim(($filters['start_date'] ? \Carbon\Carbon::parse($filters['start_date'])->isoFormat('D MMMM Y') : 'Awal') . ' - ' . ($filters['end_date'] ? \Carbon\Carbon::parse($filters['end_date'])->isoFormat('D MMMM Y') : 'Sekarang'))
            : 'Semua Periode';
    @endphp

    <table class="header-table">
        <tr>
            <td>
                <div class="header-title">Laporan Mutasi Rekening</div>
                <p class="subtitle">{{ $bank->name }} | {{ $bank->account_name }}{{ $bank->account_number ? ' | Rek. ' . $bank->account_number : '' }}</p>
            </td>
            <td class="header-meta">
                <div><strong>Dibuat:</strong> {{ $generatedAt->isoFormat('D MMMM Y, HH:mm') }}</div>
                <div><strong>Tipe:</strong> {{ $typeLabel }}</div>
                <div><strong>Periode:</strong> {{ $periodLabel }}</div>
            </td>
        </tr>
    </table>

    <div class="section-label">Ringkasan Saldo</div>

    <table class="summary-table">
        <tr>
            <td>
                <div class="summary-card">
                    <div class="summary-label">Saldo Awal Periode</div>
                    <div class="summary-value">Rp {{ number_format($filteredOpeningBalance, 0, ',', '.') }}</div>
                </div>
            </td>
            <td>
                <div class="summary-card">
                    <div class="summary-label">Total Pemasukan</div>
                    <div class="summary-value income">Rp {{ number_format($incomeTotal, 0, ',', '.') }}</div>
                </div>
            </td>
            <td>
                <div class="summary-card">
                    <div class="summary-label">Total Pengeluaran</div>
                    <div class="summary-value expense">Rp {{ number_format($expenseTotal, 0, ',', '.') }}</div>
                </div>
            </td>
            <td>
                <div class="summary-card">
                    <div class="summary-label">Saldo Akhir Periode</div>
                    <div class="summary-value">Rp {{ number_format($filteredClosingBalance, 0, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="filters-box">
        <strong>Keterangan:</strong>
        Laporan ini menampilkan saldo awal sebelum transaksi, nilai mutasi masuk atau keluar, lalu saldo sesudah tiap transaksi.
    </div>

    <table class="mutations-table">
        <thead>
            <tr>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 24%;">Deskripsi</th>
                <th style="width: 16%;">Kategori</th>
                <th style="width: 14%;">Dicatat Oleh</th>
                <th style="width: 12%;" class="text-right">Saldo Awal</th>
                <th style="width: 12%;" class="text-right">Mutasi</th>
                <th style="width: 12%;" class="text-right">Saldo Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $tx)
                <tr>
                    <td>{{ $tx->date->isoFormat('D MMM Y') }}</td>
                    <td>
                        <div><strong>{{ $tx->description }}</strong></div>
                        @if($tx->notes)
                            <div class="small">{{ $tx->notes }}</div>
                        @endif
                    </td>
                    <td>{{ $tx->category->icon }} {{ $tx->category->name }}</td>
                    <td>{{ $tx->user->name }}</td>
                    <td class="text-right">Rp {{ number_format($tx->opening_balance, 0, ',', '.') }}</td>
                    <td class="text-right {{ $tx->type === 'income' ? 'mutasi-income' : 'mutasi-expense' }}">
                        {{ $tx->balance_delta >= 0 ? '+' : '-' }} Rp {{ number_format(abs($tx->balance_delta), 0, ',', '.') }}
                    </td>
                    <td class="text-right">Rp {{ number_format($tx->closing_balance, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-state">Belum ada transaksi yang sesuai dengan filter mutasi ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
