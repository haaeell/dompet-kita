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
        <div id="transaction-error-box" class="card mb-5 p-4 bg-rose-50 border border-rose-100 text-rose-700">
            <p class="font-semibold mb-2">Periksa kembali data berikut:</p>
            <ul id="transaction-error-list" class="list-disc list-inside text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @else
        <div id="transaction-error-box" class="card mb-5 p-4 bg-rose-50 border border-rose-100 text-rose-700 hidden">
            <p class="font-semibold mb-2">Periksa kembali data berikut:</p>
            <ul id="transaction-error-list" class="list-disc list-inside text-sm space-y-1"></ul>
        </div>
    @endif

    <div class="card p-6 max-w-3xl pb-40 sm:pb-6">
        <div id="offline-transaction-notice"
            class="hidden mb-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            <div class="font-semibold">Mode offline aktif</div>
            <div>Transaksi yang kamu simpan akan masuk antrean dan otomatis sinkron saat internet kembali.</div>
        </div>

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
                <div class="transaction-field">
                    <label class="label">Jumlah (Rp)</label>
                    <input type="text" name="amount" id="amountDisplay" value="{{ old('amount') }}" required
                           placeholder="Contoh: 50.000" class="input-field rupiah font-semibold text-lg">
                </div>

                <div class="transaction-field">
                    <label class="label">Deskripsi</label>
                    <input type="text" name="description" value="{{ old('description') }}" required
                           placeholder="Beli apa atau pendapatan dari mana..." class="input-field">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="transaction-field">
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
                    <div class="transaction-field">
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

                <div class="transaction-field">
                    <label class="label">Tanggal</label>
                    <input type="text" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required
                           class="input-field js-date-picker" data-format="Y-m-d" data-alt-format="j F Y">
                </div>

                <div class="transaction-field">
                    <label class="label">Catatan</label>
                    <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Opsional..." class="input-field">
                </div>
            </div>

            <div class="hidden sm:grid grid-cols-2 gap-3 mt-6">
                <a href="{{ route('transactions.index') }}" class="btn-ghost w-full justify-center">
                    Batal
                </a>
                <button type="submit" id="desktop-submit-button" class="btn-primary w-full justify-center">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                </button>
            </div>
        </form>
    </div>

    <div id="mobile-transaction-actions"
        class="sm:hidden fixed inset-x-0 z-[1101] border-t border-slate-200 bg-white/95 backdrop-blur px-4 py-3"
        style="bottom: calc(env(safe-area-inset-bottom, 0px) + 74px);">
        <div class="grid grid-cols-2 gap-3 max-w-3xl mx-auto">
            <a href="{{ route('transactions.index') }}" class="btn-ghost w-full justify-center">
                Batal
            </a>
            <button type="submit" form="transaction-form" id="mobile-submit-button" class="btn-primary w-full justify-center">
                <i class="fa-solid fa-floppy-disk"></i> Simpan
            </button>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const selectedTransactionCategory = '{{ old('category_id', '') }}';
    const transactionForm = document.getElementById('transaction-form');
    const transactionErrorBox = document.getElementById('transaction-error-box');
    const transactionErrorList = document.getElementById('transaction-error-list');
    const desktopSubmitButton = document.getElementById('desktop-submit-button');
    const mobileSubmitButton = document.getElementById('mobile-submit-button');
    const mobileTransactionActions = document.getElementById('mobile-transaction-actions');
    const offlineTransactionNotice = document.getElementById('offline-transaction-notice');

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

    function setSubmittingState(isSubmitting) {
        [desktopSubmitButton, mobileSubmitButton].forEach(button => {
            if (!button) return;
            button.disabled = isSubmitting;
            button.classList.toggle('opacity-70', isSubmitting);
            button.classList.toggle('pointer-events-none', isSubmitting);
            button.innerHTML = isSubmitting
                ? '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...'
                : '<i class="fa-solid fa-floppy-disk"></i> Simpan';
        });
    }

    function showTransactionErrors(errors) {
        if (!transactionErrorBox || !transactionErrorList) return;

        transactionErrorList.innerHTML = '';
        errors.forEach(error => {
            const li = document.createElement('li');
            li.textContent = error;
            transactionErrorList.appendChild(li);
        });

        transactionErrorBox.classList.remove('hidden');
        transactionErrorBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function clearTransactionErrors() {
        if (!transactionErrorBox || !transactionErrorList) return;
        transactionErrorList.innerHTML = '';
        transactionErrorBox.classList.add('hidden');
    }

    function buildTransactionPayload() {
        const payload = {};

        $(transactionForm).serializeArray().forEach(item => {
            payload[item.name] = item.name === 'amount'
                ? item.value.replace(/\./g, '').replace(/,/g, '')
                : item.value;
        });

        const categoryOption = transactionForm.querySelector('[name="category_id"] option:checked');
        const bankOption = transactionForm.querySelector('[name="bank_id"] option:checked');
        payload.category_label = categoryOption ? categoryOption.textContent.trim() : '';
        payload.bank_label = bankOption ? bankOption.textContent.trim() : '';
        payload.user_label = 'Saya';

        return payload;
    }

    function queueOfflineTransaction(payload) {
        window.DompetKitaOffline.queueTransaction(payload);
        transactionForm.reset();
        setTxType('{{ old('type', 'expense') }}');
        setSubmittingState(false);
        clearTransactionErrors();
    }

    function updateOfflineNotice() {
        if (!offlineTransactionNotice) return;
        offlineTransactionNotice.classList.toggle('hidden', navigator.onLine);
    }

    transactionForm.addEventListener('submit', function (event) {
        event.preventDefault();
        event.stopImmediatePropagation();

        clearTransactionErrors();
        setSubmittingState(true);

        const payload = buildTransactionPayload();

        if (!navigator.onLine) {
            queueOfflineTransaction(payload);
            return;
        }

        $.ajax({
            url: transactionForm.action,
            method: 'POST',
            data: $.param(payload),
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            success: function (response) {
                Toast.fire({ icon: 'success', title: response.message || 'Transaksi berhasil ditambahkan!' });
                window.setTimeout(() => {
                    window.location.href = '{{ route('transactions.index') }}';
                }, 500);
            },
            error: function (xhr) {
                if (xhr.status === 0) {
                    queueOfflineTransaction(payload);
                    return;
                }

                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    showTransactionErrors(Object.values(xhr.responseJSON.errors).flat());
                    return;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal menyimpan',
                    text: xhr.responseJSON?.message || 'Coba lagi sebentar ya.',
                    background: '#fff',
                    color: '#1a1a2e',
                    confirmButtonColor: '#db2777'
                });
            },
            complete: function () {
                setSubmittingState(false);
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        setTxType('{{ old('type', 'expense') }}');
        updateOfflineNotice();
        window.DompetKitaOffline.syncTransactions();

        window.addEventListener('online', updateOfflineNotice);
        window.addEventListener('offline', updateOfflineNotice);

        if (window.visualViewport && mobileTransactionActions) {
            const updateMobileActionPosition = () => {
                const keyboardVisible = window.visualViewport.height < window.innerHeight - 120;
                mobileTransactionActions.style.transform = keyboardVisible ? 'translateY(100%)' : 'translateY(0)';
            };

            window.visualViewport.addEventListener('resize', updateMobileActionPosition);
            window.visualViewport.addEventListener('scroll', updateMobileActionPosition);
            updateMobileActionPosition();
        }

        document.querySelectorAll('.transaction-field input, .transaction-field select').forEach(field => {
            field.addEventListener('focus', function () {
                window.setTimeout(() => {
                    this.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 250);
            });
        });
    });
</script>
@endpush
