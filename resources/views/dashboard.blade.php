@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard Admin')

@section('content')
    @php
        $actor = $actor ?? Auth::user();
        $stats = $stats ?? [
            'attendance_today' => 0,
            'guest_today' => 0,
            'survey_today' => 0,
            'users_total' => 0,
        ];

        $chart = collect($chart ?? []);

        $weekGuest = (int) $chart->sum('guest');
        $weekSurvey = (int) $chart->sum('survey');

        $maxVal = (int) max(1, (int) $chart->max('guest'), (int) $chart->max('survey'));

        $fmt = fn($n) => number_format((int) $n, 0, ',', '.');
        $chartPx = 140;

        $isSuperAdmin = $actor && (($actor->role ?? null) === 'super_admin' || ($actor->role ?? null) === 'admin');
    @endphp

    <style>
        @media (prefers-reduced-motion: reduce) {

            .anim,
            .anim * {
                animation: none !important;
                transition: none !important;
            }
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-up {
            animation: fadeUp .45s ease-out both;
        }

        @keyframes barGrow {
            from {
                transform: scaleY(.12);
            }

            to {
                transform: scaleY(1);
            }
        }

        .bar-anim {
            transform-origin: bottom;
            animation: barGrow .75s cubic-bezier(.2, .95, .2, 1) both;
        }

        .soft-ring:focus-visible {
            outline: none;
            box-shadow: 0 0 0 3px rgba(15, 23, 42, .12);
        }

        /* Tooltip bubble */
        .tip {
            position: absolute;
            left: 50%;
            transform: translateX(-50%) translateY(6px);
            bottom: calc(100% + 8px);
            opacity: 0;
            pointer-events: none;
            transition: opacity .15s ease, transform .15s ease;
            white-space: nowrap;
            z-index: 50;
        }

        .tip::after {
            content: '';
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            bottom: -6px;
            width: 10px;
            height: 10px;
            background: rgba(15, 23, 42, .92);
            border-radius: 2px;
            rotate: 45deg;
            filter: drop-shadow(0 6px 10px rgba(0, 0, 0, .18));
        }

        .tip-bubble {
            background: rgba(15, 23, 42, .92);
            color: rgba(255, 255, 255, .92);
            border: 1px solid rgba(255, 255, 255, .12);
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 11px;
            filter: drop-shadow(0 10px 18px rgba(0, 0, 0, .18));
            backdrop-filter: blur(10px);
        }

        .tip-wrap:hover .tip {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        /* Card hover feel */
        .card-hover {
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 40px rgba(15, 23, 42, .10);
            border-color: rgba(148, 163, 184, .6);
        }

        /* Icon bubble hover */
        .bubble {
            transition: transform .25s ease, background-color .25s ease;
        }

        .card-hover:hover .bubble {
            transform: scale(1.08);
        }

        /* Button shine */
        .btn-shine {
            position: relative;
            overflow: hidden;
        }

        .btn-shine::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(115deg, transparent 0%, rgba(255, 255, 255, .25) 45%, transparent 55%);
            transform: translateX(-120%);
            transition: transform .65s ease;
        }

        .btn-shine:hover::after {
            transform: translateX(220%);
        }
    </style>

    {{-- PAGE HEADER --}}
    <div class="mb-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900">
                    Dashboard
                </h2>
                <p class="mt-1 text-sm text-slate-600">
                    Ringkasan aktivitas dan statistik sistem hari ini
                </p>
            </div>
        </div>

        {{-- Divider halus --}}
        <div class="mt-3 h-px w-full bg-gradient-to-r from-slate-200 via-slate-300/40 to-transparent"></div>
    </div>


    <div class="anim grid grid-cols-1 xl:grid-cols-3 gap-4">

        {{-- CHART --}}
        <div
            class="fade-up xl:col-span-2 relative overflow-hidden rounded-2xl bg-white border border-slate-200 shadow-sm p-5 card-hover">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-slate-50 via-transparent to-transparent">
            </div>

            <div class="relative flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <p class="text-xs text-slate-500">Ringkasan</p>
                    <p class="mt-1 text-lg font-extrabold tracking-tight text-slate-900">Aktivitas 7 Hari Terakhir</p>
                    <p class="mt-1 text-sm text-slate-600">Kunjungan dan survey.</p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <span
                        class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 transition hover:bg-slate-200">
                        <span class="h-2 w-2 rounded-full bg-emerald-600"></span>
                        Total Kunjungan: {{ $fmt($weekGuest) }}
                    </span>
                    <span
                        class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 transition hover:bg-slate-200">
                        <span class="h-2 w-2 rounded-full bg-sky-600"></span>
                        Total Survey: {{ $fmt($weekSurvey) }}
                    </span>
                </div>
            </div>

            {{-- CHART BOX --}}
            <div class="relative mt-5 rounded-xl bg-slate-50 border border-slate-200 overflow-visible">
                <div
                    class="pointer-events-none absolute inset-0 opacity-60
                [background-image:linear-gradient(to_right,rgba(15,23,42,0.06)_1px,transparent_1px),linear-gradient(to_bottom,rgba(15,23,42,0.06)_1px,transparent_1px)]
                [background-size:44px_44px]">
                </div>

                <div class="relative px-4 pt-4 flex flex-wrap items-center gap-4 text-xs text-slate-600">
                    <span class="inline-flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-emerald-600"></span> Kunjungan
                    </span>
                    <span class="inline-flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-sky-600"></span> Survey
                    </span>
                    <span class="ml-auto text-[11px] text-slate-500">
                        Skala max: <span class="font-semibold">{{ $fmt($maxVal) }}</span>
                    </span>
                </div>

                <div class="relative p-4 pb-8">
                    {{-- tinggi chart --}}
                    <div class="grid grid-cols-7 gap-2 items-end" style="height: {{ $chartPx }}px;">
                        @foreach ($chart as $i => $point)
                            @php
                                $g = (int) ($point['guest'] ?? 0);
                                $s = (int) ($point['survey'] ?? 0);

                                $hg = $g > 0 ? max(2, (int) round(($g / $maxVal) * $chartPx)) : 2;
                                $hs = $s > 0 ? max(2, (int) round(($s / $maxVal) * $chartPx)) : 2;

                                $delay = min(260, $i * 45);
                                $label = $point['date'] ?? '-';
                            @endphp

                            <div class="group tip-wrap relative flex flex-col items-center justify-end gap-2">
                                {{-- Tooltip custom --}}
                                <div class="tip">
                                    <div class="tip-bubble">
                                        <span class="inline-flex items-center gap-2">
                                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                            Kunjungan: <b>{{ $g }}</b>
                                        </span>
                                        <span class="mx-2 opacity-40">|</span>
                                        <span class="inline-flex items-center gap-2">
                                            <span class="h-2 w-2 rounded-full bg-sky-500"></span>
                                            Survey: <b>{{ $s }}</b>
                                        </span>
                                    </div>
                                </div>

                                <div class="w-full flex items-end justify-center gap-1">
                                    <div class="bar-anim w-2 rounded-md bg-emerald-600/75 shadow-sm
                                           transition duration-200 group-hover:bg-emerald-600 group-hover:shadow
                                           group-hover:-translate-y-0.5"
                                        style="height: {{ $hg }}px; animation-delay: {{ $delay }}ms;">
                                    </div>

                                    <div class="bar-anim w-2 rounded-md bg-sky-600/75 shadow-sm
                                           transition duration-200 group-hover:bg-sky-600 group-hover:shadow
                                           group-hover:-translate-y-0.5"
                                        style="height: {{ $hs }}px; animation-delay: {{ $delay + 20 }}ms;">
                                    </div>
                                </div>

                                <div class="text-[11px] text-slate-500 transition group-hover:text-slate-700">
                                    {{ $label }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            {{-- CHART FOOTER / INSIGHT --}}
            <div class="relative mt-4 px-4 pb-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

                    {{-- Insight --}}
                    <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
                        <p class="text-xs font-semibold text-slate-600">Insight</p>
                        <p class="mt-1 text-sm text-slate-700">
                            Aktivitas tertinggi terjadi pada
                            <span class="font-semibold text-slate-900">
                                {{ $chart->sortByDesc('guest')->first()['date'] ?? '-' }}
                            </span>
                        </p>
                    </div>

                    {{-- Tren --}}
                    <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
                        <p class="text-xs font-semibold text-slate-600">Tren Mingguan</p>
                        <div class="mt-1 flex items-center gap-2 text-sm">
                            <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold">
                                ↑ Stabil
                            </span>
                            <span class="text-slate-500 text-xs">
                                dibanding minggu sebelumnya
                            </span>
                        </div>
                    </div>

                    {{-- Range --}}
                    <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
                        <p class="text-xs font-semibold text-slate-600">Periode Data</p>
                        <p class="mt-1 text-sm text-slate-700">
                            {{ $chart->first()['date'] ?? '-' }}
                            –
                            {{ $chart->last()['date'] ?? '-' }}
                        </p>
                    </div>

                </div>
            </div>

        </div>

        {{-- KPI --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-1 gap-4">

            <div
                class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5
                    transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-slate-500">Intern Hadir Hari Ini</p>
                        <p class="mt-2 text-2xl font-extrabold text-slate-900">
                            {{ number_format($intern_present_today ?? 0, 0, ',', '.') }}
                        </p>
                        <p class="mt-1 text-xs text-slate-500">Unik berdasarkan check-in</p>
                    </div>
                    <div class="h-10 w-10 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center">
                        <x-icon name="check-circle" class="h-5 w-5" />
                    </div>
                </div>
            </div>


            <div
                class="fade-up relative overflow-hidden rounded-2xl bg-white border border-slate-200 shadow-sm p-5 card-hover">
                <div
                    class="pointer-events-none absolute inset-0 bg-gradient-to-br from-emerald-50 via-transparent to-transparent">
                </div>
                <div class="relative flex items-start justify-between">
                    <div>
                        <p class="text-xs text-slate-500">Buku Tamu Hari Ini</p>
                        <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">
                            {{ $fmt($stats['guest_today'] ?? 0) }}</p>
                        <p class="mt-1 text-xs text-slate-500">Total kunjungan hari ini</p>
                    </div>
                    <div
                        class="bubble h-11 w-11 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                        <x-icon name="book-open" class="h-5 w-5" />
                    </div>
                </div>
            </div>

            <div
                class="fade-up relative overflow-hidden rounded-2xl bg-white border border-slate-200 shadow-sm p-5 card-hover">
                <div
                    class="pointer-events-none absolute inset-0 bg-gradient-to-br from-sky-50 via-transparent to-transparent">
                </div>
                <div class="relative flex items-start justify-between">
                    <div>
                        <p class="text-xs text-slate-500">Survey Masuk</p>
                        <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">
                            {{ $fmt($stats['survey_today'] ?? 0) }}</p>
                        <p class="mt-1 text-xs text-slate-500">Total survey hari ini</p>
                    </div>
                    <div class="bubble h-11 w-11 rounded-xl bg-sky-50 text-sky-700 flex items-center justify-center">
                        <x-icon name="star" class="h-5 w-5" />
                    </div>
                </div>
            </div>

            <div
                class="fade-up relative overflow-hidden rounded-2xl bg-white border border-slate-200 shadow-sm p-5 card-hover">
                <div
                    class="pointer-events-none absolute inset-0 bg-gradient-to-br from-fuchsia-50 via-transparent to-transparent">
                </div>
                <div class="relative flex items-start justify-between">
                    <div>
                        <p class="text-xs text-slate-500">Total Users</p>
                        <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">
                            {{ $fmt($stats['users_total'] ?? 0) }}</p>
                        <p class="mt-1 text-xs text-slate-500">Akun aktif terdaftar</p>
                    </div>
                    <div
                        class="bubble h-11 w-11 rounded-xl bg-fuchsia-50 text-fuchsia-700 flex items-center justify-center">
                        <x-icon name="users" class="h-5 w-5" />
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- AKTIVITAS TERBARU --}}
    @php
        $tz = 'Asia/Jakarta';
    @endphp
    <section class="fade-up mt-4 grid grid-cols-1 xl:grid-cols-3 gap-4">
        {{-- Tamu --}}
        <div class="relative overflow-hidden rounded-2xl bg-white border border-slate-200 shadow-sm">
            <div
                class="pointer-events-none absolute inset-0 bg-gradient-to-br from-emerald-50 via-transparent to-transparent">
            </div>

            <div class="relative p-5 flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <h3 class="text-base font-extrabold tracking-tight text-slate-900">Tamu Terbaru</h3>
                    <p class="text-sm text-slate-500">Update kunjungan terakhir.</p>
                </div>
                <a href="{{ route('admin.guest.index') }}"
                    class="soft-ring inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Lihat semua
                    <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            </div>

            <div class="relative px-5 pb-5">
                @if (($recentGuestVisits ?? collect())->isEmpty())
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-600">
                        Belum ada data kunjungan.
                    </div>
                @else
                    <ul class="space-y-2">
                        @foreach ($recentGuestVisits as $visit)
                            @php
                                $arrived = $visit->arrived_at
                                    ? $visit->arrived_at->timezone($tz)->format('d M Y H:i') . ' WIB'
                                    : '-';
                                $status = $visit->completed_at ? 'Selesai' : 'Aktif';
                                $badge = $visit->completed_at
                                    ? 'bg-slate-100 text-slate-700'
                                    : 'bg-emerald-100 text-emerald-700';
                            @endphp
                            <li
                                class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3">
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-semibold text-slate-900">{{ $visit->name }}</div>
                                    <div
                                        class="mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs text-slate-500">
                                        <span>{{ $arrived }}</span>
                                        <span class="opacity-50">•</span>
                                        <span class="truncate">{{ $visit->dinas?->name ?? '—' }}</span>
                                    </div>
                                </div>
                                <span
                                    class="shrink-0 inline-flex rounded-full px-3 py-1 text-[11px] font-semibold {{ $badge }}">
                                    {{ $status }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        {{-- Survey --}}
        <div class="relative overflow-hidden rounded-2xl bg-white border border-slate-200 shadow-sm">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-sky-50 via-transparent to-transparent">
            </div>

            <div class="relative p-5 flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <h3 class="text-base font-extrabold tracking-tight text-slate-900">Survey Terbaru</h3>
                    <p class="text-sm text-slate-500">Masukan terbaru dari tamu.</p>
                </div>
                <a href="{{ route('admin.survey.index') }}"
                    class="soft-ring inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Lihat semua
                    <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            </div>

            <div class="relative px-5 pb-5">
                @if (($recentSurveys ?? collect())->isEmpty())
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-600">
                        Belum ada data survey.
                    </div>
                @else
                    <ul class="space-y-2">
                        @foreach ($recentSurveys as $survey)
                            @php
                                $submitted = $survey->submitted_at
                                    ? $survey->submitted_at->timezone($tz)->format('d M Y H:i') . ' WIB'
                                    : '-';
                                $rating = (int) ($survey->rating ?? 0);
                            @endphp
                            <li
                                class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3">
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-semibold text-slate-900">
                                        {{ $survey->visit?->name ?? '—' }}
                                    </div>
                                    <div
                                        class="mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs text-slate-500">
                                        <span>{{ $submitted }}</span>
                                        <span class="opacity-50">•</span>
                                        <span class="truncate">{{ $survey->visit?->dinas?->name ?? '—' }}</span>
                                    </div>
                                </div>
                                <span
                                    class="shrink-0 inline-flex items-center gap-1 rounded-full bg-sky-100 px-3 py-1 text-[11px] font-semibold text-sky-700">
                                    <x-icon name="star" class="h-3.5 w-3.5" />
                                    {{ $rating }}/5
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        {{-- Presensi --}}
        <div class="relative overflow-hidden rounded-2xl bg-white border border-slate-200 shadow-sm">
            <div
                class="pointer-events-none absolute inset-0 bg-gradient-to-br from-indigo-50 via-transparent to-transparent">
            </div>

            <div class="relative p-5 flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <h3 class="text-base font-extrabold tracking-tight text-slate-900">Presensi Terbaru</h3>
                    <p class="text-sm text-slate-500">Check-in/out terbaru.</p>
                </div>
                <a href="{{ route('admin.attendance.index') }}"
                    class="soft-ring inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Lihat semua
                    <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            </div>

            <div class="relative px-5 pb-5">
                @if (($recentAttendances ?? collect())->isEmpty())
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-600">
                        Belum ada data presensi.
                    </div>
                @else
                    <ul class="space-y-2">
                        @foreach ($recentAttendances as $att)
                            @php
                                $in = $att->check_in_at ? $att->check_in_at->timezone($tz)->format('d M H:i') : '-';
                                $out = $att->check_out_at ? $att->check_out_at->timezone($tz)->format('H:i') : null;
                                $status = $att->check_out_at ? 'Closed' : 'Open';
                                $badge = $att->check_out_at
                                    ? 'bg-slate-100 text-slate-700'
                                    : 'bg-indigo-100 text-indigo-700';
                            @endphp
                            <li
                                class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3">
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-semibold text-slate-900">
                                        {{ $att->user?->name ?? '—' }}</div>
                                    <div
                                        class="mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs text-slate-500">
                                        <span>{{ $in }}{{ $out ? '–' . $out : '' }} WIB</span>
                                        <span class="opacity-50">•</span>
                                        <span class="truncate">{{ $att->location?->name ?? '—' }}</span>
                                    </div>
                                </div>
                                <span
                                    class="shrink-0 inline-flex rounded-full px-3 py-1 text-[11px] font-semibold {{ $badge }}">
                                    {{ $status }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </section>

    @if ($isSuperAdmin)
        {{-- EXPORT UI SCRIPT (dropdown + loading + trigger download via iframe) --}}
        <script>
            (function() {
                const menuBtn = document.getElementById('btnExportMenu');
                const menu = document.getElementById('exportMenu');
                const chevron = document.getElementById('exportChevron');
                const dlFrame = document.getElementById('dlFrame');

                function openMenu() {
                    if (!menu) return;
                    menu.classList.remove('hidden');
                    if (chevron) chevron.style.transform = 'rotate(180deg)';
                }

                function closeMenu() {
                    if (!menu) return;
                    menu.classList.add('hidden');
                    if (chevron) chevron.style.transform = 'rotate(0deg)';
                }

                menuBtn?.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (!menu) return;
                    if (menu.classList.contains('hidden')) openMenu();
                    else closeMenu();
                });

                document.addEventListener('click', closeMenu);
                menu?.addEventListener('click', (e) => e.stopPropagation());

                const actions = document.querySelectorAll('.export-action');
                actions.forEach(btn => {
                    btn.addEventListener('click', () => {
                        const url = btn.getAttribute('data-url');
                        const label = btn.getAttribute('data-label') || 'Export';

                        // loading state
                        const original = btn.innerHTML;
                        btn.disabled = true;
                        btn.innerHTML = `
                            <span class="inline-flex items-center gap-2">
                                <span class="h-3 w-3 rounded-full border-2 border-slate-300 border-t-slate-700 animate-spin"></span>
                                <span>Mengekspor ${label}...</span>
                            </span>
                            <span class="text-xs text-slate-400">harap tunggu</span>
                        `;

                        closeMenu();

                        // download without redirect
                        if (url && dlFrame) dlFrame.src = url;

                        // restore after a moment
                        setTimeout(() => {
                            btn.disabled = false;
                            btn.innerHTML = original;
                        }, 1800);
                    });
                });
            })();
        </script>
    @endif
@endsection
