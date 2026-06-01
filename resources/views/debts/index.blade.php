@extends('layouts.app')
@section('title', 'Hutang & Piutang')

@section('content')
    <style>
        .debt-mobile-list {
            display: none;
        }

        @media (max-width: 768px) {
            .debt-page-head {
                margin: -6px -4px 16px;
            }

            .debt-page-head .page-title {
                font-size: 26px;
                line-height: 1.1;
            }

            .debt-filter-bar {
                width: 100%;
                display: grid;
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .debt-filter-bar form,
            .debt-filter-bar select {
                width: 100%;
            }

            .debt-type-tabs {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
                padding: 5px;
                border-radius: 18px;
                background: #fff;
                border: 1px solid #f1f5f9;
                box-shadow: 0 10px 28px rgba(15, 23, 42, .05);
            }

            .debt-type-tabs .btn-ghost {
                width: 100%;
                justify-content: center;
                border-radius: 14px;
                padding: 10px 12px;
                border: 0;
                background: transparent;
            }

            .debt-summary-grid {
                display: flex;
                gap: 12px;
                overflow-x: auto;
                margin: 0 -16px 18px;
                padding: 0 16px 4px;
                scroll-snap-type: x mandatory;
            }

            .debt-summary-grid > .card {
                min-width: 78%;
                scroll-snap-align: start;
                border-radius: 8px;
                padding: 16px;
                box-shadow: 0 10px 26px rgba(15, 23, 42, .05);
            }

            .debt-summary-grid .text-3xl {
                font-size: 22px;
                line-height: 1.16;
                word-break: break-word;
            }

            .debt-entry-grid {
                display: flex;
                flex-direction: column;
                gap: 14px;
            }

            .debt-entry-grid > .card {
                border-radius: 8px;
                padding: 18px;
            }

            .debt-entry-grid textarea {
                min-height: 96px;
            }

            .debt-desktop-table {
                display: none;
            }

            .debt-mobile-list {
                display: grid;
                gap: 12px;
            }

            .debt-list-card {
                border-radius: 8px;
                border: 1px solid #e2e8f0;
                background: #fff;
                padding: 14px;
                box-shadow: 0 10px 26px rgba(15, 23, 42, .04);
            }

            .debt-list-top {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 10px;
            }

            .debt-amount {
                font-size: 18px;
                font-weight: 800;
                line-height: 1.18;
            }

            .debt-meta-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px;
                margin-top: 12px;
            }

            .debt-meta-box {
                border-radius: 8px;
                background: #f8fafc;
                padding: 10px;
                min-width: 0;
            }

            .debt-meta-label {
                font-size: 10px;
                font-weight: 800;
                color: #94a3b8;
                text-transform: uppercase;
                letter-spacing: .06em;
            }

            .debt-meta-value {
                margin-top: 4px;
                font-size: 12px;
                font-weight: 700;
                color: #0f172a;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .debt-mobile-actions {
                margin-top: 12px;
                display: grid;
                gap: 8px;
            }

            .debt-mobile-actions details {
                border-radius: 8px;
            }
        }
    </style>

    <div class="debt-page-head flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="page-title mb-1">Hutang & Piutang</h1>
            <p class="page-subtitle m-0">Catat hutang dan piutang, bayar atau tandai kembali dengan mudah.</p>
        </div>
        <div class="debt-filter-bar flex flex-wrap gap-2 items-center">
            <form action="{{ route('debts.index') }}" method="GET" id="debtFilterForm" class="m-0">
                <input type="hidden" name="type" value="{{ $type }}">
                <select name="user_id" onchange="document.getElementById('debtFilterForm').submit();"
                    class="input-field py-2 px-3 rounded-xl min-w-[180px] cursor-pointer h-auto text-[13px] font-semibold">
                    <option value="">Saya & pasangan</option>
                    @foreach($coupleMembers as $member)
                        <option value="{{ $member->id }}" {{ (string) $selectedUserId === (string) $member->id ? 'selected' : '' }}>
                            {{ $member->id === auth()->id() ? 'Saya (' . $member->name . ')' : $member->name }}
                        </option>
                    @endforeach
                </select>
            </form>
            <div class="debt-type-tabs">
                <a href="{{ route('debts.index', ['type' => 'hutang', 'user_id' => $selectedUserId]) }}"
                    class="btn-ghost {{ $type === 'hutang' ? 'bg-pink-50 border-pink-200 text-pink-700' : '' }}">
                    Hutang
                </a>
                <a href="{{ route('debts.index', ['type' => 'piutang', 'user_id' => $selectedUserId]) }}"
                    class="btn-ghost {{ $type === 'piutang' ? 'bg-pink-50 border-pink-200 text-pink-700' : '' }}">
                    Piutang
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-5 rounded-3xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-5 rounded-3xl bg-rose-50 border border-rose-200 p-4 text-sm text-rose-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="debt-summary-grid grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">
        <div class="card p-4">
            <div class="text-[11px] uppercase tracking-[0.18em] text-[var(--text-secondary)] mb-3">Total Kekayaan</div>
            <div class="text-3xl font-bold">Rp {{ number_format($totalWealth, 0, ',', '.') }}</div>
            <div class="text-xs text-[var(--text-secondary)] mt-2">
                {{ $selectedUserId ? 'Saldo rekening aktif orang yang dipilih' : 'Saldo total semua rekening' }}
            </div>
        </div>
        <div class="card p-4">
            <div class="text-[11px] uppercase tracking-[0.18em] text-[var(--text-secondary)] mb-3">Hutang Belum Dibayar</div>
            <div class="text-3xl font-bold text-rose-600">Rp {{ number_format($outstandingHutang, 0, ',', '.') }}</div>
            <div class="text-xs text-[var(--text-secondary)] mt-2">
                {{ $selectedUserId ? 'Jumlah hutang orang yang dipilih yang belum dibayar' : 'Jumlah hutang yang belum dibayar' }}
            </div>
        </div>
        <div class="card p-4">
            <div class="text-[11px] uppercase tracking-[0.18em] text-[var(--text-secondary)] mb-3">Piutang Belum Kembali</div>
            <div class="text-3xl font-bold text-green-700">Rp {{ number_format($outstandingPiutang, 0, ',', '.') }}</div>
            <div class="text-xs text-[var(--text-secondary)] mt-2">
                {{ $selectedUserId ? 'Jumlah piutang orang yang dipilih yang belum dikembalikan' : 'Jumlah piutang yang belum dikembalikan' }}
            </div>
        </div>
        <div class="card p-4">
            <div class="text-[11px] uppercase tracking-[0.18em] text-[var(--text-secondary)] mb-3">Tipe Aktif</div>
            <div class="text-3xl font-bold text-[var(--pink-dark)]">{{ ucfirst($type) }}</div>
            <div class="text-xs text-[var(--text-secondary)] mt-2">Lihat daftar {{ $type }} dan tindakan berikutnya</div>
        </div>
    </div>

    <div class="debt-entry-grid grid grid-cols-1 xl:grid-cols-[1.2fr_0.8fr] gap-5 mb-6">
        <div class="card p-6">
            <h2 class="text-lg font-bold mb-4">Catat {{ $type === 'hutang' ? 'Hutang' : 'Piutang' }} Baru</h2>
            <form action="{{ route('debts.store') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">
                <div class="grid gap-4">
                    <div>
                        <label class="label" for="amount">Jumlah</label>
                        <input type="text" id="amount" name="amount" value="{{ old('amount') }}" class="input-field rupiah"
                            placeholder="1.250.000" required>
                    </div>
                    <div>
                        <label class="label" for="counterparty">{{ $type === 'hutang' ? 'Dari' : 'Ke' }}</label>
                        <input type="text" id="counterparty" name="counterparty" value="{{ old('counterparty') }}"
                            class="input-field" placeholder="Nama orang atau pihak" required>
                    </div>
                    <div>
                        <label class="label" for="purpose">Untuk Apa</label>
                        <input type="text" id="purpose" name="purpose" value="{{ old('purpose') }}"
                            class="input-field" placeholder="Contoh: Biaya pengobatan" required>
                    </div>
                    <div>
                        <label class="label" for="due_date">Tanggal Jatuh Tempo</label>
                        <input type="text" id="due_date" name="due_date" value="{{ old('due_date', now()->toDateString()) }}"
                            class="input-field js-date-picker" data-format="Y-m-d" data-alt-format="j F Y" required>
                    </div>
                    <div>
                        <label class="label" for="bank_id">Rekening {{ $type === 'hutang' ? 'Masuk' : 'Ambil Dari' }}</label>
                        <select id="bank_id" name="bank_id" class="input-field" required>
                            <option value="">Pilih rekening</option>
                            @foreach($banks as $bank)
                                <option value="{{ $bank->id }}" {{ old('bank_id') == $bank->id ? 'selected' : '' }}>
                                    {{ $bank->name }} - {{ $bank->account_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label" for="notes">Catatan Tambahan</label>
                        <textarea id="notes" name="notes" class="input-field" rows="3"
                            placeholder="Optional">{{ old('notes') }}</textarea>
                    </div>
                    <button type="submit" class="btn-primary">Simpan {{ $type === 'hutang' ? 'Hutang' : 'Piutang' }}</button>
                </div>
            </form>
        </div>

        <div class="card p-6">
            <h2 class="text-lg font-bold mb-4">Rekening Tersedia</h2>
            <div class="space-y-3">
                @forelse($banks as $bank)
                    <div class="rounded-3xl border border-slate-200 p-4">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl">{{ $bank->icon }}</span>
                            <div class="min-w-0">
                                <div class="font-semibold text-[var(--text-primary)]">{{ $bank->name }}</div>
                                <div class="text-xs text-[var(--text-secondary)]">{{ $bank->account_name }}</div>
                                <div class="mt-2 text-sm font-bold" style="color: {{ $bank->color }};">
                                    Rp {{ number_format($bank->current_balance, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-[var(--text-secondary)]">Belum ada rekening, tambahkan dulu di menu Rekening.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="card p-6 debt-desktop-table">
        <h2 class="text-lg font-bold mb-4">Daftar {{ ucfirst($type) }}</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[12px] uppercase text-[var(--text-secondary)] tracking-[0.12em] border-b border-slate-200">
                        <th class="px-4 py-3">Tipe</th>
                        <th class="px-4 py-3">Pemilik</th>
                        <th class="px-4 py-3">Keterangan</th>
                        <th class="px-4 py-3">Rekening</th>
                        <th class="px-4 py-3">Jatuh Tempo</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($debts as $debt)
                        <tr class="border-b border-slate-200">
                            <td class="px-4 py-4 align-top">
                                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ $debt->type === 'hutang' ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-700' }}">
                                    {{ $debt->type === 'hutang' ? 'Hutang' : 'Piutang' }}
                                </span>
                                <div class="mt-2 text-sm font-bold {{ $debt->type === 'hutang' ? 'text-rose-600' : 'text-green-700' }}">
                                    Rp {{ number_format($debt->amount, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-4 py-4 align-top">
                                <div class="font-semibold">{{ $debt->user->name }}</div>
                                <div class="text-xs text-[var(--text-secondary)]">
                                    {{ $debt->user->id === auth()->id() ? 'Catatan saya' : 'Catatan pasangan' }}
                                </div>
                            </td>
                            <td class="px-4 py-4 align-top max-w-[280px]">
                                <div class="font-semibold">{{ $debt->counterparty }}</div>
                                <div class="text-[13px] text-[var(--text-secondary)]">{{ $debt->purpose }}</div>
                                @if($debt->notes)
                                    <div class="mt-2 text-xs text-slate-500">{{ $debt->notes }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-4 align-top">
                                <div>{{ $debt->bank->name }}</div>
                                <div class="text-xs text-[var(--text-secondary)]">{{ $debt->bank->account_name }}</div>
                            </td>
                            <td class="px-4 py-4 align-top">
                                <div>{{ $debt->due_date->isoFormat('D MMM Y') }}</div>
                                @if($debt->paid_at)
                                    <div class="text-xs text-[var(--text-secondary)]">Lunas: {{ $debt->paid_at->isoFormat('D MMM Y') }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-4 align-top">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $debt->status === 'pending' ? 'bg-yellow-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }}">
                                    {{ ucfirst($debt->status) }}
                                </span>
                                @if($debt->settlementBank)
                                    <div class="text-[11px] text-[var(--text-secondary)] mt-2">Rek. penyelesaian: {{ $debt->settlementBank->name }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-4 align-top">
                                @if($debt->status === 'pending')
                                    <details class="rounded-2xl border border-slate-200 p-3 bg-slate-50">
                                        <summary class="cursor-pointer text-sm font-semibold text-[var(--pink-dark)]">Bayar / Tandai Kembali</summary>
                                        <form action="{{ route('debts.pay', $debt) }}" method="POST" class="mt-3 space-y-3">
                                            @csrf
                                            @method('PUT')
                                            <div>
                                                <label class="label" for="settlement_bank_id_{{ $debt->id }}">Rekening Penyelesaian</label>
                                                <select id="settlement_bank_id_{{ $debt->id }}" name="settlement_bank_id" class="input-field" required>
                                                    <option value="">Pilih rekening</option>
                                                    @foreach($banks as $bank)
                                                        <option value="{{ $bank->id }}">{{ $bank->name }} - {{ $bank->account_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="label" for="paid_at_{{ $debt->id }}">Tanggal Bayar</label>
                                                <input type="text" id="paid_at_{{ $debt->id }}" name="paid_at" class="input-field js-date-picker" data-format="Y-m-d" data-alt-format="j F Y" value="{{ now()->toDateString() }}" required>
                                            </div>
                                            <button type="submit" class="btn-primary">Catat Pembayaran</button>
                                        </form>
                                    </details>
                                    <form action="{{ route('debts.destroy', $debt) }}" method="POST" class="mt-3">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-ghost text-sm text-rose-600">Hapus</button>
                                    </form>
                                @else
                                    <div class="text-sm text-[var(--text-secondary)]">Selesai</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-[var(--text-secondary)]">Belum ada catatan {{ $type }}.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="debt-mobile-list">
        <h2 class="text-base font-extrabold text-slate-900 m-0">Daftar {{ ucfirst($type) }}</h2>
        @forelse($debts as $debt)
            <article class="debt-list-card">
                <div class="debt-list-top">
                    <div class="min-w-0">
                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-bold {{ $debt->type === 'hutang' ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-700' }}">
                            {{ $debt->type === 'hutang' ? 'Hutang' : 'Piutang' }}
                        </span>
                        <div class="debt-amount mt-2 {{ $debt->type === 'hutang' ? 'text-rose-600' : 'text-green-700' }}">
                            Rp {{ number_format($debt->amount, 0, ',', '.') }}
                        </div>
                    </div>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $debt->status === 'pending' ? 'bg-yellow-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }}">
                        {{ ucfirst($debt->status) }}
                    </span>
                </div>

                <div class="mt-3">
                    <div class="text-sm font-extrabold text-slate-900">{{ $debt->counterparty }}</div>
                    <div class="text-xs text-slate-500 mt-1">{{ $debt->purpose }}</div>
                    @if($debt->notes)
                        <div class="mt-2 rounded-lg bg-slate-50 px-3 py-2 text-xs text-slate-500">{{ $debt->notes }}</div>
                    @endif
                </div>

                <div class="debt-meta-grid">
                    <div class="debt-meta-box">
                        <div class="debt-meta-label">Pemilik</div>
                        <div class="debt-meta-value">{{ $debt->user->id === auth()->id() ? 'Saya' : $debt->user->name }}</div>
                    </div>
                    <div class="debt-meta-box">
                        <div class="debt-meta-label">Jatuh Tempo</div>
                        <div class="debt-meta-value">{{ $debt->due_date->isoFormat('D MMM Y') }}</div>
                    </div>
                    <div class="debt-meta-box">
                        <div class="debt-meta-label">Rekening</div>
                        <div class="debt-meta-value">{{ $debt->bank->name }}</div>
                    </div>
                    <div class="debt-meta-box">
                        <div class="debt-meta-label">Penyelesaian</div>
                        <div class="debt-meta-value">
                            {{ $debt->paid_at ? $debt->paid_at->isoFormat('D MMM Y') : 'Belum selesai' }}
                        </div>
                    </div>
                </div>

                <div class="debt-mobile-actions">
                    @if($debt->status === 'pending')
                        <details class="border border-slate-200 p-3 bg-slate-50">
                            <summary class="cursor-pointer text-sm font-bold text-[var(--pink-dark)]">Bayar / Tandai kembali</summary>
                            <form action="{{ route('debts.pay', $debt) }}" method="POST" class="mt-3 space-y-3">
                                @csrf
                                @method('PUT')
                                <div>
                                    <label class="label" for="mobile_settlement_bank_id_{{ $debt->id }}">Rekening Penyelesaian</label>
                                    <select id="mobile_settlement_bank_id_{{ $debt->id }}" name="settlement_bank_id" class="input-field" required>
                                        <option value="">Pilih rekening</option>
                                        @foreach($banks as $bank)
                                            <option value="{{ $bank->id }}">{{ $bank->name }} - {{ $bank->account_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="label" for="mobile_paid_at_{{ $debt->id }}">Tanggal Bayar</label>
                                    <input type="text" id="mobile_paid_at_{{ $debt->id }}" name="paid_at" class="input-field js-date-picker" data-format="Y-m-d" data-alt-format="j F Y" value="{{ now()->toDateString() }}" required>
                                </div>
                                <button type="submit" class="btn-primary w-full justify-center">Catat Pembayaran</button>
                            </form>
                        </details>
                        <form action="{{ route('debts.destroy', $debt) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-ghost w-full justify-center text-rose-600">Hapus</button>
                        </form>
                    @else
                        <div class="rounded-lg bg-emerald-50 px-3 py-2 text-center text-xs font-bold text-emerald-700">Selesai</div>
                    @endif
                </div>
            </article>
        @empty
            <div class="rounded-lg border border-dashed border-slate-200 bg-white px-4 py-8 text-center text-sm text-slate-500">
                Belum ada catatan {{ $type }}.
            </div>
        @endforelse
    </div>
@endsection
