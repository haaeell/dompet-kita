@extends('layouts.app')
@section('title', 'Transaksi')

@section('content')

    @php
        $currentCategory = request('category_id') ? $categories->firstWhere('id', request('category_id')) : null;
        $hasFilters = request()->hasAny(['type', 'user_id', 'category_id', 'bank_id', 'month', 'year']);
    @endphp

    <div class="flex items-center justify-between gap-3 mb-6 flex-wrap">
        <div>
            @if($currentCategory)
                <h1 class="page-title">Riwayat Kategori: {{ $currentCategory->name }}</h1>
                <p class="page-subtitle">Menampilkan semua transaksi untuk kategori {{ $currentCategory->name }}.</p>
            @else
                <h1 class="page-title">Transaksi</h1>
                <p class="page-subtitle">Semua catatan keuangan bersama</p>
            @endif
        </div>
        <a href="{{ route('transactions.create') }}" class="btn-primary w-full sm:w-auto justify-center">
            <i class="fa-solid fa-plus"></i> Tambah
        </a>
    </div>

    <div class="md:hidden flex items-center gap-3 mb-5">
        <button type="button" onclick="openTransactionFilters()"
            class="btn-ghost flex-1 justify-center {{ $hasFilters ? 'border-pink-200 bg-pink-50 text-pink-700' : '' }}">
            <i class="fa-solid fa-sliders"></i> Filter Transaksi
        </button>
        @if($hasFilters)
            <a href="{{ route('transactions.index') }}" class="btn-ghost justify-center px-4">
                <i class="fa-solid fa-rotate-left"></i>
            </a>
        @endif
    </div>

    <div class="card mb-5 p-4 hidden md:block">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">
                <div class="flex flex-col gap-1.5">
                    <label class="label">Tipe</label>
                    <select name="type" class="input-field">
                        <option value="">Semua Tipe</option>
                        <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>↑ Pemasukan</option>
                        <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>↓ Pengeluaran</option>
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="label">Oleh Siapa</label>
                    <select name="user_id" class="input-field">
                        <option value="">Semua Orang</option>
                        @foreach($coupleUsers as $cUser)
                            <option value="{{ $cUser->id }}" {{ request('user_id') == $cUser->id ? 'selected' : '' }}>
                                {{ $cUser->avatar ?? '👤' }}
                                {{ $cUser->id == auth()->id() ? 'Saya (' . $cUser->name . ')' : $cUser->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="label">Kategori</label>
                    <select name="category_id" class="input-field">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->icon }} {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="label">Rekening</label>
                    <select name="bank_id" class="input-field">
                        <option value="">Semua Rekening</option>
                        @foreach($banks as $bank)
                            <option value="{{ $bank->id }}" {{ request('bank_id') == $bank->id ? 'selected' : '' }}>
                                {{ $bank->icon }} {{ $bank->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="label">Bulan</label>
                    <select name="month" class="input-field">
                        <option value="">Semua Bulan</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m)->isoFormat('MMMM') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="label">Tahun</label>
                    <select name="year" class="input-field">
                        @foreach(range(now()->year, now()->year - 3) as $y)
                            <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2 sm:col-span-2 lg:col-span-3 xl:col-span-6 mt-1">
                    <button type="submit" class="btn-primary flex-1 justify-center">
                        <i class="fa-solid fa-filter"></i> Filter
                    </button>
                    @if($hasFilters)
                        <a href="{{ route('transactions.index') }}" class="btn-ghost flex-1 justify-center">
                            <i class="fa-solid fa-xmark"></i> Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div id="transactionFilterCanvas"
            class="fixed inset-0 z-[1101] hidden md:hidden"
            aria-hidden="true">
            <button type="button" onclick="closeTransactionFilters()"
                class="absolute inset-0 bg-slate-950/35 backdrop-blur-[2px]"></button>
            <div id="transactionFilterPanel"
                class="absolute right-0 top-0 h-full w-[min(92vw,380px)] bg-white shadow-2xl border-l border-slate-200 translate-x-full transition-transform duration-300 ease-out flex flex-col">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                    <div>
                        <div class="text-base font-bold text-slate-900">Filter Transaksi</div>
                        <div class="text-xs text-slate-500">Atur dulu, lalu lihat riwayatnya</div>
                    </div>
                    <button type="button" onclick="closeTransactionFilters()"
                        class="w-10 h-10 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <form method="GET" class="flex-1 flex flex-col min-h-0">
                    <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4 pb-24">
                        <div class="flex flex-col gap-1.5">
                            <label class="label">Tipe</label>
                            <select name="type" class="input-field">
                                <option value="">Semua Tipe</option>
                                <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>↑ Pemasukan</option>
                                <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>↓ Pengeluaran</option>
                            </select>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="label">Oleh Siapa</label>
                            <select name="user_id" class="input-field">
                                <option value="">Semua Orang</option>
                                @foreach($coupleUsers as $cUser)
                                    <option value="{{ $cUser->id }}" {{ request('user_id') == $cUser->id ? 'selected' : '' }}>
                                        {{ $cUser->avatar ?? '👤' }}
                                        {{ $cUser->id == auth()->id() ? 'Saya (' . $cUser->name . ')' : $cUser->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="label">Kategori</label>
                            <select name="category_id" class="input-field">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->icon }} {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="label">Rekening</label>
                            <select name="bank_id" class="input-field">
                                <option value="">Semua Rekening</option>
                                @foreach($banks as $bank)
                                    <option value="{{ $bank->id }}" {{ request('bank_id') == $bank->id ? 'selected' : '' }}>
                                        {{ $bank->icon }} {{ $bank->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="flex flex-col gap-1.5">
                                <label class="label">Bulan</label>
                                <select name="month" class="input-field">
                                    <option value="">Semua Bulan</option>
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create(null, $m)->isoFormat('MMMM') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex flex-col gap-1.5">
                                <label class="label">Tahun</label>
                                <select name="year" class="input-field">
                                    @foreach(range(now()->year, now()->year - 3) as $y)
                                        <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 p-5 border-t border-slate-100 bg-white"
                        style="padding-bottom: calc(env(safe-area-inset-bottom, 0px) + 88px);">
                        @if($hasFilters)
                            <a href="{{ route('transactions.index') }}" class="btn-ghost w-full justify-center">
                                <i class="fa-solid fa-xmark"></i> Reset
                            </a>
                        @else
                            <button type="button" onclick="closeTransactionFilters()" class="btn-ghost w-full justify-center">
                                Tutup
                            </button>
                        @endif
                        <button type="submit" class="btn-primary w-full justify-center">
                            <i class="fa-solid fa-filter"></i> Terapkan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="hidden md:block overflow-x-auto">
                <table id="txTable" class="w-full">
                    <thead class="bg-pink-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Deskripsi
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Tipe</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Kategori
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Rekening
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Oleh</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Tanggal
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Jumlah
                            </th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @forelse($transactions as $tx)
                            <tr id="tx-{{ $tx->id }}" class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg flex-shrink-0"
                                            style="background: {{ $tx->category->color }}18;">
                                            {{ $tx->category->icon }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-sm text-slate-900">{{ $tx->description }}</div>
                                            @if($tx->notes)
                                                <div class="text-xs text-slate-500 italic mt-0.5">📝 {{ $tx->notes }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="{{ $tx->type === 'income' ? 'income-badge' : 'expense-badge' }}">
                                        {{ $tx->type === 'income' ? 'Masuk' : 'Keluar' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $tx->category->icon }} {{ $tx->category->name }}
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $tx->bank->icon }} {{ $tx->bank->name }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $tx->user->avatar }} {{ $tx->user->name }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600" data-order="{{ $tx->date->format('Y-m-d') }}">
                                    {{ $tx->date->isoFormat('D MMM Y') }}
                                </td>
                                <td class="px-4 py-3" data-order="{{ $tx->amount }}">
                                    <span
                                        class="font-bold text-sm {{ $tx->type === 'income' ? 'text-green-600' : 'text-rose-600' }}">
                                        {{ $tx->type === 'income' ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @can('delete', $tx)
                                        <button onclick="deleteTransaction({{ $tx->id }})"
                                            class="text-slate-400 hover:text-rose-600 hover:bg-rose-50 px-2 py-1.5 rounded-lg text-xs font-medium transition-colors flex items-center gap-1.5">
                                            <i class="fa-solid fa-trash-can text-xs"></i> Hapus
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-16">
                                    <div class="text-center">
                                        <div class="text-4xl mb-3">📭</div>
                                        <p class="text-sm text-slate-500 mb-4">Belum ada transaksi ditemukan</p>
                                        <a href="{{ route('transactions.create') }}" class="btn-primary">
                                            <i class="fa-solid fa-plus"></i> Tambah Pertama
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="md:hidden">
                @forelse($transactions as $tx)
                    <div id="tx-mob-{{ $tx->id }}" class="p-4 border-b border-slate-100 last:border-0">
                        <div class="flex justify-between items-start gap-3 mb-2">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center text-base flex-shrink-0"
                                    style="background: {{ $tx->category->color }}18;">
                                    {{ $tx->category->icon }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-sm text-slate-900 truncate">{{ $tx->description }}</div>
                                    <div class="text-xs text-slate-400 mt-0.5">
                                        {{ $tx->date->isoFormat('D MMM Y') }} • {{ $tx->bank->icon }} {{ $tx->bank->name }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <div class="font-bold text-sm {{ $tx->type === 'income' ? 'text-green-600' : 'text-rose-600' }}">
                                    {{ $tx->type === 'income' ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                </div>
                                <span
                                    class="{{ $tx->type === 'income' ? 'income-badge' : 'expense-badge' }} text-xs px-2 py-0.5 mt-1 inline-block">
                                    {{ $tx->type === 'income' ? 'Masuk' : 'Keluar' }}
                                </span>
                            </div>
                        </div>

                        @if($tx->notes)
                            <div class="text-xs text-slate-600 bg-slate-50 px-3 py-2 rounded-lg italic mb-2">
                                📝 {{ $tx->notes }}
                            </div>
                        @endif

                        <div class="flex justify-between items-center mt-2 pt-2 border-t border-slate-50">
                            <span class="text-xs text-slate-500">
                                Oleh: {{ $tx->user->avatar }} {{ $tx->user->name }}
                            </span>
                            @can('delete', $tx)
                                <button onclick="deleteTransaction({{ $tx->id }})"
                                    class="text-xs text-slate-400 hover:text-rose-600 flex items-center gap-1.5 px-2 py-1">
                                    <i class="fa-solid fa-trash-can text-rose-500"></i> Hapus
                                </button>
                            @endcan
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 px-4">
                        <div class="text-4xl mb-3">📭</div>
                        <p class="text-sm text-slate-500 mb-4">Belum ada transaksi ditemukan</p>
                        <a href="{{ route('transactions.create') }}" class="btn-primary w-full justify-center">
                            <i class="fa-solid fa-plus"></i> Tambah Pertama
                        </a>
                    </div>
                @endforelse
            </div>
        </div>


@endsection

@push('scripts')
    <script>
        function openTransactionFilters() {
            const canvas = document.getElementById('transactionFilterCanvas');
            const panel = document.getElementById('transactionFilterPanel');
            if (!canvas || !panel) return;

            canvas.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            requestAnimationFrame(() => {
                panel.classList.remove('translate-x-full');
            });
        }

        function closeTransactionFilters() {
            const canvas = document.getElementById('transactionFilterCanvas');
            const panel = document.getElementById('transactionFilterPanel');
            if (!canvas || !panel) return;

            panel.classList.add('translate-x-full');
            document.body.style.overflow = '';

            window.setTimeout(() => {
                canvas.classList.add('hidden');
            }, 300);
        }

        $(function () {
            if ($(window).width() > 768) {
                $('#txTable').DataTable({
                    pageLength: 15,
                    order: [[5, 'desc']],
                    columnDefs: [
                        { orderable: false, targets: [1, 7] },
                        { searchable: false, targets: [1, 7] }
                    ],
                    language: {
                        search: '',
                        searchPlaceholder: 'Cari deskripsi...',
                        lengthMenu: 'Tampilkan _MENU_ baris',
                        info: 'Menampilkan _START_–_END_ dari _TOTAL_ transaksi',
                        infoEmpty: 'Tidak ada transaksi',
                        infoFiltered: '(difilter dari _MAX_ total)',
                        zeroRecords: '<div class="text-center py-9 text-sm text-slate-500">Tidak ada transaksi ditemukan</div>',
                        paginate: { previous: '‹', next: '›' }
                    },
                });
            }

            @if(session('success'))
                Toast.fire({ icon: 'success', title: '{{ session('success') }}' });
            @endif
        });

        function deleteTransaction(id) {
            deleteConfirm(`/transactions/${id}`, () => {
                if ($('#txTable').length && $('#txTable').DataTable) {
                    $('#txTable').DataTable().row($('#tx-' + id)).remove().draw();
                } else {
                    $('#tx-mob-' + id).fadeOut(300, function () { $(this).remove(); });
                }
            });
        }
    </script>
@endpush
