@extends('layouts.app')
@section('title', 'Transfer Antar Bank')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="page-title mb-1">Transfer Antar Bank</h1>
                <p class="page-subtitle">Pindahkan saldo dari satu rekening ke rekening lain.</p>
            </div>
            <a href="{{ route('banks.index') }}" class="btn-ghost whitespace-nowrap">
                <i class="fa-solid fa-arrow-left"></i> Rekening
            </a>
        </div>

        @if(session('error'))
            <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                <div class="font-bold mb-1">Transfer belum bisa diproses.</div>
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="card p-5 md:p-7">
            @if($banks->count() < 2)
                <div class="text-center py-10">
                    <div class="w-16 h-16 rounded-3xl bg-blue-50 text-blue-700 flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900 mb-2">Butuh minimal dua rekening</h2>
                    <p class="text-sm text-slate-500 max-w-sm mx-auto mb-5">
                        Tambahkan rekening atau e-wallet lain dulu supaya bisa transfer antar bank.
                    </p>
                    <a href="{{ route('banks.index') }}" class="btn-primary">
                        <i class="fa-solid fa-plus"></i> Tambah Rekening
                    </a>
                </div>
            @else
                <form action="{{ route('banks.transfer.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-[1fr_auto_1fr] gap-4 items-end">
                        <div>
                            <label class="label">Dari Rekening</label>
                            <select name="from_bank_id" id="fromBank" class="input-field" required>
                                <option value="">Pilih rekening asal</option>
                                @foreach($banks as $bank)
                                    <option value="{{ $bank->id }}" data-balance="{{ $bank->current_balance }}"
                                        {{ old('from_bank_id') == $bank->id ? 'selected' : '' }}>
                                        {{ $bank->icon }} {{ $bank->name }} - Rp {{ number_format($bank->current_balance, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="hidden md:flex w-11 h-11 rounded-2xl bg-pink-50 text-pink-600 items-center justify-center mb-0.5">
                            <i class="fa-solid fa-arrow-right-arrow-left"></i>
                        </div>

                        <div>
                            <label class="label">Ke Rekening</label>
                            <select name="to_bank_id" id="toBank" class="input-field" required>
                                <option value="">Pilih rekening tujuan</option>
                                @foreach($banks as $bank)
                                    <option value="{{ $bank->id }}" {{ old('to_bank_id') == $bank->id ? 'selected' : '' }}>
                                        {{ $bank->icon }} {{ $bank->name }} - {{ $bank->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="label">Nominal Transfer</label>
                            <input type="text" name="amount" id="transferAmount" value="{{ old('amount') }}"
                                placeholder="Contoh: 150.000" class="input-field rupiah font-bold" required>
                            <p id="balanceHint" class="text-xs text-slate-500 mt-2 mb-0"></p>
                        </div>
                        <div>
                            <label class="label">Tanggal</label>
                            <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}"
                                class="input-field" required>
                        </div>
                    </div>

                    <div>
                        <label class="label">Catatan <span class="text-slate-400 font-normal">(Opsional)</span></label>
                        <textarea name="notes" rows="3" class="input-field resize-none"
                            placeholder="Misal: pindah saldo ke tabungan">{{ old('notes') }}</textarea>
                    </div>

                    <div class="rounded-2xl bg-blue-50 border border-blue-100 px-4 py-3 text-sm text-blue-800">
                        <div class="font-bold mb-1">
                            <i class="fa-solid fa-circle-info mr-1"></i> Cara dicatat
                        </div>
                        Sistem akan membuat dua mutasi otomatis: keluar dari rekening asal dan masuk ke rekening tujuan.
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row gap-3 pt-2">
                        <a href="{{ route('banks.index') }}" class="btn-ghost justify-center flex-1">Batal</a>
                        <button type="submit" class="btn-primary justify-center flex-1">
                            <i class="fa-solid fa-paper-plane"></i> Transfer Sekarang
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fromBank = document.getElementById('fromBank');
            const toBank = document.getElementById('toBank');
            const balanceHint = document.getElementById('balanceHint');

            function formatRupiahNumber(value) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(Number(value || 0));
            }

            function updateTransferState() {
                if (!fromBank || !toBank) return;

                const selected = fromBank.options[fromBank.selectedIndex];
                const balance = selected ? selected.dataset.balance : null;
                balanceHint.textContent = balance ? 'Saldo tersedia: ' + formatRupiahNumber(balance) : '';

                Array.from(toBank.options).forEach(option => {
                    option.disabled = option.value && option.value === fromBank.value;
                });

                if (toBank.value && toBank.value === fromBank.value) {
                    toBank.value = '';
                }
            }

            fromBank?.addEventListener('change', updateTransferState);
            updateTransferState();
        });
    </script>
@endpush
