@extends('layouts.app')
@section('title', 'Mutasi Rekening')

@php
    $downloadQuery = array_filter([
        'type' => request('type'),
        'start_date' => request('start_date'),
        'end_date' => request('end_date'),
    ]);
@endphp

@section('content')
    <div class="space-y-5">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <a href="{{ route('banks.index') }}" class="text-sm font-semibold text-[var(--pink-dark)] no-underline">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Rekening
                </a>
                <h1 class="page-title mt-3 mb-1">Mutasi {{ $bank->name }}</h1>
                <p class="page-subtitle m-0">
                    {{ $bank->account_name }}
                    @if($bank->account_number)
                        <span class="mx-2 text-slate-300">&bull;</span>
                        <span>Rek. {{ substr($bank->account_number, -4) }}</span>
                    @endif
                </p>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <a href="{{ route('banks.mutations.pdf', ['bank' => $bank] + $downloadQuery) }}"
                    class="btn-ghost whitespace-nowrap">
                    <i class="fa-solid fa-file-pdf"></i> Download PDF
                </a>

                <div class="rounded-[26px] border border-slate-200 bg-white px-4 py-3 shadow-sm min-w-[220px]">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl text-2xl"
                            style="background: {{ $bank->color }}18; color: {{ $bank->color }};">
                            {{ $bank->icon }}
                        </div>
                        <div>
                            <div class="text-[11px] uppercase tracking-[0.2em] text-slate-400">Saldo Saat Ini</div>
                            <div class="text-xl font-bold text-slate-900">
                                Rp {{ number_format($bank->current_balance, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rounded-[24px] border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-[11px] uppercase tracking-[0.2em] text-slate-400">Saldo Awal Rekening</div>
                <div class="mt-3 text-2xl font-bold text-slate-900">
                    Rp {{ number_format($bank->initial_balance, 0, ',', '.') }}
                </div>
                <p class="mt-2 text-sm text-slate-500 mb-0">Saldo awal saat rekening ini pertama dicatat.</p>
            </div>
            <div class="rounded-[24px] border border-emerald-100 bg-emerald-50/70 p-5 shadow-sm">
                <div class="text-[11px] uppercase tracking-[0.2em] text-emerald-500">Total Pemasukan</div>
                <div class="mt-3 text-2xl font-bold text-emerald-700">
                    Rp {{ number_format($incomeTotal, 0, ',', '.') }}
                </div>
                <p class="mt-2 text-sm text-emerald-700/70 mb-0">Total transaksi masuk pada filter yang sedang aktif.</p>
            </div>
            <div class="rounded-[24px] border border-rose-100 bg-rose-50/70 p-5 shadow-sm">
                <div class="text-[11px] uppercase tracking-[0.2em] text-rose-500">Total Pengeluaran</div>
                <div class="mt-3 text-2xl font-bold text-rose-700">
                    Rp {{ number_format($expenseTotal, 0, ',', '.') }}
                </div>
                <p class="mt-2 text-sm text-rose-700/70 mb-0">Total transaksi keluar pada filter yang sedang aktif.</p>
            </div>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white p-4 md:p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3 flex-wrap mb-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 mb-1">Filter Mutasi</h2>
                    <p class="text-sm text-slate-500 mb-0">Pilih periode atau jenis transaksi untuk melihat mutasi rekening dengan lebih fokus.</p>
                </div>
                @if(request()->hasAny(['type', 'start_date', 'end_date']))
                    <a href="{{ route('banks.mutations', $bank) }}" class="text-sm font-semibold text-slate-500 no-underline">
                        Reset Filter
                    </a>
                @endif
            </div>

            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
                <div>
                    <label class="label">Jenis Mutasi</label>
                    <select name="type" class="input-field">
                        <option value="">Semua Tipe</option>
                        <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Pemasukan</option>
                        <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                    </select>
                </div>
                <div>
                    <label class="label">Dari Tanggal</label>
                    <input type="text" name="start_date" value="{{ request('start_date') }}"
                        class="input-field js-date-picker" data-format="Y-m-d" data-alt-format="j F Y">
                </div>
                <div>
                    <label class="label">Sampai Tanggal</label>
                    <input type="text" name="end_date" value="{{ request('end_date') }}"
                        class="input-field js-date-picker" data-format="Y-m-d" data-alt-format="j F Y">
                </div>
                <div class="flex gap-2 items-end">
                    <button type="submit" class="btn-primary flex-1 justify-center">
                        <i class="fa-solid fa-filter"></i> Terapkan
                    </button>
                    <a href="{{ route('banks.mutations.pdf', ['bank' => $bank] + $downloadQuery) }}"
                        class="btn-ghost justify-center px-4" title="Download PDF">
                        <i class="fa-solid fa-download"></i>
                    </a>
                </div>
            </form>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-200 px-5 py-4 md:px-6">
                <div class="flex items-center justify-between gap-3 flex-wrap">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 mb-1">Riwayat Mutasi</h2>
                        <p class="text-sm text-slate-500 mb-0">Setiap baris menampilkan saldo sebelum transaksi, nominal mutasi, lalu saldo sesudah transaksi.</p>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                        {{ $transactions->total() }} transaksi
                    </span>
                </div>
            </div>

            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full min-w-[980px]">
                    <thead class="bg-slate-50/90">
                        <tr class="border-b border-slate-200">
                            <th class="px-6 py-4 text-left text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">Tanggal</th>
                            <th class="px-6 py-4 text-left text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">Deskripsi Transaksi</th>
                            <th class="px-6 py-4 text-left text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">Kategori</th>
                            <th class="px-6 py-4 text-left text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">Dicatat Oleh</th>
                            <th class="px-6 py-4 text-right text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">Saldo Awal</th>
                            <th class="px-6 py-4 text-right text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">Mutasi</th>
                            <th class="px-6 py-4 text-right text-[11px] font-bold uppercase tracking-[0.18em] text-slate-500">Saldo Akhir</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($transactions as $tx)
                            <tr class="border-b border-slate-200/90 last:border-b-0 hover:bg-slate-50/70 transition">
                                <td class="px-6 py-5 align-top whitespace-nowrap text-sm text-slate-600">
                                    <div class="font-semibold text-slate-800">{{ $tx->date->isoFormat('D MMMM Y, HH:mm:ss') }}</div>
                                </td>
                                <td class="px-6 py-5 align-top min-w-[300px]">
                                    <div class="font-semibold text-slate-900">{{ $tx->description }}</div>
                                    @if($tx->notes)
                                        <div class="mt-1 text-xs leading-5 text-slate-500">{{ $tx->notes }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-5 align-top whitespace-nowrap text-sm text-slate-600">
                                    <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1">
                                        <span>{{ $tx->category->icon }}</span>
                                        <span>{{ $tx->category->name }}</span>
                                    </span>
                                </td>
                                <td class="px-6 py-5 align-top whitespace-nowrap text-sm text-slate-600">
                                    {{ $tx->user->avatar ?? 'User' }} {{ $tx->user->name }}
                                </td>
                                <td class="px-6 py-5 align-top whitespace-nowrap text-right text-sm text-slate-600">
                                    Rp {{ number_format($tx->opening_balance, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-5 align-top whitespace-nowrap text-right">
                                    <div class="font-bold {{ $tx->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $tx->balance_delta >= 0 ? '+' : '-' }} Rp {{ number_format(abs($tx->balance_delta), 0, ',', '.') }}
                                    </div>
                                    <div class="mt-2">
                                        <span class="{{ $tx->type === 'income' ? 'income-badge' : 'expense-badge' }}">
                                            {{ $tx->type === 'income' ? 'Masuk' : 'Keluar' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 align-top whitespace-nowrap text-right text-sm font-semibold text-slate-900">
                                    Rp {{ number_format($tx->closing_balance, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="mx-auto max-w-md">
                                        <div class="text-base font-semibold text-slate-700">Belum ada mutasi yang cocok.</div>
                                        <p class="mt-2 text-sm text-slate-500 mb-0">Coba ubah filter atau mulai catat transaksi di rekening ini agar riwayat mutasinya muncul di sini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="lg:hidden">
                @forelse($transactions as $tx)
                    <article class="border-b border-slate-200/90 px-4 py-4 last:border-b-0">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="{{ $tx->type === 'income' ? 'income-badge' : 'expense-badge' }}">
                                        {{ $tx->type === 'income' ? 'Masuk' : 'Keluar' }}
                                    </span>
                                    <span class="text-xs text-slate-400">{{ $tx->date->isoFormat('D MMMM Y, HH:mm:ss') }}</span>
                                </div>
                                <div class="mt-3 font-semibold text-slate-900">{{ $tx->description }}</div>
                                <div class="mt-1 text-xs text-slate-500">
                                    {{ $tx->category->icon }} {{ $tx->category->name }} <span class="mx-1 text-slate-300">&bull;</span>
                                    {{ $tx->user->avatar ?? 'User' }} {{ $tx->user->name }}
                                </div>
                                @if($tx->notes)
                                    <div class="mt-2 text-xs leading-5 text-slate-500">{{ $tx->notes }}</div>
                                @endif
                            </div>
                            <div class="text-right shrink-0">
                                <div class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Mutasi</div>
                                <div class="mt-1 text-sm font-bold {{ $tx->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $tx->balance_delta >= 0 ? '+' : '-' }} Rp {{ number_format(abs($tx->balance_delta), 0, ',', '.') }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-3">
                                <div class="text-[11px] uppercase tracking-[0.16em] text-slate-400">Saldo Awal</div>
                                <div class="mt-2 text-sm font-semibold text-slate-800">
                                    Rp {{ number_format($tx->opening_balance, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-3 py-3">
                                <div class="text-[11px] uppercase tracking-[0.16em] text-slate-400">Saldo Akhir</div>
                                <div class="mt-2 text-sm font-semibold text-slate-800">
                                    Rp {{ number_format($tx->closing_balance, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="px-5 py-16 text-center">
                        <div class="text-base font-semibold text-slate-700">Belum ada mutasi yang cocok.</div>
                        <p class="mt-2 text-sm text-slate-500 mb-0">Coba ubah filter atau mulai catat transaksi di rekening ini agar riwayat mutasinya muncul di sini.</p>
                    </div>
                @endforelse
            </div>

            @if($transactions->hasPages())
                <div class="border-t border-slate-200 bg-slate-50/70 px-4 py-4">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
