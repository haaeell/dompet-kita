@extends('layouts.app')
@section('title', 'Budgeting')

@section('content')
    @php
        $totalBudget = $budgetedCategories->sum(fn($category) => (float) $activeBudgets->get($category->id)?->amount);
        $totalSpent = $budgetedCategories->sum(fn($category) => (float) ($spentByCategory[$category->id] ?? 0));
        $totalRemaining = max(0, $totalBudget - $totalSpent);
        $totalPercent = $totalBudget > 0 ? min(100, round(($totalSpent / $totalBudget) * 100)) : 0;
    @endphp

    <style>
        .budget-hero {
            border-radius: 8px;
            padding: 20px;
            color: #fff;
            background:
                radial-gradient(circle at 86% 18%, rgba(255, 255, 255, .22) 0 15%, transparent 32%),
                linear-gradient(135deg, #0891b2, #db2777 54%, #9d174d);
            box-shadow: 0 18px 38px rgba(219, 39, 119, .18);
        }

        .budget-progress {
            height: 10px;
            overflow: hidden;
            border-radius: 999px;
            background: rgba(255, 255, 255, .22);
        }

        .budget-progress span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: #fff;
        }

        .budget-card {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #fff;
            padding: 16px;
        }

        .budget-history-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
        }

        .budget-history-card {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #fff;
            padding: 14px;
            text-decoration: none;
            color: inherit;
        }

        .budget-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(220px, .45fr);
            gap: 16px;
            align-items: center;
        }

        .budget-mini-progress {
            height: 8px;
            overflow: hidden;
            border-radius: 999px;
            background: #f1f5f9;
            margin-top: 10px;
        }

        .budget-mini-progress span {
            display: block;
            height: 100%;
            border-radius: inherit;
        }

        @media (max-width: 768px) {
            .budget-header {
                margin: -6px -4px 14px;
            }

            .budget-header .page-title {
                font-size: 26px;
                line-height: 1.1;
            }

            .budget-hero {
                margin: 0 -4px;
                padding: 18px;
            }

            .budget-row {
                grid-template-columns: 1fr;
            }

            .budget-card {
                box-shadow: 0 10px 26px rgba(15, 23, 42, .04);
            }

            .budget-card .btn-primary,
            .budget-card .btn-ghost {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <div class="budget-header flex items-center justify-between gap-3 mb-6 flex-wrap">
        <div>
            <h1 class="page-title mb-1">Budgeting</h1>
            <p class="page-subtitle m-0">Pilih kategori tertentu saja untuk dibudget. Pemakaian dihitung otomatis tiap bulan.</p>
        </div>
        <a href="{{ route('categories.index') }}" class="btn-ghost sm:w-auto justify-center">
            <i class="fa-solid fa-tag"></i> Kategori
        </a>
    </div>

    @if(session('success'))
        <div class="mb-5 rounded-3xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <section class="budget-hero mb-5">
        <div class="flex items-start justify-between gap-3 flex-wrap">
            <div>
                <div class="text-xs font-bold uppercase tracking-[.12em] opacity-80">Budget {{ $budgetMonth->translatedFormat('F Y') }}</div>
                <div class="mt-2 text-3xl font-extrabold">Rp {{ number_format($totalBudget, 0, ',', '.') }}</div>
                <div class="mt-1 text-sm opacity-85">Terpakai Rp {{ number_format($totalSpent, 0, ',', '.') }}. Sisa Rp {{ number_format($totalRemaining, 0, ',', '.') }}.</div>
            </div>
            <div class="rounded-full bg-white/15 px-4 py-2 text-sm font-extrabold">
                {{ $totalPercent }}% terpakai
            </div>
        </div>
        <div class="budget-progress mt-5">
            <span style="width: {{ $totalPercent }}%;"></span>
        </div>
    </section>

    <section class="budget-card mb-5">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900 m-0">Pilih bulan budget</h2>
                <p class="text-sm text-slate-500 m-0">Lihat atau ubah budget untuk bulan tertentu.</p>
            </div>
            <form action="{{ route('budgets.index') }}" method="GET" class="flex gap-2">
                <input type="month" name="month" value="{{ $budgetMonth->format('Y-m') }}" class="input-field">
                <button type="submit" class="btn-primary justify-center">Lihat</button>
            </form>
        </div>
    </section>

    <section class="budget-card mb-5">
        <h2 class="text-lg font-extrabold text-slate-900 mb-3">Tambah kategori yang mau dibudget</h2>
        <form action="{{ route('budgets.update') }}" method="POST" class="grid gap-3 md:grid-cols-[1fr_220px_auto]">
            @csrf
            <input type="hidden" name="budget_month" value="{{ $budgetMonth->format('Y-m') }}">
            <select name="category_id" class="input-field" required>
                <option value="">Pilih kategori pengeluaran</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">
                        {{ $category->icon }} {{ $category->name }}
                        @if($activeBudgets->has($category->id))
                            - aktif
                        @endif
                    </option>
                @endforeach
            </select>
            <input type="number" min="0" step="1000" name="amount" class="input-field" placeholder="Budget bulanan" required>
            <button type="submit" class="btn-primary justify-center">
                <i class="fa-solid fa-plus"></i> Simpan
            </button>
        </form>
    </section>

    <section class="grid gap-3">
        @forelse($budgetedCategories as $category)
            @php
                $budget = $activeBudgets->get($category->id);
                $budgetAmount = (float) ($budget?->amount ?? 0);
                $spentAmount = (float) ($spentByCategory[$category->id] ?? 0);
                $percent = $budgetAmount > 0 ? min(100, round(($spentAmount / $budgetAmount) * 100)) : 0;
                $remaining = max(0, $budgetAmount - $spentAmount);
                $isOverBudget = $budgetAmount > 0 && $spentAmount > $budgetAmount;
                $usesFallbackBudget = ! $currentMonthBudgets->has($category->id);
            @endphp
            <article class="budget-card">
                <div class="budget-row">
                    <div class="min-w-0">
                        <div class="flex items-center gap-3">
                            <div class="grid h-12 w-12 place-items-center rounded-xl text-xl shrink-0"
                                style="background: {{ $category->color }}18; border: 1px solid {{ $category->color }}30;">
                                {{ $category->icon }}
                            </div>
                            <div class="min-w-0">
                                <div class="font-extrabold text-slate-900 truncate">{{ $category->name }}</div>
                                <div class="text-xs text-slate-500">
                                    {{ $isOverBudget ? 'Lewat budget' : 'Masih aman' }} -
                                    terpakai Rp {{ number_format($spentAmount, 0, ',', '.') }}
                                    @if($usesFallbackBudget)
                                        <span class="font-bold text-pink-600">- dari budget bulan sebelumnya</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="budget-mini-progress">
                            <span style="width: {{ $percent }}%; background: {{ $isOverBudget ? '#ef4444' : $category->color }};"></span>
                        </div>
                        <div class="mt-2 flex items-center justify-between gap-2 text-xs text-slate-500">
                            <span>Budget Rp {{ number_format($budgetAmount, 0, ',', '.') }}</span>
                            <span>{{ $isOverBudget ? 'Lebih Rp ' . number_format($spentAmount - $budgetAmount, 0, ',', '.') : 'Sisa Rp ' . number_format($remaining, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <form action="{{ route('budgets.update') }}" method="POST" class="grid gap-2">
                        @csrf
                        <input type="hidden" name="budget_month" value="{{ $budgetMonth->format('Y-m') }}">
                        <input type="hidden" name="category_id" value="{{ $category->id }}">
                        <input type="number" min="0" step="1000" name="amount" class="input-field"
                            value="{{ (int) $budgetAmount }}" placeholder="Budget bulanan">
                        <div class="grid grid-cols-2 gap-2">
                            <button type="submit" class="btn-primary justify-center">Update</button>
                            <button type="submit" name="clear_budget" value="1" class="btn-ghost justify-center text-rose-600">Hapus</button>
                        </div>
                    </form>
                </div>
            </article>
        @empty
            <div class="budget-card text-center py-10">
                <div class="mx-auto mb-3 grid h-12 w-12 place-items-center rounded-full bg-pink-50 text-pink-600">
                    <i class="fa-solid fa-chart-pie"></i>
                </div>
                <div class="font-extrabold text-slate-900">Belum ada kategori yang dibudget</div>
                <div class="mt-1 text-sm text-slate-500">Contohnya pilih Makanan & Minuman saja, lalu semua transaksi kategori itu akan dihitung otomatis setiap bulan.</div>
            </div>
        @endforelse
    </section>

    <section class="budget-card mt-5">
        <div class="flex items-center justify-between gap-3 mb-4 flex-wrap">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900 m-0">Riwayat Budget Bulanan</h2>
                <p class="text-sm text-slate-500 m-0">Ringkasan budget yang pernah dibuat per bulan.</p>
            </div>
        </div>

        <div class="budget-history-grid">
            @foreach($historyMonths as $history)
                @php
                    $historyRemaining = max(0, $history['budget_total'] - $history['spent_total']);
                    $historyOver = $history['budget_total'] > 0 && $history['spent_total'] > $history['budget_total'];
                @endphp
                <a href="{{ route('budgets.index', ['month' => $history['month']->format('Y-m')]) }}"
                    class="budget-history-card {{ $history['month']->isSameMonth($budgetMonth) ? 'ring-2 ring-pink-200' : '' }}">
                    <div class="text-sm font-extrabold text-slate-900">{{ $history['month']->translatedFormat('F Y') }}</div>
                    <div class="mt-2 text-xs text-slate-500">Budget</div>
                    <div class="text-base font-extrabold text-slate-900">Rp {{ number_format($history['budget_total'], 0, ',', '.') }}</div>
                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                        <span class="block h-full rounded-full" style="width: {{ $history['percent'] }}%; background: {{ $historyOver ? '#ef4444' : '#db2777' }};"></span>
                    </div>
                    <div class="mt-2 text-xs {{ $historyOver ? 'text-rose-600' : 'text-slate-500' }}">
                        Terpakai Rp {{ number_format($history['spent_total'], 0, ',', '.') }}
                        @if(! $historyOver)
                            - sisa Rp {{ number_format($historyRemaining, 0, ',', '.') }}
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endsection
