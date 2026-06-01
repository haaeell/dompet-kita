@extends('layouts.app')
@section('title', 'Aset & Net Worth')

@section('content')
    <style>
        .asset-hero {
            border-radius: 8px;
            padding: 20px;
            color: #fff;
            background:
                radial-gradient(circle at 84% 14%, rgba(255, 255, 255, .24) 0 14%, transparent 28%),
                linear-gradient(135deg, #0f766e, #2563eb 48%, #db2777);
            box-shadow: 0 18px 42px rgba(37, 99, 235, .16);
        }

        .asset-card {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #fff;
            padding: 16px;
        }

        .asset-type-pill {
            border-radius: 999px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 800;
            color: #475569;
        }
    </style>

    <div class="flex items-center justify-between gap-3 mb-6 flex-wrap">
        <div>
            <h1 class="page-title">Aset & Net Worth</h1>
            <p class="page-subtitle">Lacak nilai aset kalian dan lihat kekayaan bersih secara utuh.</p>
        </div>
        <form action="{{ route('assets.index') }}" method="GET">
            <select name="user_id" onchange="this.form.submit()" class="input-field min-w-[190px]">
                <option value="">Semua pemilik</option>
                @foreach($members as $member)
                    <option value="{{ $member->id }}" {{ (string) $selectedUserId === (string) $member->id ? 'selected' : '' }}>
                        {{ $member->id === auth()->id() ? 'Saya (' . $member->name . ')' : $member->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    @if(session('success'))
        <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <section class="asset-hero mb-5">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <div class="text-xs font-extrabold uppercase tracking-[.14em] opacity-80">Net Worth</div>
                <div class="mt-2 text-4xl font-black">Rp {{ number_format($netWorth, 0, ',', '.') }}</div>
                <div class="mt-2 text-sm opacity-85">Saldo + aset + piutang - hutang aktif.</div>
            </div>
            <div class="rounded-full bg-white/15 px-4 py-2 text-sm font-extrabold">
                {{ $assets->count() }} aset aktif
            </div>
        </div>
    </section>

    <div class="grid gap-4 md:grid-cols-4 mb-5">
        <div class="asset-card">
            <div class="text-xs font-bold uppercase tracking-wide text-slate-400">Saldo Rekening</div>
            <div class="mt-2 text-xl font-extrabold text-blue-700">Rp {{ number_format($cashTotal, 0, ',', '.') }}</div>
        </div>
        <div class="asset-card">
            <div class="text-xs font-bold uppercase tracking-wide text-slate-400">Nilai Aset</div>
            <div class="mt-2 text-xl font-extrabold text-teal-700">Rp {{ number_format($assetTotal, 0, ',', '.') }}</div>
        </div>
        <div class="asset-card">
            <div class="text-xs font-bold uppercase tracking-wide text-slate-400">Piutang</div>
            <div class="mt-2 text-xl font-extrabold text-emerald-700">Rp {{ number_format($piutangTotal, 0, ',', '.') }}</div>
        </div>
        <div class="asset-card">
            <div class="text-xs font-bold uppercase tracking-wide text-slate-400">Hutang</div>
            <div class="mt-2 text-xl font-extrabold text-rose-600">Rp {{ number_format($hutangTotal, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="grid gap-5 xl:grid-cols-[380px_1fr]">
        <section class="asset-card h-fit">
            <h2 class="text-lg font-extrabold text-slate-900 mb-4">Tambah Aset</h2>
            <form action="{{ route('assets.store') }}" method="POST" class="grid gap-4">
                @csrf
                <div>
                    <label class="label">Nama Aset</label>
                    <input name="name" class="input-field" required placeholder="Motor, emas, laptop, deposito...">
                </div>
                <div>
                    <label class="label">Jenis Aset</label>
                    <select name="type" class="input-field" required>
                        <option value="kendaraan">Kendaraan</option>
                        <option value="properti">Properti</option>
                        <option value="emas">Emas / Logam Mulia</option>
                        <option value="investasi">Investasi</option>
                        <option value="elektronik">Elektronik</option>
                        <option value="tabungan">Tabungan / Deposito</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="label">Pemilik</label>
                    <select name="user_id" class="input-field">
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" {{ $member->id === auth()->id() ? 'selected' : '' }}>{{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="label">Harga Beli</label>
                        <input type="number" min="0" step="1000" name="purchase_value" class="input-field" required placeholder="0">
                    </div>
                    <div>
                        <label class="label">Nilai Sekarang</label>
                        <input type="number" min="0" step="1000" name="current_value" class="input-field" placeholder="Kosongkan jika sama">
                    </div>
                </div>
                <div>
                    <label class="label">Tanggal Perolehan</label>
                    <input type="text" name="acquired_at" class="input-field js-date-picker" data-format="Y-m-d" data-alt-format="j F Y">
                </div>
                <div>
                    <label class="label">Catatan</label>
                    <textarea name="notes" class="input-field" rows="3" placeholder="Nomor sertifikat, lokasi, kondisi, dll..."></textarea>
                </div>
                <button class="btn-primary justify-center">
                    <i class="fa-solid fa-gem"></i> Simpan Aset
                </button>
            </form>
        </section>

        <div class="grid gap-5">
            @if($assetByType->isNotEmpty())
                <section class="asset-card">
                    <h2 class="text-lg font-extrabold text-slate-900 mb-3">Breakdown Jenis Aset</h2>
                    <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($assetByType as $row)
                            <div class="rounded-2xl bg-slate-50 p-3">
                                <div class="asset-type-pill inline-flex">{{ ucfirst($row['type']) }}</div>
                                <div class="mt-2 text-lg font-extrabold text-slate-900">Rp {{ number_format($row['total'], 0, ',', '.') }}</div>
                                <div class="text-xs text-slate-500">{{ $row['count'] }} aset</div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="grid gap-3">
                @forelse($assets as $asset)
                    <article class="asset-card">
                        <div class="flex items-start justify-between gap-4 flex-wrap">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h2 class="text-lg font-extrabold text-slate-900 m-0">{{ $asset->name }}</h2>
                                    <span class="asset-type-pill">{{ ucfirst($asset->type) }}</span>
                                </div>
                                <div class="mt-2 text-2xl font-extrabold text-teal-700">Rp {{ number_format($asset->current_value, 0, ',', '.') }}</div>
                                <div class="mt-1 text-sm text-slate-500">
                                    Harga beli Rp {{ number_format($asset->purchase_value, 0, ',', '.') }}
                                    @if($asset->purchase_value > 0)
                                        @php $gain = $asset->current_value - $asset->purchase_value; @endphp
                                        <span class="{{ $gain >= 0 ? 'text-emerald-600' : 'text-rose-600' }} font-bold">
                                            ({{ $gain >= 0 ? '+' : '-' }}Rp {{ number_format(abs($gain), 0, ',', '.') }})
                                        </span>
                                    @endif
                                </div>
                                <div class="mt-2 text-xs font-semibold text-slate-500">
                                    Pemilik: {{ $asset->user?->name ?? 'Belum ditentukan' }}
                                    @if($asset->acquired_at)
                                        <span class="mx-1">•</span> Sejak {{ $asset->acquired_at->isoFormat('D MMM Y') }}
                                    @endif
                                </div>
                                @if($asset->notes)
                                    <p class="mt-3 text-sm text-slate-500">{{ $asset->notes }}</p>
                                @endif
                            </div>

                            <details class="min-w-[260px] rounded-2xl border border-slate-200 bg-slate-50 p-3">
                                <summary class="cursor-pointer text-sm font-bold text-pink-700">Update Nilai</summary>
                                <form action="{{ route('assets.update', $asset) }}" method="POST" class="mt-3 grid gap-3">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="name" value="{{ $asset->name }}">
                                    <input type="hidden" name="type" value="{{ $asset->type }}">
                                    <input type="hidden" name="user_id" value="{{ $asset->user_id }}">
                                    <input type="hidden" name="purchase_value" value="{{ $asset->purchase_value }}">
                                    <input type="hidden" name="acquired_at" value="{{ optional($asset->acquired_at)->format('Y-m-d') }}">
                                    <input type="hidden" name="notes" value="{{ $asset->notes }}">
                                    <input type="number" min="0" step="1000" name="current_value" class="input-field" value="{{ (int) $asset->current_value }}" required>
                                    <button class="btn-primary justify-center">Simpan Nilai</button>
                                </form>
                                <form action="{{ route('assets.destroy', $asset) }}" method="POST" class="mt-2" onsubmit="return confirm('Arsipkan aset ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-ghost justify-center text-rose-600 w-full">Arsipkan</button>
                                </form>
                            </details>
                        </div>
                    </article>
                @empty
                    <div class="asset-card text-center py-10">
                        <div class="mx-auto mb-3 grid h-12 w-12 place-items-center rounded-full bg-blue-50 text-blue-700">
                            <i class="fa-solid fa-gem"></i>
                        </div>
                        <div class="font-extrabold text-slate-900">Belum ada aset</div>
                        <p class="mt-1 text-sm text-slate-500">Tambahkan kendaraan, emas, investasi, properti, atau barang bernilai lain.</p>
                    </div>
                @endforelse
            </section>
        </div>
    </div>
@endsection
