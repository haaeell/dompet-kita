@extends('layouts.app')
@section('title', 'Target Tabungan')
@section('content')
    <div class="page-header"
        style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 class="page-title">Target Tabungan</h1>
            <p class="page-subtitle">Wujudkan impian kalian bersama 💑</p>
        </div>
        <button onclick="openModal('modalTarget')" class="btn-primary">
            <i class="fa-solid fa-plus"></i> Buat Target
        </button>
    </div>

    @php
        $active = $targets->where('status', 'active');
        $done = $targets->where('status', 'completed');
    @endphp

    @if($targets->isEmpty())
        <div class="card" style="text-align:center; padding:64px 24px;">
            <div style="font-size:52px; margin-bottom:14px;">🎯</div>
            <h2
                style="font-family:'Playfair Display',serif; font-size:22px; font-weight:700; color:var(--text-primary); margin-bottom:8px;">
                Belum Ada Target</h2>
            <p style="font-size:14px; color:var(--text-secondary); margin-bottom:22px;">Buat target tabungan pertama kalian!</p>
            <button onclick="openModal('modalTarget')" class="btn-primary">
                <i class="fa-solid fa-plus"></i> Buat Target Sekarang
            </button>
        </div>
    @endif

    @if($active->count())
        <div style="margin-bottom:28px;">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:16px;">
                <span
                    style="width:28px; height:28px; border-radius:8px; background:var(--pink-light); display:flex; align-items:center; justify-content:center; color:var(--pink-dark);">
                    <i class="fa-solid fa-bullseye" style="font-size:12px;"></i>
                </span>
                <span
                    style="font-size:11px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:var(--text-secondary);">Target
                    Aktif</span>
                <span style="font-size:12px; color:var(--text-secondary);">{{ $active->count() }} target</span>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(290px, 1fr)); gap:18px;">
                @foreach($active as $target)
                    <div class="card" id="target-{{ $target->id }}"
                        style="padding:22px; position:relative; background: #fff; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                        <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:16px;">
                            <div
                                style="width:52px; height:52px; border-radius:16px; display:flex; align-items:center; justify-content:center; font-size:26px; background:{{ $target->color }}18; border:2px solid {{ $target->color }}30; flex-shrink:0;">
                                {{ $target->icon }}
                            </div>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <span
                                    style="font-size:15px; font-weight:700; color:{{ $target->color }};">{{ $target->progress_percent }}%</span>
                                <button onclick="cancelTarget({{ $target->id }})"
                                    style="background:none; border:1px solid #fecdd3; color:#f43f5e; width:28px; height:28px; border-radius:8px; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:12px; transition:all 0.15s;"
                                    onmouseover="this.style.background='#fff1f2'" onmouseout="this.style.background='none'"
                                    title="Batalkan target">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        </div>

                        <h3
                            style="font-family:'Playfair Display',serif; font-size:17px; font-weight:700; color:var(--text-primary); margin-bottom:4px;">
                            {{ $target->name }}
                        </h3>

                        @if($target->deadline)
                            <p style="font-size:12px; color:var(--text-secondary); margin-bottom:14px;">
                                <i class="fa-regular fa-calendar" style="margin-right:4px;"></i> Target:
                                {{ $target->deadline->isoFormat('D MMM Y') }}
                            </p>
                        @else
                            <div style="margin-bottom:14px;"></div>
                        @endif

                        {{-- Progress Bar --}}
                        <div style="height:8px; border-radius:99px; background:#f1f5f9; margin-bottom:8px; overflow:hidden;">
                            <div
                                style="height:8px; border-radius:99px; width:{{ $target->progress_percent }}%; background:{{ $target->color }}; transition:width 1s;">
                            </div>
                        </div>

                        <div
                            style="display:flex; justify-content:space-between; font-size:12px; color:var(--text-secondary); margin-bottom:10px;">
                            <span>Rp {{ number_format($target->current_amount, 0, ',', '.') }}</span>
                            <span>Rp {{ number_format($target->target_amount, 0, ',', '.') }}</span>
                        </div>

                        <div style="font-size:12px; color:var(--text-secondary); margin-bottom:14px;">
                            Sisa: <span style="font-weight:700; color:var(--text-primary);">Rp
                                {{ number_format($target->remaining, 0, ',', '.') }}</span>
                        </div>

                        {{-- 1. Ringkasan Akumulasi Tabungan Per-User --}}
                        @if($target->savings->groupBy('user_id')->count())
                            <div
                                style="padding:10px 12px; background:#f8fafc; border: 1px solid #e2e8f0; border-radius:10px; margin-bottom:10px; display:flex; flex-direction:column; gap:5px;">
                                @foreach($target->savings->groupBy('user_id') as $userId => $savings)
                                    <div style="display:flex; align-items:center; justify-content:space-between; font-size:12px;">
                                        <span style="display:inline-flex; align-items:center; gap:6px; color:var(--text-secondary);">
                                            @if($savings->first()->user->profile_photo_url)
                                                <img src="{{ $savings->first()->user->profile_photo_url }}"
                                                    alt="{{ $savings->first()->user->name }}"
                                                    style="width:18px; height:18px; border-radius:50%; object-fit:cover;" />
                                            @else
                                                <span
                                                    style="display:inline-flex; width:18px; height:18px; border-radius:50%; background:#f3f4f6; color:#475569; align-items:center; justify-content:center; font-size:10px;">{{ $savings->first()->user->avatar ?? '👤' }}</span>
                                            @endif
                                            {{ $savings->first()->user->name }}
                                        </span>
                                        <span style="font-weight:600; color:var(--text-primary);">Rp
                                            {{ number_format($savings->sum('amount'), 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- 2. SEKSI BARU: Riwayat Log Menabung (Collapsible) --}}
                        <div style="margin-bottom: 14px;">
                            <button onclick="toggleHistory({{ $target->id }})"
                                style="width: 100%; background: #f1f5f9; border: none; padding: 6px 10px; border-radius: 8px; color: #475569; font-size: 11px; font-weight: 600; display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                                <span><i class="fa-solid fa-history" style="margin-right: 4px;"></i> Lihat Riwayat Transaksi</span>
                                <i id="chevron-{{ $target->id }}" class="fa-solid fa-chevron-down transition-transform"></i>
                            </button>

                            <div id="history-{{ $target->id }}"
                                style="display: none; max-height: 150px; overflow-y: auto; margin-top: 8px; padding: 4px; border-left: 2px solid #e2e8f0; gap: 8px; flex-direction: column;">
                                @forelse($target->savings as $saving)
                                    <div
                                        style="display: flex; flex-direction: column; background: #fafafa; padding: 8px; border-radius: 6px; font-size: 11px; border: 1px solid #f1f5f9;">
                                        <div style="display: flex; justify-content: space-between; font-weight: 600; color: #1a1a2e;">
                                            <span>{{ $saving->user->name }}</span>
                                            <span style="color: #16a34a;">+Rp {{ number_format($saving->amount, 0, ',', '.') }}</span>
                                        </div>
                                        <div
                                            style="display: flex; justify-content: space-between; color: #94a3b8; margin-top: 2px; font-size: 10px;">
                                            <span>{{ $saving->notes ?? 'Tanpa catatan' }}</span>
                                            <span>{{ $saving->date->format('d/m/y') }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <p style="text-align: center; color: #94a3b8; font-size: 11px; py-2">Belum ada riwayat menabung.</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- Tombol Tambah Tabungan --}}
                        <button onclick="openSaving({{ $target->id }}, '{{ addslashes($target->name) }}')" class="btn-primary"
                            style="width:100%; justify-content:center; padding:11px;">
                            <i class="fa-solid fa-piggy-bank"></i> Tambah Tabungan
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($done->count())
        <div>
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:16px;">
                <span
                    style="width:28px; height:28px; border-radius:8px; background:#f0fdf4; display:flex; align-items:center; justify-content:center; color:#16a34a;">
                    <i class="fa-solid fa-trophy" style="font-size:12px;"></i>
                </span>
                <span
                    style="font-size:11px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:#16a34a;">Target
                    Tercapai</span>
                <span style="font-size:12px; color:var(--text-secondary);">{{ $done->count() }} target</span>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(240px, 1fr)); gap:14px;">
                @foreach($done as $target)
                    <div class="card" style="padding:18px; opacity:0.8; display:flex; align-items:center; gap:14px;">
                        <div
                            style="width:46px; height:46px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:22px; background:{{ $target->color }}15; flex-shrink:0;">
                            {{ $target->icon }}
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div style="font-size:14px; font-weight:700; color:var(--text-primary); margin-bottom:3px;">
                                {{ $target->name }}
                            </div>
                            <div style="font-size:12px; color:#16a34a; font-weight:600;">
                                <i class="fa-solid fa-circle-check" style="margin-right:3px;"></i> Rp
                                {{ number_format($target->target_amount, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div id="modalTarget" class="modal-overlay">
        <div class="modal-box">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:22px;">
                <h2 class="modal-title" style="margin-bottom:0;">Buat Target Baru</h2>
                <button onclick="closeModal('modalTarget')"
                    style="background:none; border:none; cursor:pointer; color:var(--text-secondary); padding:6px; border-radius:8px; font-size:16px; line-height:1;"
                    onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div style="display:flex; flex-direction:column; gap:16px;">
                <div style="display:grid; grid-template-columns:80px 1fr; gap:12px;">
                    <div>
                        <label class="label">Icon</label>
                        <input type="text" id="tIcon" value="🎯" class="input-field" maxlength="4"
                            style="font-size:24px; text-align:center; padding:10px;">
                    </div>
                    <div>
                        <label class="label">Nama Target</label>
                        <input type="text" id="tName" placeholder="Liburan ke Bali..." class="input-field">
                    </div>
                </div>

                <div>
                    <label class="label">Target Jumlah (Rp)</label>
                    <input type="text" id="tAmount" placeholder="5.000.000" class="input-field rupiah"
                        style="font-size:18px; font-family:'Playfair Display',serif; font-weight:700;">
                </div>

                <div>
                    <label class="label">Deadline <span
                            style="font-weight:400; text-transform:none; letter-spacing:0;">(opsional)</span></label>
                    <input type="date" id="tDeadline" class="input-field">
                </div>

                <div>
                    <label class="label">Warna</label>
                    <div style="display:flex; flex-wrap:wrap; gap:8px; margin-top:2px;">
                        @foreach(['#f59e0b', '#10b981', '#3b82f6', '#d946ef', '#f43f5e', '#8b5cf6', '#06b6d4', '#f97316', '#ec4899', '#14b8a6'] as $color)
                            <button onclick="selectColor('{{ $color }}')" id="color-{{ ltrim($color, '#') }}"
                                style="width:32px; height:32px; border-radius:10px; border:2px solid transparent; background:{{ $color }}; cursor:pointer; transition:all 0.15s;"></button>
                        @endforeach
                    </div>
                    <input type="hidden" id="tColor" value="#f59e0b">
                </div>
            </div>

            <div style="display:flex; gap:10px; margin-top:22px;">
                <button onclick="closeModal('modalTarget')" class="btn-ghost"
                    style="flex:1; justify-content:center;">Batal</button>
                <button onclick="submitTarget()" class="btn-primary" style="flex:1; justify-content:center;">
                    <i class="fa-solid fa-bullseye"></i> Buat Target
                </button>
            </div>
        </div>
    </div>

    <div id="modalSaving" class="modal-overlay">
        <div class="modal-box">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:6px;">
                <h2 class="modal-title" style="margin-bottom:0;">Tambah Tabungan</h2>
                <button onclick="closeModal('modalSaving')"
                    style="background:none; border:none; cursor:pointer; color:var(--text-secondary); padding:6px; border-radius:8px; font-size:16px; line-height:1;"
                    onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <p id="savingTargetName" style="font-size:13px; color:var(--pink-dark); font-weight:600; margin-bottom:20px;">
            </p>
            <input type="hidden" id="savingTargetId">

            <div style="display:flex; flex-direction:column; gap:16px;">
                {{-- Dropdown Pilihan Sumber Bank --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label class="label">Dari Rekening (Berkurang)</label>
                        <select id="savingSourceBankId" class="input-field"
                            style="background: #fff; border: 1px solid #e2e8f0; color: #1a1a2e;">
                            <option value="">-- Pilih Sumber --</option>
                            @foreach($banks as $bank)
                                <option value="{{ $bank->id }}">{{ $bank->name }} ({{ $bank->account_name }}) (Rp
                                    {{ number_format($bank->current_balance ?? 0, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Ke Rekening (Bertambah)</label>
                        <select id="savingTargetBankId" class="input-field"
                            style="background: #fff; border: 1px solid #e2e8f0; color: #1a1a2e;">
                            <option value="">-- Pilih Tujuan --</option>
                            @foreach($banks as $bank)
                                <option value="{{ $bank->id }}">{{ $bank->name }} ({{ $bank->account_name }}) (Rp
                                    {{ number_format($bank->current_balance ?? 0, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="label">Jumlah (Rp)</label>
                    <input type="text" id="savingAmount" placeholder="100.000" class="input-field rupiah"
                        style="font-size:22px; font-family:'Playfair Display',serif; font-weight:700;">
                </div>
                <div>
                    <label class="label">Tanggal</label>
                    <input type="date" id="savingDate" class="input-field" value="{{ now()->format('Y-m-d') }}">
                </div>
                <div>
                    <label class="label">Catatan</label>
                    <input type="text" id="savingNotes" placeholder="Opsional..." class="input-field">
                </div>
            </div>

            <div style="display:flex; gap:10px; margin-top:22px;">
                <button onclick="closeModal('modalSaving')" class="btn-ghost"
                    style="flex:1; justify-content:center;">Batal</button>
                <button onclick="submitSaving()" class="btn-primary" style="flex:1; justify-content:center;">
                    <i class="fa-solid fa-piggy-bank"></i> Tabung!
                </button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function openModal(id) { $('#' + id).addClass('active'); }
        function closeModal(id) { $('#' + id).removeClass('active'); }

        $('.modal-overlay').on('click', function (e) {
            if ($(e.target).hasClass('modal-overlay')) closeModal($(e.target).attr('id'));
        });

        function selectColor(color) {
            $('#tColor').val(color);
            $('[id^="color-"]').css({ outline: 'none', transform: 'scale(1)' });
            $('#color-' + color.replace('#', '')).css({ outline: '3px solid ' + color, outlineOffset: '2px', transform: 'scale(1.15)' });
        }

        selectColor('#f59e0b');

        async function submitTarget() {
            const data = {
                name: $('#tName').val(),
                icon: $('#tIcon').val(),
                target_amount: $('#tAmount').val(),
                deadline: $('#tDeadline').val() || null,
                color: $('#tColor').val(),
            };

            if (!data.name || !data.target_amount) {
                Toast.fire({ icon: 'warning', title: 'Nama dan jumlah wajib diisi!' });
                return;
            }

            const res = await $.ajax({
                url: '{{ route("targets.store") }}',
                method: 'POST',
                contentType: 'application/json',
                headers: { 'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content'), 'Accept': 'application/json' },
                data: JSON.stringify(data)
            });

            if (res.success) {
                closeModal('modalTarget');
                Toast.fire({ icon: 'success', title: res.message });
                setTimeout(() => location.reload(), 1200);
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.message, background: '#fff', color: '#1a1a2e', confirmButtonColor: '#db2777' });
            }
        }

        function openSaving(id, name) {
            $('#savingTargetId').val(id);
            $('#savingTargetName').text('🎯 ' + name);
            openModal('modalSaving');
        }

        async function submitSaving() {
            const id = $('#savingTargetId').val();
            const data = {
                source_bank_id: $('#savingSourceBankId').val(), // Bank Asal
                target_bank_id: $('#savingTargetBankId').val(), // Bank Tujuan
                amount: $('#savingAmount').val(),
                date: $('#savingDate').val(),
                notes: $('#savingNotes').val(),
            };

            if (!data.source_bank_id || !data.target_bank_id) {
                Toast.fire({ icon: 'warning', title: 'Pilih rekening asal dan tujuan!' });
                return;
            }
            if (data.source_bank_id === data.target_bank_id) {
                Toast.fire({ icon: 'warning', title: 'Rekening asal dan tujuan tidak boleh sama!' });
                return;
            }
            if (!data.amount) {
                Toast.fire({ icon: 'warning', title: 'Masukkan jumlah!' });
                return;
            }

            // Eksekusi Ajax (URL tetap sama menuju controller)
            const res = await $.ajax({
                url: `/targets/${id}/saving`,
                method: 'POST',
                contentType: 'application/json',
                headers: { 'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content'), 'Accept': 'application/json' },
                data: JSON.stringify(data)
            });

            if (res.success) {
                closeModal('modalSaving');
                if (res.completed) {
                    Swal.fire({ icon: 'success', title: '🎉 Target Tercapai!', text: 'Selamat! Kalian berhasil mencapai target!', confirmButtonColor: '#db2777' }).then(() => location.reload());
                } else {
                    Toast.fire({ icon: 'success', title: res.message });
                    setTimeout(() => location.reload(), 1200);
                }
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.message, confirmButtonColor: '#db2777' });
            }
        }

        function cancelTarget(id) {
            deleteConfirm(`/targets/${id}`, () => {
                $('#target-' + id).fadeOut(300, function () { $(this).remove(); });
            });
        }

        function toggleHistory(targetId) {
            const historyContainer = $('#history-' + targetId);
            const chevron = $('#chevron-' + targetId);

            // Cek status display saat ini
            if (historyContainer.is(':hidden')) {
                historyContainer.css('display', 'flex').hide().slideDown(200);
                chevron.css('transform', 'rotate(180deg)');
            } else {
                historyContainer.slideUp(200);
                chevron.css('transform', 'rotate(0deg)');
            }
        }
    </script>
@endpush