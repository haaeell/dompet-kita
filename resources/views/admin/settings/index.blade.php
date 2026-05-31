@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-slate-800">
                        Maintenance Mode
                    </h1>

                    <p class="text-sm text-slate-500 mt-1">
                        Jika aktif, sistem tidak bisa diakses publik.
                        Admin tetap bisa login dan mengelola sistem.
                    </p>
                </div>

                <span class="px-3 py-1 rounded-full text-xs font-semibold
                    {{ $maintenanceMode === '1' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                    {{ $maintenanceMode === '1' ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>

            <form method="POST" action="{{ route('admin.settings.maintenance') }}" class="mt-6">
                @csrf

                <button type="submit" onclick="return confirm('Yakin ingin mengubah status maintenance?')"
                    class="px-5 py-3 rounded-xl text-white font-semibold
                        {{ $maintenanceMode === '1' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }}">
                    {{ $maintenanceMode === '1' ? 'Matikan Maintenance' : 'Aktifkan Maintenance' }}
                </button>
            </form>
        </div>
    </div>
@endsection