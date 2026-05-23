@extends('layouts.app')
@section('title', 'Transaksi')

@section('content')

    <style>
        .filter-form {
            display: grid;
            /* Diubah sedikit agar kolom baru muat dengan rapi di desktop */
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 12px;
            align-items: flex-end;
        }

        .filter-actions {
            display: flex;
            gap: 8px;
            grid-column: span 1;
        }

        .tx-desktop-table {
            width: 100%;
        }

        .tx-mobile-list {
            display: none;
        }

        @media (max-width: 768px) {
            .filter-form {
                grid-template-columns: 1fr 1fr;
            }

            .filter-form>div {
                width: 100% !important;
            }

            .filter-form select {
                width: 100% !important;
            }

            .filter-actions {
                grid-column: span 2;
                margin-top: 5px;
            }

            .filter-actions button,
            .filter-actions a {
                flex: 1;
                justify-content: center;
            }

            .tx-desktop-table {
                display: none;
            }

            .tx-mobile-list {
                display: block;
            }

            .modal-box {
                width: 92% !important;
                margin: 0 auto;
                padding: 16px !important;
            }
        }

        @media (max-width: 480px) {
            .filter-form {
                grid-template-columns: 1fr;
            }

            .filter-actions {
                grid-column: span 1;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start !important;
            }

            .page-header button {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <div class="page-header"
        style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 class="page-title">Transaksi</h1>
            <p class="page-subtitle">Semua catatan keuangan bersama</p>
        </div>
        <button onclick="openModal('modalAdd')" class="btn-primary">
            <i class="fa-solid fa-plus"></i> Tambah
        </button>
    </div>

    <!-- FILTER BAR -->
    <div class="card" style="margin-bottom:20px;">
        <form method="GET" class="filter-form">
            <div style="display:flex; flex-direction:column; gap:5px;">
                <label class="label">Tipe</label>
                <select name="type" class="input-field">
                    <option value="">Semua Tipe</option>
                    <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>↑ Pemasukan</option>
                    <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>↓ Pengeluaran</option>
                </select>
            </div>

            <!-- TAMBAHAN BARU: FILTER PER COUPLE (ANGGOTA) -->
            <div style="display:flex; flex-direction:column; gap:5px;">
                <label class="label">Oleh Anggota</label>
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

            <div style="display:flex; flex-direction:column; gap:5px;">
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
            <div style="display:flex; flex-direction:column; gap:5px;">
                <label class="label">Rekening</label>
                <select name="bank_id" class="input-field">
                    <option value="">Semua Rekening</option>
                    @foreach($banks as $bank)
                        <option value="{{ $bank->id }}" {{ request('bank_id') == $bank->id ? 'selected' : '' }}>
                            {{ $bank->icon }} {{ $bank->name }} ({{ $bank->account_name }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex; flex-direction:column; gap:5px;">
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
            <div style="display:flex; flex-direction:column; gap:5px;">
                <label class="label">Tahun</label>
                <select name="year" class="input-field">
                    @foreach(range(now()->year, now()->year - 3) as $y)
                        <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-actions" style="padding-bottom:1px;">
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
                <!-- UPDATE: Tambah deteksi user_id di tombol reset -->
                @if(request()->hasAny(['type', 'user_id', 'category_id', 'bank_id', 'month', 'year']))
                    <a href="{{ route('transactions.index') }}" class="btn-ghost"
                        style="display:inline-flex; align-items:center;">
                        <i class="fa-solid fa-xmark"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- DATA TABLE & MOBILE VIEW (Tetap Sama Seperti Sebelumnya) -->
    <div class="card" style="overflow:hidden;">
        <table id="txTable" class="tx-desktop-table">
            <thead class="bg-pink-100">
                <tr>
                    <th>Deskripsi</th>
                    <th>Tipe</th>
                    <th>Kategori</th>
                    <th>Rekening</th>
                    <th>Oleh</th>
                    <th>Tanggal</th>
                    <th>Jumlah</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                    <tr id="tx-{{ $tx->id }}">
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div
                                    style="width:38px; height:38px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:17px; flex-shrink:0; background:{{ $tx->category->color }}18;">
                                    {{ $tx->category->icon }}
                                </div>
                                <div>
                                    <div style="font-weight:600; font-size:14px; color:var(--text-primary);">
                                        {{ $tx->description }}</div>
                                    @if($tx->notes)
                                        <div style="font-size:11px; color:var(--text-secondary); font-style:italic;">📝
                                            {{ $tx->notes }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="{{ $tx->type === 'income' ? 'income-badge' : 'expense-badge' }}">
                                {{ $tx->type === 'income' ? 'Masuk' : 'Keluar' }}
                            </span>
                        </td>
                        <td style="font-size:13px; color:var(--text-secondary);">{{ $tx->category->icon }}
                            {{ $tx->category->name }}</td>
                        <td style="font-size:13px; color:var(--text-secondary);">{{ $tx->bank->icon }} {{ $tx->bank->name }}
                        </td>
                        <td style="font-size:13px; color:var(--text-secondary);">{{ $tx->user->avatar }} {{ $tx->user->name }}
                        </td>
                        <td style="font-size:13px; color:var(--text-secondary);" data-order="{{ $tx->date->format('Y-m-d') }}">
                            {{ $tx->date->isoFormat('D MMM Y') }}</td>
                        <td data-order="{{ $tx->amount }}">
                            <span
                                style="font-weight:700; font-size:14px; color:{{ $tx->type === 'income' ? '#16a34a' : '#e11d48' }};">
                                {{ $tx->type === 'income' ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </span>
                        </td>
                        <td>
                            @can('delete', $tx)
                                <button onclick="deleteTransaction({{ $tx->id }})"
                                    style="background:none; border:none; cursor:pointer; color:#94a3b8; padding:6px 8px; border-radius:8px; font-size:13px; display:flex; align-items:center; gap:4px; transition:all 0.15s; white-space:nowrap;"
                                    onmouseover="this.style.color='#e11d48';this.style.background='#fff1f2'"
                                    onmouseout="this.style.color='#94a3b8';this.style.background='none'">
                                    <i class="fa-solid fa-trash-can" style="font-size:12px;"></i> Hapus
                                </button>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div style="text-align:center; padding:48px 0;">
                                <div style="font-size:38px; margin-bottom:10px;">📭</div>
                                <p style="font-size:14px; color:var(--text-secondary); margin-bottom:16px;">Belum ada transaksi
                                    ditemukan</p>
                                <button onclick="openModal('modalAdd')" class="btn-primary">
                                    <i class="fa-solid fa-plus"></i> Tambah Pertama
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="tx-mobile-list">
            @forelse($transactions as $tx)
                <div id="tx-mob-{{ $tx->id }}"
                    style="padding:16px; border-bottom:1px solid #f1f5f9; display:flex; flex-direction:column; gap:10px;">
                    <div style="display:flex; justify-content:between; align-items:flex-start; gap:10px;">
                        <div style="display:flex; align-items:center; gap:10px; flex:1;">
                            <div
                                style="width:36px; height:36px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:16px; flex-shrink:0; background:{{ $tx->category->color }}18;">
                                {{ $tx->category->icon }}
                            </div>
                            <div>
                                <div style="font-weight:600; font-size:14px; color:var(--text-primary);">{{ $tx->description }}
                                </div>
                                <div style="font-size:11px; color:#94a3b8;">{{ $tx->date->isoFormat('D MMM Y') }} •
                                    {{ $tx->bank->icon }} {{ $tx->bank->name }}</div>
                            </div>
                        </div>
                        <div style="text-align:right;">
                            <div
                                style="font-weight:700; font-size:14px; color:{{ $tx->type === 'income' ? '#16a34a' : '#e11d48' }};">
                                {{ $tx->type === 'income' ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </div>
                            <span class="{{ $tx->type === 'income' ? 'income-badge' : 'expense-badge' }}"
                                style="font-size:10px; padding:2px 6px;">
                                {{ $tx->type === 'income' ? 'Masuk' : 'Keluar' }}
                            </span>
                        </div>
                    </div>

                    @if($tx->notes)
                        <div
                            style="font-size:11px; color:var(--text-secondary); background:#f8fafc; padding:6px 10px; border-radius:6px; font-style:italic;">
                            📝 {{ $tx->notes }}
                        </div>
                    @endif

                    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:2px;">
                        <span style="font-size:11px; color:var(--text-secondary);">
                            Oleh: {{ $tx->user->avatar }} {{ $tx->user->name }}
                        </span>
                        @can('delete', $tx)
                            <button onclick="deleteTransaction({{ $tx->id }})"
                                style="background:none; border:none; color:#94a3b8; font-size:12px; display:flex; align-items:center; gap:4px; padding:4px;">
                                <i class="fa-solid fa-trash-can" style="color:#ef4444;"></i> Hapus
                            </button>
                        @endcan
                    </div>
                </div>
            @empty
                <div style="text-align:center; padding:48px 16px;">
                    <div style="font-size:34px; margin-bottom:10px;">📭</div>
                    <p style="font-size:14px; color:var(--text-secondary); margin-bottom:16px;">Belum ada transaksi ditemukan
                    </p>
                    <button onclick="openModal('modalAdd')" class="btn-primary" style="width:100%;">
                        <i class="fa-solid fa-plus"></i> Tambah Pertama
                    </button>
                </div>
            @endforelse
        </div>
    </div>

    <!-- MODAL ADD TRANSACTION -->
    <div id="modalAdd" class="modal-overlay">
        <div class="modal-box">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:22px;">
                <h2 class="modal-title" style="margin-bottom:0;">Tambah Transaksi</h2>
                <button onclick="closeModal('modalAdd')"
                    style="background:none; border:none; cursor:pointer; color:var(--text-secondary); padding:6px; border-radius:8px; font-size:16px; line-height:1;"
                    onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div
                style="display:flex; gap:6px; margin-bottom:20px; padding:5px; border-radius:14px; background:#f8fafc; border:1px solid var(--border);">
                <button id="btnIncome" onclick="setTxType('income')"
                    style="flex:1; padding:10px; border-radius:10px; border:none; font-size:13px; font-weight:600; cursor:pointer; transition:all 0.15s; background:none; color:var(--text-secondary);">
                    <i class="fa-solid fa-arrow-up" style="margin-right:5px;"></i> Pemasukan
                </button>
                <button id="btnExpense" onclick="setTxType('expense')"
                    style="flex:1; padding:10px; border-radius:10px; border:none; font-size:13px; font-weight:600; cursor:pointer; transition:all 0.15s; background:none; color:var(--text-secondary);">
                    <i class="fa-solid fa-arrow-down" style="margin-right:5px;"></i> Pengeluaran
                </button>
            </div>
            <input type="hidden" id="addType" value="expense">

            <div style="display:flex; flex-direction:column; gap:16px;">
                <div>
                    <label class="label">Jumlah (Rp)</label>
                    <input type="number" id="addAmount" placeholder="0" class="input-field" min="1"
                        style="font-size:22px; font-weight:700;">
                </div>
                <div>
                    <label class="label">Deskripsi</label>
                    <input type="text" id="addDesc" placeholder="Apa ini?" class="input-field">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div>
                        <label class="label">Kategori</label>
                        <select id="addCategory" class="input-field">
                            <option value="">Pilih...</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Rekening</label>
                        <select id="addBank" class="input-field">
                            @foreach($banks as $b)
                                <option value="{{ $b->id }}">{{ $b->icon }} {{ $b->name }} ({{ $b->account_name }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="label">Tanggal</label>
                    <input type="date" id="addDate" class="input-field" value="{{ now()->format('Y-m-d') }}">
                </div>
                <div>
                    <label class="label">Catatan</label>
                    <input type="text" id="addNotes" placeholder="Opsional..." class="input-field">
                </div>
            </div>

            <div style="display:flex; gap:10px; margin-top:22px;">
                <button onclick="closeModal('modalAdd')" class="btn-ghost"
                    style="flex:1; justify-content:center;">Batal</button>
                <button onclick="submitAdd()" class="btn-primary" style="flex:1; justify-content:center;">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                </button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const allCategories = @json(auth()->user()->couple->categories);

        // Inisialisasi DataTables (Hanya untuk desktop)
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
                        zeroRecords: '<div style="text-align:center;padding:36px 0;font-size:14px;color:var(--text-secondary);">Tidak ada transaksi ditemukan</div>',
                        paginate: { previous: '‹', next: '›' }
                    },
                });
            }
        });

        function openModal(id) { $('#' + id).addClass('active'); }
        function closeModal(id) { $('#' + id).removeClass('active'); }

        $('#modalAdd').on('click', function (e) {
            if ($(e.target).is('#modalAdd')) closeModal('modalAdd');
        });

        function setTxType(type) {
            $('#addType').val(type);
            $('#btnIncome, #btnExpense').css({ background: 'none', color: 'var(--text-secondary)' });
            if (type === 'income') {
                $('#btnIncome').css({ background: '#f0fdf4', color: '#16a34a' });
            } else {
                $('#btnExpense').css({ background: '#fff1f2', color: '#e11d48' });
            }
            const $cat = $('#addCategory');
            $cat.html('<option value="">Pilih kategori</option>');
            $.each(allCategories.filter(c => c.type === type), function (i, c) {
                $cat.append(`<option value="${c.id}">${c.icon} ${c.name}</option>`);
            });
        }

        setTxType('expense');

        async function submitAdd() {
            const data = {
                type: $('#addType').val(),
                amount: $('#addAmount').val(),
                description: $('#addDesc').val(),
                category_id: $('#addCategory').val(),
                bank_id: $('#addBank').val(),
                date: $('#addDate').val(),
                notes: $('#addNotes').val(),
            };

            if (!data.amount || !data.description || !data.category_id) {
                Toast.fire({ icon: 'warning', title: 'Lengkapi semua field!' });
                return;
            }

            const res = await $.ajax({
                url: '{{ route("transactions.store") }}',
                method: 'POST',
                contentType: 'application/json',
                headers: { 'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content'), 'Accept': 'application/json' },
                data: JSON.stringify(data)
            });

            if (res.success) {
                closeModal('modalAdd');
                Toast.fire({ icon: 'success', title: res.message });
                setTimeout(() => location.reload(), 1200);
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.message, background: '#fff', color: '#1a1a2e', confirmButtonColor: '#db2777' });
            }
        }

        function deleteTransaction(id) {
            deleteConfirm(`/transactions/${id}`, () => {
                if ($('#txTable').innerHeight() > 0) {
                    $('#txTable').DataTable().row($('#tx-' + id)).remove().draw();
                } else {
                    $('#tx-mob-' + id).fadeOut(300, function () { $(this).remove(); });
                }
            });
        }
    </script>
@endpush