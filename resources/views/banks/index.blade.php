@extends('layouts.app')
@section('title', 'Rekening')

@section('content')

    <!-- PAGE HEADER -->
    <div class="rc-header-container">
        <div>
            <h1 class="rc-title">Rekening & Dompet</h1>
            <p class="rc-subtitle">Kelola semua pos dan tempat penyimpanan uang kalian</p>
        </div>
        <button onclick="openModal('modalBank')" class="btn-primary">
            <i class="fa-solid fa-plus"></i> Tambah Rekening
        </button>
    </div>

    <!-- REKENING CARDS GRID -->
    <div class="rc-grid">
        @forelse($banks as $bank)
            <div class="rc-card" id="bank-{{ $bank->id }}" style="--bank-theme: {{ $bank->color }};">
                <!-- Lapisan Efek Gradasi Kartu -->
                <div class="rc-card-overlay"></div>

                <div class="rc-card-header">
                    <div class="rc-icon-box">
                        {{ $bank->icon }}
                    </div>

                    @if($bank->transactions_count === 0)
                        <button onclick="deleteBank({{ $bank->id }})" class="rc-action-delete" title="Hapus Rekening">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    @endif
                </div>

                <div class="rc-card-body">
                    <div class="rc-label-balance">Saldo Saat Ini</div>
                    <div class="rc-balance">
                        <span class="rc-currency">Rp</span>{{ number_format($bank->current_balance, 0, ',', '.') }}
                    </div>
                </div>

                <div class="rc-card-footer">
                    <div class="rc-meta-left">
                        <div class="rc-bank-name">{{ $bank->name }}</div>
                        <div class="rc-owner">{{ $bank->account_name }}</div>
                    </div>

                    @if($bank->account_number)
                        <div class="rc-number-badge">
                            <span>•••• {{ substr($bank->account_number, -4) }}</span>
                        </div>
                    @endif
                </div>

                <!-- Indikator Jumlah Transaksi Kecil di Pojok Atas -->
                <div class="rc-transaction-pill">
                    <i class="fa-solid fa-clock-rotate-left"></i> {{ $bank->transactions_count }}
                </div>
            </div>
        @empty
            <div class="rc-empty-box">
                <div class="rc-empty-emoji">🏦</div>
                <h3 class="rc-empty-title">Belum Ada Rekening Aktif</h3>
                <p class="rc-empty-text">Dompet digital atau rekening bank kamu belum terdaftar. Yuk masukkan biar bisa langsung
                    hitung pengeluaran!</p>
                <button onclick="openModal('modalBank')" class="rc-btn-add" style="margin: 0 auto;">
                    <i class="fa-solid fa-plus"></i> Hubungkan Sekarang
                </button>
            </div>
        @endforelse
    </div>

    <!-- MODAL POPUP (Tetap dipertahankan strukturnya agar sinkron dengan JS) -->
    <div id="modalBank" class="modal-overlay">
        <div class="modal-box" style="max-width: 440px; border-radius: 24px; padding: 28px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
                <h2 class="modal-title" style="margin:0; font-size:20px; font-weight:700;">Tambah Rekening</h2>
                <button onclick="closeModal('modalBank')"
                    style="background:none; border:none; cursor:pointer; color:#94a3b8; padding:8px; font-size:18px;"><i
                        class="fa-solid fa-xmark"></i></button>
            </div>

            <div style="display:flex; flex-direction:column; gap:16px;">
                <div style="display:grid; grid-template-columns:80px 1fr; gap:12px;">
                    <div>
                        <label class="label">Icon</label>
                        <input type="text" id="bankIcon" value="🏦" class="input-field" maxlength="4"
                            style="font-size:24px; text-align:center; padding:10px;">
                    </div>
                    <div>
                        <label class="label">Nama Bank / E-Wallet</label>
                        <input type="text" id="bankName" placeholder="BCA, Mandiri, GoPay, OVO..." class="input-field">
                    </div>
                </div>

                <div>
                    <label class="label" style="margin-bottom:8px; display:block;">Pilih Warna Tema Kartu</label>
                    <div style="display:grid; grid-template-columns: repeat(6, 1fr); gap:8px;">
                        @foreach(['#10b981', '#3b82f6', '#f97316', '#8b5cf6', '#f43f5e', '#06b6d4', '#d946ef', '#f59e0b', '#ec4899', '#14b8a6', '#6366f1', '#6b7280'] as $c)
                            <button onclick="pickBankColor('{{ $c }}')" id="bc-{{ ltrim($c, '#') }}" class="rc-color-dot"
                                style="background:{{ $c }}; width:100%; height:34px; border-radius:10px; border:2px solid transparent; cursor:pointer; transition:0.2s;"></button>
                        @endforeach
                    </div>
                    <input type="hidden" id="bankColor" value="#10b981">
                </div>

                <div>
                    <label class="label">Nama Pemilik</label>
                    <input type="text" id="bankAccountName" placeholder="Nama sesuai aplikasi/buku tabungan"
                        class="input-field">
                </div>

                <div>
                    <label class="label">Nomor Rekening <span
                            style="color:#94a3b8; font-weight:400;">(Opsional)</span></label>
                    <input type="text" id="bankAccountNumber" placeholder="Contoh: 123456789" class="input-field">
                </div>

                <div>
                    <label class="label">Saldo Awal (Rp)</label>
                    <input type="number" id="bankBalance" placeholder="0" class="input-field" style="font-weight:600;">
                </div>
            </div>

            <div style="display:flex; gap:12px; margin-top:28px;">
                <button onclick="closeModal('modalBank')" class="btn-ghost"
                    style="flex:1; justify-content:center;">Batal</button>
                <button onclick="submitBank()" class="btn-primary" style="flex:1; justify-content:center;">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                </button>
            </div>
        </div>
    </div>

    <style>
        /* Header Area */
        .rc-header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 30px;
        }

        .rc-title {
            font-size: 26px !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            letter-spacing: -0.02em !important;
            margin: 0 0 4px 0 !important;
        }

        .rc-subtitle {
            font-size: 14px !important;
            color: #64748b !important;
            margin: 0 !important;
        }

        .rc-btn-add {
            background: #0f172a !important;
            color: white !important;
            padding: 10px 18px !important;
            border-radius: 12px !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            border: none !important;
            cursor: pointer !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px !important;
            transition: all 0.2s ease !important;
        }

        .rc-btn-add:hover {
            background: #1e293b !important;
            transform: translateY(-1px);
        }

        /* Grid System */
        .rc-grid {
            display: grid !important;
            grid-template-columns: repeat(auto-fill, minmax(310px, 1fr)) !important;
            gap: 22px !important;
        }

        /* CARD MODEL BARU: DEBIT STYLE */
        .rc-card {
            position: relative !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 24px !important;
            padding: 24px !important;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03) !important;
            overflow: hidden !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
            min-height: 195px !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        /* Efek ambient di bawah kartu saat di-hover */
        .rc-card:hover {
            transform: translateY(-6px) !important;
            box-shadow: 0 20px 32px rgba(15, 23, 42, 0.07) !important;
            border-color: var(--bank-theme) !important;
        }

        /* Aksen Bias Warna Sesuai Pilihan */
        .rc-card-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: var(--bank-theme) !important;
        }

        /* Atas Kartu */
        .rc-card-header {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            margin-bottom: 12px !important;
            z-index: 2;
        }

        .rc-icon-box {
            width: 48px !important;
            height: 48px !important;
            border-radius: 14px !important;
            background: #f1f5f9 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 22px !important;
            border: 1px solid #e2e8f0 !important;
        }

        .rc-action-delete {
            background: #fff1f2 !important;
            color: #f43f5e !important;
            border: 1px solid #ffe4e6 !important;
            width: 30px !important;
            height: 30px !important;
            border-radius: 8px !important;
            cursor: pointer !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 12px !important;
            opacity: 0;
            transform: scale(0.95);
            transition: all 0.2s ease;
        }

        .rc-card:hover .rc-action-delete {
            opacity: 1;
            transform: scale(1);
        }

        .rc-action-delete:hover {
            background: #ffe4e6 !important;
            color: #e11d48 !important;
        }

        /* Tengah Kartu (Informasi Uang) */
        .rc-card-body {
            margin-top: auto !important;
            margin-bottom: auto !important;
            padding: 12px 0 !important;
            z-index: 2;
        }

        .rc-label-balance {
            font-size: 11px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            color: #94a3b8 !important;
            margin-bottom: 4px !important;
        }

        .rc-balance {
            font-size: 26px !important;
            font-weight: 800 !important;
            letter-spacing: -0.02em !important;
            color: #0f172a !important;
            line-height: 1 !important;
        }

        .rc-currency {
            font-size: 16px !important;
            font-weight: 600 !important;
            color: #94a3b8 !important;
            margin-right: 3px !important;
        }

        /* Bawah Kartu */
        .rc-card-footer {
            display: flex !important;
            align-items: flex-end !important;
            justify-content: space-between !important;
            padding-top: 14px !important;
            border-top: 1px solid #f1f5f9 !important;
            z-index: 2;
        }

        .rc-bank-name {
            font-size: 14px !important;
            font-weight: 700 !important;
            color: #334155 !important;
        }

        .rc-owner {
            font-size: 12px !important;
            color: #64748b !important;
            font-weight: 500 !important;
        }

        .rc-number-badge {
            font-size: 11px !important;
            font-family: monospace !important;
            color: white !important;
            background: var(--bank-theme) !important;
            padding: 3px 8px !important;
            border-radius: 6px !important;
            font-weight: 600 !important;
        }

        /* Status Transaksi Melayang */
        .rc-transaction-pill {
            position: absolute !important;
            top: 24px !important;
            right: 24px !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            color: #64748b !important;
            background: #f8fafc !important;
            padding: 4px 8px !important;
            border-radius: 20px !important;
            display: flex !important;
            align-items: center !important;
            gap: 4px !important;
            border: 1px solid #e2e8f0 !important;
            transition: opacity 0.2s;
        }

        .rc-card:hover .rc-transaction-pill {
            opacity: 0;
            /* Sembunyikan pencatat transaksi saat tombol hapus muncul */
        }

        /* Empty State */
        .rc-empty-box {
            grid-column: 1 / -1 !important;
            text-align: center !important;
            padding: 50px 20px !important;
            background: #f8fafc !important;
            border: 2px dashed #cbd5e1 !important;
            border-radius: 24px !important;
        }

        .rc-empty-emoji {
            font-size: 44px !important;
            margin-bottom: 12px !important;
        }

        .rc-empty-title {
            font-size: 18px !important;
            font-weight: 700 !important;
            color: #1e293b !important;
            margin-bottom: 6px !important;
        }

        .rc-empty-text {
            font-size: 14px !important;
            color: #64748b !important;
            max-width: 380px !important;
            margin: 0 auto 20px !important;
            line-height: 1.5 !important;
        }
    </style>

@endsection

@push('scripts')
    <script>
        function openModal(id) { $('#' + id).addClass('active'); }
        function closeModal(id) { $('#' + id).removeClass('active'); }

        $('#modalBank').on('click', function (e) {
            if ($(e.target).is('#modalBank')) closeModal('modalBank');
        });

        function pickBankColor(c) {
            $('#bankColor').val(c);
            $('.rc-color-dot').css({ border: '2px solid transparent', transform: 'scale(1)', boxShadow: 'none' });
            $('#bc-' + c.replace('#', '')).css({
                border: '2px solid white',
                outline: '3px solid ' + c,
                transform: 'scale(1.1)'
            });
        }

        pickBankColor('#10b981');

        async function submitBank() {
            const data = {
                name: $('#bankName').val(),
                account_name: $('#bankAccountName').val(),
                account_number: $('#bankAccountNumber').val(),
                icon: $('#bankIcon').val(),
                color: $('#bankColor').val(),
                initial_balance: $('#bankBalance').val() || 0,
            };

            if (!data.name || !data.account_name) {
                Toast.fire({ icon: 'warning', title: 'Nama dan pemilik wajib diisi!' });
                return;
            }

            const res = await $.ajax({
                url: '{{ route("banks.store") }}',
                method: 'POST',
                contentType: 'application/json',
                headers: { 'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content'), 'Accept': 'application/json' },
                data: JSON.stringify(data)
            });

            if (res.success) {
                closeModal('modalBank');
                Toast.fire({ icon: 'success', title: res.message });
                setTimeout(() => location.reload(), 1200);
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.message, background: '#fff', color: '#1a1a2e', confirmButtonColor: '#db2777' });
            }
        }

        function deleteBank(id) {
            deleteConfirm(`/banks/${id}`, () => {
                $('#bank-' + id).css('transform', 'scale(0.9)').fadeOut(300, function () { $(this).remove(); });
            });
        }
    </script>
@endpush