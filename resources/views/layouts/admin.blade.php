<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - DompetKita</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900">
    <div class="min-h-screen lg:flex">
        <aside class="border-b border-slate-200 bg-white lg:fixed lg:inset-y-0 lg:left-0 lg:w-72 lg:border-b-0 lg:border-r">
            <div class="flex items-center justify-between gap-3 px-5 py-4 lg:block">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <span class="grid h-11 w-11 place-items-center rounded-2xl bg-pink-600 text-white">
                        <i class="fa-solid fa-shield-heart"></i>
                    </span>
                    <span>
                        <span class="block text-lg font-extrabold">Admin Panel</span>
                        <span class="block text-xs font-semibold uppercase tracking-wide text-slate-400">DompetKita</span>
                    </span>
                </a>
                <a href="{{ route('admin.settings.index') }}" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold text-slate-600 lg:mt-6 lg:inline-flex">
                    <i class="fa-solid fa-gear mr-1"></i> Settings
                </a>
            </div>

            <nav class="flex gap-2 overflow-x-auto px-5 pb-4 lg:block lg:space-y-2 lg:overflow-visible">
                <a href="{{ route('admin.dashboard') }}"
                    class="inline-flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-bold {{ request()->routeIs('admin.dashboard') ? 'bg-pink-50 text-pink-700' : 'text-slate-600 hover:bg-slate-50' }}">
                    <i class="fa-solid fa-chart-line w-5"></i> Pantauan
                </a>
                <a href="{{ route('admin.settings.index') }}"
                    class="inline-flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-bold {{ request()->routeIs('admin.settings.*') ? 'bg-pink-50 text-pink-700' : 'text-slate-600 hover:bg-slate-50' }}">
                    <i class="fa-solid fa-gear w-5"></i> Pengaturan
                </a>
                <form action="{{ route('logout') }}" method="POST" class="lg:pt-4">
                    @csrf
                    <button class="inline-flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-bold text-red-600 hover:bg-red-50">
                        <i class="fa-solid fa-right-from-bracket w-5"></i> Keluar
                    </button>
                </form>
            </nav>
        </aside>

        <main class="w-full p-4 sm:p-6 lg:ml-72 lg:p-8">
            @if(session('success'))
                <div class="mb-5 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>

</html>
