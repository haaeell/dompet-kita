@extends('layouts.app')
@section('title', 'Tambah Transaksi')

@section('content')
    <div class="flex items-center justify-between gap-3 mb-6 flex-wrap">
        <div>
            <h1 class="page-title">Tambah Transaksi</h1>
            <p class="page-subtitle">Catat pemasukan atau pengeluaran baru</p>
        </div>
        <a href="{{ route('transactions.index') }}" class="btn-ghost w-full sm:w-auto justify-center">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="card mb-5 p-4 bg-rose-50 border border-rose-100 text-rose-700">
            <p class="font-semibold mb-2">Periksa kembali data berikut:</p>
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card p-6 max-w-3xl">
        <form action="{{ route('transactions.store') }}" method="POST" id="transaction-form">
            @csrf

            <div class="flex gap-2 mb-5 p-1.5 rounded-xl bg-slate-50 border border-slate-200">
                <button type="button" id="btnIncome" class="flex-1 py-2.5 px-3 rounded-lg text-sm font-semibold transition-all text-slate-600"
                        onclick="setTxType('income')">
                    <i class="fa-solid fa-arrow-up mr-1.5"></i> Pemasukan
                </button>
                <button type="button" id="btnExpense" class="flex-1 py-2.5 px-3 rounded-lg text-sm font-semibold transition-all text-slate-600"
                        onclick="setTxType('expense')">
                    <i class="fa-solid fa-arrow-down mr-1.5"></i> Pengeluaran
                </button>
            </div>
            <input type="hidden" name="type" id="typeField" value="{{ old('type', 'expense') }}">

            <div class="space-y-5">
                <div>
                    <label class="label">Jumlah (Rp)</label>
                    <input type="text" name="amount" id="amountDisplay" value="{{ old('amount') }}" required
                           placeholder="Contoh: 50.000" class="input-field rupiah font-semibold text-lg">
                </div>

                <div>
                    <label class="label">Deskripsi</label>
                    <input type="text" name="description" value="{{ old('description') }}" required
                           placeholder="Beli apa atau pendapatan dari mana..." class="input-field">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="label">Kategori</label>
                        <select name="category_id" id="category_id" required class="input-field">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" data-type="{{ $category->type }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->icon }} {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Rekening</label>
                        <select name="bank_id" required class="input-field">
                            <option value="">-- Pilih Rekening --</option>
                            @foreach($banks as $bank)
                                <option value="{{ $bank->id }}" {{ old('bank_id') == $bank->id ? 'selected' : '' }}>
                                    {{ $bank->icon }} {{ $bank->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="label">Tanggal</label>
                    <input type="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required class="input-field">
                </div>

                <div>
                    <label class="label">Catatan</label>
                    <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Opsional..." class="input-field">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 mt-6">
                <a href="{{ route('transactions.index') }}" class="btn-ghost w-full justify-center">
                    Batal
                </a>
                <button type="submit" class="btn-primary w-full justify-center">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    const selectedTransactionCategory = '{{ old('category_id', '') }}';

    const allCategories = [];
    document.querySelectorAll('#category_id option[data-type]').forEach(option => {
        allCategories.push({
            value: option.value,
            type: option.dataset.type,
            text: option.textContent,
        });
    });

    function setTxType(type) {
        document.getElementById('typeField').value = type;

        const btnIncome = document.getElementById('btnIncome');
        const btnExpense = document.getElementById('btnExpense');

        btnIncome.classList.remove('bg-green-50', 'text-green-700', 'text-slate-600');
        btnExpense.classList.remove('bg-rose-50', 'text-rose-700', 'text-slate-600');

        if (type === 'income') {
            btnIncome.classList.add('bg-green-50', 'text-green-700');
            btnExpense.classList.add('text-slate-600');
        } else {
            btnExpense.classList.add('bg-rose-50', 'text-rose-700');
            btnIncome.classList.add('text-slate-600');
        }

        const categorySelect = document.getElementById('category_id');
        categorySelect.innerHTML = '<option value="">-- Pilih Kategori --</option>';

        allCategories
            .filter(cat => cat.type === type)
            .forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.value;
                option.dataset.type = cat.type;
                option.textContent = cat.text;

                if (selectedTransactionCategory && cat.value == selectedTransactionCategory) {
                    option.selected = true;
                }

                categorySelect.appendChild(option);
            });
    }

    document.getElementById('transaction-form').addEventListener('submit', function () {
        const amountInput = document.getElementById('amountDisplay');
        amountInput.value = amountInput.value.replace(/\./g, '').replace(/,/g, '');
    });

    document.addEventListener('DOMContentLoaded', function () {
        setTxType('{{ old('type', 'expense') }}');
    });
</script>
@endpush