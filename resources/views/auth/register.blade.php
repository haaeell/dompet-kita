<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar – DompetKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1f2937;
        }

        .gradient-text {
            background: linear-gradient(135deg, #ec4899, #f43f5e);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .input-field {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 12px 16px;
            color: #1f2937;
            font-size: 14px;
            width: 100%;
            outline: none;
            transition: all 0.2s;
            font-family: inherit;
        }

        .input-field:focus {
            border-color: #ec4899;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.15);
        }

        .input-field::placeholder {
            color: #9ca3af;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ec4899, #f43f5e);
            color: white;
            border: none;
            border-radius: 14px;
            padding: 14px 24px;
            font-weight: 700;
            font-size: 15px;
            width: 100%;
            cursor: pointer;
            transition: all 0.2s;
            font-family: inherit;
        }

        .btn-primary:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            box-shadow: 0 10px 25px rgba(244, 63, 94, 0.25);
        }

        .tab-btn {
            flex: 1;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
            cursor: pointer;
            border: 1px solid transparent;
            color: #6b7280;
            background: transparent;
            font-family: inherit;
        }

        .tab-btn.active {
            background: #ffffff;
            color: #ec4899;
            border-color: #f3f4f6;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }

        .label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .mesh {
            position: fixed;
            inset: 0;
            pointer-events: none;
            background: radial-gradient(ellipse at 20% 20%, rgba(236, 72, 153, 0.06) 0%, transparent 50%), radial-gradient(ellipse at 80% 80%, rgba(244, 63, 94, 0.04) 0%, transparent 50%);
        }

        .card {
            background: #ffffff;
            border: 1px solid #f3f4f6;
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        }

        .emoji-btn {
            font-size: 20px;
            cursor: pointer;
            padding: 6px;
            border-radius: 8px;
            border: 2px solid transparent;
            transition: all 0.15s;
        }

        .emoji-btn:hover,
        .emoji-btn.selected {
            border-color: #ec4899;
            background: rgba(236, 72, 153, 0.1);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center px-4 py-8">
    <div class="mesh"></div>
    <div class="relative z-10 w-full max-w-lg">
        <div class="text-center mb-6">
            <div class="text-4xl mb-2">💑</div>
            <h1 class="font-bold text-3xl gradient-text mb-1" style="font-family: 'Bricolage Grotesque', sans-serif;">
                DompetKita</h1>
            <p class="text-gray-500 text-sm">Buat akun dan kelola keuangan bersama</p>
        </div>

        <div class="card">
            <h2 class="text-lg font-bold text-gray-800 mb-5" style="font-family: 'Bricolage Grotesque', sans-serif;">
                Buat
                Akun Baru</h2>

            {{-- Personal info --}}
            <div class="space-y-4 mb-5">
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="label">Avatar Kamu</label>
                        <div class="flex flex-wrap gap-1" id="avatarList">
                            @foreach(['👨', '👩', '👦', '👧', '🧑', '👤', '🦊', '🐱', '🐶', '🐼', '🦁', '🐸'] as $em)
                                <button type="button" class="emoji-btn" onclick="selectAvatar('{{ $em }}')"
                                    id="av-{{ $loop->index }}">{{ $em }}</button>
                            @endforeach
                        </div>
                        <input type="hidden" id="userAvatar" value="👤">
                    </div>
                    <div class="col-span-2">
                        <label class="label">Nama Lengkap</label>
                        <input type="text" id="name" placeholder="Nama kamu" class="input-field">
                    </div>
                </div>
                <div>
                    <label class="label">Email</label>
                    <input type="email" id="email" placeholder="email@kamu.com" class="input-field">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">Password</label>
                        <input type="password" id="password" placeholder="Min. 8 karakter" class="input-field">
                    </div>
                    <div>
                        <label class="label">Konfirmasi</label>
                        <input type="password" id="password_confirmation" placeholder="Ulangi password"
                            class="input-field">
                    </div>
                </div>
            </div>

            <hr style="border-color: #f3f4f6; margin-bottom: 20px;">

            {{-- Couple action --}}
            <label class="label mb-3">Bergabung sebagai</label>
            <div class="flex gap-2 p-1 rounded-xl mb-4" style="background: #f3f4f6;">
                <button type="button" id="tabCreate" class="tab-btn active" onclick="setTab('create')">✨ Buat Pasangan
                    Baru</button>
                <button type="button" id="tabJoin" class="tab-btn" onclick="setTab('join')">🔗 Gabung Pasangan</button>
            </div>
            <input type="hidden" id="coupleAction" value="create">

            {{-- Create couple --}}
            <div id="panelCreate" class="space-y-3">
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="label">Icon Couple</label>
                        <div class="flex flex-wrap gap-1">
                            @foreach(['💑', '👫', '💏', '🥰', '💕', '❤️', '💍', '🌸'] as $em)
                                <button type="button" class="emoji-btn" onclick="selectCoupleEmoji('{{ $em }}')"
                                    id="cem-{{ $loop->index }}">{{ $em }}</button>
                            @endforeach
                        </div>
                        <input type="hidden" id="coupleAvatar" value="💑">
                    </div>
                    <div class="col-span-2">
                        <label class="label">Nama Pasangan Kalian</label>
                        <input type="text" id="coupleName" placeholder="Misal: Rizky & Dinda" class="input-field">
                    </div>
                </div>
            </div>

            {{-- Join couple --}}
            <div id="panelJoin" class="hidden">
                <label class="label">Kode Undangan</label>
                <input type="text" id="inviteCode" placeholder="Masukkan 8 karakter kode"
                    class="input-field text-center text-xl font-mono tracking-widest" maxlength="8"
                    oninput="this.value = this.value.toUpperCase()">
                <p class="text-xs text-gray-400 mt-2 text-center">Minta kode dari pasanganmu yang sudah mendaftar</p>
            </div>

            <button onclick="doRegister()" class="btn-primary mt-6">Daftar Sekarang 🚀</button>
            <p class="text-center text-gray-400 text-sm mt-4">
                Sudah punya akun? <a href="{{ route('login') }}" style="color: #ec4899;"
                    class="font-semibold hover:text-pink-600 transition-colors">Masuk</a>
            </p>
        </div>
    </div>

    <script>
        function setTab(tab) {
            document.getElementById('coupleAction').value = tab;
            document.getElementById('tabCreate').className = 'tab-btn' + (tab === 'create' ? ' active' : '');
            document.getElementById('tabJoin').className = 'tab-btn' + (tab === 'join' ? ' active' : '');
            document.getElementById('panelCreate').className = tab === 'create' ? 'space-y-3' : 'hidden';
            document.getElementById('panelJoin').className = tab === 'join' ? '' : 'hidden';
        }

        function selectAvatar(em) {
            document.getElementById('userAvatar').value = em;
            document.querySelectorAll('[id^="av-"]').forEach(b => b.classList.remove('selected'));
            event.target.classList.add('selected');
        }

        function selectCoupleEmoji(em) {
            document.getElementById('coupleAvatar').value = em;
            document.querySelectorAll('[id^="cem-"]').forEach(b => b.classList.remove('selected'));
            event.target.classList.add('selected');
        }

        async function doRegister() {
            const btn = document.querySelector('.btn-primary');
            const data = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                password_confirmation: document.getElementById('password_confirmation').value,
                user_avatar: document.getElementById('userAvatar').value,
                couple_action: document.getElementById('coupleAction').value,
                couple_name: document.getElementById('coupleName').value,
                couple_avatar: document.getElementById('coupleAvatar').value,
                invite_code: document.getElementById('inviteCode').value,
            };

            btn.textContent = 'Memproses...'; btn.disabled = true;

            const res = await fetch('{{ route("register") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                body: JSON.stringify(data)
            }).then(r => r.json()).catch(() => ({ success: false, message: 'Terjadi kesalahan!' }));

            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    title: '🎉 Berhasil!',
                    text: 'Akun berhasil dibuat! Selamat datang!',
                    background: '#ffffff',
                    color: '#1f2937',
                    confirmButtonColor: '#ec4899',
                    timer: 2000
                }).then(() => window.location.href = res.redirect);
            } else {
                const msg = res.errors ? Object.values(res.errors).flat().join('\n') : res.message;
                Swal.fire({
                    icon: 'error',
                    title: 'Pendaftaran Gagal',
                    text: msg,
                    background: '#ffffff',
                    color: '#1f2937',
                    confirmButtonColor: '#ec4899'
                });
                btn.textContent = 'Daftar Sekarang 🚀'; btn.disabled = false;
            }
        }
    </script>
</body>

</html>