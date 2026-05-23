@extends('layouts.app')
@section('title', 'Rekening')

@section('content')

    <div class="flex items-center justify-between gap-4 mb-8 flex-wrap">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 mb-1">Rekening & Dompet</h1>
            <p class="text-sm text-slate-500">Kelola semua pos dan tempat penyimpanan uang kalian</p>
        </div>
        <button onclick="openModal('modalBank')" class="btn-primary whitespace-nowrap">
            <i class="fa-solid fa-plus"></i> Tambah Rekening
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($banks as $bank)
            <div id="bank-{{ $bank->id }}" class="relative bg-white border border-slate-200 rounded-3xl p-6 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 overflow-hidden group">

                <div class="absolute top-0 left-0 right-0 h-1.5 rounded-t-3xl" style="background: {{ $bank->color }};"></div>

                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center text-2xl">
                        {{ $bank->icon }}
                    </div>

                    @if($bank->transactions_count === 0)
                        <button onclick="deleteBank({{ $bank->id }})" class="w-8 h-8 rounded-lg bg-rose-50 border border-rose-100 text-rose-500 hover:bg-rose-100 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    @endif
                </div>

                <div class="my-4">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Saldo Saat Ini</div>
                    <div class="text-3xl font-extrabold text-slate-900">
                        <span class="text-base font-semibold text-slate-400 mr-1">Rp</span>{{ number_format($bank->current_balance, 0, ',', '.') }}
                    </div>
                </div>

                <div class="flex items-end justify-between pt-3.5 border-t border-slate-100">
                    <div>
                        <div class="text-sm font-bold text-slate-700">{{ $bank->name }}</div>
                        <div class="text-xs text-slate-500 font-medium">{{ $bank->account_name }}</div>
                    </div>

                    @if($bank->account_number)
                        <div class="text-xs font-semibold text-white px-2 py-1 rounded" style="background: {{ $bank->color }};">
                            •••• {{ substr($bank->account_number, -4) }}
                        </div>
                    @endif
                </div>

                <div class="absolute top-6 right-6 text-xs font-semibold text-slate-500 bg-slate-50 px-2 py-1 rounded-full border border-slate-200 flex items-center gap-1.5 group-hover:opacity-0 transition-opacity">
                    <i class="fa-solid fa-clock-rotate-left"></i> {{ $bank->transactions_count }}
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16 px-5 bg-slate-50 border-2 border-dashed border-slate-300 rounded-3xl">
                <div class="text-5xl mb-3">🏦</div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Belum Ada Rekening Aktif</h3>
                <p class="text-sm text-slate-500 max-w-sm mx-auto mb-5 leading-relaxed">
                    Dompet digital atau rekening bank kamu belum terdaftar. Yuk masukkan biar bisa langsung hitung pengeluaran!
                </p>
                <button onclick="openModal('modalBank')" class="btn-primary mx-auto">
                    <i class="fa-solid fa-plus"></i> Hubungkan Sekarang
                </button>
            </div>
        @endforelse
    </div>

    <div id="modalBank" class="modal-overlay" onclick="if(event.target === this) closeModal('modalBank')">
        <div class="w-full max-w-md bg-white rounded-3xl p-7 shadow-2xl m-4 max-h-[90vh] overflow-y-auto md:m-0">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-slate-900">Tambah Rekening</h2>
                <button onclick="closeModal('modalBank')" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-slate-600 text-xl rounded-lg hover:bg-slate-100 transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-[80px_1fr] gap-3">
                    <div>
                        <label class="label">Icon</label>
                        <input type="text" id="bankIcon" value="🏦" maxlength="4" class="input-field text-2xl text-center p-2.5">
                    </div>
                    <div>
                        <label class="label">Nama Bank / E-Wallet</label>
                        <input type="text" id="bankName" placeholder="BCA, Mandiri, GoPay, OVO..." class="input-field">
                    </div>
                </div>

                <div>
                    <label class="label mb-2 block">Pilih Warna Tema Kartu</label>
                    <div class="grid grid-cols-6 gap-2">
                        @foreach(['#10b981', '#3b82f6', '#f97316', '#8b5cf6', '#f43f5e', '#06b6d4', '#d946ef', '#f59e0b', '#ec4899', '#14b8a6', '#6366f1', '#6b7280'] as $c)
                            <button onclick="pickBankColor('{{ $c }}')" id="bc-{{ ltrim($c, '#') }}" class="w-full h-9 rounded-lg border-2 border-transparent hover:scale-105 transition-transform" style="background: {{ $c }};"></button>
                        @endforeach
                    </div>
                    <input type="hidden" id="bankColor" value="#10b981">
                </div>

                <div>
                    <label class="label">Nama Pemilik</label>
                    <input type="text" id="bankAccountName" placeholder="Nama sesuai aplikasi/buku tabungan" class="input-field">
                </div>

                <div>
                    <label class="label">Nomor Rekening <span class="text-slate-400 font-normal">(Opsional)</span></label>
                    <input type="text" id="bankAccountNumber" placeholder="Contoh: 123456789" class="input-field">
                </div>

                <div>
                    <label class="label">Saldo Awal (Rp)</label>
                    <input type="number" id="bankBalance" placeholder="0" class="input-field font-semibold">
                </div>
            </div>

            <div class="flex gap-3 mt-7">
                <button onclick="closeModal('modalBank')" class="btn-ghost flex-1 justify-center">Batal</button>
                <button onclick="submitBank()" class="btn-primary flex-1 justify-center">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                </button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function openModal(id) { 
            $('#' + id).addClass('active'); 
            if (window.innerWidth <= 768) {
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(id) { 
            $('#' + id).removeClass('active'); 
            document.body.style.overflow = '';
        }

        function pickBankColor(c) {
            $('#bankColor').val(c);
            $('.grid button[id^="bc-"]').css({ 
                border: '2px solid transparent', 
                transform: 'scale(1)',
                outline: 'none'
            });
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