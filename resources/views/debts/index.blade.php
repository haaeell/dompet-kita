@extends('layouts.app')
@section('title', 'Hutang & Piutang')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="page-title mb-1">Hutang & Piutang</h1>
            <p class="page-subtitle m-0">Catat hutang dan piutang, bayar atau tandai kembali dengan mudah.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('debts.index', ['type' => 'hutang']) }}"
                class="btn-ghost {{ $type === 'hutang' ? 'bg-pink-50 border-pink-200 text-pink-700' : '' }}">
                Hutang
            </a>
            <a href="{{ route('debts.index', ['type' => 'piutang']) }}"
                class="btn-ghost {{ $type === 'piutang' ? 'bg-pink-50 border-pink-200 text-pink-700' : '' }}">
                Piutang
            </a>
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

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">
        <div class="card p-4">
            <div class="text-[11px] uppercase tracking-[0.18em] text-[var(--text-secondary)] mb-3">Total Kekayaan</div>
            <div class="text-3xl font-bold">Rp {{ number_format($totalWealth, 0, ',', '.') }}</div>
            <div class="text-xs text-[var(--text-secondary)] mt-2">Saldo total semua rekening</div>
        </div>
        <div class="card p-4">
            <div class="text-[11px] uppercase tracking-[0.18em] text-[var(--text-secondary)] mb-3">Hutang Belum Dibayar</div>
            <div class="text-3xl font-bold text-rose-600">Rp {{ number_format($outstandingHutang, 0, ',', '.') }}</div>
            <div class="text-xs text-[var(--text-secondary)] mt-2">Jumlah hutang yang belum dibayar</div>
        </div>
        <div class="card p-4">
            <div class="text-[11px] uppercase tracking-[0.18em] text-[var(--text-secondary)] mb-3">Piutang Belum Kembali</div>
            <div class="text-3xl font-bold text-green-700">Rp {{ number_format($outstandingPiutang, 0, ',', '.') }}</div>
            <div class="text-xs text-[var(--text-secondary)] mt-2">Jumlah piutang yang belum dikembalikan</div>
        </div>
        <div class="card p-4">
            <div class="text-[11px] uppercase tracking-[0.18em] text-[var(--text-secondary)] mb-3">Tipe Aktif</div>
            <div class="text-3xl font-bold text-[var(--pink-dark)]">{{ ucfirst($type) }}</div>
            <div class="text-xs text-[var(--text-secondary)] mt-2">Lihat daftar {{ $type }} dan tindakan berikutnya</div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[1.2fr_0.8fr] gap-5 mb-6">
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
                        <input type="date" id="due_date" name="due_date" value="{{ old('due_date', now()->toDateString()) }}"
                            class="input-field" required>
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

    <div class="card p-6">
        <h2 class="text-lg font-bold mb-4">Daftar {{ ucfirst($type) }}</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[12px] uppercase text-[var(--text-secondary)] tracking-[0.12em] border-b border-slate-200">
                        <th class="px-4 py-3">Tipe</th>
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
                                <div>{{ $debt->due_date->format('d M Y') }}</div>
                                @if($debt->paid_at)
                                    <div class="text-xs text-[var(--text-secondary)]">Lunas: {{ $debt->paid_at->format('d M Y') }}</div>
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
                                                <input type="date" id="paid_at_{{ $debt->id }}" name="paid_at" class="input-field" value="{{ now()->toDateString() }}" required>
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
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-[var(--text-secondary)]">Belum ada catatan {{ $type }}.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
