@extends('layouts.admin')

@section('title', 'Update App')

@section('content')
    @php
        $types = [
            'feature' => 'Fitur Baru',
            'improvement' => 'Peningkatan',
            'fix' => 'Perbaikan Bug',
            'info' => 'Info',
        ];
    @endphp

    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm font-bold uppercase tracking-wide text-pink-600">Update App</p>
            <h1 class="mt-1 text-2xl font-extrabold text-slate-900 sm:text-3xl">Pengumuman Fitur & Perubahan</h1>
            <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-500">
                Buat pemberitahuan untuk user saat ada fitur baru, perbaikan, atau perubahan penting. User hanya melihat pengumuman yang belum mereka baca.
            </p>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[420px_1fr]">
        <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm h-fit">
            <h2 class="text-lg font-extrabold text-slate-900">Buat Pengumuman</h2>
            <p class="mt-1 text-sm text-slate-500">Setelah disimpan aktif, pop-up akan muncul ke user.</p>

            <form action="{{ route('admin.feature-announcements.store') }}" method="POST" class="mt-5 grid gap-4">
                @csrf
                <div>
                    <label class="mb-1 block text-xs font-extrabold uppercase tracking-wide text-slate-500">Judul</label>
                    <input name="title" required maxlength="160" value="{{ old('title') }}"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold outline-none focus:border-pink-400 focus:ring-4 focus:ring-pink-100"
                        placeholder="Contoh: Kalender Tagihan sudah hadir">
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-extrabold uppercase tracking-wide text-slate-500">Jenis</label>
                        <select name="type"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold outline-none focus:border-pink-400 focus:ring-4 focus:ring-pink-100">
                            @foreach($types as $value => $label)
                                <option value="{{ $value }}" @selected(old('type', 'feature') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-extrabold uppercase tracking-wide text-slate-500">Versi</label>
                        <input name="version" maxlength="40" value="{{ old('version') }}"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold outline-none focus:border-pink-400 focus:ring-4 focus:ring-pink-100"
                            placeholder="v1.2">
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-extrabold uppercase tracking-wide text-slate-500">Tanggal Tayang</label>
                    <input name="published_at" type="datetime-local" value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold outline-none focus:border-pink-400 focus:ring-4 focus:ring-pink-100">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-extrabold uppercase tracking-wide text-slate-500">Isi Pemberitahuan</label>
                    <textarea name="body" rows="5" required maxlength="1200"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold leading-relaxed outline-none focus:border-pink-400 focus:ring-4 focus:ring-pink-100"
                        placeholder="Tulis ringkas: fitur apa yang baru, manfaatnya, dan user bisa menemukannya di mana.">{{ old('body') }}</textarea>
                </div>
                <label class="flex items-center gap-3 rounded-2xl bg-pink-50 px-4 py-3 text-sm font-bold text-pink-700">
                    <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded border-pink-300 text-pink-600">
                    Aktifkan dan tampilkan ke user
                </label>
                <button class="rounded-2xl bg-pink-600 px-5 py-3 text-sm font-extrabold text-white hover:bg-pink-700">
                    <i class="fa-solid fa-paper-plane mr-2"></i> Kirim Update
                </button>
            </form>
        </section>

        <section class="space-y-4">
            @forelse($announcements as $announcement)
                <article class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col justify-between gap-4 lg:flex-row lg:items-start">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full bg-pink-50 px-3 py-1 text-xs font-extrabold text-pink-700">
                                    {{ $types[$announcement->type] ?? 'Info' }}
                                </span>
                                @if($announcement->version)
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-extrabold text-slate-600">{{ $announcement->version }}</span>
                                @endif
                                <span class="rounded-full px-3 py-1 text-xs font-extrabold {{ $announcement->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $announcement->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                            <h2 class="mt-3 text-lg font-extrabold text-slate-900">{{ $announcement->title }}</h2>
                            <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-slate-500">{{ $announcement->body }}</p>
                            <div class="mt-3 text-xs font-bold text-slate-400">
                                Tayang {{ optional($announcement->published_at)->isoFormat('D MMMM Y HH:mm') ?? '-' }}
                                <span class="mx-1">•</span>
                                Dibaca {{ number_format($announcement->reads_count, 0, ',', '.') }} user
                            </div>
                        </div>
                        <div class="flex shrink-0 gap-2">
                            <details class="relative">
                                <summary class="list-none rounded-2xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-600 hover:bg-slate-50 cursor-pointer">
                                    Edit
                                </summary>
                                <div class="absolute right-0 z-20 mt-2 w-[min(92vw,420px)] rounded-3xl border border-slate-200 bg-white p-4 shadow-xl">
                                    <form action="{{ route('admin.feature-announcements.update', $announcement) }}" method="POST" class="grid gap-3">
                                        @csrf
                                        @method('PUT')
                                        <input name="title" required maxlength="160" value="{{ $announcement->title }}" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold">
                                        <div class="grid grid-cols-2 gap-3">
                                            <select name="type" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold">
                                                @foreach($types as $value => $label)
                                                    <option value="{{ $value }}" @selected($announcement->type === $value)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <input name="version" maxlength="40" value="{{ $announcement->version }}" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold" placeholder="Versi">
                                        </div>
                                        <input name="published_at" type="datetime-local" value="{{ optional($announcement->published_at)->format('Y-m-d\TH:i') }}" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold">
                                        <textarea name="body" rows="4" required maxlength="1200" class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold">{{ $announcement->body }}</textarea>
                                        <label class="flex items-center gap-3 rounded-2xl bg-slate-50 px-4 py-3 text-sm font-bold text-slate-600">
                                            <input type="checkbox" name="is_active" value="1" @checked($announcement->is_active)>
                                            Aktif
                                        </label>
                                        <button class="rounded-2xl bg-pink-600 px-4 py-3 text-sm font-extrabold text-white">Simpan</button>
                                    </form>
                                </div>
                            </details>
                            <form action="{{ route('admin.feature-announcements.destroy', $announcement) }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="rounded-2xl border border-rose-200 px-4 py-3 text-sm font-bold text-rose-600 hover:bg-rose-50">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-center">
                    <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-pink-50 text-pink-600">
                        <i class="fa-solid fa-bullhorn"></i>
                    </div>
                    <div class="mt-3 font-extrabold text-slate-900">Belum ada pengumuman update</div>
                    <p class="mt-1 text-sm text-slate-500">Buat satu pengumuman saat fitur baru siap dipakai user.</p>
                </div>
            @endforelse
        </section>
    </div>
@endsection
