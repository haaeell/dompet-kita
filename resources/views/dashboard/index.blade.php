@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    {{-- Page Header --}}
    <div class="page-header"
        style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px; margin-bottom: 24px;">
        <div style="flex: 1; min-width: 250px;">
            <h1 class="page-title" style="margin-bottom: 4px;">Halo, {{ auth()->user()->name }}! 👋</h1>
            <p class="page-subtitle" style="margin: 0;">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
        </div>

        {{-- Filter & Button Group --}}
        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap; width: auto;">
            <!-- Dropdown Filter Anggota -->
            <form action="{{ url()->current() }}" method="GET" id="filterForm" style="margin: 0;">
                <select name="user_id" onchange="document.getElementById('filterForm').submit();" class="input-field"
                    style="padding: 8px 12px; border-radius: 10px; min-width: 160px; cursor: pointer; height: auto; font-size: 13px; font-weight: 600;">
                    <option value="">👨‍👩‍bullet Semua Transaksi</option>
                    @foreach($coupleMembers as $member)
                        <option value="{{ $member->id }}" {{ $selectedUserId == $member->id ? 'selected' : '' }}>
                            {{ $member->avatar ?? '👤' }}
                            {{ $member->id == auth()->id() ? 'Saya (' . $member->name . ')' : $member->name }}
                        </option>
                    @endforeach
                </select>
            </form>

            <button onclick="openModal('modalTransaction')" class="btn-primary" style="white-space: nowrap;">
                <i class="fa-solid fa-plus"></i> Tambah Transaksi
            </button>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:16px; margin-bottom:28px;">
        @php $balance = $monthlyIncome - $monthlyExpense; @endphp

        <div class="card" style="border-left:4px solid #f472b6; padding: 16px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                <span
                    style="font-size:11px; font-weight:700; letter-spacing:0.07em; text-transform:uppercase; color:var(--text-secondary);">
                    Saldo Bersih
                </span>
                <span
                    style="width:34px; height:34px; border-radius:10px; background:var(--pink-light); display:flex; align-items:center; justify-content:center; color:var(--pink-dark); flex-shrink:0;">
                    <i class="fa-solid fa-wallet" style="font-size:14px;"></i>
                </span>
            </div>
            <div
                style="font-size:24px; font-weight:700; color:{{ $balance >= 0 ? '#16a34a' : '#e11d48' }}; margin-bottom:4px; word-break: break-all;">
                Rp {{ number_format($balance, 0, ',', '.') }}
            </div>
            <div style="font-size:12px; color:var(--text-secondary);">{{ $balance >= 0 ? '+' : '-' }} Bulan
                {{ now()->isoFormat('MMMM Y') }}
            </div>
        </div>

        <div class="card" style="border-left:4px solid #22c55e; padding: 16px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                <span
                    style="font-size:11px; font-weight:700; letter-spacing:0.07em; text-transform:uppercase; color:var(--text-secondary);">Pemasukan</span>
                <span
                    style="width:34px; height:34px; border-radius:10px; background:#f0fdf4; display:flex; align-items:center; justify-content:center; color:#16a34a; flex-shrink:0;">
                    <i class="fa-solid fa-arrow-trend-up" style="font-size:14px;"></i>
                </span>
            </div>
            <div style="font-size:24px; font-weight:700; color:#16a34a; margin-bottom:4px; word-break: break-all;">
                Rp {{ number_format($monthlyIncome, 0, ',', '.') }}
            </div>
            <div style="font-size:12px; color:var(--text-secondary);">Total pemasukan bulan ini</div>
        </div>

        <div class="card" style="border-left:4px solid #f43f5e; padding: 16px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                <span
                    style="font-size:11px; font-weight:700; letter-spacing:0.07em; text-transform:uppercase; color:var(--text-secondary);">Pengeluaran</span>
                <span
                    style="width:34px; height:34px; border-radius:10px; background:#fff1f2; display:flex; align-items:center; justify-content:center; color:#e11d48; flex-shrink:0;">
                    <i class="fa-solid fa-arrow-trend-down" style="font-size:14px;"></i>
                </span>
            </div>
            <div style="font-size:24px; font-weight:700; color:#e11d48; margin-bottom:4px; word-break: break-all;">
                Rp {{ number_format($monthlyExpense, 0, ',', '.') }}
            </div>
            <div style="font-size:12px; color:var(--text-secondary);">Total pengeluaran bulan ini</div>
        </div>
    </div>

    {{-- Mid Row Layout --}}
    <div class="mid-grid">

        {{-- Recent Transactions --}}
        <div class="card" style="padding: 16px;">
            <div
                style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap: wrap; gap: 8px;">
                <h3 style="font-size:18px; font-weight:700; color:var(--text-primary); margin:0;">Transaksi Terbaru</h3>
                <a href="{{ route('transactions.index') }}"
                    style="font-size:13px; color:var(--pink-dark); font-weight:600; text-decoration:none;">Lihat Semua →</a>
            </div>
            <div style="display:flex; flex-direction:column; gap:4px;">
                @forelse($transactions as $tx)
                    <div class="transaction-item"
                        style="display:flex; align-items:center; gap:14px; padding:11px 10px; border-radius:12px; transition:background 0.15s;"
                        onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
                        <div
                            style="width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; background:{{ $tx->category->color }}18;">
                            {{ $tx->category->icon }}
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div
                                style="font-size:14px; font-weight:600; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                {{ $tx->description }}
                            </div>
                            <div
                                style="font-size:12px; color:var(--text-secondary); margin-top:2px; display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                                <span style="white-space: nowrap;">{{ $tx->user->avatar }} {{ $tx->user->name }}</span>
                                <span style="opacity:0.4;">•</span>
                                <span style="white-space: nowrap;">{{ $tx->date->format('d M') }}</span>
                                <span style="opacity:0.4;">•</span>
                                <span style="white-space: nowrap;">{{ $tx->bank->icon }} {{ $tx->bank->name }}</span>
                            </div>
                        </div>
                        <div
                            style="text-align:right; flex-shrink:0; display: flex; flex-direction: column; align-items: flex-end; gap: 4px;">
                            <div
                                style="font-size:14px; font-weight:700; color:{{ $tx->type === 'income' ? '#16a34a' : '#e11d48' }}; white-space: nowrap;">
                                {{ $tx->type === 'income' ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </div>
                            <span class="{{ $tx->type === 'income' ? 'income-badge' : 'expense-badge' }}">
                                {{ $tx->type === 'income' ? 'Masuk' : 'Keluar' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div style="text-align:center; padding:40px 0;">
                        <div style="font-size:36px; margin-bottom:8px;">💸</div>
                        <p style="font-size:13px; color:var(--text-secondary); margin:0;">Belum ada transaksi</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Right Side Column --}}
        <div class="right-sidebar">

            {{-- Top Pengeluaran --}}
            <div class="card" style="padding: 16px;">
                <h3 style="font-size:16px; font-weight:700; color:var(--text-primary); margin-top:0; margin-bottom:18px;">
                    Top Pengeluaran</h3>
                @php $totalExp = $expenseByCategory->sum('amount') ?: 1; @endphp
                @forelse($expenseByCategory as $cat)
                    <div style="margin-bottom:14px;">
                        <div
                            style="display:flex; align-items:center; justify-content:space-between; margin-bottom:6px; gap: 8px;">
                            <span
                                style="font-size:13px; color:var(--text-primary); display:flex; align-items:center; gap:8px; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                <span>{{ $cat['icon'] }}</span> {{ $cat['name'] }}
                            </span>
                            <span
                                style="font-size:12px; font-weight:700; color:var(--text-secondary); flex-shrink:0;">{{ number_format($cat['amount'] / $totalExp * 100, 1) }}%</span>
                        </div>
                        <div style="height:6px; border-radius:99px; background:#f1f5f9;">
                            <div
                                style="height:6px; border-radius:99px; width:{{ $cat['amount'] / $totalExp * 100 }}%; background:{{ $cat['color'] }}; transition:width 0.6s;">
                            </div>
                        </div>
                    </div>
                @empty
                    <p style="font-size:13px; color:var(--text-secondary); text-align:center; padding:16px 0; margin:0;">Belum
                        ada pengeluaran 🎉</p>
                @endforelse
            </div>

            {{-- Rekening --}}
            <div class="card" style="padding: 16px;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; gap: 8px;">
                    <h3 style="font-size:16px; font-weight:700; color:var(--text-primary); margin:0;">Rekening</h3>
                    <a href="{{ route('banks.index') }}"
                        style="font-size:12px; color:var(--pink-dark); font-weight:600; text-decoration:none;">Kelola →</a>
                </div>
                <div style="display:flex; flex-direction:column; gap:10px;">
                    @forelse($banks as $bank)
                        <div
                            style="display:flex; align-items:center; gap:12px; padding:10px 12px; border-radius:12px; background:{{ $bank->color }}12; border:1px solid {{ $bank->color }}28; min-width:0;">
                            <span style="font-size:20px; flex-shrink:0;">{{ $bank->icon }}</span>
                            <div style="flex:1; min-width:0;">
                                <div
                                    style="font-size:13px; font-weight:600; color:var(--text-primary); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    {{ $bank->name }}
                                </div>
                                <div
                                    style="font-size:12px; font-weight:700; color:{{ $bank->color }}; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    Rp {{ number_format($bank->current_balance, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @empty
                        <p style="font-size:12px; color:var(--text-secondary); text-align:center; padding:8px 0; margin:0;">
                            Tambahkan rekening dulu</p>
                    @endforelse
                </div>
            </div>

            {{-- Target --}}
            <div class="card" style="padding: 16px;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; gap: 8px;">
                    <h3 style="font-size:16px; font-weight:700; color:var(--text-primary); margin:0;">Target Nabung</h3>
                    <a href="{{ route('targets.index') }}"
                        style="font-size:12px; color:var(--pink-dark); font-weight:600; text-decoration:none;">Kelola →</a>
                </div>
                <div style="display:flex; flex-direction:column; gap:10px;">
                    @forelse($targets as $target)
                        <div
                            style="padding:12px; border-radius:12px; background:{{ $target->color }}10; border:1px solid {{ $target->color }}25;">
                            <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
                                <span style="flex-shrink:0;">{{ $target->icon }}</span>
                                <span
                                    style="font-size:13px; font-weight:600; color:var(--text-primary); flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $target->name }}</span>
                                <span
                                    style="font-size:12px; font-weight:700; color:{{ $target->color }}; flex-shrink:0;">{{ $target->progress_percent }}%</span>
                            </div>
                            <div style="height:6px; border-radius:99px; background:#f1f5f9; margin-bottom:6px;">
                                <div
                                    style="height:6px; border-radius:99px; width:{{ $target->progress_percent }}%; background:{{ $target->color }};">
                                </div>
                            </div>
                            <div
                                style="font-size:11px; color:var(--text-secondary); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                Rp {{ number_format($target->current_amount, 0, ',', '.') }} / Rp
                                {{ number_format($target->target_amount, 0, ',', '.') }}
                            </div>
                        </div>
                    @empty
                        <p style="font-size:12px; color:var(--text-secondary); text-align:center; padding:8px 0; margin:0;">Buat
                            target dulu!</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    {{-- Modal Tambah Transaksi --}}
    <div id="modalTransaction" class="modal-overlay">
        <div class="modal-box" style="width: 100%; max-width: 500px; margin: 16px; box-sizing: border-box;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:22px;">
                <h2 class="modal-title" style="margin-bottom:0; font-size: 20px;">Tambah Transaksi</h2>
                <button onclick="closeModal('modalTransaction')"
                    style="background:none; border:none; cursor:pointer; color:var(--text-secondary); padding:6px; border-radius:8px; line-height:1; font-size:16px;"
                    onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            {{-- Type Toggle --}}
            <div
                style="display:flex; gap:6px; margin-bottom:20px; padding:5px; border-radius:14px; background:#f8fafc; border:1px solid var(--border);">
                <button id="typeIncome" onclick="setType('income')"
                    style="flex:1; padding:10px; border-radius:10px; border:none; font-size:13px; font-weight:600; cursor:pointer; transition:all 0.15s; background:none; color:var(--text-secondary);">
                    <i class="fa-solid fa-arrow-up" style="margin-right:5px;"></i> Pemasukan
                </button>
                <button id="typeExpense" onclick="setType('expense')"
                    style="flex:1; padding:10px; border-radius:10px; border:none; font-size:13px; font-weight:600; cursor:pointer; transition:all 0.15s; background:none; color:var(--text-secondary);">
                    <i class="fa-solid fa-arrow-down" style="margin-right:5px;"></i> Pengeluaran
                </button>
            </div>
            <input type="hidden" id="txType" value="expense">

            <div style="display:flex; flex-direction:column; gap:16px;">
                <div>
                    <label class="label">Jumlah</label>
                    <input type="number" id="txAmount" placeholder="0" class="input-field" min="0"
                        style="font-size:22px; font-weight:700; width: 100%; box-sizing: border-box;">
                </div>
                <div>
                    <label class="label">Deskripsi</label>
                    <input type="text" id="txDescription" placeholder="Makan siang bersama..." class="input-field"
                        style="width: 100%; box-sizing: border-box;">
                </div>
                <div class="modal-grid-row">
                    <div>
                        <label class="label">Kategori</label>
                        <select id="txCategory" class="input-field" style="width: 100%; box-sizing: border-box;">
                            <option value="">Pilih kategori</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Rekening</label>
                        <select id="txBank" class="input-field" style="width: 100%; box-sizing: border-box;">
                            @foreach($banks as $bank)
                                <option value="{{ $bank->id }}">{{ $bank->icon }} {{ $bank->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="label">Tanggal</label>
                    <input type="date" id="txDate" class="input-field" value="{{ now()->format('Y-m-d') }}"
                        style="width: 100%; box-sizing: border-box;">
                </div>
                <div>
                    <label class="label">Catatan (opsional)</label>
                    <input type="text" id="txNotes" placeholder="Tambahkan catatan..." class="input-field"
                        style="width: 100%; box-sizing: border-box;">
                </div>
            </div>

            <div style="display:flex; gap:10px; margin-top:22px;">
                <button onclick="closeModal('modalTransaction')" class="btn-ghost"
                    style="flex:1; justify-content:center;">Batal</button>
                <button onclick="submitTransaction()" class="btn-primary" style="flex:1; justify-content:center;">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                </button>
            </div>
        </div>
    </div>

    {{-- Responsive Layout Styles CSS --}}
    <style>
        /* Base Grid System Desktop */
        .mid-grid {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 20px;
            margin-bottom: 20px;
        }

        .right-sidebar {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .modal-grid-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        /* Responsive Breakpoints Tablet & Mobile */
        @media (max-width: 992px) {
            .mid-grid {
                grid-template-columns: 1fr;
            }

            .right-sidebar {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 16px;
            }
        }

        @media (max-width: 576px) {
            .right-sidebar {
                grid-template-columns: 1fr;
            }

            .modal-grid-row {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .transaction-item {
                flex-wrap: wrap;
                gap: 10px !important;
            }

            .transaction-item>div:last-child {
                width: 100%;
                flex-direction: row !important;
                justify-content: space-between;
                align-items: center !important;
                border-top: 1px dashed #f1f5f9;
                padding-top: 8px;
                margin-top: 4px;
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
@endsection

@push('scripts')
    <script>
        const categories = @json($couple->categories);

        function openModal(id) { $('#' + id).addClass('active'); }
        function closeModal(id) { $('#' + id).removeClass('active'); }

        $('#modalTransaction').on('click', function (e) {
            if ($(e.target).is('#modalTransaction')) closeModal('modalTransaction');
        });

        function setType(type) {
            $('#txType').val(type);
            const $inc = $('#typeIncome');
            const $exp = $('#typeExpense');

            $inc.css({ background: '', color: 'var(--text-secondary)' });
            $exp.css({ background: '', color: 'var(--text-secondary)' });

            if (type === 'income') {
                $inc.css({ background: '#f0fdf4', color: '#16a34a' });
            } else {
                $exp.css({ background: '#fff1f2', color: '#e11d48' });
            }

            const $cat = $('#txCategory');
            $cat.html('<option value="">Pilih kategori</option>');
            $.each(categories.filter(c => c.type === type), function (i, c) {
                $cat.append(`<option value="${c.id}">${c.icon} ${c.name}</option>`);
            });
        }

        setType('expense');

        async function submitTransaction() {
            const data = {
                type: $('#txType').val(),
                amount: $('#txAmount').val(),
                description: $('#txDescription').val(),
                category_id: $('#txCategory').val(),
                bank_id: $('#txBank').val(),
                date: $('#txDate').val(),
                notes: $('#txNotes').val(),
            };

            if (!data.amount || !data.description || !data.category_id || !data.bank_id || !data.date) {
                Toast.fire({ icon: 'warning', title: 'Lengkapi semua field!' });
                return;
            }

            try {
                const res = await $.ajax({
                    url: '{{ route("transactions.store") }}',
                    method: 'POST',
                    contentType: 'application/json',
                    headers: { 'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content'), 'Accept': 'application/json' },
                    data: JSON.stringify(data)
                });

                if (res.success) {
                    closeModal('modalTransaction');
                    Toast.fire({ icon: 'success', title: res.message });
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: res.message, background: '#fff', color: '#1a1a2e', confirmButtonColor: '#db2777' });
                }
            } catch (err) {
                let errorMsg = 'Gagal menyimpan transaksi.';
                if (err.responseJSON && err.responseJSON.message) {
                    errorMsg = err.responseJSON.message;
                }
                Swal.fire({ icon: 'error', title: 'Error ' + err.status, text: errorMsg, background: '#fff', color: '#1a1a2e', confirmButtonColor: '#db2777' });
            }
        }
    </script>
@endpush