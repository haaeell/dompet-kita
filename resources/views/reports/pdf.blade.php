<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan DompetKita</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #0f172a; font-size: 11px; }
        h1 { margin: 0 0 4px; font-size: 20px; }
        .muted { color: #64748b; }
        .summary { display: table; width: 100%; margin: 18px 0; }
        .summary div { display: table-cell; border: 1px solid #e2e8f0; padding: 10px; }
        .label { color: #64748b; font-size: 9px; text-transform: uppercase; }
        .value { font-size: 15px; font-weight: bold; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border-bottom: 1px solid #e2e8f0; padding: 7px 6px; text-align: left; }
        th { background: #f8fafc; font-size: 9px; text-transform: uppercase; color: #475569; }
        .right { text-align: right; }
        .income { color: #047857; font-weight: bold; }
        .expense { color: #be123c; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Laporan DompetKita</h1>
    <div class="muted">{{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</div>

    <div class="summary">
        <div><span class="label">Pemasukan</span><div class="value">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div></div>
        <div><span class="label">Pengeluaran</span><div class="value">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div></div>
        <div><span class="label">Saldo</span><div class="value">Rp {{ number_format($balance, 0, ',', '.') }}</div></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Tipe</th>
                <th>Kategori</th>
                <th>Deskripsi</th>
                <th>User</th>
                <th class="right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->date->format('d/m/Y') }}</td>
                    <td>{{ $transaction->type === 'income' ? 'Masuk' : 'Keluar' }}</td>
                    <td>{{ $transaction->category?->name }}</td>
                    <td>{{ $transaction->description }}</td>
                    <td>{{ $transaction->user?->name }}</td>
                    <td class="right {{ $transaction->type === 'income' ? 'income' : 'expense' }}">
                        {{ $transaction->type === 'income' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
