@extends('layouts.admin')

@section('title', 'Pantauan Sistem')

@section('content')
    @php
        $rupiah = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
        $number = fn ($value) => number_format((int) $value, 0, ',', '.');
    @endphp

    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm font-bold uppercase tracking-wide text-pink-600">Pantauan Sistem</p>
            <h1 class="mt-1 text-2xl font-extrabold text-slate-900 sm:text-3xl">Ringkasan DompetKita</h1>
            <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-500">
                Monitor pertumbuhan user, pasangan, transaksi, budget, target, dan aktivitas terbaru dari satu tempat.
            </p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-600">
            <i class="fa-regular fa-clock mr-2 text-pink-600"></i>
            Update: {{ now()->isoFormat('D MMMM Y HH:mm') }}
        </div>
    </div>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-sm font-bold text-slate-500">User Publik</span>
                <span class="grid h-10 w-10 place-items-center rounded-2xl bg-pink-50 text-pink-600">
                    <i class="fa-solid fa-users"></i>
                </span>
            </div>
            <div class="mt-4 text-3xl font-extrabold">{{ $number($stats['users']) }}</div>
            <div class="mt-1 text-xs font-semibold text-slate-400">+{{ $number($monthly['new_users']) }} bulan ini</div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-sm font-bold text-slate-500">Pasangan</span>
                <span class="grid h-10 w-10 place-items-center rounded-2xl bg-rose-50 text-rose-600">
                    <i class="fa-solid fa-heart"></i>
                </span>
            </div>
            <div class="mt-4 text-3xl font-extrabold">{{ $number($stats['couples']) }}</div>
            <div class="mt-1 text-xs font-semibold text-slate-400">{{ $number($stats['complete_couples']) }} lengkap, {{ $number($stats['waiting_couples']) }} menunggu pasangan</div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-sm font-bold text-slate-500">Transaksi</span>
                <span class="grid h-10 w-10 place-items-center rounded-2xl bg-blue-50 text-blue-600">
                    <i class="fa-solid fa-arrow-right-arrow-left"></i>
                </span>
            </div>
            <div class="mt-4 text-3xl font-extrabold">{{ $number($stats['transactions']) }}</div>
            <div class="mt-1 text-xs font-semibold text-slate-400">{{ $number($monthly['transaction_count']) }} bulan ini</div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-sm font-bold text-slate-500">Aktif Sekarang</span>
                <span class="grid h-10 w-10 place-items-center rounded-2xl bg-emerald-50 text-emerald-600">
                    <i class="fa-solid fa-signal"></i>
                </span>
            </div>
            <div class="mt-4 text-3xl font-extrabold">{{ $number($sessions['online']) }}</div>
            <div class="mt-1 text-xs font-semibold text-slate-400">{{ $number($sessions['last_day']) }} sesi dalam 24 jam</div>
        </div>
    </div>

    <div class="mb-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <div class="text-sm font-bold text-slate-500">Pemasukan Bulan Ini</div>
            <div class="mt-2 text-xl font-extrabold text-emerald-600">{{ $rupiah($monthly['income']) }}</div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <div class="text-sm font-bold text-slate-500">Pengeluaran Bulan Ini</div>
            <div class="mt-2 text-xl font-extrabold text-rose-600">{{ $rupiah($monthly['expense']) }}</div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <div class="text-sm font-bold text-slate-500">Budget Dipakai</div>
            <div class="mt-2 text-xl font-extrabold text-slate-900">{{ $number($budgetHealth['active']) }}</div>
            <div class="mt-1 text-xs font-semibold text-amber-600">{{ $number($budgetHealth['near_limit']) }} hampir batas, {{ $number($budgetHealth['over']) }} lewat batas</div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <div class="text-sm font-bold text-slate-500">Objek Aktif</div>
            <div class="mt-2 text-sm font-bold text-slate-700">
                {{ $number($stats['banks']) }} rekening, {{ $number($stats['targets_active']) }} target, {{ $number($stats['debts_unpaid']) }} hutang/piutang
            </div>
            <div class="mt-1 text-xs font-semibold text-slate-400">{{ $number($stats['chat_messages']) }} pesan chat</div>
        </div>
    </div>

    <div class="mb-6 grid gap-6 xl:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-2">
            <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-extrabold">Aktivitas 14 Hari</h2>
                    <p class="text-sm text-slate-500">Jumlah transaksi non-transfer per hari.</p>
                </div>
            </div>
            <div class="h-72">
                <canvas id="activityChart"></canvas>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-extrabold">Pasangan Teraktif</h2>
            <p class="mb-4 text-sm text-slate-500">Berdasarkan total transaksi.</p>
            <div class="space-y-3">
                @forelse($topCouples as $couple)
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="truncate font-bold">{{ $couple->couple_name }}</div>
                                <div class="mt-1 text-xs font-semibold text-slate-400">{{ $number($couple->transactions_count) }} transaksi</div>
                            </div>
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-pink-700">
                                {{ $rupiah($couple->monthly_expense ?? 0) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl bg-slate-50 p-4 text-sm font-semibold text-slate-500">Belum ada transaksi.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-extrabold">Pasangan Terbaru</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full min-w-[620px] text-left text-sm">
                    <thead class="text-xs uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="pb-3">Pasangan</th>
                            <th class="pb-3">Member</th>
                            <th class="pb-3">Data</th>
                            <th class="pb-3">Dibuat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($latestCouples as $couple)
                            <tr>
                                <td class="py-3">
                                    <div class="font-bold">{{ $couple->couple_name }}</div>
                                    <div class="text-xs font-semibold text-slate-400">{{ $couple->invite_code }}</div>
                                </td>
                                <td class="py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($couple->users as $member)
                                            <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-bold text-slate-600">{{ $member->name }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-3 text-xs font-semibold text-slate-500">
                                    {{ $number($couple->transactions_count) }} trx, {{ $number($couple->banks_count) }} rekening, {{ $number($couple->targets_count) }} target
                                </td>
                                <td class="py-3 text-xs font-semibold text-slate-400">{{ $couple->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-5 text-center font-semibold text-slate-400">Belum ada pasangan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-extrabold">User Terbaru</h2>
            <div class="mt-4 space-y-3">
                @forelse($recentUsers as $user)
                    <div class="flex items-center justify-between gap-4 rounded-2xl bg-slate-50 p-4">
                        <div class="min-w-0">
                            <div class="truncate font-bold">{{ $user->name }}</div>
                            <div class="truncate text-xs font-semibold text-slate-400">{{ $user->email }}</div>
                            <div class="mt-1 text-xs font-semibold text-slate-500">{{ $user->couple->couple_name ?? 'Belum punya pasangan' }}</div>
                        </div>
                        <div class="text-right">
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-bold text-slate-600">{{ $user->role }}</span>
                            <div class="mt-2 text-xs font-semibold text-slate-400">{{ $user->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl bg-slate-50 p-4 text-sm font-semibold text-slate-500">Belum ada user.</div>
                @endforelse
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-2">
            <h2 class="text-lg font-extrabold">Transaksi Terakhir</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead class="text-xs uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="pb-3">Tanggal</th>
                            <th class="pb-3">Pasangan</th>
                            <th class="pb-3">User</th>
                            <th class="pb-3">Kategori</th>
                            <th class="pb-3">Deskripsi</th>
                            <th class="pb-3 text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentTransactions as $transaction)
                            <tr>
                                <td class="py-3 text-xs font-semibold text-slate-400">{{ $transaction->date->format('d/m/Y H:i') }}</td>
                                <td class="py-3 font-semibold">{{ $transaction->couple->couple_name ?? '-' }}</td>
                                <td class="py-3 text-slate-600">{{ $transaction->user->name ?? '-' }}</td>
                                <td class="py-3 text-slate-600">{{ $transaction->category->icon ?? '' }} {{ $transaction->category->name ?? '-' }}</td>
                                <td class="py-3 text-slate-500">{{ $transaction->description }}</td>
                                <td class="py-3 text-right font-extrabold {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $transaction->type === 'income' ? '+' : '-' }} {{ $rupiah($transaction->amount) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-5 text-center font-semibold text-slate-400">Belum ada transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const activityChart = document.getElementById('activityChart');

        if (activityChart) {
            new Chart(activityChart, {
                type: 'line',
                data: {
                    labels: @json($activityChart['labels']),
                    datasets: [{
                        label: 'Transaksi',
                        data: @json($activityChart['values']),
                        borderColor: '#db2777',
                        backgroundColor: 'rgba(219, 39, 119, 0.12)',
                        borderWidth: 3,
                        pointRadius: 4,
                        pointBackgroundColor: '#db2777',
                        fill: true,
                        tension: 0.35,
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            },
                            grid: {
                                color: '#f1f5f9'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    </script>
@endpush
