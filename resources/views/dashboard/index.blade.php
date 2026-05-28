@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    {{-- Page Header --}}
    <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
        <div class="flex-1 min-w-[250px]">
            <h1 class="page-title mb-1">Halo, {{ auth()->user()->name }}! 👋</h1>
            <p class="page-subtitle m-0">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
        </div>

        {{-- Filter Pasangan --}}
        <form action="{{ url()->current() }}" method="GET" id="filterForm" class="m-0">
            <select name="user_id" onchange="document.getElementById('filterForm').submit();"
                class="input-field py-2 px-3 rounded-xl min-w-[160px] cursor-pointer h-auto text-[13px] font-semibold">
                <option value="">👨‍👩‍ Semua Transaksi</option>
                @foreach($coupleMembers as $member)
                    <option value="{{ $member->id }}" {{ $selectedUserId == $member->id ? 'selected' : '' }}>
                        {{ $member->avatar ?? '👤' }}
                        {{ $member->id == auth()->id() ? 'Saya (' . $member->name . ')' : $member->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Summary Cards --}}
    @php $balance = $monthlyIncome - $monthlyExpense; @endphp
    <div class="grid grid-cols-[repeat(auto-fit,minmax(240px,1fr))] gap-4 mb-7">

        <div class="card border-l-4 border-pink-400 p-4">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold tracking-widest uppercase text-[var(--text-secondary)]">Saldo
                    Bersih</span>
                <span
                    class="w-[34px] h-[34px] rounded-[10px] bg-[var(--pink-light)] flex items-center justify-center text-[var(--pink-dark)] shrink-0">
                    <i class="fa-solid fa-wallet text-sm"></i>
                </span>
            </div>
            <div class="text-2xl font-bold mb-1 break-all {{ $balance >= 0 ? 'text-green-600' : 'text-rose-600' }}">
                Rp {{ number_format($balance, 0, ',', '.') }}
            </div>
            <div class="text-xs text-[var(--text-secondary)]">{{ $balance >= 0 ? '+' : '-' }} Bulan
                {{ now()->isoFormat('MMMM Y') }}
            </div>
        </div>

        <div class="card border-l-4 border-blue-500 p-4">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold tracking-widest uppercase text-[var(--text-secondary)]">
                    {{ $selectedUserId ? 'Total Kekayaan Pribadi' : 'Total Kekayaan' }}
                </span>
                <span
                    class="w-[34px] h-[34px] rounded-[10px] bg-blue-50 flex items-center justify-center text-blue-700 shrink-0">
                    <i class="fa-solid fa-coins text-sm"></i>
                </span>
            </div>
            <div class="text-2xl font-bold text-blue-700 mb-1 break-all">
                Rp {{ number_format($totalWealth, 0, ',', '.') }}
            </div>
            <div class="text-xs text-[var(--text-secondary)]">
                @if($selectedUserId)
                    Saldo rekening aktif milik {{ $coupleMembers->firstWhere('id', $selectedUserId)->name ?? 'pasangan' }}
                @else
                    Saldo seluruh rekening aktif
                @endif
            </div>
            <div class="text-xs text-blue-700 mt-2 font-medium">
                Jika ditambah piutang: Rp {{ number_format($totalWealthIncludingPiutang, 0, ',', '.') }}
            </div>
        </div>

        <div class="card border-l-4 border-rose-500 p-4">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold tracking-widest uppercase text-[var(--text-secondary)]">Hutang
                    Belum Dibayar</span>
                <span
                    class="w-[34px] h-[34px] rounded-[10px] bg-rose-50 flex items-center justify-center text-rose-600 shrink-0">
                    <i class="fa-solid fa-hand-holding-dollar text-sm"></i>
                </span>
            </div>
            <div class="text-2xl font-bold text-rose-600 mb-1 break-all">
                Rp {{ number_format($outstandingHutang, 0, ',', '.') }}
            </div>
            <div class="text-xs text-[var(--text-secondary)]">
                {{ $selectedUserId ? 'Total hutang orang yang dipilih yang harus dibayar' : 'Total hutang yang harus dibayar' }}
            </div>
        </div>

        <div class="card border-l-4 border-green-500 p-4">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold tracking-widest uppercase text-[var(--text-secondary)]">Piutang
                    Belum Kembali</span>
                <span
                    class="w-[34px] h-[34px] rounded-[10px] bg-green-50 flex items-center justify-center text-green-700 shrink-0">
                    <i class="fa-solid fa-hand-holding-hand text-sm"></i>
                </span>
            </div>
            <div class="text-2xl font-bold text-green-700 mb-1 break-all">
                Rp {{ number_format($outstandingPiutang, 0, ',', '.') }}
            </div>
            <div class="text-xs text-[var(--text-secondary)]">
                {{ $selectedUserId ? 'Total piutang orang yang dipilih yang belum kembali' : 'Total piutang yang belum kembali' }}
            </div>
        </div>
    </div>

    {{-- Mid Row Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-5 mb-5">

        {{-- Recent Transactions --}}
        <div class="card p-4">
            <div class="flex items-center justify-between mb-5 flex-wrap gap-2">
                <h3 class="text-lg font-bold text-[var(--text-primary)] m-0">Transaksi Terbaru</h3>
                <a href="{{ route('transactions.index') }}"
                    class="text-[13px] text-[var(--pink-dark)] font-semibold no-underline">Lihat Semua →</a>
            </div>
            <div class="flex flex-col gap-1">
                @forelse($transactions as $tx)
                    <div class="flex items-center gap-3.5 px-2.5 py-2.5 rounded-xl transition-colors hover:bg-gray-50
                                                            max-[576px]:flex-wrap max-[576px]:gap-2.5">
                        <div class="w-[42px] h-[42px] rounded-xl flex items-center justify-center text-lg shrink-0"
                            style="background:{{ $tx->category->color }}18;">
                            {{ $tx->category->icon }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div
                                class="text-sm font-semibold text-[var(--text-primary)] whitespace-nowrap overflow-hidden text-ellipsis">
                                {{ $tx->description }}
                            </div>
                            <div class="text-xs text-[var(--text-secondary)] mt-0.5 flex items-center gap-1.5 flex-wrap">
                                <span class="whitespace-nowrap inline-flex items-center gap-2">
                                    @if($tx->user->profile_photo_url)
                                        <img src="{{ $tx->user->profile_photo_url }}" alt="{{ $tx->user->name }}" style="width:20px; height:20px; border-radius:50%; object-fit:cover;" />
                                    @else
                                        <span style="display:inline-flex; width:20px; height:20px; border-radius:50%; background:#f3f4f6; color:#374151; align-items:center; justify-content:center; font-size:12px;">{{ $tx->user->avatar }}</span>
                                    @endif
                                    {{ $tx->user->name }}
                                </span>
                                <span class="opacity-40">•</span>
                                <span class="whitespace-nowrap">{{ $tx->date->isoFormat('D MMM') }}</span>
                                <span class="opacity-40">•</span>
                                <span class="whitespace-nowrap">{{ $tx->bank->icon }} {{ $tx->bank->name }}</span>
                            </div>
                        </div>
                        <div
                            class="text-right shrink-0 flex flex-col items-end gap-1
                                                                max-[576px]:w-full max-[576px]:flex-row max-[576px]:justify-between max-[576px]:items-center max-[576px]:border-t max-[576px]:border-dashed max-[576px]:border-gray-100 max-[576px]:pt-2 max-[576px]:mt-1">
                            <div
                                class="text-sm font-bold whitespace-nowrap {{ $tx->type === 'income' ? 'text-green-700' : 'text-rose-600' }}">
                                {{ $tx->type === 'income' ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </div>
                            <span class="{{ $tx->type === 'income' ? 'income-badge' : 'expense-badge' }}">
                                {{ $tx->type === 'income' ? 'Masuk' : 'Keluar' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10">
                        <div class="text-4xl mb-2">💸</div>
                        <p class="text-[13px] text-[var(--text-secondary)] m-0">Belum ada transaksi</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Right Side Column --}}
        <div
            class="flex flex-col gap-5 max-[992px]:grid max-[992px]:grid-cols-[repeat(auto-fit,minmax(280px,1fr))] max-[576px]:grid-cols-1">

            {{-- Top Pengeluaran --}}
            <div class="card p-4">
                <h3 class="text-base font-bold text-[var(--text-primary)] mt-0 mb-4">Top Pengeluaran</h3>
                @php $totalExp = $expenseByCategory->sum('amount') ?: 1; @endphp
                @forelse($expenseByCategory as $cat)
                    <div class="mb-3.5">
                        <div class="flex items-center justify-between mb-1.5 gap-2">
                            <span
                                class="text-[13px] text-[var(--text-primary)] flex items-center gap-2 min-w-0 overflow-hidden text-ellipsis whitespace-nowrap">
                                <span>{{ $cat['icon'] }}</span> {{ $cat['name'] }}
                            </span>
                            <span class="text-xs font-bold text-[var(--text-secondary)] shrink-0">
                                {{ number_format($cat['amount'] / $totalExp * 100, 1) }}%
                            </span>
                        </div>
                        <div class="h-1.5 rounded-full bg-slate-100">
                            <div class="h-1.5 rounded-full transition-[width] duration-[0.6s]"
                                style="width:{{ $cat['amount'] / $totalExp * 100 }}%; background:{{ $cat['color'] }};"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-[13px] text-[var(--text-secondary)] text-center py-4 m-0">Belum ada pengeluaran 🎉</p>
                @endforelse
            </div>

            {{-- Rekening --}}
            <div class="card p-4">
                <div class="flex items-center justify-between mb-4 gap-2">
                    <h3 class="text-base font-bold text-[var(--text-primary)] m-0">Rekening</h3>
                    <a href="{{ route('banks.index') }}"
                        class="text-xs text-[var(--pink-dark)] font-semibold no-underline">Kelola →</a>
                </div>
                <div class="flex flex-col gap-2.5">
                    @forelse($banks as $bank)
                        <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl min-w-0"
                            style="background:{{ $bank->color }}12; border:1px solid {{ $bank->color }}28;">
                            <span class="text-xl shrink-0">{{ $bank->icon }}</span>
                            <div class="flex-1 min-w-0">
                                <div
                                    class="text-[13px] font-semibold text-[var(--text-primary)] overflow-hidden text-ellipsis whitespace-nowrap">
                                    {{ $bank->name }} ({{ $bank->account_name }})
                                </div>
                                <div class="text-xs font-bold overflow-hidden text-ellipsis whitespace-nowrap"
                                    style="color:{{ $bank->color }};">
                                    Rp {{ number_format($bank->current_balance, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-[var(--text-secondary)] text-center py-2 m-0">Tambahkan rekening dulu</p>
                    @endforelse
                </div>
            </div>

            {{-- Target Nabung --}}
            <div class="card p-4">
                <div class="flex items-center justify-between mb-4 gap-2">
                    <h3 class="text-base font-bold text-[var(--text-primary)] m-0">Target Nabung</h3>
                    <a href="{{ route('targets.index') }}"
                        class="text-xs text-[var(--pink-dark)] font-semibold no-underline">Kelola →</a>
                </div>
                <div class="flex flex-col gap-2.5">
                    @forelse($targets as $target)
                        <div class="p-3 rounded-xl"
                            style="background:{{ $target->color }}10; border:1px solid {{ $target->color }}25;">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="shrink-0">{{ $target->icon }}</span>
                                <span
                                    class="text-[13px] font-semibold text-[var(--text-primary)] flex-1 overflow-hidden text-ellipsis whitespace-nowrap">
                                    {{ $target->name }}
                                </span>
                                <span class="text-xs font-bold shrink-0" style="color:{{ $target->color }};">
                                    {{ $target->progress_percent }}%
                                </span>
                            </div>
                            <div class="h-1.5 rounded-full bg-slate-100 mb-1.5">
                                <div class="h-1.5 rounded-full"
                                    style="width:{{ $target->progress_percent }}%; background:{{ $target->color }};"></div>
                            </div>
                            <div
                                class="text-[11px] text-[var(--text-secondary)] overflow-hidden text-ellipsis whitespace-nowrap">
                                Rp {{ number_format($target->current_amount, 0, ',', '.') }} / Rp
                                {{ number_format($target->target_amount, 0, ',', '.') }}
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-[var(--text-secondary)] text-center py-2 m-0">Buat target dulu!</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
@endsection
