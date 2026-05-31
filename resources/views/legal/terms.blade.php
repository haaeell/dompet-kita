<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <title>Syarat Penggunaan - DompetKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800">
    <main class="mx-auto max-w-3xl px-5 py-10">
        <a href="{{ route('login') }}" class="text-sm font-bold text-pink-600">DompetKita</a>
        <h1 class="mt-5 text-3xl font-extrabold text-slate-950">Syarat Penggunaan</h1>
        <p class="mt-2 text-sm text-slate-500">Terakhir diperbarui: {{ now()->translatedFormat('d F Y') }}</p>

        <section class="mt-8 space-y-5 leading-relaxed">
            <p>Dengan menggunakan DompetKita, kamu setuju memakai layanan ini secara wajar untuk mencatat dan mengelola keuangan bersama pasangan.</p>

            <h2 class="text-xl font-bold text-slate-950">Akun dan kode undangan</h2>
            <p>Kamu bertanggung jawab menjaga keamanan akun dan kode undangan pasangan. Setiap pasangan saat ini dibatasi maksimal dua anggota.</p>

            <h2 class="text-xl font-bold text-slate-950">Data keuangan</h2>
            <p>DompetKita adalah alat pencatatan, bukan penasihat keuangan. Keputusan finansial tetap menjadi tanggung jawab pengguna.</p>

            <h2 class="text-xl font-bold text-slate-950">Konten chat dan lampiran</h2>
            <p>Jangan mengunggah konten ilegal, berbahaya, atau melanggar hak orang lain. Pengelola dapat membatasi akses jika terjadi penyalahgunaan.</p>

            <h2 class="text-xl font-bold text-slate-950">Ketersediaan layanan</h2>
            <p>Kami berusaha menjaga layanan tetap berjalan, tetapi tidak menjamin aplikasi selalu bebas gangguan, terutama saat maintenance atau pembaruan sistem.</p>

            <h2 class="text-xl font-bold text-slate-950">Perubahan</h2>
            <p>Syarat ini dapat diperbarui mengikuti perkembangan fitur dan kebutuhan layanan.</p>
        </section>
    </main>
</body>
</html>
