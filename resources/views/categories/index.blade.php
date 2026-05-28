@extends('layouts.app')
@section('title', 'Kategori')
@section('content')
    <div class="page-header"
        style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 class="page-title">Kategori</h1>
            <p class="page-subtitle">Kelola kategori pemasukan dan pengeluaran</p>
        </div>
        <button onclick="openModal('modalCat')" class="btn-primary">
            <i class="fa-solid fa-plus"></i> Tambah Kategori
        </button>
    </div>

    @php
        $income = $categories->where('type', 'income');
        $expense = $categories->where('type', 'expense');
    @endphp

    <div class="card p-3 sm:p-4 mb-5">
        <div class="category-tabs">
            <a href="{{ route('categories.index', ['tab' => 'expense']) }}"
                class="category-tab {{ $activeTab === 'expense' ? 'active expense' : '' }}">
                <i class="fa-solid fa-arrow-trend-down"></i>
                <span>Pengeluaran</span>
                <span class="category-tab-count">{{ $expense->count() }}</span>
            </a>
            <a href="{{ route('categories.index', ['tab' => 'income']) }}"
                class="category-tab {{ $activeTab === 'income' ? 'active income' : '' }}">
                <i class="fa-solid fa-arrow-trend-up"></i>
                <span>Pemasukan</span>
                <span class="category-tab-count">{{ $income->count() }}</span>
            </a>
        </div>
    </div>

    <div style="display:flex; flex-direction:column; gap:8px;">
        @php
            $activeCategories = $activeTab === 'income' ? $income : $expense;
            $activeColor = $activeTab === 'income' ? '#16a34a' : '#e11d48';
            $activeBg = $activeTab === 'income' ? '#f0fdf4' : '#fff1f2';
            $activeIcon = $activeTab === 'income' ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down';
            $activeLabel = $activeTab === 'income' ? 'Pemasukan' : 'Pengeluaran';
        @endphp

        <div style="display:flex; align-items:center; gap:8px; margin-bottom:14px;">
            <span
                style="width:28px; height:28px; border-radius:8px; background:{{ $activeBg }}; display:flex; align-items:center; justify-content:center; color:{{ $activeColor }};">
                <i class="fa-solid {{ $activeIcon }}" style="font-size:13px;"></i>
            </span>
            <span
                style="font-size:11px; font-weight:700; letter-spacing:0.08em; text-transform:uppercase; color:{{ $activeColor }};">{{ $activeLabel }}</span>
            <span style="font-size:12px; color:var(--text-secondary); font-weight:500;">{{ $activeCategories->count() }}
                kategori</span>
        </div>

        @foreach($activeCategories as $cat)
            <div class="card cat-item" id="cat-{{ $cat->id }}"
                style="display:flex; align-items:center; gap:14px; padding:14px 16px; border-radius:14px; cursor:pointer;"
                onclick="window.location='{{ route('transactions.index', ['category_id' => $cat->id]) }}'">
                <div
                    style="width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0; background:{{ $cat->color }}18; border:1px solid {{ $cat->color }}30;">
                    {{ $cat->icon }}
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="font-size:14px; font-weight:600; color:var(--text-primary);">{{ $cat->name }}</div>
                    <div style="font-size:12px; color:var(--text-secondary); margin-top:2px;">
                        {{ $cat->transactions_count }} transaksi</div>
                </div>
                <div style="width:10px; height:10px; border-radius:50%; background:{{ $cat->color }}; flex-shrink:0;">
                </div>
                @if(!$cat->is_default || $cat->transactions_count === 0)
                    <button onclick="event.stopPropagation(); deleteCategory({{ $cat->id }})" class="cat-del-btn"
                        style="background:none; border:none; cursor:pointer; color:#cbd5e1; padding:6px; border-radius:8px; font-size:13px; line-height:1; transition:all 0.15s; opacity:0;"
                        onmouseover="this.style.color='#e11d48';this.style.background='#fff1f2'"
                        onmouseout="this.style.color='#cbd5e1';this.style.background='none'">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                @endif
            </div>
        @endforeach

        @if($activeCategories->isEmpty())
            <div style="text-align:center; padding:32px 0; color:var(--text-secondary); font-size:13px;">
                Belum ada kategori {{ strtolower($activeLabel) }}
            </div>
        @endif
    </div>

    <div id="modalCat" class="modal-overlay">
        <div class="modal-box">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:22px;">
                <h2 class="modal-title" style="margin-bottom:0;">Tambah Kategori</h2>
                <button onclick="closeModal('modalCat')"
                    style="background:none; border:none; cursor:pointer; color:var(--text-secondary); padding:6px; border-radius:8px; font-size:16px; line-height:1;"
                    onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div
                style="display:flex; gap:6px; margin-bottom:20px; padding:5px; border-radius:14px; background:#f8fafc; border:1px solid var(--border);">
                <button id="catExpBtn" onclick="setCatType('expense')"
                    style="flex:1; padding:10px; border-radius:10px; border:none; font-size:13px; font-weight:600; font-family:'DM Sans',sans-serif; cursor:pointer; transition:all 0.15s; background:none; color:var(--text-secondary);">
                    <i class="fa-solid fa-arrow-down" style="margin-right:5px;"></i> Pengeluaran
                </button>
                <button id="catIncBtn" onclick="setCatType('income')"
                    style="flex:1; padding:10px; border-radius:10px; border:none; font-size:13px; font-weight:600; font-family:'DM Sans',sans-serif; cursor:pointer; transition:all 0.15s; background:none; color:var(--text-secondary);">
                    <i class="fa-solid fa-arrow-up" style="margin-right:5px;"></i> Pemasukan
                </button>
            </div>
            <input type="hidden" id="catType" value="expense">

            <div style="display:flex; flex-direction:column; gap:16px;">
                <div style="display:grid; grid-template-columns:80px 1fr; gap:12px;">
                    <div>
                        <label class="label">Icon</label>
                        <input type="text" id="catIcon" value="📦" class="input-field" maxlength="4"
                            style="font-size:24px; text-align:center; padding:10px;">
                    </div>
                    <div>
                        <label class="label">Nama Kategori</label>
                        <input type="text" id="catName" placeholder="Nama kategori..." class="input-field">
                    </div>
                </div>
                <div>
                    <label class="label">Warna</label>
                    <div style="display:flex; flex-wrap:wrap; gap:8px; margin-top:2px;">
                        @foreach(['#ef4444', '#f97316', '#f59e0b', '#10b981', '#06b6d4', '#3b82f6', '#8b5cf6', '#d946ef', '#f43f5e', '#6b7280', '#ec4899', '#14b8a6'] as $c)
                            <button onclick="pickCatColor('{{ $c }}')" id="cc-{{ ltrim($c, '#') }}"
                                style="width:32px; height:32px; border-radius:10px; border:2px solid transparent; background:{{ $c }}; cursor:pointer; transition:all 0.15s;"></button>
                        @endforeach
                    </div>
                    <input type="hidden" id="catColor" value="#6b7280">
                </div>
            </div>

            <div style="display:flex; gap:10px; margin-top:22px;">
                <button onclick="closeModal('modalCat')" class="btn-ghost"
                    style="flex:1; justify-content:center;">Batal</button>
                <button onclick="submitCat()" class="btn-primary" style="flex:1; justify-content:center;">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                </button>
            </div>
        </div>
    </div>

    <style>
        .cat-item:hover .cat-del-btn {
            opacity: 1 !important;
        }

        .category-tabs {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .category-tab {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 14px;
            border: 1px solid var(--border);
            background: #fff;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.15s ease;
        }

        .category-tab:hover {
            transform: translateY(-1px);
            border-color: #fbcfe8;
        }

        .category-tab.active.expense {
            background: #fff1f2;
            border-color: #fecdd3;
            color: #e11d48;
        }

        .category-tab.active.income {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: #16a34a;
        }

        .category-tab-count {
            min-width: 26px;
            height: 26px;
            padding: 0 8px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            background: #f8fafc;
            color: var(--text-secondary);
        }

        .category-tab.active.expense .category-tab-count {
            background: #ffe4e6;
            color: #e11d48;
        }

        .category-tab.active.income .category-tab-count {
            background: #dcfce7;
            color: #16a34a;
        }

        @media (max-width: 640px) {
            .category-tab {
                padding: 11px 10px;
                gap: 8px;
                font-size: 13px;
            }
        }
    </style>

@endsection

@push('scripts')
    <script>
        function openModal(id) { $('#' + id).addClass('active'); }
        function closeModal(id) { $('#' + id).removeClass('active'); }

        $('#modalCat').on('click', function (e) {
            if ($(e.target).is('#modalCat')) closeModal('modalCat');
        });

        function setCatType(type) {
            $('#catType').val(type);
            $('#catExpBtn, #catIncBtn').css({ background: 'none', color: 'var(--text-secondary)' });
            if (type === 'expense') {
                $('#catExpBtn').css({ background: '#fff1f2', color: '#e11d48' });
            } else {
                $('#catIncBtn').css({ background: '#f0fdf4', color: '#16a34a' });
            }
        }

        setCatType('expense');

        function pickCatColor(c) {
            $('#catColor').val(c);
            $('[id^="cc-"]').css({ outline: 'none', transform: 'scale(1)' });
            $('#cc-' + c.replace('#', '')).css({ outline: '3px solid ' + c, outlineOffset: '2px', transform: 'scale(1.15)' });
        }

        pickCatColor('#6b7280');

        async function submitCat() {
            const data = {
                name: $('#catName').val(),
                icon: $('#catIcon').val(),
                color: $('#catColor').val(),
                type: $('#catType').val(),
            };

            if (!data.name) {
                Toast.fire({ icon: 'warning', title: 'Nama kategori wajib diisi!' });
                return;
            }

            const res = await $.ajax({
                url: '{{ route("categories.store") }}',
                method: 'POST',
                contentType: 'application/json',
                headers: { 'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content'), 'Accept': 'application/json' },
                data: JSON.stringify(data)
            });

            if (res.success) {
                closeModal('modalCat');
                Toast.fire({ icon: 'success', title: res.message });
                setTimeout(() => location.reload(), 1200);
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.message, background: '#fff', color: '#1a1a2e', confirmButtonColor: '#db2777' });
            }
        }

        function deleteCategory(id) {
            deleteConfirm(`/categories/${id}`, () => {
                $('#cat-' + id).fadeOut(300, function () { $(this).remove(); });
            });
        }
    </script>
@endpush
