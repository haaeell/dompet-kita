<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <title>Kebijakan Privasi - DompetKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800">
    <main class="mx-auto max-w-3xl px-5 py-10">
        <a href="{{ route('login') }}" class="text-sm font-bold text-pink-600">DompetKita</a>
        <h1 class="mt-5 text-3xl font-extrabold text-slate-950">Kebijakan Privasi</h1>
        <p class="mt-2 text-sm text-slate-500">Terakhir diperbarui: {{ now()->translatedFormat('d F Y') }}</p>

        <section class="mt-8 space-y-5 leading-relaxed">
            <p>DompetKita dibuat untuk membantu pasangan mencatat keuangan bersama. Kami menyimpan data yang kamu masukkan agar fitur seperti transaksi, rekening, target, chat, dan lokasi pasangan dapat berjalan.</p>

            <h2 class="text-xl font-bold text-slate-950">Data yang disimpan</h2>
            <p>Data akun seperti nama, email, password terenkripsi, foto profil, data pasangan, transaksi, rekening, kategori, target tabungan, utang/piutang, pesan chat, lampiran chat, dan lokasi terakhir jika kamu mengaktifkan fitur lokasi.</p>

            <h2 class="text-xl font-bold text-slate-950">Lokasi</h2>
            <p>Lokasi hanya dibagikan setelah kamu memberi izin di browser dan menekan update lokasi. Kamu bisa menghentikan berbagi lokasi dari halaman Lokasi Pasangan.</p>

            <h2 class="text-xl font-bold text-slate-950">Akses pasangan</h2>
            <p>Data dalam satu ruang pasangan dapat dilihat oleh anggota pasangan tersebut sesuai fitur yang tersedia. Jangan bagikan kode undangan kepada orang yang tidak kamu percaya.</p>

            <h2 class="text-xl font-bold text-slate-950">Keamanan</h2>
            <p>Password disimpan dalam bentuk hash. Kami juga membatasi percobaan login dan register untuk mengurangi penyalahgunaan.</p>

            <h2 class="text-xl font-bold text-slate-950">Penghapusan data</h2>
            <p>Jika ingin menghapus akun atau data pasangan, hubungi pengelola layanan. Fitur hapus mandiri dapat ditambahkan pada versi berikutnya.</p>
        </section>
    </main>
</body>
</html>
