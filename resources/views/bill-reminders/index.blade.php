@extends('layouts.app')
@section('title', 'Reminder Tagihan')

@section('content')
    @php
        $calendarEvents = $reminders->map(function ($reminder) {
            $daysLeft = now()->startOfDay()->diffInDays($reminder->due_date->copy()->startOfDay(), false);
            $status = $reminder->is_paid ? 'paid' : ($daysLeft < 0 ? 'late' : ($daysLeft <= 3 ? 'soon' : 'upcoming'));
            $statusLabel = match ($status) {
                'paid' => 'Selesai',
                'late' => 'Terlambat',
                'soon' => $daysLeft === 0 ? 'Hari ini' : $daysLeft . ' hari lagi',
                default => $daysLeft . ' hari lagi',
            };
            $colors = match ($status) {
                'paid' => ['#059669', '#ecfdf5'],
                'late' => ['#e11d48', '#fff1f2'],
                'soon' => ['#d97706', '#fffbeb'],
                default => ['#db2777', '#fdf2f8'],
            };

            return [
                'id' => (string) $reminder->id,
                'title' => $reminder->title,
                'start' => $reminder->due_date->format('Y-m-d'),
                'allDay' => true,
                'backgroundColor' => $colors[1],
                'borderColor' => $colors[0],
                'textColor' => $colors[0],
                'extendedProps' => [
                    'amount' => 'Rp ' . number_format($reminder->amount, 0, ',', '.'),
                    'status' => $status,
                    'statusLabel' => $statusLabel,
                    'repeat' => $reminder->repeat === 'none' ? 'Sekali saja' : 'Berulang ' . $reminder->repeat,
                    'owner' => $reminder->user?->name ?? 'Semua user',
                    'targetId' => 'reminder-card-' . $reminder->id,
                ],
            ];
        })->values();
    @endphp

    <style>
        .reminder-page-wrap {
            padding-bottom: 96px;
        }

        .reminder-calendar-card {
            border: 1px solid #fbcfe8;
            border-radius: 18px;
            background: linear-gradient(180deg, #fff7fb, #ffffff);
            padding: 12px;
        }

        .reminder-date-pills {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
            margin-top: 10px;
        }

        .reminder-date-pills button {
            border-radius: 999px;
            background: #fff;
            border: 1px solid #fbcfe8;
            color: #be185d;
            padding: 8px 10px;
            font-size: 11px;
            font-weight: 800;
        }

        #reminderCalendar .flatpickr-calendar {
            width: 100%;
            box-shadow: none;
            border: 0;
            background: transparent;
        }

        .reminder-calendar-source {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }

        #reminderCalendar .flatpickr-rContainer,
        #reminderCalendar .flatpickr-days,
        #reminderCalendar .dayContainer {
            width: 100%;
            max-width: none;
        }

        .reminder-planner-card {
            overflow: hidden;
            border: 1px solid #fbcfe8;
            background:
                radial-gradient(circle at top left, rgba(244, 114, 182, 0.16), transparent 30%),
                linear-gradient(180deg, #fff7fb 0%, #ffffff 42%);
        }

        .reminder-planner-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 16px;
        }

        .reminder-planner-icon {
            width: 48px;
            height: 48px;
            border-radius: 18px;
            display: grid;
            place-items: center;
            color: #db2777;
            background: #fce7f3;
            border: 1px solid #fbcfe8;
            box-shadow: 0 14px 30px rgba(219, 39, 119, 0.12);
            flex-shrink: 0;
        }

        .reminder-calendar-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .reminder-calendar-legend span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            border: 1px solid #fce7f3;
            background: rgba(255, 255, 255, 0.82);
            padding: 7px 10px;
            font-size: 11px;
            font-weight: 800;
            color: #64748b;
        }

        .reminder-calendar-legend i {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            display: inline-block;
        }

        .reminder-calendar-board {
            border-radius: 20px;
            border: 1px solid #fce7f3;
            background: rgba(255, 255, 255, 0.9);
            padding: 14px;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.9);
        }

        #billReminderCalendar {
            min-height: 610px;
        }

        #billReminderCalendar .fc {
            font-family: 'Poppins', sans-serif;
        }

        #billReminderCalendar .fc-toolbar-title {
            font-size: 20px;
            font-weight: 900;
            color: #0f172a;
        }

        #billReminderCalendar .fc-button-primary {
            border: 0;
            border-radius: 999px;
            background: #fce7f3;
            color: #be185d;
            font-size: 12px;
            font-weight: 800;
            padding: 8px 12px;
            box-shadow: none;
        }

        #billReminderCalendar .fc-button-primary:hover,
        #billReminderCalendar .fc-button-primary:focus,
        #billReminderCalendar .fc-button-primary:disabled {
            background: #fbcfe8;
            color: #9d174d;
            box-shadow: none;
        }

        #billReminderCalendar .fc-col-header-cell {
            padding: 10px 0;
            background: #fff7fb;
        }

        #billReminderCalendar .fc-col-header-cell-cushion {
            color: #be185d;
            font-size: 12px;
            font-weight: 900;
            text-decoration: none;
        }

        #billReminderCalendar .fc-daygrid-day-number {
            color: #334155;
            font-size: 12px;
            font-weight: 800;
            text-decoration: none;
        }

        #billReminderCalendar .fc-day-today {
            background: #fff1f2;
        }

        #billReminderCalendar .fc-daygrid-day-frame {
            min-height: 92px;
        }

        #billReminderCalendar .fc-event {
            border-width: 1px;
            border-radius: 12px;
            padding: 4px 6px;
            margin-top: 3px;
            cursor: pointer;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.05);
        }

        #billReminderCalendar .fc-event-title {
            font-size: 11px;
            font-weight: 900;
            line-height: 1.25;
        }

        .reminder-calendar-event {
            display: grid;
            gap: 2px;
            min-width: 0;
        }

        .reminder-calendar-event-title,
        .reminder-calendar-event-meta {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .reminder-calendar-event-title {
            font-size: 11px;
            font-weight: 900;
        }

        .reminder-calendar-event-meta {
            font-size: 10px;
            font-weight: 800;
            opacity: 0.78;
        }

        .reminder-list-card {
            scroll-margin-top: 24px;
        }

        .reminder-list-card.is-highlighted {
            border-color: #f9a8d4;
            box-shadow: 0 16px 40px rgba(219, 39, 119, 0.16);
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }

        @media (max-width: 768px) {
            .reminder-page-wrap {
                padding-bottom: 118px;
            }

            .reminder-planner-head {
                flex-direction: column;
            }

            .reminder-calendar-board {
                padding: 10px;
                margin-left: -6px;
                margin-right: -6px;
            }

            #billReminderCalendar {
                min-height: 560px;
            }

            #billReminderCalendar .fc-header-toolbar {
                align-items: stretch;
                flex-direction: column;
                gap: 10px;
            }

            #billReminderCalendar .fc-toolbar-chunk {
                display: flex;
                justify-content: center;
            }

            #billReminderCalendar .fc-daygrid-day-frame {
                min-height: 78px;
            }

            #billReminderCalendar .fc-event {
                padding: 3px 4px;
                border-radius: 9px;
            }

            #billReminderCalendar .fc-event-title,
            .reminder-calendar-event-title {
                font-size: 10px;
            }

            .reminder-calendar-event-meta {
                display: none;
            }
        }
    </style>

    <div class="reminder-page-wrap">
    <div class="flex items-center justify-between gap-3 mb-6 flex-wrap">
        <div>
            <h1 class="page-title">Reminder Tagihan</h1>
            <p class="page-subtitle">Catat tagihan berulang dan pantau jadwalnya di kalender bulanan.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-5 lg:grid-cols-[380px_1fr]">
        <section class="card p-5 h-fit">
            <h2 class="text-lg font-extrabold text-slate-900 mb-4">Tambah Tagihan</h2>
            <form action="{{ route('reminders.store') }}" method="POST" class="grid gap-4">
                @csrf
                <div>
                    <label class="label">Nama Tagihan</label>
                    <input name="title" class="input-field" required placeholder="Internet, listrik, kontrakan...">
                </div>
                <div>
                    <label class="label">Nominal</label>
                    <input name="amount" type="number" min="0" step="1000" class="input-field" required placeholder="250000">
                </div>
                <div>
                    <label class="label">Jatuh Tempo</label>
                    <div class="reminder-calendar-card">
                        <input id="reminderDueDate" name="due_date" type="hidden" required value="{{ now()->format('Y-m-d') }}">
                        <input id="reminderCalendarInput" type="text" class="reminder-calendar-source" aria-hidden="true" tabindex="-1">
                        <div id="reminderDateLabel" class="mb-2 rounded-2xl bg-white px-3 py-2 text-sm font-extrabold text-pink-700">
                            {{ now()->isoFormat('dddd, D MMMM Y') }}
                        </div>
                        <div id="reminderCalendar"></div>
                        <div class="reminder-date-pills">
                            <button type="button" data-reminder-date="{{ now()->format('Y-m-d') }}">Hari ini</button>
                            <button type="button" data-reminder-date="{{ now()->addDay()->format('Y-m-d') }}">Besok</button>
                            <button type="button" data-reminder-date="{{ now()->addWeek()->format('Y-m-d') }}">Minggu depan</button>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="label">Pengulangan</label>
                    <select name="repeat" class="input-field">
                        <option value="none">Sekali saja</option>
                        <option value="monthly">Bulanan</option>
                        <option value="weekly">Mingguan</option>
                        <option value="yearly">Tahunan</option>
                    </select>
                </div>
                <div>
                    <label class="label">Untuk User</label>
                    <select name="user_id" class="input-field">
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" {{ $member->id === auth()->id() ? 'selected' : '' }}>{{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Rekening Pembayaran</label>
                    <select name="bank_id" class="input-field">
                        <option value="">Belum ditentukan</option>
                        @foreach($banks as $bank)
                            <option value="{{ $bank->id }}">{{ $bank->name }} - {{ $bank->account_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Kategori Transaksi</label>
                    <select name="category_id" class="input-field">
                        <option value="">Belum ditentukan</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->icon }} {{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Catatan</label>
                    <textarea name="notes" class="input-field" rows="3" placeholder="Nomor pelanggan, catatan kecil..."></textarea>
                </div>
                <button class="btn-primary justify-center">
                    <i class="fa-solid fa-bell"></i> Simpan Reminder
                </button>
            </form>
        </section>

        <section class="grid gap-3">
            <div class="card p-5 reminder-planner-card">
                <div class="reminder-planner-head">
                    <div class="flex items-start gap-3">
                        <div class="reminder-planner-icon">
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>
                        <div>
                            <h2 class="m-0 text-xl font-extrabold text-slate-900">Kalender Tagihan</h2>
                            <p class="mt-1 text-sm text-slate-500">Lihat tanggal jatuh tempo dalam satu bulan. Klik tagihan di kalender untuk buka detailnya.</p>
                        </div>
                    </div>
                    <div class="reminder-calendar-legend">
                        <span><i style="background:#db2777;"></i> Aman</span>
                        <span><i style="background:#d97706;"></i> Dekat</span>
                        <span><i style="background:#e11d48;"></i> Telat</span>
                        <span><i style="background:#059669;"></i> Selesai</span>
                    </div>
                </div>
                <div class="reminder-calendar-board">
                    <div id="billReminderCalendar"></div>
                </div>
            </div>

            @forelse($reminders as $reminder)
                @php
                    $daysLeft = now()->startOfDay()->diffInDays($reminder->due_date->startOfDay(), false);
                    $statusClass = $reminder->is_paid ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($daysLeft < 0 ? 'bg-rose-50 text-rose-700 border-rose-200' : ($daysLeft <= 3 ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-slate-50 text-slate-600 border-slate-200'));
                    $statusText = $reminder->is_paid ? 'Selesai' : ($daysLeft < 0 ? 'Terlambat ' . abs($daysLeft) . ' hari' : ($daysLeft === 0 ? 'Hari ini' : $daysLeft . ' hari lagi'));
                @endphp
                <article id="reminder-card-{{ $reminder->id }}" class="card p-5 reminder-list-card">
                    <div class="flex items-start justify-between gap-4 flex-wrap">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h2 class="text-lg font-extrabold text-slate-900 m-0">{{ $reminder->title }}</h2>
                                <span class="rounded-full border px-3 py-1 text-xs font-bold {{ $statusClass }}">{{ $statusText }}</span>
                            </div>
                            <div class="mt-2 text-2xl font-extrabold text-pink-600">Rp {{ number_format($reminder->amount, 0, ',', '.') }}</div>
                            <div class="mt-2 text-sm text-slate-500">
                                Jatuh tempo {{ $reminder->due_date->isoFormat('D MMMM Y') }}
                                <span class="mx-1">•</span>
                                {{ $reminder->repeat === 'none' ? 'Sekali saja' : 'Berulang ' . $reminder->repeat }}
                            </div>
                            <div class="mt-2 text-xs font-semibold text-slate-500">
                                {{ $reminder->user?->name ?? 'Semua user' }}
                                @if($reminder->bank)
                                    <span class="mx-1">•</span>{{ $reminder->bank->name }}
                                @endif
                                @if($reminder->category)
                                    <span class="mx-1">•</span>{{ $reminder->category->icon }} {{ $reminder->category->name }}
                                @endif
                            </div>
                            @if($reminder->notes)
                                <p class="mt-3 text-sm text-slate-500">{{ $reminder->notes }}</p>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            @if(! $reminder->is_paid)
                                <form action="{{ route('reminders.paid', $reminder) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="create_transaction" value="1">
                                    <button class="btn-primary justify-center whitespace-nowrap">
                                        <i class="fa-solid fa-check"></i> Bayar
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('reminders.destroy', $reminder) }}" method="POST" onsubmit="return confirm('Hapus reminder ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn-ghost justify-center text-rose-600">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="card p-10 text-center">
                    <div class="mx-auto mb-3 grid h-12 w-12 place-items-center rounded-full bg-pink-50 text-pink-600">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                    <div class="font-extrabold text-slate-900">Belum ada reminder tagihan</div>
                    <p class="mt-1 text-sm text-slate-500">Tambahkan tagihan rutin seperti internet, listrik, kontrakan, atau subscription.</p>
                </div>
            @endforelse
        </section>
    </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/locales-all.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('reminderDueDate');
            const calendarInput = document.getElementById('reminderCalendarInput');
            const label = document.getElementById('reminderDateLabel');
            const calendarEl = document.getElementById('reminderCalendar');
            const billCalendarEl = document.getElementById('billReminderCalendar');
            const billReminderEvents = @json($calendarEvents);

            if (!input || !calendarInput || !calendarEl || !window.flatpickr) return;

            const formatLabel = date => new Intl.DateTimeFormat('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric',
            }).format(date);

            const calendar = flatpickr(calendarInput, {
                inline: true,
                appendTo: calendarEl,
                defaultDate: input.value,
                dateFormat: 'Y-m-d',
                locale: 'id',
                onChange: function (selectedDates, dateStr) {
                    input.value = dateStr;
                    if (selectedDates[0]) {
                        label.textContent = formatLabel(selectedDates[0]);
                    }
                },
            });

            document.querySelectorAll('[data-reminder-date]').forEach(button => {
                button.addEventListener('click', function () {
                    calendar.setDate(this.dataset.reminderDate, true);
                });
            });

            if (billCalendarEl && window.FullCalendar) {
                const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, char => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;',
                })[char]);

                const billCalendar = new FullCalendar.Calendar(billCalendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'id',
                    height: 'auto',
                    firstDay: 1,
                    dayMaxEvents: 3,
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,listMonth',
                    },
                    buttonText: {
                        today: 'Hari ini',
                        month: 'Bulan',
                        list: 'List',
                    },
                    events: billReminderEvents,
                    eventContent: function (arg) {
                        const props = arg.event.extendedProps;
                        return {
                            html: `
                                <div class="reminder-calendar-event">
                                    <div class="reminder-calendar-event-title">${escapeHtml(arg.event.title)}</div>
                                    <div class="reminder-calendar-event-meta">${escapeHtml(props.amount)} - ${escapeHtml(props.statusLabel)}</div>
                                </div>
                            `,
                        };
                    },
                    eventDidMount: function (info) {
                        const props = info.event.extendedProps;
                        info.el.title = `${info.event.title} - ${props.amount} - ${props.statusLabel} - ${props.owner}`;
                    },
                    eventClick: function (info) {
                        const target = document.getElementById(info.event.extendedProps.targetId);

                        if (!target) return;

                        target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        target.classList.add('is-highlighted');
                        window.setTimeout(() => target.classList.remove('is-highlighted'), 1600);
                    },
                });

                billCalendar.render();
            }
        });
    </script>
@endpush
