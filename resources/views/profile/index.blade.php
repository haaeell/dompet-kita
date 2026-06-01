@extends('layouts.app')
@section('title', 'Profil Pasangan')

@section('content')
    <style>
        @media (max-width: 768px) {
            .profile-page-head {
                margin: -6px -4px 14px;
            }

            .profile-page-head .page-title {
                font-size: 26px;
                line-height: 1.1;
            }

            .profile-page-head .btn-ghost {
                width: auto;
                padding: 9px 13px;
                border-radius: 999px;
            }

            .profile-grid {
                display: flex;
                flex-direction: column;
                gap: 14px;
            }

            .profile-hero-card {
                border: 0;
                border-radius: 8px;
                padding: 18px;
                color: #fff;
                background:
                    radial-gradient(circle at 86% 16%, rgba(255, 255, 255, .22) 0 15%, transparent 30%),
                    linear-gradient(135deg, #ec4899, #db2777 48%, #9d174d);
                box-shadow: 0 18px 38px rgba(219, 39, 119, .24);
            }

            .profile-hero-card .text-\[var\(--text-secondary\)\],
            .profile-hero-card .text-\[var\(--pink-dark\)\] {
                color: rgba(255, 255, 255, .82) !important;
            }

            .profile-hero-card h2,
            .profile-hero-card .text-2xl {
                color: #fff;
            }

            .profile-hero-card .rounded-3xl.bg-white {
                background: rgba(255, 255, 255, .14) !important;
                border-color: rgba(255, 255, 255, .2) !important;
                color: #fff;
            }

            .profile-hero-card .input-field {
                background: rgba(255, 255, 255, .92);
                border: 0;
                font-size: 12px;
            }

            .profile-hero-card .btn-primary {
                padding: 10px 12px;
                border-radius: 12px;
                background: #fff;
                color: #db2777;
                box-shadow: none;
            }

            .profile-hero-card .w-20 {
                width: 72px;
                height: 72px;
                border-radius: 24px;
                border-color: rgba(255, 255, 255, .45);
            }

            .profile-form-stack {
                gap: 14px;
            }

            .profile-form-card {
                border-radius: 8px;
                padding: 18px;
                box-shadow: 0 10px 26px rgba(15, 23, 42, .04);
            }

            .profile-form-card .input-field {
                min-height: 46px;
            }

            .profile-form-card .btn-primary {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <div class="profile-page-head flex items-center justify-between gap-3 mb-6 flex-wrap">
        <div>
            <h1 class="page-title mb-1">Hai, {{ $user->name }} 💕</h1>
            <p class="page-subtitle m-0">Kelola profil pasangan dan keamanan akun dengan tampilan yang manis.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn-ghost sm:w-auto justify-center">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="mb-5 rounded-3xl bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-5 rounded-3xl bg-rose-50 border border-rose-200 p-4 text-sm text-rose-800">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-5 rounded-3xl bg-rose-50 border border-rose-200 p-4 text-sm text-rose-800">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="profile-grid grid gap-5 lg:grid-cols-[1.6fr_1fr]">
        <div class="profile-hero-card card p-6">
            <div class="flex flex-col gap-5">
                <div class="flex items-center gap-4">
                    @if($user->profile_photo_url)
                        <img src="{{ $user->profile_photo_url }}" alt="Foto Profil"
                            class="w-20 h-20 rounded-[28px] object-cover border border-slate-200">
                    @else
                        <div
                            class="w-20 h-20 rounded-[28px] bg-pink-100 text-[32px] flex items-center justify-center text-pink-700">
                            {{ $user->avatar }}
                        </div>
                    @endif
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-[var(--pink-dark)] font-semibold mb-1">Pasangan
                        </p>
                        <h2 class="text-2xl font-bold">{{ $user->name }}</h2>
                        <p class="text-sm text-[var(--text-secondary)]">Pasangan di
                            “{{ $user->couple->couple_name ?? 'DompetKita' }}”</p>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-3xl bg-white border border-slate-200 p-4">
                        <div class="text-[11px] uppercase tracking-[0.16em] text-[var(--text-secondary)] mb-2">Kode Undangan
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <div class="text-lg font-semibold">{{ $user->couple->invite_code ?? '-' }}</div>
                            <button type="button" class="copy-btn" onclick="copyInvite()" title="Salin kode">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="rounded-3xl bg-white border border-slate-200 p-4">
                        <div class="text-[11px] uppercase tracking-[0.16em] text-[var(--text-secondary)] mb-2">Peran</div>
                        <div class="text-lg font-semibold">{{ $user->role === 'owner' ? 'Pemilik' : 'Pasangan' }}</div>
                    </div>
                </div>

                <div class="rounded-3xl bg-white border border-pink-100 p-4" id="profileInviteLink">
                    <div class="text-[11px] uppercase tracking-[0.16em] text-[var(--text-secondary)] mb-2">Link Undangan Pasangan</div>
                    <div class="flex items-center gap-2">
                        <input type="text" class="input-field flex-1 min-w-0" readonly
                            value="{{ route('register', ['invite' => $user->couple->invite_code ?? '', 'action' => 'join']) }}">
                        <button type="button" class="btn-primary shrink-0" onclick="copyInviteLink()">
                            <i class="fa-solid fa-link"></i> Salin
                        </button>
                    </div>
                    <p class="text-xs text-[var(--text-secondary)] mt-2">Kirim link ini ke pasanganmu agar kode undangan terisi otomatis.</p>
                </div>

                <div class="rounded-3xl bg-pink-50 border border-pink-100 p-4">
                    <div class="text-sm font-semibold text-[var(--pink-dark)] mb-2">Tentang</div>
                    <p class="text-sm text-[var(--text-secondary)] leading-relaxed">Ubah nama, email, avatar, dan password
                        akun pasanganmu di halaman ini dengan mudah.</p>
                </div>
            </div>
        </div>

        <div class="profile-form-stack space-y-5">
            <div class="profile-form-card card p-6">
                <h2 class="text-lg font-semibold mb-4">Ubah Profil</h2>
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="grid gap-4">
                        <div>
                            <label class="label" for="name">Nama</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                class="input-field" required>
                        </div>
                        <div>
                            <label class="label" for="email">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                class="input-field" required>
                        </div>
                        <div>
                            <label class="label" for="avatar">Avatar</label>
                            <input type="text" id="avatar" name="avatar" value="{{ old('avatar', $user->avatar) }}"
                                class="input-field" maxlength="3" placeholder="Emoji atau inisial">
                        </div>
                        <div>
                            <label class="label" for="profile_photo">Unggah Foto Profil</label>
                            <input type="file" id="profile_photo" name="profile_photo" accept="image/jpeg,image/png"
                                class="input-field">
                            <p class="text-xs text-[var(--text-secondary)] mt-2">
                                Ukuran maksimal 2MB. Hanya JPG, JPEG, atau PNG.
                            </p>
                            <p id="photoWarning" class="text-xs text-rose-600 mt-2 hidden"></p>
                        </div>
                        <button type="submit" class="btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>

            <div class="profile-form-card card p-6">
                <h2 class="text-lg font-semibold mb-4">Ubah Password</h2>
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid gap-4">
                        <div>
                            <label class="label" for="current_password">Password Saat Ini</label>
                            <input type="password" id="current_password" name="current_password" class="input-field"
                                required>
                        </div>
                        <div>
                            <label class="label" for="password">Password Baru</label>
                            <input type="password" id="password" name="password" class="input-field" required>
                        </div>
                        <div>
                            <label class="label" for="password_confirmation">Konfirmasi Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="input-field" required>
                        </div>
                        <button type="submit" class="btn-primary">Ubah Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const profilePhotoInput = document.getElementById('profile_photo');
            const photoWarning = document.getElementById('photoWarning');
            const profileForm = document.querySelector('form[action="{{ route('profile.update') }}"]');

            if (profilePhotoInput && photoWarning && profileForm) {
                const maxSizeBytes = 2 * 1024 * 1024;

                profilePhotoInput.addEventListener('change', function () {
                    photoWarning.classList.add('hidden');
                    photoWarning.textContent = '';

                    if (!this.files || !this.files[0]) {
                        return;
                    }

                    if (this.files[0].size > maxSizeBytes) {
                        photoWarning.textContent = 'Ukuran file terlalu besar. Maksimal 2MB.';
                        photoWarning.classList.remove('hidden');
                    }
                });

                profileForm.addEventListener('submit', function (event) {
                    if (profilePhotoInput.files && profilePhotoInput.files[0] && profilePhotoInput.files[0].size > maxSizeBytes) {
                        event.preventDefault();
                        photoWarning.textContent = 'Ukuran file terlalu besar. Maksimal 2MB.';
                        photoWarning.classList.remove('hidden');
                    }
                });
            }
        </script>
    @endpush
@endsection
